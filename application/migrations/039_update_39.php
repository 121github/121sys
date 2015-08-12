<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_39 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
		$this->load->dbforge();
    }

    public function up(){
		  $this->firephp->log("starting migration 39");
		  
		  $fields = array(
                        'virgin_order_string' => array(
                                                 'type' =>'VARCHAR',
                                                 'constraint' => '150'
                                          ),
						 'virgin_order_join' => array(
                                                 'type' =>'VARCHAR',
                                                 'constraint' => '250'
                                          ),
						'telephone_protocol' => array(
                                                 'type' =>'VARCHAR',
                                                 'constraint' => '25',
												 'default' => 'callto:',
                                          ),
						'telephone_prefix' => array(
                                                 'type' =>'VARCHAR',
                                                 'constraint' => '25'
                                          )
                );
		  
		  $this->dbforge->add_column('campaigns', $fields);
		  
	}
	
}
