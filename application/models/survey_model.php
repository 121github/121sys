<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Survey_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
        $this->load->helper('array');
        if ($_SESSION['config']['use_fullname'] == 1) {
            $this->name_field = "fullname";
        } else {
            $this->name_field = "concat(title,' ',firstname,' ',lastname)";
        }
    }
    //function to return all the questions in a specific survey reference. This is used load the questions into a new survey.
    public function get_questions($survey_ref)
    {
        $qry = "select * from questions left join question_options using(question_id) left join questions_to_categories using(question_cat_id) where survey_info_id= '$survey_ref' order by sort,option_id";
        return $this->db->query($qry)->result_array();
    }
    
    
    //function to return all the question categories
    public function get_categories()
    {
        $qry    = "select * from questions_to_categories";
        $result = $this->db->query($qry)->result_array();
        $array  = array();
        foreach ($result as $row) {
            $array[$row['question_cat_id']] = htmlentities($row['question_cat_name']);
        }
        return $array;
    }
    
    //This function just returns a 1 if a survey has already been completed today. I used it to detect duplicate surveys etc.
    public function get_last_survey($urn)
    {
        $qry = "select survey_id from surveys where urn = '$urn' and date(completed_date) = curdate() order by survey_id desc limit 1";
        if ($this->db->query($qry)->num_rows()) {
            return $this->db->query($qry)->row('survey_id');
        } else {
            return false;
        }
    }
    
    //This function just returns any survey complete entries from the history table. There shouldn't be more than 1 in a day
    public function find_survey_updates($urn)
    {
        $qry = "select history_id from history where urn = '$urn' and outcome_id = '60' and date(contact) = curdate()";
        if ($this->db->query($qry)->num_rows()) {
            return 1;
        } else {
            return false;
        }
    }
    
    
    //function to return all the surveys in a specific campaign. Used in drop down menus to allow people to select a survey but lets us restrict the available surveys for a given campaign.
    public function get_surveys($campaign_id = "")
    {
        if (empty($campaign_id)):
            $qry = "select * from surveys_to_campaigns left join survey_info using(survey_info_id) and campaign_id in({$_SESSION['campaign_access']['list']})";
        else:
            $qry = "select * from surveys_to_campaigns left join survey_info using(survey_info_id) where campaign_id = '$campaign_id' and campaign_id in({$_SESSION['campaign_access']['list']})";
        endif;
        
        return $this->db->query($qry)->result_array();
    }
    
    
    public function get_record_surveys($urn = "")
    {
        $qry = "select s.urn,survey_id,date_format(s.date_created,'%d/%m/%y %H:%i') date_created,IF(s.completed_date is NULL,'Incomplete',date_format(s.completed_date,'%d/%m/%y %H:%i')) completed_date,IF(s.completed = 1,'Complete','Incomplete') is_completed,`{$this->name_field}` contact_name,if(urgent=1,'Yes','No') urgent, u.name client_name,survey_name, progress_id,progress_color,IF(pd.description IS NULL, 'Not Required', pd.description) progress , answer score,IF(answer is null,'-',answer) as answer, completed,s.user_id from surveys s left join survey_answers using(survey_id) left join survey_info using(survey_info_id) left join questions using(question_id)  left join records using(urn) left join progress_description pd using(progress_id) left join contacts using(contact_id) left join users u on s.user_id = u.user_id where 1 and survey_id is not null and nps_question = 1 and campaign_id in({$_SESSION['campaign_access']['list']})";
        if (!empty($urn)) {
            $qry .= " and s.urn = '$urn' ";
        }
        $qry .= " order by date_created desc";
        $array = array();
        foreach ($this->db->query($qry)->result_array() as $row) {
            if ($row['survey_id']) {
                $array[$row['survey_id']] = $row;
            }
        }
        return $array;
    }
    
    //function to return all the data relating to survey id. This is used on the edit survey page because it returns all the questions and the associated answers.
    public function get_existing_survey($survey)
    {
        $qry = "select survey_name, question_cat_id, question_cat_name, surveys.survey_info_id, {$this->name_field} client_name,contact_id,user_id,date_format(completed_date,'%d/%m/%y') completed_date,completed,date_format(surveys.date_created,'%d/%m/%y'), surveys.date_created, survey_id,surveys.urn,answer_id,question_id,answer,answer_notes.notes,question_name,other,question_script,question_guide,questions.sort,nps_question,multiple,option_name,question_options.option_id, answers_to_options.option_id as oid from surveys left join surveys_to_campaigns using(survey_info_id) left join survey_info using(survey_info_id) left join survey_answers using(survey_id) left join questions using(question_id) left join questions_to_categories using(question_cat_id) left join question_options using(question_id) left join answers_to_options using(answer_id) left join answer_notes using(answer_id)   left join contacts using(contact_id) where survey_id = '$survey' and campaign_id in({$_SESSION['campaign_access']['list']}) order by questions.sort";
        return $this->db->query($qry)->result_array();
    }
    
    //function to return all the answers for a specific survey id. I think this was used in the old popup style surveys
    public function get_answers($survey_id = "")
    {
        if (!empty($survey_id)) {
            $qry = "select * from survey_answers left join questions using(question_id)  left join survey_info using(survey_info_id) left join surveys using(survey_id) left join answer_notes using(answer_id) where survey_id= '$survey_id' ";
        }
        return $this->db->query($qry)->result_array();
    }
    
    //function to save a new survey and all the answers. Multiple choice options_id's and slider values are put into the answers_to_options table. Slider values also go into the survey_answers table in the answer column.
    public function save_survey($post)
    {
        //create the new survey array
        $survey = array(
            "urn" => $post["urn"],
            "date_created" => date('Y-m-d H:i:s'),
            "survey_updated" => date('Y-m-d H:i:s'),
            "contact_id" => $post["contact_id"],
            "survey_info_id" => $post["survey_info_id"],
            "user_id" => $_SESSION['user_id']
        );
        //if survey ID is posted we update the survey
        if (isset($post['survey_id'])) {
            $id = intval($post['survey_id']);
            $this->db->where('survey_id', $id);
            $this->db->update("surveys", array(
                'survey_updated' => date('Y-m-d H:i:s')
            ));
            $this->db->where('survey_id', $id);
            $this->db->delete('survey_answers');
        } else {
            //insert to db and get the new survey id
            $this->db->insert("surveys", $survey);
            $id = $this->db->insert_id();
            //update the recordcs table with the latest survey id
            $this->db->where("urn", intval($post["urn"]));
            $this->db->update("records", array(
                "last_survey_id" => $id
            ));
        }
        
        
        //insert a new row to the answer table for each question in the survey
        foreach ($post["answers"] as $question_id => $answer_array) {
            //insert the question
            if (isset($answer_array['slider'])) {
                $score = $answer_array['answer'][0];
            } else {
                $score = '';
            }
            $question = array(
                "survey_id" => $id,
                "question_id" => $question_id,
                "answer" => $score
            );
            $this->db->insert("survey_answers", elements(array(
                "survey_id",
                "question_id",
                "answer"
            ), array_filter($question), null));
            $insert_id = $this->db->insert_id();
            //insert the notes to the question
            if (!empty($answer_array['notes'])) {
                $notes = array(
                    "answer_id" => $insert_id,
                    "notes" => $answer_array['notes']
                );
                $this->db->insert("answer_notes", $notes);
            }
            if (isset($answer_array['answer'])&& !isset($answer_array['slider'])) {
                foreach ($answer_array['answer'] as $answer) {
                    if (!empty($answer)) {
                        $answers = array(
                            "answer_id" => $insert_id,
                            "option_id" => $answer
                        );
                        $this->db->insert("answers_to_options", $answers);
                    }
                }
            }
        }
        return $id;
    }
    
    
    //sets a survey as completed
    public function complete_survey($id)
    {
        //create the completed survey array
        $survey = array(
            "survey_updated" => date('Y-m-d H:i:s'),
            "completed_date" => date('Y-m-d H:i:s'),
            "completed" => "1"
        );
        $this->db->where("survey_id", $id);
        //update the record
        $this->db->update("surveys", $survey);
        
    }
    
    public function get_slider_triggers($survey_id)
    {
        $triggers = array();
        $qry      = "select question_id,trigger_score from surveys left join records using(urn) left join questions using(survey_info_id) where trigger_score is not null and survey_id = '$survey_id'";
        $result   = $this->db->query($qry)->result_array();
        foreach ($result as $row) {
            $triggers[$row['question_id']] = $row['trigger_score'];
        }
        return $triggers;
    }
    
    
    public function get_slider_answers($survey_id)
    {
        $answers = array();
        $qry     = "select question_id, answer,question_name from survey_answers left join questions using(question_id) where survey_id = '$survey_id'";
        $result  = $this->db->query($qry)->result_array();
        foreach ($result as $row) {
            $answers[$row['question_id']] = array(
                "answer" => $row['answer'],
                "question" => $row['question_name']
            );
        }
        return $answers;
    }
    
    
    public function get_option_triggers($survey_id)
    {
        $triggers = array();
        $qry      = "select question_id,option_id from surveys left join records using(urn) left join questions using(survey_info_id) left join question_options using(question_id) where trigger_email is not null and survey_id = '$survey_id'";
        $result   = $this->db->query($qry)->result_array();
        foreach ($result as $row) {
            $triggers[$row['question_id']][] = $row['option_id'];
        }
        return $triggers;
    }
    
    
    public function get_option_answers($survey_id)
    {
        $answers = array();
        $qry     = "select option_id, question_name, survey_answers.question_id,option_name from survey_answers left join answers_to_options using(answer_id) left join questions using(question_id) left join question_options using(option_id) where survey_id = '$survey_id'";
        $result  = $this->db->query($qry)->result_array();
        foreach ($result as $row) {
            $answers[$row['question_id']] = array(
                "question" => $row['question_name'],
                "option_id" => $row['option_id'],
                "option_name" => $row['option_name']
            );
        }
        return $answers;
    }
    
    public function get_all_surveys($options)
    {
        $table_columns = array(
            "campaign_name",
            "name",
            "fullname",
            "survey_name",
            "date_format(s.completed_date,'%d/%m/%y %H:%i')",
            "answer",
            "description",
            "rand()"
        );
        
        $qry = "select s.urn,campaign_name, survey_id,date_format(s.date_created,'%d/%m/%y %H:%i') date_created,IF(s.completed_date is NULL,'Incomplete',date_format(s.completed_date,'%d/%m/%y %H:%i')) completed_date,IF(s.completed = 1,'Complete','Incomplete') is_completed,fullname,if(urgent=1,'Yes','No') urgent, u.name,survey_name, progress_id,progress_color,IF(pd.description IS NULL, 'Not Required', pd.description) progress, answer, completed,s.user_id from surveys s left join survey_answers using(survey_id) left join survey_info using(survey_info_id) left join questions using(question_id)  left join records using(urn) left join campaigns using(campaign_id) left join progress_description pd using(progress_id) left join contacts using(contact_id) left join users u on s.user_id = u.user_id where 1 and survey_id is not null and nps_question = 1 ";
		
				$where = " and campaigns.campaign_id in(".$_SESSION['campaign_access']['list'].") ";
				if(isset($_SESSION['current_campaign'])){
				$where .= " and campaigns.campaign_id = '".$_SESSION['current_campaign']."'";
				}
				
			$qry .= $where;
		
        //check the tabel header filter
        foreach ($options['columns'] as $k => $v) {
            //if the value is not empty we add it to the where clause
            if ($v['search']['value'] <> "") {
                $qry .= " and " . $table_columns[$k] . " like '%" . $v['search']['value'] . "%' ";
            }
        }
        $count = $this->db->query($qry)->num_rows();
        $qry .= " order by CASE WHEN " . $table_columns[$options['order'][0]['column']] . " IS NULL THEN 1 ELSE 0 END," . $table_columns[$options['order'][0]['column']] . " " . $options['order'][0]['dir'];
        $start  = $options['start'];
        $length = $options['length'];
        
        $qry .= "  limit $start,$length";
        
        $data = $this->db->query($qry)->result_array();
        //$this->firephp->log($qry);
        return array(
            "data" => $data,
            "count" => $count
        );
    }
    
}

?>