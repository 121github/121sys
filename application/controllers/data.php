<?php
require('upload.php');
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Data extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
$this->_campaigns = campaign_access_dropdown();

        $this->load->model('Form_model');
        $this->load->model('Data_model');
        $this->load->model('Company_model');
        $this->load->model('Contacts_model');
        $this->load->model('Records_model');
    }
	
	function clear_records(){
	if(1==1||$_SESSION['session_name']=="121sys_prosales"){
		$response = $this->Data_model->clear_records();
		if($response){
		echo "<span class='text-success glyphicon glyphicon-ok'></span> Demo data has been deleted";
		} else {
		echo "<span class='text-danger glyphicon glyphicon-info-sign'></span> There was an error deleting the demo data";	
		}
	}	
	}
	
    //this loads the data management view
    public function index()
    {
        $campaigns = $this->Form_model->get_campaigns();
        $sources   = $this->Form_model->get_sources();
		$pots   = $this->Form_model->get_pots();
        $data      = array(
            'campaign_access' => $this->_campaigns,

			'pageId' => 'Admin',
            'title' => 'Admin | Import',
            'page' =>  'import',
            'javascript' => array(
                'plugins/jqfileupload/vendor/jquery.ui.widget.js',
                'plugins/jqfileupload/jquery.iframe-transport.js',
                'plugins/jqfileupload/jquery.fileupload.js',
                'data.js'
            ),
            'campaigns' => $campaigns,
            'sources' => $sources,
			'pots' => $pots,
            'css' => array(
                'dashboard.css',
                'plugins/jqfileupload/jquery.fileupload.css'
            )
        );
        $this->template->load('default', 'data/import.php', $data);
    }
    public function import_fields($echo = true)
    {
        $fields['records']           = array(
            "urn" => "urn",
            "client_ref" => "Client Reference",
            "nextcall" => "Next Call",
            "urgent" => "Urgent",
			"client_ref"=> "Client Reference"
        );
        $fields['contacts']          = array(
            "fullname" => "Full name",
            "title" => "Title",
            "firstname" => "Firstname",
            "lastname" => "Lastname",
            "position" => "Position/Job"
        );
        $fields['contact_telephone'] = array(
            "telephone_Telephone" => "Contact Telephone",
            "telephone_Landline" => "Contact Landline",
            "telephone_Mobile" => "Contact Mobile",
            "telephone_Work" => "Contact Work",
            "telephone_Fax" => "Contact Fax",
            "tps" => "TPS Status"
        );
        if ($this->input->post('type') == "B2B") {
            $fields['companies']         = array(
                "name" => "Company Name",
                "description" => "Description",
                "conumber" => "LTD CoNumber",
                "turnover" => "Turnover",
                "employees" => "employees"
            );
            $fields['company_addresses'] = array(
                "c_add1" => "Address 1",
                "c_add2" => "Address 2",
                "c_add3" => "Address 3",
                "c_county" => "County",
                "c_postcode" => "Postcode"
            );
            $fields['company_telephone'] = array(
                "telephone_Tel" => "Company Telephone",
                "ctps" => "CTPS Status"
            );
        } else {
            $fields['contact_addresses'] = array(
                "add1" => "Address 1",
                "add2" => "Address 2",
                "add3" => "Address 3",
                "county" => "County",
                "postcode" => "Postcode"
            );
        }
        $custom = $this->Data_model->get_custom_fields($this->input->post('campaign'));
        foreach ($custom as $k => $v) {
            $fields['record_details'][$k] = $v;
        }
        if ($echo) {
            echo json_encode($fields);
        } else {
            return $fields;
        }
    }
    //this controller loads the view for the data management page
    public function management()
    {
        $campaigns = $this->Form_model->get_campaigns();
        $data      = array(
            'campaign_access' => $this->_campaigns,

			'pageId' => 'Admin',
            'title' => 'Admin | Data Management',
            'page' =>'data_allocation',
            'campaigns' => $campaigns,
            'css' => array(
                'dashboard.css'
            ),
            'javascript' => array(
                'data.js',
				"lib/bootstrap-slider.js"
            )
        );
        $this->template->load('default', 'data/data_management.php', $data);
    }

    //this controller loads the view for the daily ration page
    public function daily_ration()
    {
        $campaigns = $this->Form_model->get_campaigns();
        $data      = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Admin',
            'title' => 'Admin | Daily Ration',
            'page' => 'daily_ration',
            'campaigns' => $campaigns,
            'css' => array(
                'dashboard.css'
            ),
            'javascript' => array(
                'data.js',
                'lib/jquery.numeric.min.js'
            )
        );
        $this->template->load('default', 'data/daily_ration.php', $data);
    }

    //this controller gets the data for the daily ration page
    public function daily_ration_data()
    {
        $parked_codes = $this->Form_model->get_parked_codes();
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            $results = $this->Data_model->get_daily_ration_data($form);
            $aux = array();
            $url = base_url() . "search/custom/records";
            foreach($results as $result) {
                $result_aux = array();
                $campaign_id = $result['campaign_id'];
                if (!isset($aux[$campaign_id])) {
                    $aux[$campaign_id] = array();
                    $aux[$campaign_id]['campaign_id'] = $result['campaign_id'];
                    $aux[$campaign_id]['campaign_name'] = $result['campaign_name'];
                    $aux[$campaign_id]['total_records'] = $result['total_records'];
                    $aux[$campaign_id]['daily_data'] = $result['daily_data'];
                    $aux[$campaign_id]['total_records_url'] = $url."/campaign/".$campaign_id;
                    $aux[$campaign_id]['total_parked_url'] = $url."/campaign/".$campaign_id."/parked/yes";
                    $aux[$campaign_id]['total_parked'] = 0;
                }
                if ($result['parked_code']) {
                    unset($result['campaign_id']);
                    unset($result['campaign_name']);
                    unset($result['total_records']);
                    unset($result['daily_data']);
                    $aux[$campaign_id]['total_parked'] = ($aux[$campaign_id]['total_parked'] + $result['count']);
                    $result["url"] = $url . "/campaign/" . $campaign_id . "/parked/yes/parked-code/" . $result['parked_code'];
                    $aux[$campaign_id][$result['parked_code']] = $result;
                }
            }
        }

        $results = $aux;

        echo json_encode(array(
            "success" => (!empty($results)),
            "data" => $results,
            "parked_codes" => $parked_codes,
            "msg" => (!empty($results))?"":"Nothing found"
        ));
    }

    //this controller set the daily ration for a particular campaign
    public function set_daily_ration()
    {
        if ($this->input->is_ajax_request()) {
            $campaign_id = $this->input->post('campaign_id');
            $daily_data = $this->input->post('daily_data');
            $results = $this->Data_model->set_daily_ration($campaign_id, $daily_data);
        }

        echo json_encode(array(
            "success" => ($results),
            "msg" => ($results)?"Daily Ration Saved":"ERROR: The daily ration was not saved"
        ));

    }


    public function import()
    {
        $options               = array();
        $options['upload_dir'] = dirname($_SERVER['SCRIPT_FILENAME']) . '/datafiles/';
        $options['upload_url'] = base_url() . '/datafiles/';
        $upload_handler        = new Upload($options, true);
    }
    public function get_sample()
    {
        $file     = $this->input->post('file');
        $path     = dirname($_SERVER['SCRIPT_FILENAME']) . '/datafiles/';
        $fullpath = $path . $file;
        $result   = array();
        $i        = 0;
        if (($handle = fopen($fullpath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $i++;
                $result[] = $data;
                if ($i == 5) {
                    break;
                }
            }
            fclose($handle);
        } else {
		echo json_encode(array("success"=>false));
		exit;	
		}
        $json = json_encode($result);
        echo $json;
    }
    public function reassign_data()
    {
        if ($this->input->is_ajax_request()) {
            $assignment = $this->input->post('user');
            foreach ($assignment as $k => $v) {
                $array[] = array(
                    "count" => $v,
                    "user" => $k
                );
            }
			/*
            usort($array, function($a, $b)
            {
                return $b['count'] - $a['count'];
            });
			*/
            $i = 0;
            foreach ($array as $user) {
                if ($i > 0) {
                    $this->Data_model->reassign_data($user['user'], $this->input->post('state'), $this->input->post('campaign'), $this->input->post('dials'), $user['count']);
                } else {
                    $this->Data_model->reassign_data($user['user'], $this->input->post('state'), $this->input->post('campaign'), $this->input->post('dials'));
                }
                $i++;
            }
            echo json_encode(array(
                "success" => true
            ));
        }
    }
    public function get_user_data()
    {
        if ($this->input->is_ajax_request()) {
            $this->Data_model->get_user_data($this->input->post('campaign'), $this->input->post('state'));
        }
    }
    public function start_import()
    {
        if ($this->input->is_ajax_request()) {
            /*we have to close the session file to allow the progress requests to work due to some php limitations  */
            session_write_close();
			$errors = array();
            $filename      = $this->input->post('filename');
            $autoincrement = $this->input->post('autoincrement');
            $duplicates    = $this->input->post('duplicates');
            $format        = $this->input->post('dateformat');
            $header        = $this->input->post('header');
            $campaign      = $this->input->post('campaign');
            $new_source    = $this->input->post('new_source');
			$new_pot    = $this->input->post('new_pot');
            $source        = $this->input->post('source');
            $tables        = $this->import_fields(false);
            $final         = array(
                "records" => array()
            );
            $response      = array();
            $current       = 0;
            foreach ($this->input->post('field') as $k => $v) {
                if (!empty($v)) {
                    $fields[$v] = $k;
                }
            }
            if (!empty($new_source)) {
                $source = $this->Data_model->create_source($new_source);
            }
			 if (!empty($new_pot)) {
                $pot = $this->Data_model->create_pot($new_pot);
            }
            $options = array(
                "autoincrement" => $autoincrement,
                "duplicates" => $duplicates,
                "campaign" => $campaign,
                "source" => $source,
				 "pot" => $pot
            );
            $row     = 0;
            if (($handle = fopen("datafiles/" . $filename, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $row++;
                    $num = count($data);
                    //ignore first row if data contains a header row
                    if ($header == "1" && $row == 1) {
                        continue;
                    }
                    foreach ($fields as $k => $v) {
                        $record[$k]   = $data[$v];
                        $import[$row] = $record;
                    }
                }
                fclose($handle);
            }
            if ($header == "1") {
                $rows = $row - 1;
            } else {
                $rows = $row;
            }
            
            foreach ($import as $row => $details) {
                $current++;
                /* now we can insert into the database*/
                $progress = ceil($current / $rows * 100);
                file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt", "Importing data...".$progress."% [row $current]");
                /*format each row so the column names match the ones in the database and split the columns into the relevant tables*/
                foreach ($details as $col => $val) {
                    $sqlformat = "";
                    if (!empty($val)) {
                        foreach ($tables as $table => $field_list) {
                            if (array_key_exists($col, $field_list)) {
                                if ($col == "d1" || $col == "d2" || $col == "d3") {
                                    $sqlformat = 'Y-m-d';
                                }
                                if ($col == "n1" || $col == "n2" || $col == "n3") {
                                    $sqlformat = 'Y-m-d H:i';
                                }
                                if (!empty($sqlformat)) {
                                    if ($format == "DD/MM/YYYY") {
                                        $dt = DateTime::createFromFormat('d/m/Y', $val);
                                    }
                                    if ($format == "DD/MM/YY") {
                                        $dt = DateTime::createFromFormat('d/m/y', $val);
                                    }
                                    if ($format == "YY-MM-DD") {
                                        $dt = DateTime::createFromFormat('y-m-d', $val);
                                    }
                                    if ($format == "YYYY-MM-DD") {
                                        $dt = DateTime::createFromFormat('Y-m-d', $val);
                                    }
                                    $dt_error = DateTime::getLastErrors();
                                    if ($dt_error['error_count'] > 0) {
                                        file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploaderrors.txt", "Date format error on row $row");
                                        exit;
                                    } else {
                                        $val = $dt->format($sqlformat);
                                    }
                                }
                                if (strpos($col, "telephone_") !== false) {
                                    $final[$table]["description"]      = str_replace("telephone_", "", $col);
                                    $final[$table]["telephone_number"] = $val;
                                } else {
                                    $final[$table][str_replace("c_", "", $col)] = $val;
                                }
                            }
                        }
                    }
                }
                /* end formatting */
                $errors = $this->Data_model->import_record($final, $options);
                if (count($errors) > 0) {
                    echo json_encode(array(
                        "success" => false,
                        "rows" => $row,
                        "error" => $errors
                    ));
                    file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploaderrors.txt", "Import stopped on row $row. Error: " . $errors[0]);
					 $this->firephp->log("Error adding row $row");
                    exit;
                }

			}
                       
            if (count($errors) <= 0) {
            	$company_locations = $this->check_company_postcodes();
				$contact_locations = $this->check_contact_postcodes();
				file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt", "Locations updated");
            }
            
			echo json_encode(array(
                "success" => true,
                "rows" => $row,
				"company_locations"=>$company_locations,
				"contact_locations"=>$contact_locations,
                "data" => "Import was successful"
            ));
        }
    }
	
    public function get_progress()
    {
    	if ($this->input->post('first')=="1") {
			file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploaderrors.txt","");
			file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt","Importing data...0%");
			$progress = "0";
        } else {
			$progress = file_get_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt");
		}
        	/*
			$file = dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt";
        	$data = file($file);
        	$progress = $data[count($data)-1];
			*/
        
        
        $array =array(
            "progress" => $progress, 
        );
        if ($progress=="Locations updated") {
        	$array['success'] = true;
		}
        echo json_encode($array);
    }
    
    public function check_company_postcodes() {
    	 
    	//Get the contactAddresses without Coords
    	$companyAddrWithoutCoords = $this->Company_model->get_company_addresses_without_coords();
		
    	$errors     = array();
    	$current = 0;
    	$rows = count($companyAddrWithoutCoords);
		if($rows>2500){ $rows = 2500; }
    	if (count($companyAddrWithoutCoords) > 0) {
    		foreach ($companyAddrWithoutCoords as $companyAddr) {
    			$current++;
    			$progress = ceil($current / $rows * 100);
    			file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt", "Updating contact locations...".$progress."%");
				if(!empty($companyAddr['postcode'])){
    			$coords = postcode_to_coords($companyAddr['postcode']);
				if(array_key_exists("error",$coords)){
					file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploaderrors.txt", $coords['error']);
					return $coords;
				}
    			if ($coords['lat'] && $coords['lng']) {
    				$companyAddr['latitude'] = $coords['lat'];
    				$companyAddr['longitude'] = $coords['lng'];
    				$this->Company_model->update_company_address($companyAddr);
    			}
    			
				}
    		}	
    	}
		return true;
    }
    
    
    public function check_contact_postcodes() {
    	 
    	//Get the contactAddresses without Coords
    	$contactAddrWithoutCoords = $this->Contacts_model->get_contact_addresses_without_coords();
		
    	$errors     = array();
    	$current = 0;
    	$rows = count($contactAddrWithoutCoords);
		if($rows>2500){ $rows = 2500; }
    	if (count($contactAddrWithoutCoords) > 0) {
    		foreach ($contactAddrWithoutCoords as $contactAddr) {
    			$current++;
    			$progress = ceil($current / $rows * 100);
    			file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt", "Updating contact locations...".$progress."%");
				if(!empty($contactAddr['postcode'])){
    			$coords = postcode_to_coords($contactAddr['postcode']);
				if(array_key_exists("error",$coords)){
					file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploaderrors.txt", $coords['error']);
					return $coords;
				}
    			if ($coords['lat'] && $coords['lng']) {
    				$contactAddr['latitude'] = $coords['lat'];
    				$contactAddr['longitude'] = $coords['lng'];
    				$this->Contacts_model->update_contact_address($contactAddr);
    			}
    			
				}
    		}	
    	}
		return true;
    }
    
    
    
    
    
    //Add record
    public function add_record()
    {
		if($_SESSION['role']==1){
		$campaigns = $this->Form_model->get_campaigns();
		} else {
    	$campaigns = $this->Form_model->get_campaigns_by_user($_SESSION['user_id']);
		}
    	$data = array(
    			'campaign_access' => $this->_campaigns,

    			'pageId' => 'Admin',
    			'title' => 'Admin | Add Record',
    			'page' =>'add_record',
    			'campaigns' => $campaigns,
    			'css' => array(
    					'dashboard.css'
    			),
    			'javascript' => array(
                    'data.js?v1',
                    'lib/jquery.numeric.min.js'
    			)
    	);
    	$this->template->load('default', 'data/add_record.php', $data);
    }
    
    /**
     * Insert a record
     */
    public function save_record()
    {
		 $this->load->model('Locations_model');
    	$form = $this->input->post();
    	$status = $this->Records_model->get_status_by_name("Live");
    	$source = $this->Records_model->get_source_by_name("Manual");
    	$form['record_status'] = $status->record_status_id;
    	$form['source_id'] = $source->source_id;
		$form['added_by'] = $_SESSION['user_id'];
    	
    	$company = array();
    	$contact = array();

		
    	if (!empty($form['company_name'])) {
    		$company['name'] = $form['company_name'];
			if(!empty($form['company_telephone'])){
			$company_telephone=array("telephone_number"=>$form['company_telephone'],"description"=>"Telephone");
			}
			if(!empty($form['company_add1'])&&!empty($form['company_postcode'])){
				$form['company_postcode'] = postcodeCheckFormat($form['company_postcode']);
				if (!$form['company_postcode']) {
                echo json_encode(array("success" => false, "error" => "Please enter a valid postcode"));
                exit;
            }
                $company_address = array(
                    "postcode" => $form['company_postcode'],
                    "add1" => $form['company_add1'],
                    "add2" => $form['company_add2'],
                    "add3" => $form['company_add3'],
                    "add4" => $form['company_add4'],
                    "locality" => $form['company_locality'],
                    "city" => $form['company_city'],
                    "county" => $form['company_county'],
                    "country" => $form['company_country'],
                    "primary" => "1"
                );

			}
			
    		$response = true;
    	}
    	
    	else if (!empty($form['contact_name'])) {	
    		$contact['fullname'] = $form['contact_name'];#
			if(!empty($form['contact_telephone'])){
			$contact_telephone=array("telephone_number"=>$form['contact_telephone'],"description"=>"Telephone");
			}
			if(!empty($form['contact_add1'])&&!empty($form['contact_postcode'])){
			$form['contact_postcode'] = postcodeCheckFormat($form['contact_postcode']);
			if (!$form['contact_postcode']) {
                echo json_encode(array("success" => false, "error" => "Please enter a valid postcode"));
                exit;
            }
                $contact_address = array(
                    "postcode" => $form['contact_postcode'],
                    "add1" => $form['contact_add1'],
                    "add2" => $form['contact_add2'],
                    "add3" => $form['contact_add3'],
                    "add4" => $form['contact_add4'],
                    "locality" => $form['contact_locality'],
                    "city" => $form['contact_city'],
                    "county" => $form['contact_county'],
                    "country" => $form['contact_country'],
                    "primary" => "1"
                );

			} else if(empty($form['contact_add1'])&&!empty($form['contact_postcode'])){
			 echo json_encode(array("success" => false, "error" => "Please enter the first line of the address"));
                exit;	
			} else if(!empty($form['contact_add1'])&&empty($form['contact_postcode'])){
			 echo json_encode(array("success" => false, "error" => "Please enter the postcode"));
                exit;	
			}
			
			
    		$name = explode(' ', $form['contact_name'], 2);
			str_replace(array("Mr","Miss","Mrs","Ms"),array("","","",""),$name);
    		$contact['firstname'] = $name[0];
    		@$contact['lastname'] = @$name[1];
			if(!isset($name[1])){
			echo json_encode(array("success"=>false,"error"=>"You must enter a full name"));
			exit;
			}
    		$response = true;
    	}
    	else {
    		echo json_encode(array("success"=>false,"error"=>"You must enter a name"));
			exit;
    	}
    
		
		
    	if ($response) {
    		unset($form['company_name']);
			unset($form['company_postcode']);
			unset($form['company_add1']);
            unset($form['company_add2']);
            unset($form['company_add3']);
            unset($form['company_add4']);
            unset($form['company_locality']);
            unset($form['company_city']);
            unset($form['company_county']);
            unset($form['company_country']);
			unset($form['company_telephone']);
            unset($form['company_house_number']);
            unset($form['company_address']);

    		unset($form['contact_name']);
			unset($form['contact_postcode']);
			unset($form['contact_add1']);
            unset($form['contact_add2']);
            unset($form['contact_add3']);
            unset($form['contact_add4']);
            unset($form['contact_locality']);
            unset($form['contact_city']);
            unset($form['contact_county']);
            unset($form['contact_country']);
			unset($form['contact_telephone']);
            unset($form['contact_house_number']);
            unset($form['contact_address']);
			 
    		if (!empty($form['campaign_id'])) {
    			$record_id = $this->Records_model->save_record($form);
    			if ($record_id) {
    				if (!empty($company)) {
    					$company['urn'] = $record_id;
    					$insert_id = $this->Company_model->save_company($company);
						if(isset($company_telephone)){
						$company_telephone["company_id"] = $insert_id;
						$this->Company_model->add_telephone($company_telephone);	
						}
						if(isset($company_address)){
						$company_address["company_id"] = $insert_id;
						$this->Company_model->add_address($company_address);
						$this->Locations_model->set_location_id($company_address['postcode']);			
						}
    					$response = ($insert_id)?true:false;
    				}
    				elseif (!empty($contact)) {
    					$contact['urn'] = $record_id;
    					$insert_id = $this->Contacts_model->save_contact($contact);
						if(isset($contact_telephone)){
						$contact_telephone["contact_id"] = $insert_id;
						$this->firephp->log($contact_telephone);
						$this->Contacts_model->add_telephone($contact_telephone);	
						}
						if(isset($contact_address)){
						$contact_address["contact_id"] = $insert_id;
						$this->Contacts_model->add_address($contact_address);	
						$this->Locations_model->set_location_id($contact_address['postcode']);	
						}
    					$response = ($insert_id)?true:false;
    				}
    			}
    			else {
    				$response = false;
    			}
    			 
    		} else {
    			$response = false;
    		}
    	} 

		
    	echo json_encode(array("success"=>$response, "record_id" => (isset($record_id))?$record_id:false));
    
    }

    //this controller loads the view for the backup_restore page
    public function backup_restore()
    {
        $campaigns = $this->Form_model->get_campaigns();
        $data      = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Admin',
            'title' => 'Admin | Backup and Restore',
            'page' =>  'backup_restore',
            'campaigns' => $campaigns,
            'css' => array(
                'dashboard.css'
            ),
            'javascript' => array(
                'data.js',
                'lib/jquery.numeric.min.js'
            )
        );
        $this->template->load('default', 'data/backup_restore.php', $data);
    }

    //this controller gets the data for the backup_restore page
    public function backup_data()
    {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            $results = $this->Data_model->get_backup_data($form);

            $aux = array();
            foreach ($results as $result) {
                $current_date_from = date('d/m/Y', strtotime('-'.$result['months_ago'].' months'));
                $current_date_to = date('d/m/Y', strtotime('-'.($result['months_ago']-$result['months_num']).' months'));
                $result['update_date_from'] = ($result['months_num']&&$result['months_ago']?$current_date_from:"");
                $result['update_date_to'] = ($result['months_num']&&$result['months_ago']?$current_date_to:"");
                $result['renewal_date_from'] = ($result['months_num']&&$result['months_ago']?$current_date_from:"");
                $result['renewal_date_to'] = ($result['months_num']&&$result['months_ago']?$current_date_to:"");
                unset($result['months_ago']);
                unset($result['months_num']);

                //Check if exist the renewal date
                $renewal_date_field =  $this->Form_model->get_renewal_date_field($result['campaign_id']);
                $renewal_date_field = ($renewal_date_field)?$renewal_date_field[0]['field']:"";
                $result['renewal_date_field'] = $renewal_date_field;

                array_push($aux,$result);
            }
            $results = $aux;
        }

        echo json_encode(array(
            "success" => (!empty($results)),
            "data" => $results,
            "msg" => (!empty($results))?"":"Nothing found"
        ));
    }

    //this controller gets the data for the backup_restore by campaign page
    public function backup_data_by_campaign()
    {
        $url = base_url() . "search/custom/records";

        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            $form['update_date_from'] = ($form['update_date_from']?to_mysql_datetime($form['update_date_from']):"");
            $form['update_date_to'] = ($form['update_date_to']?to_mysql_datetime($form['update_date_to']):"");
            $form['renewal_date_from'] = ($form['renewal_date_from']?to_mysql_datetime($form['renewal_date_from']):"");
            $form['renewal_date_to'] = ($form['renewal_date_to']?to_mysql_datetime($form['renewal_date_to']):"");
            $renewal_date_field = $form['renewal_date_field'];
            unset($form['renewal_date_field']);
            $results = $this->Data_model->get_backup_data_by_campaign($form, $renewal_date_field);
            $records_num = count($results);
            $records_url = $url."/campaign/".$form['campaign_id'];
            $records_url .= ($form['update_date_from']?"/update-date-from/".$form['update_date_from']:"");
            $records_url .= ($form['update_date_to']?"/update-date-to/".$form['update_date_to']:"");
            $records_url .= ($form['renewal_date_from']?"/renewal-date-from/".$form['renewal_date_from']:"");
            $records_url .= ($form['renewal_date_to']?"/renewal-date-to/".$form['renewal_date_to']:"");
        }

        echo json_encode(array(
            "success" => "true",
            "records_num" => $records_num,
            "records_url" => $records_url,
            "msg" => (!empty($results))?"":"Nothing found"
        ));
    }

    //this controller gets the backup history data for the backup_restore page
    public function backup_history_data()
    {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            $results = $this->Data_model->get_backup_history_data($form);
        }

        echo json_encode(array(
            "success" => (!empty($results)),
            "data" => $results,
            "msg" => (!empty($results))?"":"Nothing found"
        ));
    }

    public function save_backup(){
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();

            //Get the urn's
            $form['update_date_from'] = ($form['update_date_from']?to_mysql_datetime($form['update_date_from']):"");
            $form['update_date_to'] = ($form['update_date_to']?to_mysql_datetime($form['update_date_to']):"");
            $form['renewal_date_from'] = ($form['renewal_date_from']?to_mysql_datetime($form['renewal_date_from']):"");
            $form['renewal_date_to'] = ($form['renewal_date_to']?to_mysql_datetime($form['renewal_date_to']):"");
            $results = $this->Data_model->get_backup_data_by_campaign($form);

            //Divide the url_list in parts of 500 elements
            $aux = array();
            $pos_ini = 0;
            $pos_end = 500;
            while (count($results) >= $pos_end) {
                array_push($aux,array_slice($results, $pos_ini, $pos_end));
                $results = array_slice($results,$pos_end);
            }
            array_push($aux,array_slice($results, $pos_ini, count($results)));
            $results = $aux;

            if (!empty($results)) {
                //Create the file if doesn't exist
                if (!file_exists(BACKUP_PATH)) {
                    if (!mkdir(BACKUP_PATH, 0755)){
                        echo json_encode(array(
                            "success" => false,
                            "msg" => "ERROR: Backup ended with errors. Backup path does not exist"
                        ));
                        return;
                    }
                }
                $form['path'] = BACKUP_PATH.$form['name'].'.sql';
                $form['user_id'] = $_SESSION['user_id'];
                $form['backup_date'] = date('Y-m-d H:i:s');
                if (file_exists($form['path'])) {
                    unlink($form['path']);
                }
                //Open the file
                $fp = fopen($form['path'], 'a');
                //Write the header
                fwrite($fp, '-- ------------------------------------------------------------------------'.PHP_EOL);
                fwrite($fp, '-- ------------------------------------------------------------------------'.PHP_EOL);
                fwrite($fp, '-- CAMPAIGN BACKUP '.PHP_EOL);
                fwrite($fp, '-- File name - '.$form['name'].PHP_EOL);
                fwrite($fp, '-- Total Number of records - '.$form['num_records'].PHP_EOL);
                fwrite($fp, '-- Number of parts - '.count($results).PHP_EOL);
                fwrite($fp, '-- ------------------------------------------------------------------------'.PHP_EOL);
                fwrite($fp, '-- ------------------------------------------------------------------------'.PHP_EOL);
                fwrite($fp, PHP_EOL.PHP_EOL);

                //Get the urn_lists and execute the backup queries
                $part_num = 0;
                foreach($results as $result_part) {
                    $urn_list = "";
                    $part_num++;

                    foreach($result_part as $result) {
                        $urn_list .= $result['urn'].", ";
                    }
                    if (strlen($urn_list) > 0) {
                        $urn_list = "(".substr($urn_list,0,strlen($urn_list)-2).")";
                    }

                    //Exec the mysqldump queries
                    $qry = array();
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' records --where="urn IN '.$urn_list.'" --compact --no-create-info', $qry['records']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' history --where="urn IN '.$urn_list.'" --compact --no-create-info', $qry['history']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' cross_transfers --where="history_id IN (select history_id from history where urn IN '.$urn_list.')" --compact --no-create-info  --single-transaction', $qry['cross_transfers']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' record_details --where="urn IN '.$urn_list.'" --compact --no-create-info', $qry['record_details']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' email_history --where="urn IN '.$urn_list.'" --compact --no-create-info --extended-insert --net_buffer_length=5000', $qry['email_history']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' email_history_attachments --where="email_id IN (select email_id from email_history where urn IN '.$urn_list.')" --compact --no-create-info  --single-transaction', $qry['email_history_attachments']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' appointments --where="urn IN '.$urn_list.'" --compact --no-create-info', $qry['appointments']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' appointment_attendees --where="appointment_id IN (select appointment_id from appointments where urn IN '.$urn_list.')" --compact --no-create-info  --single-transaction', $qry['appointment_attendees']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' attachments --where="urn IN '.$urn_list.'" --compact --no-create-info', $qry['attachments']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' client_refs --where="urn IN '.$urn_list.'" --compact --no-create-info', $qry['client_refs']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' companies --where="urn IN '.$urn_list.'" --compact --no-create-info', $qry['companies']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' company_telephone --where="company_id IN (select company_id from companies where urn IN '.$urn_list.')" --compact --no-create-info  --single-transaction', $qry['company_telephone']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' company_addresses --where="company_id IN (select company_id from companies where urn IN '.$urn_list.')" --compact --no-create-info  --single-transaction', $qry['company_addresses']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' contacts --where="urn IN '.$urn_list.'" --compact --no-create-info', $qry['contacts']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' contact_telephone --where="contact_id IN (select contact_id from contacts where urn IN '.$urn_list.')" --compact --no-create-info  --single-transaction', $qry['contact_telephone']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' contact_addresses --where="contact_id IN (select contact_id from contacts where urn IN '.$urn_list.')" --compact --no-create-info  --single-transaction', $qry['contact_addresses']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' surveys --where="urn IN '.$urn_list.'" --compact --no-create-info', $qry['surveys']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' survey_answers --where="survey_id IN (select survey_id from surveys where urn IN '.$urn_list.')" --compact --no-create-info  --single-transaction', $qry['survey_answers']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' answer_notes --where="answer_id IN (select answer_id from survey_answers where survey_id IN (select survey_id from surveys where urn IN '.$urn_list.'))" --compact --no-create-info  --single-transaction', $qry['answer_notes']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' answers_to_options --where="answer_id IN (select answer_id from survey_answers where survey_id IN (select survey_id from surveys where urn IN '.$urn_list.'))" --compact --no-create-info  --single-transaction', $qry['answers_to_options']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' webform_answers --where="urn IN '.$urn_list.'" --compact --no-create-info', $qry['webform_answers']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' sticky_notes --where="urn IN '.$urn_list.'" --compact --no-create-info', $qry['sticky_notes']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' favorites --where="urn IN '.$urn_list.'" --compact --no-create-info', $qry['favorites']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' ownership --where="urn IN '.$urn_list.'" --compact --no-create-info', $qry['ownership']);
                    exec('mysqldump -u'.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' campaign_xfers --where="campaign_id = '.$form["campaign_id"].'" --compact --no-create-info', $qry['campaign_xfers']);

                    //write the backup_query into the file
                    fwrite($fp, '-- ------------------------------------------------------------------------'.PHP_EOL);
                    fwrite($fp, '-- PART '.$part_num.')'.PHP_EOL);
                    fwrite($fp, '-- URN List - '.$urn_list.PHP_EOL);
                    fwrite($fp, '-- Number of records - '.count($result_part).PHP_EOL);
                    fwrite($fp, '-- ------------------------------------------------------------------------'.PHP_EOL);

                    fwrite($fp, ''.PHP_EOL);
                    fwrite($fp, 'START TRANSACTION;'.PHP_EOL);
                    fwrite($fp, ''.PHP_EOL);

                    foreach($qry as $table => $query) {
                        fwrite($fp, ''.PHP_EOL);
                        fwrite($fp, '-- '.strtoupper($table).PHP_EOL);
                        if (!empty($query)) {
                            foreach ($query as $qry) {
                                fwrite($fp, $qry.PHP_EOL);
                            }
                        }
                        else {
                            fwrite($fp, '-- No data'.PHP_EOL);
                        }
                    }
                    fwrite($fp, PHP_EOL.PHP_EOL);

                    //Remove the data stored from the database
                    $this->Data_model->remove_backup_campaign_data($urn_list, $form['campaign_id']);
                }
                fwrite($fp, ''.PHP_EOL);
                fwrite($fp, 'COMMIT;'.PHP_EOL);
                fwrite($fp, ''.PHP_EOL);
                //Close the file
                fclose($fp);
            }

            //Save the backup
            $backup_id = $this->Data_model->save_backup_campaign_history($form);

            echo json_encode(array(
                "success" => ($backup_id),
                "data" => $backup_id,
                "msg" => ($backup_id)?"Backup finished successfully":"ERROR: Backup ended with errors"
            ));
        }
    }

    public function restore_backup() {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();

            exec('"mysql" -u '.$this->db->username.' -p'.$this->db->password.' '.$this->db->database.' < "'.$form['path'].'"', $results, $status);
            if ($status == 0) {
                $this->Data_model->update_backup_campaign_history_by_id(array(
                    'backup_campaign_id' => $form['backup_campaign_id'],
                    'restored' => 1,
                    'restored_date' => date('Y-m-d H:i:s')
                ));
            }
        }

        echo json_encode(array(
            "success" => ($status == 0),
            "msg" => ($status == 0?"Restored successfully":"ERROR: The restored doesn't finish successfully")
        ));
    }

    //################################################################################################
    //################################### OUTCOMES functions #########################################
    //################################################################################################

    //this controller loads the view for the outcomes page
    public function outcomes()
    {
        $status_list = $this->Form_model->get_status_list();
        $progress_list = $this->Form_model->get_progress_descriptions();

        $data      = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Admin',
            'title' => 'Admin | Outcomes',
            'page' => 'outcomes',
            'css' => array(
                'dashboard.css'
            ),
            'javascript' => array(
                'data.js'
            ),
            'status_list' => $status_list,
            'progress_list' => $progress_list
        );
        $this->template->load('default', 'data/outcomes.php', $data);
    }

    public function get_outcomes() {
        $outcomes = $this->Data_model->get_outcomes();

        echo json_encode(array(
            "success" => (!empty($outcomes)),
            "data" => (!empty($outcomes)?$outcomes:"No data created")
        ));
    }

    /**
     * Insert/Update an outcome
     */
    public function save_outcome() {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();

            $form['sort'] = ((isset($form['sort']) && ($form['sort']))?$form['sort']:null);
            $form['delay_hours'] = ((isset($form['delay_hours']) && ($form['delay_hours']))?$form['delay_hours']:null);

            $form['disabled'] = (isset($form['disabled'])?$form['disabled']:null);
            $form['positive'] = (isset($form['positive'])?$form['positive']:null);
            $form['dm_contact'] = (isset($form['dm_contact'])?$form['dm_contact']:null);
            $form['enable_select'] = (isset($form['enable_select'])?$form['enable_select']:null);
            $form['force_comment'] = (isset($form['force_comment'])?$form['force_comment']:null);
            $form['force_nextcall'] = (isset($form['force_nextcall'])?$form['force_nextcall']:null);
            $form['no_history'] = (isset($form['no_history'])?$form['no_history']:null);
            $form['keep_record'] = (isset($form['keep_record'])?$form['keep_record']:null);

            if ($form['outcome_id']) {
                $outcome_id = $form['outcome_id'];
                unset($form['outcome_id']);
                //Update the outcome
                $this->Data_model->update_outcome($outcome_id, $form);
            }
            else {
                //Insert a new outcome
                $outcome_id = $this->Data_model->insert_outcome($form);
            }

            echo json_encode(array(
                "success" => ($outcome_id),
                "msg" => ($outcome_id?"Outcome saved successfully!":"ERROR: The outcome was not save successfully!")
            ));
        }
    }

    /**
     * Disable an outcome
     */
    public function disable_outcome() {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();

            $form['disabled'] = ((isset($form['disabled']) && ($form['disabled']))?$form['disabled']:null);

            $disabled = ($form['disabled']?"disabled":"enabled");

            $outcome_id = $form['outcome_id'];
            unset($form['outcome_id']);
            //Update the outcome
            $results = $this->Data_model->update_outcome($outcome_id, $form);

            echo json_encode(array(
                "success" => ($results),
                "msg" => ($results?"Outcome ".$disabled." successfully!":"ERROR: The outcome was not save successfully!")
            ));
        }
    }

    /**
     * Delete outcome
     */
    public function delete_outcome() {
        if ($this->input->is_ajax_request()) {
            $outcome_id = $this->input->post("outcome_id");

            $results = $this->Data_model->delete_outcome($outcome_id);

            echo json_encode(array(
                "success" => ($results),
                "msg" => ($results?"Outcome deleted successfully!":"ERROR: The outcome was not deleted successfully!")
            ));
        }
    }

    //################################################################################################
    //################################### TRIGGERS functions #########################################
    //################################################################################################

    //this controller loads the view for the triggers page
    public function triggers()
    {
        $campaigns = $this->Form_model->get_campaigns();
        $outcomes = $this->Form_model->get_outcomes();
        $templates = $this->Form_model->get_templates();
        $users = $this->Form_model->get_users();

        $data      = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Admin',
            'title' => 'Admin | Triggers',
            'page' => 'triggers',
            'css' => array(
                'dashboard.css'
            ),
            'javascript' => array(
                'data.js'
            ),
            'campaigns' => $campaigns,
            'outcomes' => $outcomes,
            'templates' => $templates,
            'users' => $users
        );
        $this->template->load('default', 'data/triggers.php', $data);
    }

    /**
     * Get email triggers
     */
    public function get_email_triggers() {
        if ($this->input->is_ajax_request()) {

            $form = $this->input->post();

            $results = $this->Data_model->get_email_triggers($form);

            echo json_encode(array(
                "success" => (!empty($results)),
                "data" => (!empty($results)?$results:"No email triggers found")
            ));

        }
    }

    /**
     * Get email triggers recipients
     */
    public function get_email_trigger_recipients() {
        if ($this->input->post()) {
            $trigger_id = $this->input->post('trigger_id');

            $results = $this->Data_model->get_email_trigger_recipients($trigger_id);

            $auxList = array();
            foreach ($results as $user) {
                array_push($auxList, $user["user_id"]);
            }
            $results = $auxList;

            echo json_encode(array(
                "success" => ($results),
                "data" => $results
            ));
        }
    }

    //Save or update an email trigger
    public function save_email_trigger(){
        if ($this->input->post()) {
            $form = $this->input->post();

            $users = (isset($form['user_id'])?$form['user_id']:array());
            unset($form['user_id']);


            if (!empty($form['trigger_id'])) {
                $results = $this->Data_model->update_email_trigger($form);
                $trigger_id = $form['trigger_id'];
            }
            else {
                $trigger_id = $this->Data_model->insert_email_trigger($form);
            }

            if ($trigger_id) {
                $results = $this->Data_model->update_email_trigger_recipients($users, $trigger_id);
            }

            echo json_encode(array(
                "success" => ($trigger_id),
                "msg" => ($trigger_id?"Email Trigger saved successfully":"ERROR: The Email Trigger was not saved successfully!")
            ));
        }
    }

    //Delete an email trigger
    public function delete_email_trigger(){
        if ($this->input->post()) {
            $trigger_id = $this->input->post("trigger_id");

            $results = $this->Data_model->delete_email_trigger($trigger_id);

            //Delete the users for this trigger
            if ($results) {
                $results = $this->Data_model->update_email_trigger_recipients(array(), $trigger_id);
            }

            echo json_encode(array(
                "success" => ($results),
                "msg" => ($results?"Email Trigger deleted successfully":"ERROR: The Email Trigger was not deleted successfully!")
            ));
        }
    }

    /**
     * Get ownership triggers
     */
    public function get_ownership_triggers() {
        if ($this->input->is_ajax_request()) {

            $form = $this->input->post();

            $results = $this->Data_model->get_ownership_triggers($form);

            echo json_encode(array(
                "success" => (!empty($results)),
                "data" => (!empty($results)?$results:"No ownership triggers found")
            ));

        }
    }

    /**
     * Get ownership triggers recipients
     */
    public function get_ownership_trigger_recipients() {
        if ($this->input->post()) {
            $trigger_id = $this->input->post('trigger_id');

            $results = $this->Data_model->get_ownership_trigger_recipients($trigger_id);

            $auxList = array();
            foreach ($results as $user) {
                array_push($auxList, $user["user_id"]);
            }
            $results = $auxList;

            echo json_encode(array(
                "success" => ($results),
                "data" => $results
            ));
        }
    }

    //Save or update an ownership trigger
    public function save_ownership_trigger(){
        if ($this->input->post()) {
            $form = $this->input->post();

            $users = (isset($form['user_id'])?$form['user_id']:array());
            unset($form['user_id']);


            if (!empty($form['trigger_id'])) {
                $results = $this->Data_model->update_ownership_trigger($form);
                $trigger_id = $form['trigger_id'];
            }
            else {
                $trigger_id = $this->Data_model->insert_ownership_trigger($form);
            }

            if ($trigger_id) {
                $results = $this->Data_model->update_ownership_trigger_recipients($users, $trigger_id);
            }

            echo json_encode(array(
                "success" => ($trigger_id),
                "msg" => ($trigger_id?"Ownership Trigger saved successfully":"ERROR: The Ownership Trigger was not saved successfully!")
            ));
        }
    }

    //Delete an ownership trigger
    public function delete_ownership_trigger(){
        if ($this->input->post()) {
            $trigger_id = $this->input->post("trigger_id");

            $results = $this->Data_model->delete_ownership_trigger($trigger_id);

            //Delete the users for this trigger
            if ($results) {
                $results = $this->Data_model->update_ownership_trigger_recipients(array(), $trigger_id);
            }

            echo json_encode(array(
                "success" => ($results),
                "msg" => ($results?"Ownership Trigger deleted successfully":"ERROR: The Ownership Trigger was not deleted successfully!")
            ));
        }
    }

    //################################################################################################
    //################################### DUPLICATES functions #######################################
    //################################################################################################

    //this controller loads the view for the duplicates page
    public function duplicates()
    {
        $filter = array(
            array('field'=>'telephone_number', 'name'=>'Contact Telephone number'),
            array('field'=>'postcode', 'name'=>'Contact Postcode'),
            array('field'=>'fullname', 'name'=>'Contact Fullname'),
            array('field'=>'coname', 'name'=>'Company Name'),
            array('field'=>'company_telephone_number', 'name'=>'Company Telephone number'),
            array('field'=>'company_postcode', 'name'=>'Company Postcode'),
            array('field'=>'client_ref', 'name'=>'Client Reference')
        );

        $campaigns = $this->Form_model->get_campaigns();

        $data      = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Admin',
            'title' => 'Admin | Duplicates',
            'page' => 'duplicates',
            'css' => array(
                'dashboard.css'
            ),
            'javascript' => array(
                'data.js'
            ),
            'filter' => $filter,
            'campaigns' => $campaigns
        );
        $this->template->load('default', 'data/duplicates.php', $data);
    }

    /**
     * Get the duplicates records by a filter
     */
    public function get_duplicates() {
        if ($this->input->is_ajax_request()) {

            $form = $this->input->post();

            if (isset($form['field'])) {

                $results = $this->Data_model->get_duplicates($form);
            }

            echo json_encode(array(
                "success" => (!empty($results)),
                "data" => (!empty($results)?$results:"No duplicates found")
            ));
        }
    }

    /**
     * Delete the duplicates records (keeps the old one)
     */
    public function delete_duplicates() {
        if ($this->input->is_ajax_request()) {

            $form = $this->input->post();

            if (isset($form['field'])) {

                //Get the duplicate records
                $results = $this->Data_model->get_duplicate_records($form);
                //Group the duplicates by filter
                $aux = array();
                foreach($results as $result) {
                    $index = $result['filter'];
                    if (!isset($aux[$index])) {
                        $aux[$index] = array();
                    }
                    unset($result['filter']);
                    array_push($aux[$index], $result);
                }
                $results = $aux;

                //Keep the oldest duplicate records. For each duplicate group, remove from the array to delete the oldest
                $records_to_delete = array();
                foreach($results as $duplicate_records) {
                    $date_added = NULL;
                    $date_updated = NULL;
                    $urn = false;
                    $aux_dup = array();
                    foreach($duplicate_records as $key => $result) {

                        if (!$date_added || ($result['date_added'] && $date_added > $result['date_added'])) {
                            $date_added = $result['date_added'];
                            $date_updated = $result['date_updated'];
                            $urn = $result['urn'];
                        }
                        elseif ($date_added == $result['date_added']) {
                            if (!$date_updated || ($result['date_updated'] && $date_updated > $result['date_updated'])) {
                                $date_added = $result['date_added'];
                                $date_updated = $result['date_updated'];
                                $urn = $result['urn'];
                            }
                        }
                        $aux_dup[$result['urn']] = $result;
                    }
                    unset($aux_dup[$urn]);
                    foreach($aux_dup as $dup) {
                        array_push($records_to_delete, $dup['urn']);
                    }
                }
                $num_records = count(array_unique($records_to_delete));
                $records_to_delete = "(".implode(",",array_unique($records_to_delete)).")";

                //Delete the duplicate records (set the parked_code as Duplicate)
                $results = $this->Data_model->delete_duplicates($records_to_delete);
            }

            echo json_encode(array(
                "success" => (($results)),
                "num_records" => $num_records,
                "urn_list" => $records_to_delete,
                "msg" => (($results)?"Duplicates removed successfully":"The duplicates was not removed successfully")
            ));
        }
    }

    //################################################################################################
    //################################### SUPPRESSION functions ######################################
    //################################################################################################

    //this controller loads the view for the suppression page
    public function suppression()
    {
        $campaigns = $this->Form_model->get_campaigns();

        $data      = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Admin',
            'title' => 'Admin | Suppression',
            'page' =>  'suppression' ,
            'css' => array(
                'dashboard.css',
                'daterangepicker-bs3.css'
            ),
            'javascript' => array(
                'data.js',
                'lib/moment.js',
                'lib/daterangepicker.js'
            ),
            'campaigns' => $campaigns
        );
        $this->template->load('default', 'data/suppression.php', $data);
    }

    /**
     * Get the suppression numbers by a filter
     */
    public function get_suppression_numbers() {
        if ($this->input->is_ajax_request()) {

            $form = $this->input->post();

            $results = $this->Data_model->get_suppression_numbers($form);

            echo json_encode(array(
                "success" => (!empty($results)),
                "data" => (!empty($results)?$results:"No suppression numbers found")
            ));
        }
    }

    /**
     * Get the suppression numbers by telephone_number
     */
    public function get_suppression_by_telephone_number() {
        if ($this->input->is_ajax_request()) {

            $telephone_number = ($this->input->post('telephone_number')?$this->input->post('telephone_number'):"''");

            $result = $this->Data_model->get_suppression_by_telephone_number($telephone_number);

            if (!empty($result)) {
                $result['campaign_id_list'] = explode(',',str_replace(" ","",$result['campaign_id_list']));
            }

            echo json_encode(array(
                "success" => (!empty($result)),
                "data" => ($result)
            ));
        }
    }

    /**
     * Save the suppression number
     */
    public function save_suppression() {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();

            $reason = $form['reason'];
            $telephone_number = $form['telephone_number'];
            $campaign_list = (isset($form['suppression_campaign_id'])?$form['suppression_campaign_id']:array());

            if ($form['suppression_id']) {
                $results = $this->Data_model->update_suppression($form['suppression_id'],$telephone_number, $reason);
                $suppression_id = $form['suppression_id'];
            }
            else {
                $suppression_id = $this->Data_model->insert_suppression($telephone_number, $reason);
            }

            if ($suppression_id) {
                // Save the campaigns
                $this->Data_model->save_suppression_by_campaign($suppression_id, $campaign_list);
            }

            echo json_encode(array(
                "success" => ($suppression_id),
                "msg" => ($suppression_id?"Suppression number saved successfully!":"ERROR: The suppression number was not saved successfully!")
            ));

        }
    }

    //################################################################################################
    //################################### PARK CODE functions ########################################
    //################################################################################################

    //this controller loads the view for the parkcodes page
    public function parkcodes()
    {
        $data      = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Admin',
            'title' => 'Admin | Park Codes',
            'page' => 'parkcode',
            'css' => array(
                'dashboard.css'
            ),
            'javascript' => array(
                'data.js'
            )
        );
        $this->template->load('default', 'data/parkcodes.php', $data);
    }

    public function get_parkcodes() {
        $parkcodes = $this->Data_model->get_parkcodes();

        echo json_encode(array(
            "success" => (!empty($parkcodes)),
            "data" => (!empty($parkcodes)?$parkcodes:"No data found")
        ));
    }

    /**
     * Insert/Update an parkcode
     */
    public function save_parkcode() {
        if ($this->input->is_ajax_request()) {
            if (!empty($this->input->post('parked_code'))) {
                //Update the parkcode
                $response = $this->Data_model->update_parkcode($this->input->post());
            }
            else {
                //Insert a new parkcode
                $response = $this->Data_model->insert_parkcode($this->input->post());
            }

            echo json_encode(array(
                "success" => $response,
                "msg" => $response?"Park code saved successfully":"There was an error saving the parked code"
            ));
        }
    }

    /**
     * Delete parkcode
     */
    public function delete_parkcode() {
        if ($this->input->is_ajax_request()) {
            $results = $this->Data_model->delete_parkcode($this->input->post("parked_code"));
            echo json_encode(array(
                "success" => ($results),
                "msg" => ($results?"Park code was deleted successfully":"The park code was not deleted")
            ));
        }
    }

}