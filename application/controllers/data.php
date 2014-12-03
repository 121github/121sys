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
    //this loads the data management view
    public function index()
    {
        $campaigns = $this->Form_model->get_campaigns();
        $sources   = $this->Form_model->get_sources();
        $data      = array(
            'campaign_access' => $this->_campaigns,
			'pageId' => 'Admin',
            'title' => 'Admin | Import',
            'page' => array(
                'admin' => 'data',
            	'inner' => 'import'
            		
            ),
            'javascript' => array(
                'plugins/jqfileupload/vendor/jquery.ui.widget.js',
                'plugins/jqfileupload/jquery.iframe-transport.js',
                'plugins/jqfileupload/jquery.fileupload.js',
                'data.js'
            ),
            'campaigns' => $campaigns,
            'sources' => $sources,
            'css' => array(
                'dashboard.css',
                'plugins/jqfileupload/jquery.fileupload.css'
            )
        );
        $this->template->load('default', 'data/data.php', $data);
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
                "company_number" => "LTD CoNumber",
                "turnover" => "Turnover",
                "Employees" => "employees"
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
            'page' => array(
                'admin' => 'data',
            	'inner' => 'management'
            ),
            'campaigns' => $campaigns,
            'css' => array(
                'dashboard.css'
            ),
            'javascript' => array(
                'data.js'
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
            'page' => array(
                'admin' => 'data',
                'inner' => 'daily_ration'
            ),
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
        $parked_codes = array();
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
                }
                if (!in_array($result['park_reason'], $parked_codes)) {
                    $parked_codes[$result['parked_code']] = $result['park_reason'];
                }
                unset($result['campaign_id']);
                unset($result['campaign_name']);
                unset($result['total_records']);
                unset($result['daily_data']);
                $aux[$campaign_id]['total_parked'] = (isset($aux[$campaign_id]['total_parked']))?($aux[$campaign_id]['total_parked']+$result['count']):$result['count'];
                $result["url"] = $url."/campaign/".$campaign_id."/parked/yes/parked-code/".$result['parked_code'];
                $aux[$campaign_id][$result['parked_code']] = $result;
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
                    $this->Data_model->reassign_data($user['user'], $this->input->post('state'), $this->input->post('campaign'), $user['count']);
                } else {
                    $this->Data_model->reassign_data($user['user'], $this->input->post('state'), $this->input->post('campaign'));
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
            $options = array(
                "autoincrement" => $autoincrement,
                "duplicates" => $duplicates,
                "campaign" => $campaign,
                "source" => $source
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
    	$campaigns = $this->Form_model->get_campaigns();
    	
    	$data = array(
    			'campaign_access' => $this->_campaigns,
    			'pageId' => 'Admin',
    			'title' => 'Admin | Add Record',
    			'page' => array(
    					'admin' => 'data',
    					'inner' => 'add_record'
    			),
    			'campaigns' => $campaigns,
    			'css' => array(
    					'dashboard.css'
    			),
    			'javascript' => array(
    					'data.js'
    			)
    	);
    	$this->template->load('default', 'data/add_record.php', $data);
    }
    
    /**
     * Insert a record
     */
    public function save_record()
    {
    	$form = $this->input->post();
    	$status = $this->Records_model->get_status_by_name("Live");
    	$source = $this->Records_model->get_source_by_name("Manual");
    	$form['record_status'] = $status->record_status_id;
    	$form['source_id'] = $source->source_id;
    	
    	$company = array();
    	$contact = array();
    	
    	if (!empty($form['company_name'])) {
    		$company['name'] = $form['company_name'];
    		$response = true;
    	}
    	
    	else if (!empty($form['contact_name'])) {
    		$contact['fullname'] = $form['contact_name'];
    		$name = explode(' ', $form['contact_name'], 2);
    		$contact['firstname'] = $name[0];
    		$contact['lastname'] = $name[1];
    		$response = true;
    	}
    	else {
    		$response = false;
    	}
    	
    	if ($response) {
    		unset($form['company_name']);
    		unset($form['contact_name']);
    		 
    		$this->firephp->log($form['campaign_id']);
    		$this->firephp->log($company);
    		$this->firephp->log($contact);
    		 
    		if (!empty($form['campaign_id'])) {
    			$this->firephp->log($form);
    			$record_id = $this->Records_model->save_record($form);
    			if ($record_id) {
    				if (!empty($company)) {
    					$company['urn'] = $record_id;
    					$insert_id = $this->Company_model->save_company($company);
    					$response = ($insert_id)?true:false;
    				}
    				elseif (!empty($contact)) {
    					$contact['urn'] = $record_id;
    					$insert_id = $this->Contacts_model->save_contact($contact);
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
}