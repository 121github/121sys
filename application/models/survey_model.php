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
        $qry = "select * from questions q left join question_options o using(question_id) left join questions_to_categories using(question_cat_id) where survey_info_id= '$survey_ref' order by q.sort,o.sort, option_id";
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
        $qry = "select survey_name, question_cat_id, question_cat_name, surveys.survey_info_id, {$this->name_field} client_name,contact_id,user_id,date_format(completed_date,'%d/%m/%y') completed_date,completed,date_format(surveys.date_created,'%d/%m/%y'), surveys.date_created, survey_id,surveys.urn,answer_id,question_id,answer,answer_notes.notes,question_name,other,question_script,question_guide,questions.sort,nps_question,multiple,option_name,question_options.option_id, answers_to_options.option_id as oid from surveys left join surveys_to_campaigns using(survey_info_id) left join survey_info using(survey_info_id) left join survey_answers using(survey_id) left join questions using(question_id) left join questions_to_categories using(question_cat_id) left join question_options using(question_id) left join answers_to_options using(answer_id) left join answer_notes using(answer_id)   left join contacts using(contact_id) where survey_id = '$survey' and campaign_id in({$_SESSION['campaign_access']['list']}) order by questions.sort,question_options.sort";
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
        $qry     = "select question_id, answer,question_name, notes from survey_answers left join answer_notes using(answer_id) left join questions using(question_id) where survey_id = '$survey_id'";
        $result  = $this->db->query($qry)->result_array();
        foreach ($result as $row) {
            $answers[$row['question_id']] = array(
                "answer" => $row['answer'],
                "question" => $row['question_name'],
				"notes" => $row['notes']
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
        $qry     = "select option_id, question_name, survey_answers.question_id,option_name,an.notes from survey_answers left join answers_to_options using(answer_id) left join answer_notes an using(answer_id) left join questions using(question_id) left join question_options using(option_id) where survey_id = '$survey_id'";
        $result  = $this->db->query($qry)->result_array();
        foreach ($result as $row) {
            $answers[$row['question_id']] = array(
                "question" => $row['question_name'],
                "option_id" => $row['option_id'],
                "option_name" => $row['option_name'],
				"notes" => $row['notes']
            );
        }
        return $answers;
    }
    
    public function get_survey_data($options)
    {	
		 $tables = $options['visible_columns']['tables'];
      $columns =  $options['visible_columns']['columns'];
        $table_columns = $options['visible_columns']['select'];
        $filter_columns = $options['visible_columns']['filter'];
        $order_columns = $options['visible_columns']['order'];	
$datafield_ids = array();
		foreach($table_columns as $k=>$col){
				$datafield_ids[$k] = 0;	
		if(strpos($col,"custom_")!==false){
			$split = explode("_",$col);
			$datafield_ids[$k] = intval($split[1]);
			$filter_columns[$k] = "t_".intval($split[1]).".value";
			$order_columns[$k] = "t_".intval($split[1]).".value";
			$table_columns[$k] = "t_".intval($split[1]).".value " .$columns[$k]['data'];
		}
		}
  //these tables must be joined to the query regardless of the selected columns to allow the map to function
        $required_tables = array("campaigns", "companies","company_addresses", "survey_contacts" ,"survey_locations", "company_locations","ownership");
        foreach ($required_tables as $rt) {
            if (!in_array($rt, $tables)) {
                $tables[] = $rt;
            }
        }
		   $join = array();
        //add mandatory column selections here
           $required_select_columns = array(
            "r.urn",
			 "r.record_color"
        );

				          //if any of the mandatory columns are missing from the columns array we push them in
        foreach ($required_select_columns as $required) {
            if (!in_array($required, $table_columns)) {
                $table_columns[] = $required;
            }
        }
		 $qry = "";
        //turn the selection array into a list
        $selections = implode(",", $table_columns);	  
        $select = "select $selections from surveys join records r using(urn) ";
	$numrows = "select count(distinct surveys.survey_id) numrows
                from surveys join records r using(urn) ";
        $table_joins = table_joins();
		unset($table_joins['appointments']);
        $join_array = join_array();
		unset($join_array['appointments']);

      $tablenum=0;
	  $tableappnum=0;
        foreach ($tables as $k=>$table) {
			if($table=="custom_panels"){ $tablenum++;
			$field_id = $datafield_ids[$k];
				$join[] = " left join (select max(id) id,urn from custom_panel_values join custom_panel_data using(data_id) where field_id = '$field_id' group by urn) mc_$field_id on mc_$field_id.urn =  r.urn left join  custom_panel_values t_$field_id on t_$field_id.id = mc_$field_id.id ";
			}
			if($table=="custom_panels_appointments"){ $tableappnum++;
			$field_id = $datafield_ids[$k];
				$join[] = " left join (select id,appointment_id from custom_panel_values join custom_panel_data using(data_id) where field_id = '$field_id') mc_$field_id on mc_$field_id.appointment_id =  a.appointment_id left join custom_panel_values t_$field_id on t_$field_id.id = mc_$field_id.id ";
			}
			if($table<>"custom_panels"){
            if (array_key_exists($table, $join_array)) {
                foreach ($join_array[$table] as $t) {
					if(isset($table_joins[$t])){
                    $join[$t] = @$table_joins[$t];
					}
                }
            } else if(isset($table_joins[$table])&&isset($table_joins[$table])){
                $join[$table] = @$table_joins[$table];
            }
        }
		}

        foreach ($join as $join_query) {

            $qry .= $join_query;
        }
		
        $qry .= get_where($options, $filter_columns);

		 //get the total number of records before any limits or pages are applied
        $count = $this->db->query($numrows . $qry)->row()->numrows;
        $qry .= " group by surveys.survey_id";
        $start = $options['start'];
        $length = $options['length'];
        if (isset($_SESSION['survey_table']['order']) && $options['draw'] == "1") {
            $order = $_SESSION['survey_table']['order'];
        } else {
            $order = " order by CASE WHEN " . $order_columns[$options['order'][0]['column']] . " IS NULL THEN 1 ELSE 0 END," . $order_columns[$options['order'][0]['column']] . " " . $options['order'][0]['dir'];
            unset($_SESSION['survey_table']['order']);
            unset($_SESSION['survey_table']['values']['order']);
        }

        $qry .= $order;
		if($length>0){
        $qry .= "  limit $start,$length";
		}
			
        $result = $this->db->query($select . $qry)->result_array();
		$result['count'] = $count;
        return $result;
    }
    
}

?>