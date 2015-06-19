	<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trackvia_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        
    }

    /*
     * Get records in our system from trackvia ids (client_ref)
     */
    public function getRecordsByTVIds($tv_record_ids) {
        $qry = "SELECT
                  r.campaign_id,
                  r.urn,
                  cr.client_ref,
                  rd.dt1 as survey_date,
                  r.parked_code
				from records r
				inner join client_refs cr using(urn)
				left join record_details rd using(urn)
				WHERE cr.client_ref IN (".implode(',',$tv_record_ids).")";

        return $this->db->query($qry)->result_array();
    }


    //Update records in our system
    public function updateRecords($records) {
        return $this->db->update_batch('records', $records, 'urn');
    }

}