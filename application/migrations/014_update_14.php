
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_14 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 14");
		
	$this->Database_model->remove_dupes("company_telephone","company_id","telephone_number");
	$this->Database_model->remove_dupes("contact_telephone","contact_id","telephone_number");
$this->db->query("alter table contact_telephone add unique(contact_id,telephone_number)");
$this->db->query("alter table company_telephone add unique(company_id,telephone_number)");
	}
	
}
