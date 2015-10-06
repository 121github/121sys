<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Survey extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        user_auth_check();
		check_page_permissions('view surveys');
$this->_campaigns = campaign_access_dropdown();

        $this->load->model('Survey_model');
        $this->load->model('Records_model');
        $this->load->model('Contacts_model');
        $this->load->model('Form_model');
		$this->load->model('User_model');
		$this->_access = $this->User_model->campaign_access_check($this->input->post('urn'), true);
    }
    
    //return questions in ajax format
    public function get_questions()
    {
        if ($this->input->is_ajax_request()) {
            $survey_ref = intval($this->input->post('survey_ref'));
            $data       = $this->Survey_model->get_questions($survey_ref);
            if ($data):
                echo json_encode(array(
                    "success" => true,
                    "data" => $data
                ));
            else:
                echo json_encode(array(
                    "success" => false,
                    "data" => "Failed to get survey questions"
                ));
            endif;
        }
    }
    
    //save the survey form
    public function save_survey()
    {
        if ($this->input->is_ajax_request()) {
            $id = $this->Survey_model->save_survey($this->input->post());
            if ($this->input->post("complete")) {
                $this->Survey_model->complete_survey($id);
            }
            if ($id):
                echo json_encode(array(
                    "success" => true,
                    "msg" => "Survey was saved",
                    "id" => $id
                ));
            else:
                echo json_encode(array(
                    "success" => false,
                    "msg" => "Failed to save survey"
                ));
            endif;
        }
    }
    
    
    //return the questions and answers for a given survey id
    public function get_answers()
    {
        if ($this->input->is_ajax_request()) {
            $id   = intval($this->input->post('id'));
            $data = $this->Survey_model->get_answers($id);
            echo json_encode(array(
                "success" => true,
                "data" => $data
            ));
        }
    }
    
    //load all the questions into a new survey form
    public function create()
    {
        $urn             = intval($this->uri->segment(4));
        $survey          = intval($this->uri->segment(3));
        $contact_id      = intval($this->uri->segment(5));
        $categories      = $this->Survey_model->get_categories();
        $campaign        = $this->Records_model->get_campaign($urn);
        $contact_details = $this->Contacts_model->get_contact($contact_id);
        $question_data   = $this->Survey_model->get_questions($survey);
        $questions       = array();
        foreach ($question_data as $id => $row) {
            $questions[$row['question_cat_id']][$row['question_id']]['question_id']                = $row['question_id'];
            $questions[$row['question_cat_id']][$row['question_id']]['options'][$row['option_id']] = htmlspecialchars($row['option_name'], ENT_QUOTES, 'UTF-8');
            $questions[$row['question_cat_id']][$row['question_id']]['question']                   = htmlspecialchars($row['question_name'], ENT_QUOTES, 'UTF-8');
            $questions[$row['question_cat_id']][$row['question_id']]['script']                     = htmlspecialchars($row['question_script'], ENT_QUOTES, 'UTF-8');
            $questions[$row['question_cat_id']][$row['question_id']]['other']                      = htmlspecialchars($row['other'], ENT_QUOTES, 'UTF-8');
            $questions[$row['question_cat_id']][$row['question_id']]['guide']                      = htmlspecialchars($row['question_guide'], ENT_QUOTES, 'UTF-8');
            $questions[$row['question_cat_id']][$row['question_id']]['multiple']                   = $row['multiple'];
        }
        
        $data = array(
            'urn' => $urn,
            'campaign_access' => $this->_campaigns,
'campaign_pots' => $this->_pots,
'pageId' => 'Create-survey',
            'title' => 'Create new survey',
            'campaign' => $campaign,
            "contact_id" => $contact_id,
            "contact" => $contact_details['general'],
            "questions" => $questions,
            'survey_info_id' => $survey,
            'categories' => $categories,
            "javascript" => array(
				"lib/bootstrap-slider.js",
                "survey.js",
			
            )
        );
        
        $this->template->load('default', 'survey/view.php', $data);
        
    }
    
    
    //load all the questions into a new survey form
    public function edit()
    {
        $survey      = intval($this->uri->segment(3));
        $survey_data = $this->Survey_model->get_existing_survey($survey);
        $categories  = $this->Survey_model->get_categories();
        $questions   = array();
        foreach ($survey_data as $row) {
            $contact        = $row['client_name'];
            $contact_id     = $row['contact_id'];
            $user_id        = $row['user_id'];
            $date_created   = $row['date_created'];
            $completed_date = $row['completed_date'];
            $urn            = $row['urn'];
            //administrators can edit any survey. USers can only edit their own surveys
            if ($user_id == $_SESSION['user_id'] || $_SESSION['role'] == "1" || in_array("edit surveys",$_SESSION['permissions'])) {
                $locked = false;
            } else {
                $locked = true;
            }
            
            if (isset($row['option_id'])) {
                $questions[$row['question_cat_id']][$row['question_id']]['options'][$row['option_id']] = htmlspecialchars($row['option_name'], ENT_QUOTES, 'UTF-8');
            }
            $questions[$row['question_cat_id']][$row['question_id']]['question_id']                     = $row['question_id'];
            $questions[$row['question_cat_id']][$row['question_id']]['question']                        = htmlspecialchars($row['question_name'], ENT_QUOTES, 'UTF-8');
            $questions[$row['question_cat_id']][$row['question_id']]['script']                          = htmlspecialchars($row['question_script'], ENT_QUOTES, 'UTF-8');
            $questions[$row['question_cat_id']][$row['question_id']]['guide']                           = htmlspecialchars($row['question_guide'], ENT_QUOTES, 'UTF-8');
            $questions[$row['question_cat_id']][$row['question_id']]['multiple']                        = $row['multiple'];
            $questions[$row['question_cat_id']][$row['question_id']]['other']                           = htmlspecialchars($row['other']);
            $questions[$row['question_cat_id']][$row['question_id']]['answers']['options'][$row['oid']] = $row['oid'];
            $questions[$row['question_cat_id']][$row['question_id']]['answers']['answer']               = htmlspecialchars($row['answer'], ENT_QUOTES, 'UTF-8');
            $questions[$row['question_cat_id']][$row['question_id']]['answers']['note']                 = htmlspecialchars($row['notes'], ENT_QUOTES, 'UTF-8');
        }
        
        $data = array(
            'campaign_access' => $this->_campaigns,
'campaign_pots' => $this->_pots,
'pageId' => 'Edit-survey',
            'title' => 'Edit survey',
            'urn' => $urn,
            'survey_id' => $survey,
            'survey_info_id' => $survey_data[0]['survey_info_id'],
            'survey_name' => $survey_data[0]['survey_name'],
            'contact' => $contact,
            'contact_id' => $contact_id,
            'user_id' => $user_id,
            'categories' => $categories,
            "questions" => $questions,
            "date_created" => $date_created,
            "completed_date" => $completed_date,
            "locked" => $locked,
            "javascript" => array(
			"lib/bootstrap-slider.js",
                "survey.js",
				
            )
        );
        $this->template->load('default', 'survey/edit.php', $data);
        
    }
    
    public function view()
    {
        
        //this array contains data for the visible columsn in the table on the view page
        $visible_columns   = array();
        $visible_columns[] = array(
            "column" => "campaign_name",
            "header" => "Campaign"
        );
        $visible_columns[] = array(
            "column" => "name",
            "header" => "Agent"
        );
        $visible_columns[] = array(
            "column" => "fullname",
            "header" => "Contact"
        );
        $visible_columns[] = array(
            "column" => "survey_name",
            "header" => "Survey"
        );
        $visible_columns[] = array(
            "column" => "completed_date",
            "header" => "Date Completed"
        );
        $visible_columns[] = array(
            "column" => "score",
            "header" => "NPS"
        );
        $visible_columns[] = array(
            "column" => "progress",
            "header" => "Follow Up"
        );
        $visible_columns[] = array(
            "column" => "options",
            "header" => "Options"
        );
        
        
        $data = array(
            'campaign_access' => $this->_campaigns,
'campaign_pots' => $this->_pots,
'pageId' => 'List-survey',
            'title' => 'List Surveys',
            'columns' => $visible_columns,
            'javascript' => array(
				'plugins/DataTables/js/jquery.dataTables.min.js',
			"lib/bootstrap-slider.js",
                'survey_view.js',
			
            ),

        );
        
        $this->template->load('default', 'survey/list.php', $data);
    }
    
    public function process_view()
    {
		 if ($this->input->is_ajax_request()) {
			 			
            $surveys = $this->Survey_model->get_all_surveys($this->input->post());
            foreach ($surveys['data'] as $k => $v) {
                $surveys['data'][$k]["options"] = '<a href="'.base_url().'survey/edit/' . $v['survey_id'] . '"><span class="glyphicon glyphicon-eye-open view-survey"></span></a> <a href="'.base_url().'records/detail/' . $v['urn'] . '"><span class="glyphicon glyphicon glyphicon-play padl"></span></a>';
            }
            
            $data = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $surveys['count'],
                "recordsFiltered" => $surveys['count'],
                "data" => $surveys['data']
            );
            echo json_encode($data);
        }
    } 
    
}

?>