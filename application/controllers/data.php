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
    }
    //this loads the data management view
    public function index()
    {
        $campaigns = $this->Form_model->get_campaigns();
        $sources   = $this->Form_model->get_sources();
        $data      = array(
            'campaign_access' => $this->_campaigns,
'pageId' => 'Dashboard',
            'title' => 'Dashboard',
            'page' => array(
                'admin' => 'data'
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
'pageId' => 'Dashboard',
            'title' => 'Dashboard',
            'page' => array(
                'admin' => 'management'
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
            usort($array, function($a, $b)
            {
                return $b['count'] - $a['count'];
            });
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
            //set the progress status
            file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt", "0"."\n",FILE_APPEND);
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
                file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt", $progress."\n",FILE_APPEND);
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
                                        file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt", "Date format error on row $row"."\n",FILE_APPEND);
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
                        "data" => $errors
                    ));
                    file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt", "Import stopped on row $row. Error: " . $errors[0]."\n",FILE_APPEND);
					 $this->firephp->log("Error adding row $row");
                    exit;
                } else {
                    $this->firephp->log("$row was added ok");

                }

            }
            
            //If a company_postcode is added, run the script to calculate the longitude and latitude
            foreach ($import as $data) {
            	if (array_key_exists('c_postcode', $data) && array_key_exists("company_addresses", $tables)) {
            		file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt", "0"."\n",FILE_APPEND);
            		$errors = $this->check_company_postcodes();
            		if (count($errors) > 0) {
            			echo json_encode(array(
            					"success" => false,
            					"rows" => $row,
            					"data" => $errors
            			));
            			file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt", "Error importing the coords for some postcodes in the company address. Error: " . $errors[0]."\n",FILE_APPEND);
            			$this->firephp->log("Error importing the coords for some postcode in the company address");
            			exit;
            		} else {
            			$this->firephp->log("Coords added");
            		
            		}
            		break;
            	}
            }
            
            //If a contact_postcode is added, run the script to calculate the longitude and latitude
            foreach ($import as $data) {
            	if (array_key_exists('postcode', $data) && array_key_exists("contact_addresses", $tables)) {
            		file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt", "0"."\n",FILE_APPEND);
            		$errors = $this->check_contact_postcodes();
            		if (count($errors) > 0) {
            			echo json_encode(array(
            					"success" => false,
            					"rows" => $row,
            					"data" => $errors
            			));
            			file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt", "Error importing the coords for some postcodes  in the contact address. Error: " . $errors[0]."\n",FILE_APPEND);
            			$this->firephp->log("Error importing the coords for some postcode in the contact address");
            			exit;
            		} else {
            			$this->firephp->log("Coords added");
            
            		}
            		break;
            	}
            }
            
            if (count($errors) <= 0) {
            	file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt", "success"."\n",FILE_APPEND);
            }
            
			echo json_encode(array(
                "success" => true,
                "rows" => $row,
                "data" => "Import was successfull"
            ));
        }
    }
    public function get_progress()
    {
    	if ($this->input->post('first') && !$this->input->post('locations')) {
            file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt", "0"."\n",FILE_APPEND);
            $progress = 0;
        }
        elseif ($this->input->post('first') && $this->input->post('locations')) { 
        	file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt", "1"."\n",FILE_APPEND);
        	$progress = 1;
        }
        else {
        	//$progress = file_get_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt");
        	$file = dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt";
        	$data = file($file);
        	$progress = $data[count($data)-1];
        }
        
        $array =array(
            "progress" => $progress,	 
        );
        if ($this->input->post('locations')) {
        	$array['locations'] = true;
        }
        
        
        $content = file_get_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt");
        
        if ($content == "success"){
        	$array['success'] = true;
        }
        
        echo json_encode($array);
    }
    
    public function check_company_postcodes() {
    	
    	//Get the companyAddresses without Coords
    	$compAddrWithoutCoords = $this->Company_model->get_company_addresses_without_coords();
    	$errors     = array();
    	$current = 0;
    	$rows = count($compAddrWithoutCoords);
    	foreach ($compAddrWithoutCoords as $compAddr) {
    		$current++;
    		$progress = ceil($current / $rows * 100);
    		file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt", $progress."\n",FILE_APPEND);
    		$coords = postcode_to_coords($compAddr['postcode']);
    		if ($coords['lat'] && $coords['lng']) {
    			$compAddr['latitude'] = $coords['lat'];
    			$compAddr['longitude'] = $coords['lng'];
    			$this->Company_model->update_company_address($compAddr);
    			if ($this->db->_error_message()) {
    				$errors[] = $this->db->_error_message();
    			}
    		}
    		else {
    			$errors[] = "Error getting the coords from google";
    		}
    	}
    	return $errors;
    }
    
    
    public function check_contact_postcodes() {
    	 
    	//Get the contactAddresses without Coords
    	$contactAddrWithoutCoords = $this->Contacts_model->get_contact_addresses_without_coords();
    	$errors     = array();
    	$current = 0;
    	$rows = count($contactAddrWithoutCoords);
    	if (count($contactAddrWithoutCoords) > 0) {
    		foreach ($contactAddrWithoutCoords as $contactAddr) {
    			$current++;
    			$progress = ceil($current / $rows * 100);
    			file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/uploadprogress.txt", $progress."\n",FILE_APPEND);
    			$coords = postcode_to_coords($contactAddr['postcode']);
    			if ($coords['lat'] && $coords['lng']) {
    				$contactAddr['latitude'] = $coords['lat'];
    				$contactAddr['longitude'] = $coords['lng'];
    				$this->Contacts_model->update_contact_address($contactAddr);
    				if ($this->db->_error_message()) {
    					$errors[] = $this->db->_error_message();
    				}
    			}
    			else {
    				$errors[] = "Error getting the coords from google";
    			}
    		}	
    	}
    	else {
    		$errors[] = "There is no contact addresses without coords";
    	}
    	return $errors;
    }
}