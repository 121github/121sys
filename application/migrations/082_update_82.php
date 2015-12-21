<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_82 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 82");
		
		file_get_contents(base_url()."database/remove_dupes_now/appointment_slot_override/appointment_slot_id/user_id/date");
		
					$this->db->query("delete from appointment_slots where slot_name = 'EVE'");
			$this->db->query("update appointment_slots set slot_group_id = '1' where slot_name in('AM','PM')");
			$this->db->query("update appointment_slots set slot_group_id = '4' where slot_name = 'All day'");
		
		$this->db->query("alter table `appointment_slot_override` add unique(appointment_slot_id,user_id,date)");
		
		$qry = "insert ignore into appointment_slot_override (appointment_slot_id,user_id,max_slots,`date`,notes) select appointment_slot_id,user_id,'0',block_day,if(reason_id=3,other_reason,reason) from appointment_rules left join appointment_rule_reasons using(reason_id) where appointment_slot_id is not null";
		$this->db->query($qry);
		
		$qry = "select *,if(reason_id=3,other_reason,reason) reason from appointment_rules left join appointment_rule_reasons using(reason_id) where appointment_slot_id is null";
		
		foreach($this->db->query($qry)->result_array() as $row){
			//get the slots in use
			$slots = $this->db->query("select appointment_slot_id from appointment_slot_assignment where user_id = '{$row['user_id']}' group by appointment_slot_id")->result_array();
			
			
			foreach($slots as $slot){
		$qry = "insert ignore into appointment_slot_override (appointment_slot_id,user_id,max_slots,`date`,notes) values({$slot['appointment_slot_id']},{$row['user_id']},'0','{$row['block_day']}','{$row['reason']}') ";
		$this->db->query($qry);
		
			}
		}

		$this->db->query("drop table if exists appointment_rules");
$this->db->query("drop table if exists appointment_rule_reasons");		
	}
	 public function down()
    {
	}
}