
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_22 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 22");

        $this->db->query("ALTER TABLE `sms_templates` DROP `template_from`");
        $this->db->query("ALTER TABLE `sms_templates` CHANGE `template_body` `template_text` LONGTEXT");
    }

}