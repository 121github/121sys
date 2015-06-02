
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_9 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 9");

        $this->db->query("drop table if exists company_sectors");
        
//adding sic codes
                $this->db->query("ALTER TABLE `sectors` ADD `section` VARCHAR(1) NULL DEFAULT NULL");
                
         $this->db->query("INSERT INTO `sectors` VALUES ('13', ' Agriculture, Forestry and Fishing', 'A ')");
$this->db->query("INSERT INTO `sectors` VALUES ('14', ' Mining and Quarrying', 'B ')");
$this->db->query("INSERT INTO `sectors` VALUES ('15', ' Manufacturing', 'C ')");
$this->db->query("INSERT INTO `sectors` VALUES ('16', ' Electricity, gas, steam and air conditioning supply', 'D ')");
$this->db->query("INSERT INTO `sectors` VALUES ('17', ' Water supply, sewerage, waste management and remediation activities', 'E ')");
$this->db->query("INSERT INTO `sectors` VALUES ('18', ' Construction', 'F ')");
$this->db->query("INSERT INTO `sectors` VALUES ('19', ' Wholesale and retail trade; repair of motor vehicles and motorcycles', 'G ')");
$this->db->query("INSERT INTO `sectors` VALUES ('20', ' Transportation and storage', 'H ')");
$this->db->query("INSERT INTO `sectors` VALUES ('21', ' Accommodation and food service activities', 'I ')");
$this->db->query("INSERT INTO `sectors` VALUES ('22', ' Information and communication', 'J ')");
$this->db->query("INSERT INTO `sectors` VALUES ('23', ' Financial and insurance activities', 'K ')");
$this->db->query("INSERT INTO `sectors` VALUES ('24', ' Real estate activities', 'L ')");
$this->db->query("INSERT INTO `sectors` VALUES ('25', ' Professional, scientific and technical activities', 'M ')");
$this->db->query("INSERT INTO `sectors` VALUES ('26', ' Administrative and support service activities', 'N ')");
$this->db->query("INSERT INTO `sectors` VALUES ('27', ' Public administration and defence; compulsory social security', 'O ')");
$this->db->query("INSERT INTO `sectors` VALUES ('28', ' Education', 'P ')");
$this->db->query("INSERT INTO `sectors` VALUES ('29', ' Human health and social work activities', 'Q ')");
$this->db->query("INSERT INTO `sectors` VALUES ('30', ' Arts, entertainment and recreation', 'R ')");
$this->db->query("INSERT INTO `sectors` VALUES ('31', ' Other service activities', 'S ')");
$this->db->query("INSERT INTO `sectors` VALUES ('32', ' Activities of households as employers; undifferentiated goods and services producing activities of households for own use', 'T ')");
$this->db->query("INSERT INTO `sectors` VALUES ('33', ' Activities of extraterritorial organisations and bodies', 'U ')");

        
    }

        
    public function down()
    {

    }
	
}