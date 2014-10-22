	<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*  This page contains functiosn to populate dropdown menus on forms and filters. The queries simply return each id and value in the table in the format id=>name */
class Cron_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        
    }
    
	public function clear_hours(){
	$this->db->query("truncate table hours_logged");	
	}
	
    public function update_hours($agents)
    {
        foreach ($agents as $agent) {
            $qry   = "select sum(TIME_TO_SEC(TIMEDIFF(if(end_time is null,now(),end_time),start_time))) duration,campaign_id from hours_logged where user_id = '{$agent['id']}' and date(`start_time`)=curdate()  group by campaign_id having duration > 1";
            $query = $this->db->query($qry);
            if ($query->num_rows()) {
                
                $campaigns = $query->result_array();
                foreach ($campaigns as $row) {
                    $qry    = "update hours set duration = '{$row['duration']}',`date`=now() where user_id = '{$agent['id']}' and campaign_id = {$row['campaign_id']}";
                    $update = $this->db->query($qry);
                    if ($this->db->affected_rows() == 0) {
                        $qry    = "insert into hours set duration = '{$row['duration']}',user_id = '{$agent['id']}',campaign_id = {$row['campaign_id']},exception =0,`date`=now()";
                        $insert = $this->db->query($qry);
                    }
                }
            }
        }
    }
    
}