<<<<<<< HEAD

=======
>>>>>>> 084f61f87db2df5967b80d8711811b3cf1a34649
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_80 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 80");

		$check = $this->db->query("SHOW COLUMNS FROM `appointment_slot_override` LIKE 'notes'");
		if(!$check->num_rows()){
        $this->db->query("ALTER TABLE `appointment_slot_override` ADD `notes` VARCHAR(255) NOT NULL ;");
		}

$check = $this->db->query("SHOW COLUMNS FROM `outcomes` LIKE 'contact_made'");
		if(!$check->num_rows()){
        $this->db->query("ALTER TABLE outcomes ADD contact_made tinyint DEFAULT 0 NOT NULL");
		}
    }

}