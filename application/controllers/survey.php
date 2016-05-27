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
        $this->project_version = $this->config->item('project_version');
		$this->load->model('Filter_model');
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
                "survey.js?v" . $this->project_version,
			
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
        $title = "Edit survey";
        $data = array(
            'campaign_access' => $this->_campaigns,
			'page'=>$title,
			'title'=>$title,
			'submenu' => array(
                "file"=>'edit_survey.php',
                "title"=>$title
            ),
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
                "survey.js?v" . $this->project_version,
				
            )
        );
        $this->template->load('default', 'survey/edit.php', $data);
        
    }
    
    public function view()
    {
		
        
//this array contains data for the visible columns in the table on the view page
        $this->load->model('Datatables_model');
        $visible_columns = $this->Datatables_model->get_visible_columns(5);

        //Get the campaign_triggers if exists
        $campaign_triggers = array();
        if (isset($_SESSION['current_campaign'])) {
            $campaign_triggers = $this->Form_model->get_campaign_triggers_by_campaign_id($_SESSION['current_campaign']);
        }

        if (!$visible_columns) {
            $this->load->model('Admin_model');
            $this->Datatables_model->set_default_columns($_SESSION['user_id']);
            $visible_columns = $this->Datatables_model->get_visible_columns(5);
        }
        $_SESSION['col_order'] = $this->Datatables_model->selected_columns(false, 5);

        $title = "Surveys";
			$global_filter = false;
if(in_array("enable global filter",$_SESSION['permissions'])){
      		$global_filter = $this->Filter_model->build_global_filter();
		}
        $data = array(
		'global_filter'=>$global_filter,
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Surveys',
            'title' => $title,
            'page' => 'surveys',
            'submenu' => array(
                "file"=>'survey_list.php',
                "title"=>$title
            ),
            'columns' => $visible_columns,
            'css' => array(
                'daterangepicker-bs3.css',
                'plugins/bootstrap-toggle/bootstrap-toggle.min.css',
                'map.css',
                'plugins/bootstrap-iconpicker/icon-fonts/font-awesome-4.2.0/css/font-awesome.min.css',
                'plugins/bootstrap-iconpicker/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css'
            ),
            'javascript' => array(
                'view.js?v' . $this->project_version,
                'plugins/bootstrap-toggle/bootstrap-toggle.min.js',
                'plugins/fontawesome-markers/fontawesome-markers.min.js',
                'plugins/DataTables/datatables.min.js',
                'plugins/bootstrap-iconpicker/bootstrap-iconpicker/js/iconset/iconset-fontawesome-4.2.0.min.js',
                'plugins/bootstrap-iconpicker/bootstrap-iconpicker/js/bootstrap-iconpicker.min.js',
                'lib/moment.js',
                'lib/daterangepicker.js'
            ),
            "campaign_triggers" => $campaign_triggers
        );
        
        $this->template->load('default', 'survey/list.php', $data);
    }
    
    public function process_view()
    {
		 if ($this->input->is_ajax_request()) {
			 			
            session_write_close();
			/* debug loading times */
            $options = $this->input->post();
			$this->load->model('Datatables_model');
			$visible_columns = $this->Datatables_model->get_visible_columns(5);
			//$this->firephp->log($visible_columns);
			$options['visible_columns'] = $visible_columns;

			foreach($options['columns'] as $k=>$column){
				//$this->firephp->log($column);				
				if($column['data']=="color_icon"&&$column['search']['value']=="Icon"){
					$options['columns'][$k]['search']['value']="";
				}
					if($column['data']=="distance"){
					$distance_sql = $this->Datatables_model->get_distance_query();
					$options['visible_columns']['select'][$k] = $distance_sql . "distance";
					$options['visible_columns']['order'][$k] = $distance_sql;
					}
			}

            $surveys = $this->Survey_model->get_survey_data($options);
			$count = $surveys['count'];
			unset($surveys['count']);
            foreach ($surveys as $k => $v) {
               // $surveys[$k]["options"] = '<a href="'.base_url().'survey/edit/' . $v['survey_id'] . '"><span class="glyphicon glyphicon-eye-open view-survey"></span></a> <a href="'.base_url().'records/detail/' . $v['urn'] . '"><span class="glyphicon glyphicon glyphicon-play padl"></span></a>';
            }
            
            $data = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "data" => $surveys
            );
            echo json_encode($data);
        }
    } 
    
}

?>