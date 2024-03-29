<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trackvia_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();

    }
	
	public function update_record_from_trackvia($data){
		$this->db->where("c1",$data["c1"]);
		if($this->db->get("record_details")->num_rows()){
			$this->db->where("c1",$data["c1"]);
			$this->db->update("record_details",$data);
		} else if(isset($data['urn'])){
			$this->db->replace("record_details",$data);
		}
	}
	
	public function get_client_ref_from_urn($urn){
	$this->db->where("urn",$urn);
	return $this->db->get("client_refs")->row()->client_ref;	
	}
		public function get_urn_from_client_ref($id){
	$this->db->where("client_ref",$id);
	return $this->db->get("client_refs")->row()->urn;	
	}
    public function get_rebookings($campaign = "")
    {
        $qry = "select urn,fullname,campaign_name,if(records.date_updated is null,'Never',date_format(records.date_updated,'%d/%m/%y')) lastcall, if(dials>0,'warning','danger') col from records left join contacts using(urn) left join appointments a using(urn) left join campaigns using(campaign_id) where urgent = 1 and cancellation_reason is not null and record_status = 1";
        if (!empty($campaign)) {
            $qry .= " and campaign_id = '" . $campaign . "'";
        }
        $qry .= " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= " group by urn order by records.date_updated asc";
        return $this->db->query($qry)->result_array();
    }


    public function get_record($urn)
    {
        $query = "select * from records inner join campaigns using(campaign_id) join client_refs using(urn) left join record_details using(urn) left join webform_answers using(urn) left join contacts using(urn) left join contact_addresses using(contact_id) left join contact_telephone using(contact_id) left join outcomes using(outcome_id) left join outcome_reasons using(outcome_reason_id) left join data_sources on records.source_id = data_sources.source_id where urn = '$urn' group by urn";
        $row = $this->db->query($query)->row_array();
        return $row;
    }


    public function get_record_rows($urn)
    {
        $query = "select * from records left join client_refs using(urn) left join record_details using(urn) left join webform_answers using(urn) left join contacts using(urn) left join contact_addresses using(contact_id) left join contact_telephone using(contact_id) left join outcomes using(outcome_id) left join outcome_reasons using(outcome_reason_id) left join history using(urn) left join data_sources on records.source_id = data_sources.source_id where urn = '$urn'";
        return $this->db->query($query)->result_array();
    }

    public function get_appointment($urn)
    {
        $query = "select a.urn,a.title,a.`text`,client_ref,date(a.`start`) `date`,if(time(`start`)<'13:00:00','am','pm') slot,fullname,campaign_id, appointment_type_id,records.source_id,source_name,records.pot_id from records left join (select max(appointment_id) appointment_id, urn from appointments apps group by urn) ma on ma.urn = records.urn left join appointments a using(appointment_id) inner join contacts on contacts.urn = records.urn left join client_refs on records.urn = client_refs.urn left join record_details on record_details.urn = records.urn left join webform_answers on webform_answers.urn = records.urn left join data_sources on records.source_id = data_sources.source_id where a.urn = '$urn' group by a.appointment_id";
        return $this->db->query($query)->row_array();
    }

    /*
     * Get records in our system from trackvia ids (client_ref)
     */
    public function getRecordsByTVIds($tv_record_ids)
    {
        //having to put a 3000 limit on this or msql timesout
        if (count($tv_record_ids) < 3000) {
            $qry = "SELECT
                  r.campaign_id,
                  r.urn,
                  cr.client_ref,
                  a.start as survey_datetime,
				  date(a.start) as survey_date,
                  r.parked_code,
				  r.record_status,
				  r.urgent,
				  r.record_color,
				  r.pot_id,
				  a.appointment_type_id
				from records r
				inner join client_refs cr using(urn)
				left join (select max(appointment_id) appointment_id,urn from appointments group by urn) ma using(urn)
				left join appointments a on a.appointment_id = ma.appointment_id
				WHERE cr.client_ref IN (" . implode(',', $tv_record_ids) . ")";

            return $this->db->query($qry)->result_array();
        } else {
            return array();
        }
    }


    public function update_extra($data)
    {
//check the record details are in the table. If not add it, then update the rest
        foreach ($data as $k => $row) {
            $qry = "select urn from record_details where urn = '" . $row['urn'] . "'";
            if (!$this->db->query($qry)->num_rows()) {
                $this->db->insert("record_details", $row);
                unset($data[$k]);
            }
        }
        return $this->db->update_batch('record_details', $data, 'urn');
    }

    //Update records in our system
    public function updateRecords($records)
    {
        return $this->db->update_batch('records', $records, 'urn');
    }

    //Update notes in our system
    public function updateNotes($notes)
    {
        return $this->db->insert_update_batch('sticky_notes', $notes);
    }

    public function create_appointment($fields, $records, $start, $title, $text, $appointment_type_id,$attendee)
    {

        $address = "";
        $postcode = "";
        if (isset($fields['House No.'])) {
            $address .= ' ' . $fields['House No.'];
        }
        if (isset($fields['Address 1'])) {
            $address .= ' ' . $fields['Address 1'];
        }
        if (isset($fields['Address 2'])) {
            $address .= ' ' . $fields['Address 2'];
        }
        if (isset($fields['PostCode'])) {
            $address .= ' ' . $fields['PostCode'];
            $postcode = postcodeFormat($fields['PostCode']);
        }

        //check if exists
        $insert = array(
            "urn" => $records['urn'],
            "title" => $title,
            "text" => $text,
            "start" => $start,
            "end" => date('Y-m-d H:i:s', strtotime("+1 hour", strtotime($start))),
            "postcode" => $postcode, "status" => 1,
            "created_by" => "123",
            "date_updated" => NULL,
            "date_added" => date('Y-m-d H:i:s'),
            "updated_by" => 123,
            "address" => $address,
            'appointment_type_id' => $appointment_type_id
        );
        $insert_query = $this->db->insert_string("appointments", $insert);
        $insert_query = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insert_query);
        $this->db->query($insert_query);
        $appointment_id = $this->db->insert_id();
        if (isset($appointment_id) && $appointment_id != 0) {
            $this->db->insert("appointment_attendees", array("appointment_id" => $appointment_id, "user_id" => $attendee));
        }
    }

    public function uncancel_appointment($urn, $date)
    {
        $this->db->_protect_identifiers = false;
        $this->db->where(array("urn" => $urn, "date(start)" => $date));
        $this->db->update("appointments", array("cancellation_reason" => NULL, "status" => 1));
    }

    public function cancel_appointment($urn, $start)
    {
        $this->db->_protect_identifiers = false;
        $this->db->where(array("urn" => $urn, "date(start)" => $start));
        $this->db->update("appointments", array("cancellation_reason" => "Needs rebooking", "status" => 0, "updated_by" => 1, "date_updated" => date('Y-m-d H:i:s')));
    }

    public function add_record($data)
    {
        $this->db->insert("records", $data);
        return $this->db->insert_id();
    }

    public function add_client_refs($data)
    {
        $this->db->insert("client_refs", $data);
    }

    public function add_contact($data)
    {
        $this->db->insert("contacts", $data);
        return $this->db->insert_id();
    }

    public function add_address($data)
    {
        $this->db->insert("contact_addresses", $data);
    }

    public function add_telephone($data)
    {
        $this->db->insert("contact_telephone", $data);
    }

    public function add_record_details($data)
    {
        $insert_query = $this->db->insert_string("record_details", $data);
        $insert_query = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insert_query);
        $this->db->query($insert_query);
    }


    public function get_121_counts($name)
    {    if ($name == "GHS Southway") {
            $qry = "select urn from records where campaign_id = 22 and pot_id = '28'";
            return $this->db->query($qry)->num_rows();
        }
        else if ($name == "GHS Southway survey") {
            $qry = "select urn from records where campaign_id = 22 and pot_id = '34'";
            return $this->db->query($qry)->num_rows();
        } else if ($name == "GHS Southway rebook") {
            $qry = "select urn from records where campaign_id = 22 and pot_id = '35'";
            return $this->db->query($qry)->num_rows();
        } else if ($name == "GHS Southway booked") {
            $qry = "select urn from records inner join appointments using(urn) where campaign_id = 22 and pot_id = '37'";
            return $this->db->query($qry)->num_rows();
        } else if ($name == "GHS Private") {
            $qry = "select urn from records where campaign_id = 29 and pot_id = '41'";
            return $this->db->query($qry)->num_rows();
        } else if ($name == "GHS Private survey") {
            $qry = "select urn from records where campaign_id = 29 and pot_id = '39' ";
            return $this->db->query($qry)->num_rows();
        } else if ($name == "GHS Private rebook") {
            $qry = "select urn from records where campaign_id = 29 and pot_id = '38' ";
            return $this->db->query($qry)->num_rows();
        } else if ($name == "GHS Private booked") {
            $qry = "select urn from records where campaign_id = 29 and pot_id = '36'";
            return $this->db->query($qry)->num_rows();
        } else if ($name == "GHS Private not viable") {
            $qry = "select urn from records where campaign_id = 28 and pot_id = '40' ";
            return $this->db->query($qry)->num_rows();
        }  else if ($name == "GHS Citywest") {
            $qry = "select urn from records where campaign_id = 32 and pot_id = '49'";
            return $this->db->query($qry)->num_rows();
        }  else if ($name == "GHS Citywest survey") {
            $qry = "select urn from records where campaign_id = 32 and pot_id = '46'";
            return $this->db->query($qry)->num_rows();
        } else if ($name == "GHS Citywest rebook") {
            $qry = "select urn from records where campaign_id = 32 and pot_id = '47'";
            return $this->db->query($qry)->num_rows();
        } else if ($name == "GHS Citywest booked") {
            $qry = "select urn from records where campaign_id = 32 and pot_id = '48'";
            return $this->db->query($qry)->num_rows();
        } else if ($name == "GHS Southway installation booked") {
            $qry = "select urn from records where campaign_id = 52 and pot_id = '52'";
            return $this->db->query($qry)->num_rows();
        }
		else if ($name == "GHS Southway installation") {
            $qry = "select urn from records where campaign_id = 52 and pot_id = '51'";
            return $this->db->query($qry)->num_rows();
        } else if ($name == "GHS New Leads") {
            $qry = "select urn from records where campaign_id = 29 and pot_id = '55'";
            return $this->db->query($qry)->num_rows();
        }
    }

}