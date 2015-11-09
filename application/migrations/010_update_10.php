
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_10 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
 $this->firephp->log("starting migration 10");
		$this->db->query("alter table contacts add unique(urn,fullname,dob)");		
			$this->db->query("alter table `company_addresses` add unique(company_id,add1,add2,postcode)");
			
			//add tables for keyword search
			$this->db->query("CREATE TABLE IF NOT EXISTS `keywords` (
  `keyword_id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(50) NOT NULL,
  PRIMARY KEY (`keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ");
	$this->db->query("CREATE TABLE IF NOT EXISTS `record_keywords` (
  `urn` int(11) NOT NULL,
  `keyword_id` int(11) NOT NULL,
  UNIQUE KEY `urn` (`urn`,`keyword_id`),
  KEY `urn_2` (`urn`),
  KEY `keyword_id` (`keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");

$this->db->query("ALTER TABLE `keywords` ADD UNIQUE (
`keyword`
)");
				$this->db->query("insert ignore into keywords (select '',subsector_name from subsectors inner join company_subsectors using(subsector_id) where subsector_id < 250)");
						$this->db->query("insert ignore into keywords (select '',sector_name from sectors inner join subsectors using(sector_id) inner join company_subsectors using(subsector_id) where subsector_id < 250)");
							
		$this->db->query("insert ignore into record_keywords (select urn,keyword_id from subsectors inner join keywords on subsector_name=keyword inner join company_subsectors using(subsector_id) inner join companies using(company_id))");
		$this->db->query("insert ignore into record_keywords (select urn,keyword_id from sectors inner join keywords on sector_name=keyword inner join subsectors using(sector_id) inner join company_subsectors using(subsector_id) inner join companies using(company_id))");
		
        $this->db->query("update company_subsectors set subsector_id = 66220 where subsector_id = 239");
        $this->db->query("update company_subsectors set subsector_id = 66220 where subsector_id = 235");
        $this->db->query("update company_subsectors set subsector_id = 92000 where subsector_id = 149");
        $this->db->query("update company_subsectors set subsector_id = 49410 where subsector_id = 173");
        $this->db->query("update company_subsectors set subsector_id = 73110 where subsector_id = 184");
        $this->db->query("update company_subsectors set subsector_id = 64910 where subsector_id = 83");
        $this->db->query("update company_subsectors set subsector_id = 11050 where subsector_id = 22");
        $this->db->query("update company_subsectors set subsector_id = 65300 where subsector_id = 81");
        $this->db->query("update company_subsectors set subsector_id = 80100 where subsector_id = 172");
        $this->db->query("update company_subsectors set subsector_id = 62020 where subsector_id = 221");
        $this->db->query("update company_subsectors set subsector_id = 45111 where subsector_id = 127");
        $this->db->query("update company_subsectors set subsector_id = 64999 where subsector_id = 56");
        $this->db->query("update company_subsectors set subsector_id = 47910 where subsector_id = 54");
        $this->db->query("update company_subsectors set subsector_id = 56210 where subsector_id = 180");
        $this->db->query("update company_subsectors set subsector_id = 47910 where subsector_id = 55");
        $this->db->query("update company_subsectors set subsector_id = 32990 where subsector_id = 51");
        $this->db->query("update company_subsectors set subsector_id = 93290 where subsector_id = 182");
        $this->db->query("update company_subsectors set subsector_id = null where subsector_id = 187");
        $this->db->query("update company_subsectors set subsector_id = 41100 where subsector_id = 110");
        $this->db->query("update company_subsectors set subsector_id = null where subsector_id = 134");
        $this->db->query("update company_subsectors set subsector_id = 94120 where subsector_id = 79");
         $this->db->query("update company_subsectors set subsector_id = 86900 where subsector_id = 82");
          $this->db->query("update company_subsectors set subsector_id = 69102 where subsector_id = 156");
           $this->db->query("update company_subsectors set subsector_id = 45111 where subsector_id = 240");
            $this->db->query("update company_subsectors set subsector_id = null where subsector_id = 188");
             $this->db->query("update company_subsectors set subsector_id = null where subsector_id = 183");
             $this->db->query("update company_subsectors set subsector_id = 27400 where subsector_id = 50");
             $this->db->query("update company_subsectors set subsector_id = null where subsector_id = 122");
             $this->db->query("update company_subsectors set subsector_id = 35130 where subsector_id = 224");
              $this->db->query("update company_subsectors set subsector_id = null where subsector_id = 169");
               $this->db->query("update company_subsectors set subsector_id = null where subsector_id = 32");
                $this->db->query("update company_subsectors set subsector_id = null where subsector_id = 102");
                    $this->db->query("delete from company_subsectors where subsector_id < 250"); 
                    
                           $this->db->query("delete from subsectors where subsector_id < 250"); 
                            $this->db->query("delete from sectors where section is null"); 		
			     //add travelMode
        $this->db->query("ALTER TABLE `records` ADD `record_color` VARCHAR(6) NULL DEFAULT NULL");
	}


    public function down()
    {
        $this->db->query("ALTER TABLE `records` DROP `record_color`");
    }

	
}