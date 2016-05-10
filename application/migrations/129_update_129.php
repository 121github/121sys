
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_129 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		$this->Database_model->remove_dupes(true,'datatables_views','user_id','table_id','view_name');
		$this->db->query("ALTER TABLE datatables_views ADD UNIQUE (
user_id,
table_id,
view_name
)");

	}
	
}