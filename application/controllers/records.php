<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Records extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        user_auth_check();
		$this->_campaigns = campaign_access_dropdown();
        $this->load->model('User_model');
        $this->load->model('Records_model');
        $this->load->model('Survey_model');
        $this->load->model('Form_model');
        $this->_access = $this->User_model->campaign_access_check($this->input->post('urn'), true);
		$this->_campaign = $_SESSION['current_campaign'];
    }
    
    //list the records in the callpot
    
    public function view()
    {
        //this array contains data for the visible columns in the table on the view page
        $visible_columns[] = array(
            "column" => "campaign_name",
            "header" => "Campaign"
        );
        $visible_columns[] = array(
            "column" => "fullname",
            "header" => "Client Name"
        );
        $visible_columns[] = array(
            "column" => "outcome",
            "header" => "Last Outcome"
        );
        $visible_columns[] = array(
            "column" => "date_updated",
            "header" => "Last Updated"
        );
        $visible_columns[] = array(
            "column" => "nextcall",
            "header" => "Next Call"
        );
        $visible_columns[] = array(
            "column" => "options",
            "header" => "Options"
        );
        
        $data = array(
            'campaign_access' => $this->_campaigns,
'pageId' => 'list-records',
            'title' => 'List Records',
            'columns' => $visible_columns,
            'javascript' => array(
                'view.js'
            )
        );
        $this->template->load('default', 'records/list_records.php', $data);
        
    }
    
    public function process_view()
    {
        if ($this->input->is_ajax_request()) {
            
			if(!isset($_SESSION['filter'])){
				$_SESSION['filter']['where'] = " and r.campaign_id in(".$_SESSION['campaign_access']['list'].") and r.campaign_id = '".$_SESSION['current_campaign']."' and r.record_status = 1";
			}
			
            $records = $this->Records_model->get_records($this->input->post());
            $nav     = $this->Records_model->get_nav($this->input->post());
            
            foreach ($records as $k => $v) {
                $records[$k]["options"] = "<a href='" . base_url() . "records/detail/" . $v['urn'] . "'>View</a>";
            }
            
            $data = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => count($nav),
                "recordsFiltered" => count($nav),
                "data" => $records
            );
            echo json_encode($data);
        }
    }
    
    public function detail()
    {
		if(!in_array('search',$_SESSION['campaign_features'])&&!in_array("search page",$_SESSION['permissions'])||!intval($this->uri->segment(3))){
		//if the campaign does not have search enable and the user does not have search permissions then they are given a record
		$urn = $this->Records_model->get_record();
		$automatic = true;
		//unsetting navigation array because it's not needed when users are using the automated method
		if(isset($_SESSION['nav'])){
		unset($_SESSION['nav']);
		}
		} else {
		//a record can be selected using a crafted url /records/detail/[urn]
		$automatic = false;
        $urn = intval($this->uri->segment(3));
		}
        //$this->User_model->campaign_access_check($urn);
		
        
        //get the features for the campaign and put the ID's into an array
        $campaign_features = $this->Form_model->get_campaign_features($this->_campaign);
        $features          = array();
        foreach ($campaign_features as $row) {
            $features[]         = $row['id'];
			if(!empty($row['path'])){
            $panels[$row['id']] = $row['path'];
			}
        }
        
        //get the details of the record for the specified features
        $details          = $this->Records_model->get_details($urn, $features);
        $progress_options = $this->Form_model->get_progress_descriptions();
        $outcomes         = $this->Records_model->get_outcomes($this->_campaign);
        $xfers            = $this->Records_model->get_xfers($this->_campaign);
		if(isset($details['contacts'])){
        foreach ($details['contacts'] as $contact_id => $contact_data) {
            $survey_options["contacts"][$contact_id] = $contact_data["name"]["fullname"];
        }
		}
        $prev = false;
        $next = false;
        if (isset($_SESSION['navigation'])) {
            foreach ($_SESSION['navigation'] as $key => $val):
                if ($val == $urn) {
                    $prev = ($key > 0 ? $_SESSION['navigation'][$key - 1] : "");
                    $next = ($key < count($_SESSION['navigation']) - 1 ? $_SESSION['navigation'][$key + 1] : "");
                }
            endforeach;
        }
        
        
        $data = array(
            'campaign_access' => $this->_campaigns,
'pageId' => 'list-records',
            'title' => 'Record Details',
            'details' => $details,
            'outcomes' => $outcomes,
            "features" => $features,
            "panels" => $panels,
            "xfer_campaigns" => $xfers,
            "progress_options" => $progress_options,
			"automatic"=>$automatic,
            "javascript" => array(
                "detail2.js"
            ),
            'nav' => array(
                'prev' => $prev,
                'next' => $next
            )
        );
        
        //get the users if we need the ownership feature is on
        if (in_array(5, $features)) {
            $users         = $this->Records_model->get_users();
            $data['users'] = $users;
        }
        //get surveys if this feature is turned on
        if (in_array(11, $features)) {
            $available_surveys         = $this->Form_model->get_surveys($this->_campaign);
            $survey_options["surveys"] = $available_surveys;
            $data['survey_options']    = $survey_options;
        }
        
        //get templates for Emails if this feature is turned on
        if (in_array(9, $features)) {
            $email_options              = array();
            $templates                  = $this->Form_model->get_templates_by_campaign_id($this->_campaign);
            $email_options["templates"] = $templates;
            $data['email_options']      = $email_options;
        }
        
        $this->template->load('default', 'records/custom/2col.php', $data);
        
    }
    
    /*save sticky note on the details page*/
    public function save_notes()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            $this->db->where("urn", $this->input->post('urn'));
            if ($this->db->replace('sticky_notes', array(
                "note" => $this->input->post('notes'),
                "urn" => $this->input->post('urn'),
                "updated_by" => $_SESSION['user_id']
            ))):
                echo json_encode(array(
                    "success" => true
                ));
            endif;
        } else {
            echo "Denied";
            exit;
        }
    }
    /*save contact details */
    public function save_contact()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            $this->db->where("urn", $this->input->post('urn'));
            if ($this->db->update('contacts', array(
                "notes" => $this->input->post('notes')
            ))):
                echo json_encode(array(
                    "success" => true
                ));
            endif;
        } else {
            echo "Denied";
            exit;
        }
    }
    
    /*add/remove record from user favorites list */
    public function set_favorites()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            //if the action is add we add it to the table using "replace" to avoud dupes
            if ($this->input->post('action') == "add") {
                $this->db->replace('favorites', array(
                    "urn" => intval($this->input->post('urn')),
                    "user_id" => $_SESSION['user_id']
                ));
                echo json_encode(array(
                    "added" => true,
                    "msg" => "Record was added to your favourites"
                ));
                exit;
            } else {
                //we delete iot form the table
                $this->db->where(array(
                    "urn" => intval($this->input->post('urn')),
                    "user_id" => $_SESSION['user_id']
                ));
                $this->db->delete("favorites");
                echo json_encode(array(
                    "removed" => true,
                    "msg" => "Record was removed from your favourites"
                ));
                exit;
            }
            
        } else {
            echo "Denied";
            exit;
        }
    }
    
    
    /*set/unset record as urgent */
    public function set_urgent()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            //we set it as urgent and set pending to 1
            if ($this->input->post('action') == "add") {
                $this->db->where("urn", intval($this->input->post('urn')));
                $this->db->update('records', array(
                    "urgent" => "1"
                ));
                echo json_encode(array(
                    "added" => true,
                    "msg" => "Record was set as urgent"
                ));
                exit;
            } else {
                //we unset it as urgent
                $this->db->where("urn", intval($this->input->post('urn')));
                $this->db->update('records', array(
                    "urgent" => NULL
                ));
                echo json_encode(array(
                    "removed" => true,
                    "msg" => "Record was unset as urgent"
                ));
                exit;
            }
            
        } else {
            echo "Denied";
            exit;
        }
    }
    
    public function get_triggers($outcome_id = "")
    {
        $this->db->where("outcome_id", $outcome_id);
        $outcome  = $this->db->get("outcomes")->result_array();
        $triggers = array();
        foreach ($outcome as $k => $v) {
            if (!empty($v)) {
                $triggers = $v;
            }
        }
        return $triggers;
    }
    
    public function check_email_triggers($outcome_id = "")
    {
        $this->db->where(array(
            "outcome_id" => $outcome_id,
            "campaign_id" => $this->_campaign
        ));
        if ($this->db->get("email_triggers")->num_rows() > 0) {
            return true;
        }
    }
    
    //this will reset a record - outcomes, progress and nextcall will be set to null and the status will be set to live again
    public function reset_record()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            if ($this->Records_model->reset_record(intval($this->input->post('urn')))):
                echo json_encode(array(
                    "success" => true,
                    "msg" => "Record was reset. Please check the ownership is correct"
                ));
            else:
                echo json_encode(array(
                    "success" => false,
                    "msg" => "Record could not be reset. Contact the administrator"
                ));
            endif;
        } else {
            echo "Denied";
            exit;
        }
    }
		
	
    /*update record details */
    public function update()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            $survey_outcome  = false;
            $update_array    = $this->input->post();
            $triggers        = array();
            $survey_triggers = array();
            $last_survey_id  = false;
            //by default we add an entry to the history table unless the outcome has a no_history flag in the outcomes table, in which case we dont want to add an entry
            $no_history      = false;
            if (!$this->input->post('pending_manager')) {
                $update_array['pending_manager'] = "";
            }
            
            //check the outcome and execute any triggers
            if ($this->input->post('outcome_id')) {
                $triggers = $this->get_triggers(intval($this->input->post('outcome_id')));
                if ($triggers["force_comment"] == "1" && trim($update_array['comments']) == "") {
                    echo json_encode(array(
                        "success" => false,
                        "msg" => "This outcome requires you to leave notes"
                    ));
                    exit;
                }
                if ($update_array["pending_manager"] <> "" && trim($update_array['comments']) == "") {
                    echo json_encode(array(
                        "success" => false,
                        "msg" => "Please leave notes where further action is required"
                    ));
                    exit;
                }
                if ($triggers["set_status"] && $update_array["pending_manager"] == "") {
                    //if set_status is not null and client action is not required then we set the status to whatever is defined in the outcome table
                    $this->Records_model->set_status($update_array['urn'], $triggers["set_status"]);
                }
                if (intval($triggers["delay_hours"]) > 0) {
					if($_SESSION['permissions']<>"keep records"){
						//delete all owners so it can get called back by anyone (answer machines etc)
						$this->Records_model->save_ownership($update_array['urn'],array());
					}
                    $delay                    = $triggers['delay_hours'];
                    $update_array['nextcall'] = date('Y-m-d H:i', strtotime("+$delay hours"));
                }
                
                if ($triggers["no_history"] == "1") {
                    $no_history = true;
                }
                //the survey_complete ID is 60 on this system
                if ($update_array['outcome_id'] == 60) {
                   
					$survey_outcome = true;
					 /*
                    if ($this->Survey_model->find_survey_updates($update_array['urn'])) {
                        //if a survey complete outcome already exists in the history table for today
                        echo json_encode(array(
                            "success" => false,
                            "msg" => "Survey complete outcome has already been set today"
                        ));
                        exit;
                        
                    }
					*/
                    $last_survey_id = $this->Survey_model->get_last_survey($update_array['urn']);
                    if (!$last_survey_id) {
                        //if a survey has not been completed
                        echo json_encode(array(
                            "success" => false,
                            "msg" => "No survey has been completed on this record today"
                        ));
                        exit;
                    }
					
                }
				
            }
			if(isset($update_array['xfer_campaign'])){
			$xfer_campaign = $update_array['xfer_campaign'];
			unset($update_array['xfer_campaign']);
			}
			
            $this->Records_model->update_record($update_array);
            $hist = $update_array;
            $hist['campaign_id'] = $this->_campaign;

            //if the pending_manager requires attention we assign the record to the campaign managers
            if ($update_array['pending_manager'] > 0 || $survey_outcome || $this->input->post('outcome_id')) {
				//check if the outcome triggers an ownership update
				$outcome_owners = $this->Records_model->get_owners_for_outcome($this->_campaign,$update_array['outcome_id']);
				
				if(count($outcome_owners)>0){
					$this->Records_model->save_ownership(intval($this->input->post('urn')), $outcome_owners);
				} else {
				$owners = $this->Records_model->get_campaign_managers($this->_campaign);
                if (count($owners) > 0) {
                    $this->Records_model->save_ownership(intval($this->input->post('urn')), $owners);
                }
				}
            } 
			else {
                //check ownership and if nobody has this record assign it to the person that just updated it
				if($_SESSION['permissions']=="keep records"){
                $owners = $this->Records_model->get_ownership(intval($this->input->post('urn')));
                if (!count($owners)) {
                    $owner = array(
                        $_SESSION['user_id']
                    );
                    $this->Records_model->save_ownership(intval($this->input->post('urn')), $owner);
                }
				}
            }
            
            if ($survey_outcome) {
                $hist['last_survey'] = $last_survey_id;
                //get all the answer options and question trigger thresholds
                $slider_triggers     = $this->Survey_model->get_slider_triggers($last_survey_id);
                $option_triggers     = $this->Survey_model->get_option_triggers($last_survey_id);
                //then we loop through each answer in the last survey and if any scored below 6 on nps we email the managers
                $slider_answers      = $this->Survey_model->get_slider_answers($last_survey_id);
                $option_answers      = $this->Survey_model->get_option_answers($last_survey_id);
                //$this->survey_alert($last_survey_id, $survey_triggers);
                $email = array();
                foreach ($slider_triggers as $q => $a) {
                    if ($slider_answers[$q]['answer'] <= $a && intval($slider_answers[$q]['answer']) > 0) {
                        $pending             = true;
                        $hist['progress_id'] = "1";
                        $send_email          = true;
                        $survey_triggers[]   = array(
                            "question" => $slider_answers[$q]['question'],
                            "answer" => $slider_answers[$q]['answer']
                        );
                    }
                }
                
                foreach ($option_answers as $q => $row) {
                    if (array_key_exists($row['option_id'], $option_triggers)) {
                        $pending             = true;
                        $hist['progress_id'] = "1";
                        $send_email          = true;
                        $survey_triggers[]   = array(
                            "question" => $row['question_name'],
                            "answer" => $row['option_name']
                        );
                    }
                }
				$this->firephp->log($survey_triggers);
                if (count($survey_triggers) > 0) {
					$this->Records_model->set_pending($update_array['urn']);
                    $this->survey_alert($last_survey_id, $survey_triggers);
                }
            }
			
			
            //if a progress_id was sent we should add this to the history entry
            if ($this->input->post("progress_id")) {
                $hist['progress_id'] = $this->input->post("progress_id");
            }
            //push a description into the post array and then send it to add_history function
            $hist['description'] = "Record was updated";
            //if the outcome does not have a "no_history" flag we add the update to the history table
            if (!$no_history) {
                $id = $this->Records_model->add_history($hist);
				if(isset($xfer_campaign)){
				$this->Records_model->add_xfer($id,$xfer_campaign);
				}
            }
            
            //return success to page
            echo json_encode(array(
                "success" => true,
                "msg" => "Record was updated"
            ));
        } else {
            echo "Denied";
            exit;
        }
    }
    
    public function load_appointments()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            $appts = $this->Records_model->get_appointments($this->input->post("urn"));
            foreach ($appts as $k => $row) {
                $appts[$k]['time'] = date('g:i a', strtotime($row['start']));
                $appts[$k]['date'] = date('jS M y', strtotime($row['start']));
            }
            //return success to page
            echo json_encode(array(
                "success" => true,
                "data" => $appts
            ));
        } else {
            echo "Denied";
            exit;
        }
    }
    
    public function save_appointment()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            $this->Records_model->save_appointment($this->input->post());
            
            //return success to page
            echo json_encode(array(
                "success" => true
            ));
        } else {
            echo "Denied";
            exit;
        }
    }
    
    public function delete_appointment()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            $this->Records_model->delete_appointment($this->input->post('id'));
            
            //return success to page
            echo json_encode(array(
                "success" => true
            ));
        } else {
            echo "Denied";
            exit;
        }
    }
    
    
    //this function is triggered during the update if the question trigger is below the answer or if an option on a multiple choise is set as a trigger. It sends the email with a list of all teh answers that triggered it. The email is sent to the recipients in the trigger_recipients table for the trigger with the survey outcome. If it does not exit then it does not get sent.
    public function survey_alert($survey, $survey_triggers)
    {
        $this->load->model('Contacts_model');
        $this->load->model('Email_model');
        $data = $this->Email_model->survey_email($survey);
        foreach ($data as $row) {
            if ($row['nps_question'] == 1) {
                $urn         = $row['urn'];
                $this->_campaign = $row['campaign_id'];
                $nps         = $row['answer'];
                $contact_id  = $row['contact_id'];
                $survey_name = $row['survey_name'];
                $notes       = $row['notes'];
                $urgent      = $row['urgent'];
            }
        }
        $outcome_id   = 60; //the survey outcome_id
        $recipients   = $this->Email_model->get_recipients($this->_campaign, $outcome_id);
        $contact      = $this->Contacts_model->get_contact($contact_id);
        $contact_name = $contact['general']['fullname'];
        $comments     = $this->Records_model->get_last_comment($urn);
        
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        
        $this->email->from("noreply@leadcontrol.co.uk", '121 Systems');
        $this->email->subject('Survey Response');
        $this->email->to($recipients);
        $this->email->bcc("bradf@121customerinsight.co.uk");
        $msg = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Survey Email</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
</html><body>';
        $msg .= "<span style='font-family: Arial, Helvetica, sans-serif;font-size: 12px;'><h4>A survey was completed with one of more answers triggering an alert</h4>" . "<table width='100%' style='text-align: center;font-family: Arial, Helvetica, sans-serif; border-spacing: 0;border-collapse: collapse;'>" . "<tr>" . "<th style='border-bottom: 2px solid #ddd;'>URN</th>" . "<th style='border-bottom: 2px solid #ddd;'>Survey</th>" . "<th style='border-bottom: 2px solid #ddd;'>NPS</th>" . "<th style='border-bottom: 2px solid #ddd;'>Notes</th>" . "<th style='border-bottom: 2px solid #ddd;'>Urgent</th>" . "</tr>";
        $i       = 0;
        $bgColor = $i % 2 === 0 ? '#F3F3F3' : '#FFFFFF';
        $msg .= "<tr style='background-color: $bgColor'>" . "<td style='border-bottom: 1px solid #ddd;'>" . $urn . "</td>" . "<td style='border-bottom: 1px solid #ddd;'>" . $survey_name . "</td>" . "<td style='border-bottom: 1px solid #ddd;'>" . (!empty($nps) ? $nps : "-") . "</td>" . "<td style='border-bottom: 1px solid #ddd;'>" . (!empty($notes) ? $notes : "-") . "</td>" . "<td style='border-bottom: 1px solid #ddd;" . (!empty($urgent) ? "color:red" : "") . "'>" . (!empty($urgent) ? "Yes" : "No") . "</td>" . "</tr>";
        
        $msg .= "</table>";
        $msg .= "<h4>Answers that triggered this alert</h4>";
        $msg .= "<table width='100%' style='text-align: center;font-family: Arial, Helvetica, sans-serif; border-spacing: 0;border-collapse: collapse;'>" . "<tr>" . "<th style='border-bottom: 2px solid #ddd;'>Question</th>" . "<th style='border-bottom: 2px solid #ddd;'>Answer</th>" . "<th style='border-bottom: 2px solid #ddd;'>Notes</th>" . "</tr>";
        
        foreach ($survey_triggers as $row) {
            $msg .= "<tr style='background-color: $bgColor'>" . "<td style='border-bottom: 1px solid #ddd;'>" . $row['question'] . "</td>" . "<td style='border-bottom: 1px solid #ddd;'>" . $row['answer'] . "</td>" . "<td style='border-bottom: 1px solid #ddd;'>" . (!empty($notes) ? $notes : "-") . "</td>" . "</tr>";
        }
        $msg .= "</table>";
        $msg .= "<p>Contact Name: <b>" . $contact_name . "<b>";
        //only include comments if there are any
        if (!empty($comments)) {
            $msg .= "<p>Comments<br>" . $comments . "</p>";
        }
        
        
        
        $msg .= "</span><p style='font-family: Arial, Helvetica, sans-serif;font-size: 12px;'>Please click the link below to view the record details<br>
					<a href='" . base_url() . "records/detail/$urn'>" . base_url() . "records/detail/$urn</a>
					</p></body></html>";
        
        $i++;
        
        $this->email->message($msg);
        $this->email->send();
        //$this->firephp->log($this->email->print_debugger());
        $this->email->clear();
        
        
    }
    
    
}
?>