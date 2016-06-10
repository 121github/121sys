<?php
require('upload.php');

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Records extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        user_auth_check();
        $this->_campaigns = campaign_access_dropdown();
        $this->project_version = $this->config->item('project_version');
        $this->load->model('User_model');
		$this->load->model('Filter_model');
        $this->load->model('Records_model');
        $this->load->model('Survey_model');
        $this->load->model('Form_model');
		$this->load->model('Audit_model');
        $this->load->model('Appointments_model');
        $this->load->model('Datatables_model');
        $this->_access = $this->User_model->campaign_access_check($this->input->post('urn'), true);
		$this->_global_filter = isset($_SESSION['filter']['values']);
    }

	public function save_related_records(){
		//$query = "select urn from records inner join companies using(urn) left join contacts using(urn) left join contact_telephone cont using(contact_id) left join company_telephone comt using(company_id) where cont.telephone_number is not null or comt.telephone_number is not null group by urn";
		$query = "select urn from records where campaign_id = 18";
		$urns = $this->db->query($query)->result_array();
		foreach($urns as $urn){
			$this->Records_model->find_related_records($urn['urn']);
		}
	}

	public function related_records(){
	  if ($this->input->is_ajax_request()) {
		  //this function find duplicate or associated records. You can check against an array of fields (original) or you can check against a urn
		  if($this->input->post('urn')){
            $urn = $this->input->post('urn');
			$campaign = $this->Records_model->get_campaign_from_urn($urn);
			$original = false;
		  } else {
			 $urn = false;
			 $original = $this->input->post();
			 $campaign = $this->input->post('campaign');
		  }
			$result = $this->Records_model->find_related_records($urn,$campaign,$original);
			echo json_encode(array("success"=>true,"data"=>$result,"msg"=>"No matches were found"));
	  }

	}

    //list the records in the callpot

    public function view()
    {
        //this array contains data for the visible columns in the table on the view page
		$this->load->model('Datatables_model');
		$visible_columns = $this->Datatables_model->get_visible_columns(1);
		if(!$visible_columns){
		 $this->load->model('Admin_model');
		$this->Datatables_model->set_default_columns($_SESSION['user_id']);
		$visible_columns = $this->Datatables_model->get_visible_columns(1);
		}
		$_SESSION['col_order'] = $this->Datatables_model->selected_columns(false,1);
		
		$title = "Record List";
		$global_filter = false;
			if(in_array("enable global filter",$_SESSION['permissions'])){
				$global_filter = $this->Filter_model->build_filter_options();
					$global_filter['campaigns'] = $this->_campaigns;
		}
        $data = array(
		'global_filter' => $global_filter,
            'campaign_access' => $this->_campaigns,
            'page' => 'list_records',
            'title' => $title,
            'columns' => $visible_columns,
			'submenu' => array("file"=>'record_list.php',"title"=>$title),
            'css' => array(
                'plugins/bootstrap-toggle/bootstrap-toggle.min.css',
				'map.css',
                'plugins/bootstrap-iconpicker/icon-fonts/font-awesome-4.2.0/css/font-awesome.min.css',
                'plugins/bootstrap-iconpicker/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css',
            ),
            'javascript' => array(
                'view.js',
				'location.js',
                'plugins/bootstrap-toggle/bootstrap-toggle.min.js',
                'plugins/fontawesome-markers/fontawesome-markers.min.js',
				'plugins/DataTables/datatables.min.js',
				//'plugins/DataTables/js/dataTables.bootstrap.js',
		'plugins/bootstrap-iconpicker/bootstrap-iconpicker/js/iconset/iconset-fontawesome-4.2.0.min.js',
                'plugins/bootstrap-iconpicker/bootstrap-iconpicker/js/bootstrap-iconpicker.min.js',
            )
        );
        $this->template->load('default', 'records/list_records.php', $data);

    }

	    public function mapview()
    {
        //this array contains data for the visible columns in the table on the view page
		$this->load->model('Datatables_model');
		$visible_columns = $this->Datatables_model->get_visible_columns(1);
		if(!$visible_columns){
		 $this->load->model('Admin_model');
		$this->Datatables_model->set_default_columns($_SESSION['user_id']);
		$visible_columns = $this->Datatables_model->get_visible_columns(1);
		}
		$_SESSION['col_order'] = $this->Datatables_model->selected_columns(false,1);
				$global_filter = false;
					if(in_array("enable global filter",$_SESSION['permissions'])){
				$global_filter = $this->Filter_model->build_filter_options();
					$global_filter['campaigns'] = $this->_campaigns;
		}
		$title = "Record List";
        $data = array(
		'global_filter' => $global_filter,
            'campaign_access' => $this->_campaigns,
            'page' => 'list_records',
            'title' => $title,
            'columns' => $visible_columns,
			'submenu' => array("file"=>'record_list.php',"title"=>$title),
            'css' => array(
                'plugins/bootstrap-toggle/bootstrap-toggle.min.css',
				'map.css',
                'plugins/bootstrap-iconpicker/icon-fonts/font-awesome-4.2.0/css/font-awesome.min.css',
                'plugins/bootstrap-iconpicker/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css'
            ),
            'javascript' => array(
				'location.js',
                'map.js',
                'view-map.js',
                'plugins/bootstrap-toggle/bootstrap-toggle.min.js',
                'plugins/fontawesome-markers/fontawesome-markers.min.js',
				'plugins/DataTables/datatables.min.js',
		'plugins/bootstrap-iconpicker/bootstrap-iconpicker/js/iconset/iconset-fontawesome-4.2.0.min.js',
                'plugins/bootstrap-iconpicker/bootstrap-iconpicker/js/bootstrap-iconpicker.min.js'
            )
        );
        $this->template->load('default', 'records/list_records_and_map.php', $data);

    }


    public function get_used_icons() {
        //Get icons used so far
        $icons = $this->Records_model->get_used_icons();

        $aux = array('empty');
        foreach($icons as $icon) {
            if ($icon['record_map_icon']) {
                array_push($aux,$icon['record_map_icon']);
            }
            if ($icon['campaign_map_icon']) {
                array_push($aux,$icon['campaign_map_icon']);
            }
        }
        $icons = array_unique($aux);

        $aux = array();
        foreach($icons as $icon) {
            array_push($aux,$icon);
        }
        $icons = $aux;

        echo json_encode(array(
            "icons" => $icons
        ));
    }

    public function process_view()
    {
        if ($this->input->is_ajax_request()) {
			session_write_close();
			/* debug loading times */
			$this->benchmark->mark('code_start');
            $options = $this->input->post();
			$this->load->model('Datatables_model');
			$visible_columns = $this->Datatables_model->get_visible_columns(1);
			//$this->firephp->log($visible_columns);
			$options['visible_columns'] = $visible_columns;
			//check the options
			foreach($options['columns'] as $k=>$column){
				//$this->firephp->log($column);				
				if($column['data']=="color_icon"&&$column['search']['value']=="Icon"){
					$options['columns'][$k]['search']['value']="";
				}
					if($column['data']=="distance"){
					$distance_sql = $this->Datatables_model->get_distance_query();
					$options['visible_columns']['select'][$k] = $distance_sql . "distance";
					$options['visible_columns']['order'][$k] = $distance_sql;
					}
			}
			$this->benchmark->mark('query_start');

            $records = $this->Records_model->get_records($options);
			//$this->Records_model->get_nav($options);
			$this->benchmark->mark('query_end');
			$count = $records['count'];
			unset($records['count']);
			$urns     = array();

            foreach ($records as $k => $v) {
				$urns[] = $v['urn'];
                //Location
                if ($records[$k]["company_location"]) {
                    $location_ar = explode(',',$records[$k]["company_location"]);
                }
                else if ($records[$k]["contact_location"]) {
                    $location_ar = explode(',',$records[$k]["contact_location"]);
                }
                if (!empty($location_ar)) {
                    $postcode_ar = explode("|",$location_ar[0]);
                    $postcode = substr($postcode_ar[0],0,stripos($postcode_ar[0],'('));
                    $location = explode('/',substr($postcode_ar[0],stripos($postcode_ar[0],'(')));
                    $records[$k]["postcode"] = $postcode;
                    $records[$k]["lat"] = substr($location[0],1);
                    $records[$k]["lng"] = substr($location[1],0,strlen($location[1])-1);
                    $records[$k]["location_id"] = $postcode_ar[1];
                }
                else {
                    $records[$k]["postcode"] = NULL;
                    $records[$k]["lat"] = NULL;
                    $records[$k]["lng"] = NULL;
                    $records[$k]["location_id"] = NULL;
                }

			  	//Record color
                $records[$k]["record_color"] = ($options['group']?genColorCodeFromText($records[$k][$options['group']]):($records[$k]["record_color"]?'#'.$records[$k]["record_color"]:genColorCodeFromText($records[$k]["urn"])));
                $records[$k]["record_color_map"] = $records[$k]["record_color"];

                //Add the icon to the record color
                $map_icon = ((in_array("planner", $_SESSION['permissions']) && $records[$k]['record_planner_id'])?'fa-flag':($records[$k]['map_icon']?$records[$k]['map_icon']:($records[$k]['campaign_map_icon']?$records[$k]['campaign_map_icon']:'fa-map-marker')));
                $records[$k]["color_icon"] = '<span class="fa '.$map_icon.'" style="font-size:20px; color: '.$records[$k]["record_color"].'">&nbsp;</span>';
				// color dot
				  $records[$k]["color_dot"] = '<span class="fa fa-circle" style="font-size:20px; color: '.$records[$k]["record_color"].'">&nbsp;</span>';

                //Map Icon
                $records[$k]["map_icon"] = ($records[$k]['map_icon']?str_replace("FA_","",str_replace("-","_",strtoupper($records[$k]['map_icon']))):NULL);
                $records[$k]["campaign_map_icon"] = ($records[$k]['campaign_map_icon']?str_replace("FA_","",str_replace("-","_",strtoupper($records[$k]['campaign_map_icon']))):NULL);

                //Planner addresses options
                $records[$k]["planner_addresses"] = array(
                    $records[$k]["location_id"] => $records[$k]["postcode"],
                    //$records[$k]["appointment_location_id"] => $records[$k]["appointment_postcode"]
                );
            }



			$this->benchmark->mark('code_end');
			$query_time = $this->benchmark->elapsed_time('query_start', 'query_end');
            $data = array(
				"process_time" => number_format($this->benchmark->elapsed_time('code_start', 'code_end')-$query_time,3),
				"query_time" => number_format($query_time,3),
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "data" => $records,
                "planner_permission" => (in_array("planner", $_SESSION['permissions']))
            );
            echo json_encode($data);
        }
    }

    public function detail()
    {
        if (!intval($this->uri->segment(3))) {
            if (!isset($_SESSION['current_campaign'])) {
                redirect('error/campaign');
            }
            //if no urn is entered into the url then we allocate one
            $urn       = $this->Records_model->get_record();
            $automatic = true;
            //unsetting navigation array because it's not needed when users are using the automated method
            if (isset($_SESSION['nav'])) {
                unset($_SESSION['nav']);
            }
        } else {
            //a record has been selected using a crafted url /records/detail/[urn]
            $automatic = false;
            $urn       = $this->uri->segment(3);
        }

        /* work out the previous and next urns */
        $previous = (isset($_SESSION['prev']) ? $_SESSION['prev'] : "");
        $current  = (isset($_SESSION['curr']) ? $_SESSION['curr'] : $urn);
        $next     = (isset($_SESSION['next']) ? $_SESSION['next'] : "0");

        if ($urn == $previous) {
            $_SESSION['next'] = $current;
        } else if ($urn == $current) {
            $_SESSION['curr'] = $urn;
            $_SESSION['next'] = "0";
        } else if ($urn == $next || empty($next)) {
            $_SESSION['prev'] = $current;
            $_SESSION['curr'] = $urn;
            $_SESSION['next'] = "0";
        }
        /* end nav config */
        $this->User_model->campaign_access_check($urn);

		//first time a record is loaded set the logging fields
	if(!isset($_SESSION['record_urn'])||!isset($_SESSION['record_loaded'])){
		$_SESSION['record_urn'] = $urn;
		$_SESSION['record_loaded'] = date('Y-m-d H:i:s');
	}
	//for subsequent page loads we only log it if the urn has changed
	if($urn<>$_SESSION['record_urn']){
		$_SESSION['record_loaded'] = date('Y-m-d H:i:s');
		$_SESSION['record_urn'] = $urn;
	}
        $campaign                     = $this->Records_model->get_campaign($urn);
        $campaign_id                  = $campaign['campaign_id'];
        $campaign_layout              = $campaign['record_layout'];

if(!isset($_SESSION['current_campaign'])||$campaign_id<>$_SESSION['current_campaign']){
	$this->User_model->set_permissions();
	 foreach ($this->User_model->campaign_permissions($campaign_id) as $row) {
                    //a 1 indicates the permission should be added otherwize it is revoked!
                    if ($row['permission_state'] == "1") {
                        $_SESSION['permissions'][$row['permission_id']] = $row['permission_name'];
                    } else {
                        unset($_SESSION['permissions'][$row['permission_id']]);
                    }
                }
				if($campaign['campaign_status']=="1"){
                $_SESSION['current_client'] = $campaign['client_name'];
                $_SESSION['current_campaign_name'] = $campaign['campaign_name'];
                $_SESSION['current_campaign'] = $campaign_id;
				}
}

        //get the features for the campaign and put the ID's into an array
        $campaign_features            = $this->Form_model->get_campaign_features($campaign_id);
        //get the panels for the different features. These panels will be laoded in the view if they have been selected on the campaign
        $features                     = array();
        $panels                       = array();
				
        foreach ($campaign_features as $row) {
            $features[] = $row['id'];
            if (!empty($row['path'])) {
                $panels[$row['id']] = $row['path'];
            }
        }

		//add custom panels
            $custom_panels         = $this->Records_model->get_custom_panels($campaign_id);
        //get the details of the record for the specified features eg appointment details etc. This saves us having to join every single table when they may not be needed
        $details                   = $this->Records_model->get_details($urn, $features);
        //add the logo file to the record details so we can load it in the view
        $details['record']['logo'] = $campaign['logo'];
        //check if this user has already updated the record and if they have they can select next without an update
        $allow_skip                = $this->Records_model->updated_recently($urn);
        if (in_array("quick search", $_SESSION['permissions'])||in_array("advanced search", $_SESSION['permissions'])) {
            $allow_skip = true;
        }
        $progress_options = $this->Form_model->get_progress_descriptions();
        $outcomes         = $this->Records_model->get_outcomes($campaign_id);
		$outcome_reasons         = $this->Records_model->get_outcome_reasons($campaign_id);
        $xfers            = $this->Records_model->get_xfers($campaign_id);

        $prev = false;
        $next = false;
        if (isset($_SESSION['navigation'])) {
            foreach ($_SESSION['navigation'] as $key => $val):
                if ($val == $urn) {
                    $prev = ($key > 0 ? $_SESSION['navigation'][$key - 1] : "");
                    $next = ($key < count($_SESSION['navigation']) - 1 ? $_SESSION['navigation'][$key + 1] : "");
                }
            endforeach;
        }
		
        //Get the campaign_triggers if exists
        $campaign_triggers = array();
        if ($campaign['campaign_id']) {
            $campaign_triggers = $this->Form_model->get_campaign_triggers_by_campaign_id($campaign['campaign_id']);
        }
		$title = "View Details";
		$global_filter = false;
					if(in_array("enable global filter",$_SESSION['permissions'])){
				$global_filter = $this->Filter_model->build_filter_options();
					$global_filter['campaigns'] = $this->_campaigns;
		}
        $data = array(
		'global_filter' => $global_filter,
		'submenu' => array("file"=>'record_details.php',"title"=>$title,"data"=>$details),
            'campaign_access' => $this->_campaigns,
			'title'=>$title,
            'page' => '',
            'campaign' => $campaign,
            'title' => 'Record Details',
            'details' => $details,
            'outcomes' => $outcomes,
			'outcome_reasons' => $outcome_reasons,
            "features" => $features,
            "panels" => $panels,
			//'global_filter' => $global_filter,
            "allow_skip" => $allow_skip,
            "xfer_campaigns" => $xfers,
            "progress_options" => $progress_options,
            "automatic" => $automatic,
            "map_icon" => $details['record']['map_icon'],
            "campaign_triggers" => $campaign_triggers,
            "javascript" => array(
                "detail2.js",
				"availability.js",
				"custom_panels.js",
				'plugins/bootstrap-toggle/bootstrap-toggle.min.js',
                'plugins/jqfileupload/vendor/jquery.ui.widget.js',
                'plugins/jqfileupload/jquery.iframe-transport.js',
                'plugins/jqfileupload/jquery.fileupload.js',
                'plugins/jqfileupload/jquery.fileupload-process.js',
                'plugins/jqfileupload/jquery.fileupload-validate.js',
                'plugins/countdown/jquery.plugin.min.js',
                'plugins/countdown/jquery.countdown.min.js',
                'plugins/responsive-calendar/0.8/responsive-calendar.js',
                'lib/jquery.numeric.min.js',
                'lib/jquery.alphanum.js',
                'plugins/bootstrap-iconpicker/bootstrap-iconpicker/js/iconset/iconset-fontawesome-4.2.0.min.js',
                'plugins/bootstrap-iconpicker/bootstrap-iconpicker/js/bootstrap-iconpicker.min.js',
				'lib/bootstrap-datetimepicker.js',
				'plugins/jQuery-contextMenu-master/dist/jquery.contextMenu.min.js',
				'plugins/fullcalendar-2.6.1/fullcalendar.min.js',
				'plugins/fullcalendar-2.6.1/gcal.js',
				'booking.js',
				'plugins/mmenu2/addons/js/jquery.mmenu.fixedelements.min.js'
            ),
            'css' => array(
				'bootstrap-datetimepicker.css',
				'plugins/bootstrap-toggle/bootstrap-toggle.min.css',
                'plugins/jqfileupload/jquery.fileupload.css',
                'plugins/countdown/jquery.countdown.css',
                'plugins/responsive-calendar/responsive-calendar.css',
                'plugins/bootstrap-iconpicker/icon-fonts/font-awesome-4.2.0/css/font-awesome.min.css',
                'plugins/bootstrap-iconpicker/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css',
				'plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css',
				'plugins/jQuery-contextMenu-master/dist/jquery.contextMenu.min.css',
				'plugins/fullcalendar-2.6.1/fullcalendar.min.css'
            ),
            'nav' => array(
                'prev' => $prev,
                'next' => $next
            )
        );
		
		if(count($custom_panels)>0){
		 $data['custom_panels'] = $custom_panels;	
		}

        //if appointment setting is on we need the available addresses
        if (in_array(14, $features)) {
            $webforms         = $this->Records_model->get_webforms($urn);
            $data['webforms'] = $webforms;
        }

        //if appointment setting is on we need the available addresses
        if (in_array(10, $features)||in_array(17, $features)||in_array(20, $features)) {
            $addresses         = $this->Records_model->get_addresses($urn);
            $data['addresses'] = $addresses;
			$postcode = isset($addresses[0]['postcode'])?$addresses[0]['postcode']:false;
			if(!in_array(17, $features)&&!in_array(20, $features)){
			$postcode = false;
			}
            $attendees         = $this->Records_model->get_attendees(false, $campaign_id, $postcode);
            $data['attendees'] = $attendees;
			$types         = $this->Records_model->get_appointment_types(false, $campaign_id);
            $data['types'] = $types;
        }

        //get the users we need the ownership feature is on
        if (in_array(5, $features)) {
            $users         = $this->Records_model->get_users(false, $campaign_id);
            $data['users'] = $users;
        }
		 //get the users we need the ownership feature is on
        if (in_array(18, $features)) {
            $regions         = $this->Form_model->get_campaign_regions($campaign_id);
            $data['regions'] = $regions;
        }

			//take ownership of unassigned record if nobody else is on it
			if(in_array('take ownership',$_SESSION['permissions'])){
			$this->Records_model->take_ownership($urn);
			}

        $this->template->load('default', 'records/custom/' . (!empty($campaign_layout) ? $campaign_layout : "2col.php"), $data);

    }

	public function update_record_task(){
		if ($this->input->is_ajax_request() && $this->_access) {
			$this->Records_model->save_task($this->input->post());
		}
	}

	public function get_task_history(){
		if ($this->input->is_ajax_request() && $this->_access) {
			$history = $this->Records_model->get_task_history($this->input->post('urn'));
			echo json_encode(array("success"=>true,"data"=>$history));
		}
	}

	public function get_campaign_tasks(){
		if ($this->input->is_ajax_request() && $this->_access) {
			$campaign_id = $this->Records_model->get_campaign_from_urn($this->input->post('urn'));
			 $result = $this->Records_model->get_campaign_tasks($campaign_id);
			 $tasks = $this->Records_model->get_record_tasks($this->input->post('urn'));
			 $data = array();
			 $selected = array();
			 $task_statuses = array();
			 foreach($result as $row){
				 $data[$row['task_id']] = $row;
				if(!empty($row['task_status_id'])){
	//if any task statuses are in the tasks_to_options table they are put here and shown as a drop down menu.
				$task_statuses[$row['task_id']][$row['task_status_id']] = $row['task_status'];
				 }
				 //find the selected task status from the available options
				foreach($tasks as $t){
					if($row['task_id']==$t['task_id']){
				$selected[$row['task_id']]=$t['task_status_id'];
					}
				}
				//add the selected task status for the drop down menu
				if(isset($selected[$row['task_id']])){
				$data[$row['task_id']]["selected"] = $selected[$row['task_id']];
				}
				//add the full list of task statuses for the drop down menu
				if(isset($task_statuses[$row['task_id']])){
				$data[$row['task_id']]["statuses"] = $task_statuses[$row['task_id']];
				}
			 }

				echo json_encode(array("success"=>true,"data"=>$data,"count"=>count($data)));
		}
	}

	public function get_tasks(){
		if ($this->input->is_ajax_request() && $this->_access) {
			 $result = $this->Records_model->get_tasks($this->input->post('urn'));
			 echo json_encode(array("success"=>true,"data"=>$result));
		}
	}
    /*save sticky note on the details page*/
    public function save_notes()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
			if($this->Records_model->save_notes($this->input->post('urn'),$this->input->post('notes'))){
                echo json_encode(array(
                    "success" => true
                ));
			} else {
				  echo json_encode(array(
                    "success" => false
                ));
			}
        } else {
            echo "Denied";
            exit;
        }
    }

    /*save contact details */
    public function save_contact()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            $this->db->where("urn", $this->input->post('urn'));
            if ($this->db->update('contacts', array(
                "notes" => $this->input->post('notes')
            ))):
                echo json_encode(array(
                    "success" => true
                ));
            endif;
        } else {
            echo "Denied";
            exit;
        }
    }

    /*add/remove record from user favorites list */
    public function set_favorites()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            //if the action is add we add it to the table using "replace" to avoud dupes
            if ($this->input->post('action') == "add") {
                $this->db->replace('favorites', array(
                    "urn" => intval($this->input->post('urn')),
                    "user_id" => $_SESSION['user_id']
                ));
                echo json_encode(array(
                    "added" => true,
                    "msg" => "Record was added to your favourites"
                ));
                exit;
            } else {
                //we delete iot form the table
                $this->db->where(array(
                    "urn" => intval($this->input->post('urn')),
                    "user_id" => $_SESSION['user_id']
                ));
                $this->db->delete("favorites");
                echo json_encode(array(
                    "removed" => true,
                    "msg" => "Record was removed from your favourites"
                ));
                exit;
            }

        } else {
            echo "Denied";
            exit;
        }
    }


    /*set/unset record as urgent */
    public function set_urgent()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            //we set it as urgent and set pending to 1
            if ($this->input->post('action') == "add") {
                $this->db->where("urn", intval($this->input->post('urn')));
                $this->db->update('records', array(
                    "urgent" => "1"
                ));
                echo json_encode(array(
                    "added" => true,
                    "msg" => "Record was set as urgent"
                ));
                exit;
            } else {
                //we unset it as urgent
                $this->db->where("urn", intval($this->input->post('urn')));
                $this->db->update('records', array(
                    "urgent" => NULL
                ));
                echo json_encode(array(
                    "removed" => true,
                    "msg" => "Record was unset as urgent"
                ));
                exit;
            }

        } else {
            echo "Denied";
            exit;
        }
    }



    public function get_triggers($outcome_id = "")
    {
        $this->db->where("outcome_id", $outcome_id);
        $outcome  = $this->db->get("outcomes")->result_array();
        $triggers = array();
        foreach ($outcome as $k => $v) {
            if (!empty($v)) {
                $triggers = $v;
            }
        }
        return $triggers;
    }

    //this will reset a record - outcomes, progress and nextcall will be set to null and the status will be set to live again
    public function reset_record()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            if ($this->Records_model->reset_record(intval($this->input->post('urn')))):
                echo json_encode(array(
                    "success" => true,
                    "msg" => "Record was reset. Please check the ownership is correct"
                ));
            else:
                echo json_encode(array(
                    "success" => false,
                    "msg" => "Record could not be reset. Contact the administrator"
                ));
            endif;
        } else {
            echo "Denied";
            exit;
        }
    }


	//unpark a record by urn
        public function unpark_record()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            if ($this->Records_model->unpark_record(intval($this->input->post('urn')))):
                echo json_encode(array(
                    "success" => true,
                    "msg" => "Record was unparked and restored to previous state"
                ));
            else:
                echo json_encode(array(
                    "success" => false,
                    "msg" => "Record could not be unparked. Contact the administrator"
                ));
            endif;
        } else {
            echo "Denied";
            exit;
        }
    }

    /*update record details */
    public function update()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            $survey_outcome    = false;
            $update_array      = $this->input->post();
            $triggers          = array();
            $survey_triggers   = array();
            $last_survey_id    = false;
            $campaign_id       = $this->Records_model->get_campaign_from_urn($update_array['urn']);
            $original_nextcall = $update_array['original_nextcall'];
			$token = md5(serialize($update_array));
            unset($update_array['original_nextcall']);
            //by default we add an entry to the history table unless the outcome has a no_history flag in the outcomes table, in which case we dont want to add an entry
            $no_history = false;
			if(isset($_SESSION['token'])){
			if($_SESSION['token']==$token){
				//if the post data is identical to the previous post then exit because its a dupe
				exit;
			}
			}
			$_SESSION['token']=$token;
            if (!$this->input->post('pending_manager')) {
                $update_array['pending_manager'] = "";
            }

            //check the outcome and execute any triggers
            if ($this->input->post('outcome_id')) {
                if (isset($_POST['outcome_reason_id']) && $_POST['outcome_reason_id'] == "0" && ($this->input->post('comments') == '')) {
					 echo json_encode(array(
                        "success" => false,
                        "msg" => "Please note the reason for this outcome"
                    ));
                    exit;
				}


                //check if an sms should be sent for this outcome	
                $sms_triggers = $this->Records_model->get_sms_triggers($campaign_id, intval($this->input->post('outcome_id')));
				//check if an email should be sent for this outcome
                $email_triggers = $this->Records_model->get_email_triggers($campaign_id, intval($this->input->post('outcome_id')));
				//check if any other function should be called
				 $function_triggers = $this->Records_model->get_function_triggers($campaign_id, intval($this->input->post('outcome_id')));
                //get the outcome triggers
                $triggers       = $this->get_triggers(intval($this->input->post('outcome_id')));
                if ($triggers["force_comment"] == "1" && trim($update_array['comments']) == "" && !isset($update_array['outcome_reason_id'])) {
                    echo json_encode(array(
                        "success" => false,
                        "msg" => "This outcome requires you to leave notes"
                    ));
                    exit;
                }
                if ($triggers["force_nextcall"] == "1"||intval($triggers["delay_hours"])) {
                    if (strtotime(to_mysql_datetime($update_array['nextcall'])) < strtotime('now +20 minutes')) {
                        echo json_encode(array(
                            "success" => false,
                            "msg" => "Next action time must be at least 20 minutes in the future"
                        ));
                        exit;
                    }
                }
                if ($update_array["pending_manager"] <> "" && trim($update_array['comments']) == "") {
                    echo json_encode(array(
                        "success" => false,
                        "msg" => "Please leave notes where further action is required"
                    ));
                    exit;
                }
				 //the survey_complete ID is 60 on this system
                if ($update_array['outcome_id'] == 60) {

                    $survey_outcome = true;

                    if ($this->Survey_model->find_survey_updates($update_array['urn'])) {
                        //if a survey complete outcome already exists in the history table for today
                        echo json_encode(array(
                            "success" => false,
                            "msg" => "Survey complete outcome has already been set today"
                        ));
                        exit;

                    }

                    $last_survey_id = $this->Survey_model->get_last_survey($update_array['urn']);
                    if (!$last_survey_id) {
                        //if a survey has not been completed
                        echo json_encode(array(
                            "success" => false,
                            "msg" => "No survey has been completed on this record today"
                        ));
                        exit;
                    }

                }
				$trigger_updates = array();
                if ($triggers["set_status"] && $update_array["pending_manager"] == "") {
                    //if the outcome triggers a status update do it now
                    $trigger_updates["record_status"] = $triggers["set_status"];
                }
				 if ($triggers["set_parked_code"]) {
                    //if the outcome triggers a status update do it now
                     $trigger_updates["parked_code"] = $triggers["set_parked_code"];
					 $trigger_updates["parked_date"] = date('Y-m-d H:i:s');
                }
                if ($triggers['set_progress'] && $update_array["pending_manager"] == "") {
                   $trigger_updates["progress_id"] = $triggers["set_progress"];
                    //if the outcome triggers a progress update do it now
                }
				if(!empty($trigger_updates)){
				$this->Records_model->update($update_array['urn'],$trigger_updates);
				}

                if (intval($triggers["delay_hours"]) > 0) {
                    if (!in_array("keep records", $_SESSION['permissions'])) {
                        //delete all owners so it can get called back by anyone (answer machines etc)
                        $this->Records_model->save_ownership($update_array['urn'], array());
                    }
					//This is now done using javascipt to change the input box when a delayed outcome is selected so we dont need to change it in the array. If we wanted to force a nextcall
                    $delay                    = $triggers['delay_hours'];
                    $update_array['nextcall'] = date('Y-m-d H:i', strtotime("+$delay hours"));
                }

                if ($triggers["no_history"] == "1") {
                    $no_history = true;
                }


            }
            if (isset($update_array['xfer_campaign'])) {
                $xfer_campaign = $update_array['xfer_campaign'];
                unset($update_array['xfer_campaign']);
            }

            $this->Records_model->update_record($update_array);
            $hist                = $update_array;
            $hist['campaign_id'] = $campaign_id;

			$new_owners = array();

            if ($this->input->post('outcome_id')) {
                //check if the outcome triggers an ownership update
                $new_owners = $this->Records_model->get_owners_for_outcome($campaign_id, $update_array['outcome_id']);

			if(count($new_owners)==0){
				//if a callback DM was previously set then keep the user on it
				if($this->input->post('keep')){
					$new_owners[]=$_SESSION['user_id'];
				}
				  //if the pending_manager or survey outcome we can add the campaign managers if they have been set
			if($update_array['pending_manager'] > 0 || $survey_outcome){
			        $new_managers = $this->Records_model->get_campaign_managers($campaign_id);
                    foreach($new_managers as $manager){
						$new_owners[] = $manager ;
					}
                    }
			}

            }
				//if the user has the keep record permission or the outcome is keeper then we keep the user on the record too
				if ($_SESSION['permissions'] == "keep records" || @$triggers['keep_record'] == "1"){
					if(!in_array($_SESSION['user_id'],$new_owners)){
					$new_owners[]=$_SESSION['user_id'];
					}
				}
                    $this->Records_model->save_ownership(intval($this->input->post('urn')), $new_owners);

            if ($survey_outcome) {
                $hist['last_survey'] = $last_survey_id;
                //get all the answer options and question trigger thresholds
                $slider_triggers     = $this->Survey_model->get_slider_triggers($last_survey_id);
                $option_triggers     = $this->Survey_model->get_option_triggers($last_survey_id);
                //then we loop through each answer in the last survey and if any scored below 6 on nps we email the managers
                $slider_answers      = $this->Survey_model->get_slider_answers($last_survey_id);
                $option_answers      = $this->Survey_model->get_option_answers($last_survey_id);
                //$this->survey_alert($last_survey_id, $survey_triggers);
                $email               = array();
                foreach ($slider_triggers as $q => $a) {
                    if ($slider_answers[$q]['answer'] <= $a && intval($slider_answers[$q]['answer']) > 0) {
                        $pending             = true;
                        $hist['progress_id'] = "1";
                        $send_email          = true;
                        $survey_triggers[]   = array(
                            "question" => $slider_answers[$q]['question'],
                            "answer" => $slider_answers[$q]['answer'],
							"notes" => $slider_answers[$q]['notes']
                        );
                    }
                }

                foreach ($option_answers as $q => $row) {
                    if (array_key_exists($row['option_id'], $option_triggers)) {
                        $pending             = true;
                        $hist['progress_id'] = "1";
                        $send_email          = true;
                        $survey_triggers[]   = array(
                            "question" => $row['question_name'],
                            "answer" => $row['option_name'],
							"notes" => $row['notes']
                        );
                    }
                }

                if (count($survey_triggers) > 0) {
                    $this->Records_model->set_pending($update_array['urn']);
                    $this->survey_alert($last_survey_id, $survey_triggers);
                }
            }


            //if a progress_id was sent we should add this to the history entry
            if ($this->input->post("progress_id")) {
                $hist['progress_id'] = $this->input->post("progress_id");
            }
            //push a description into the post array and then send it to add_history function
            $hist['description'] = "Record was updated";
            //if the outcome does not have a "no_history" flag we add the update to the history table
            if (!$no_history) {
                $id = $this->Records_model->add_history($hist);
                if (isset($xfer_campaign)) {
                    $this->Records_model->add_xfer($id, $xfer_campaign);
                }
            }
            $reached_max = $this->Records_model->check_max_dials($update_array['urn']);
			if(!empty($reached_max)){
			//the outcome was changed to maxdials so we need to recheck for any max dial functions on the campaign
			$function_triggers = $this->Records_model->get_function_triggers($campaign_id,137);
			}
            //return success to page
            $response = array(
                "success" => true,
                "msg" => "Record was updated"
            );

            if (isset($email_triggers) && count($email_triggers) > 0) {
                $response['email_trigger'] = true;
            }
			if (isset($email_triggers) && count($email_triggers) > 0) {
                $response['sms_trigger'] = true;
            }
			if (isset($function_triggers) && count($function_triggers) > 0) {
                $response['function_triggers'] = $function_triggers;
            }
            echo json_encode($response);

        } else {
            echo "Denied";
            exit;
        }
    }





    public function load_appointments()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
			session_write_close();
            $appts = $this->Records_model->get_appointments($this->input->post("urn"));
            foreach ($appts as $k => $row) {
                $appts[$k]['time'] = date('g:i a', strtotime($row['start']));
                $appts[$k]['date'] = date('jS M y', strtotime($row['start']));
            }
            //return success to page
            echo json_encode(array(
                "success" => true,
                "data" => $appts
            ));
        } else {
            echo "Denied";
            exit;
        }
    }

    public function save_appointment()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            session_write_close();
            $data = $this->input->post();
            unset($data['add1'], $data['add2'], $data['add3'], $data['county'], $data['new_postcode']);
            unset($data['access_add1'], $data['access_add2'], $data['access_add3'], $data['access_county'], $data['access_new_postcode']);


            //check the attendee
            if (empty($data['attendees'])) {
                echo json_encode(array(
                    "success" => false,
                    "msg" => "You must confirm the attendee"
                ));
                exit;
            }

            //check the address
            if (!isset($data['address']) || $data['address'] == "Other" || empty($data['address'])) {
                echo json_encode(array(
                    "success" => false,
                    "msg" => "You must confirm the address"
                ));
                exit;
            }
            //check the type
            if (empty($data['appointment_type_id'])) {
                echo json_encode(array(
                    "success" => false,
                    "msg" => "You must set appointment type"
                ));
                exit;
            }
            //check the contact
            if (empty($data['contact_id'])) {
                echo json_encode(array(
                    "success" => false,
                    "msg" => "You must set the contact"
                ));
                exit;
            }

            $address_field = explode('|', $data['address']);
            $data['address'] = $address_field[0];
            $postcode = $address_field[1];
			if(isset($address_field[2])){
			$data['address_table'] = $address_field[2];
			}
			if(isset($address_field[3])){
			$data['address_id'] = $address_field[3];	
			}
			
            $data['postcode'] = postcodeCheckFormat($postcode);
			
            if (isset($data['access_address']) && !empty($data['access_address'])) {
                if ($data['access_address'] == "Other") {
                    echo json_encode(array(
                        "success" => false,
                        "msg" => "You must confirm the access address"
                    ));
                    exit;
                }
                $access_address_field = explode('|', $data['access_address']);
                $data['access_address'] = $access_address_field[0];
                $access_postcode = $access_address_field[1];
				if(isset($access_address_field[2])){
				$data['access_address_table'] = $access_address_field[2];
				}
				if(isset($access_address_field[3])){
				$data['access_address_id'] = $access_address_field[3];	
				}
				
                $data['access_postcode'] = postcodeCheckFormat($access_postcode);
                unset($data['access_add_check']);
            }
            else {
                $data['access_address'] = "";
                $data['access_postcode'] = "";
            }

            //check the attendees
            if (!isset($data['attendees']) || empty($data['attendees'][0])) {
                echo json_encode(array(
                    "success" => false,
                    "msg" => "You must add an attendee"
                ));
                exit(0);
            }
            //Check if the attendee has a block_day between the start and the end date
            if ($this->Appointments_model->checkDayBlocked($data['attendees'][0], $data['start'], $data['end'])) {
                echo json_encode(array(
                    "success" => false,
                    "msg" => "The attendee has one or more days blocked between the start and the end dates"
                ));
                exit(0);
            }

            //TODO (Function pending to define) check if there is a slot available for this date
            //Check if the attendee has an slot available for the date selected between the start and the end date
            if ($this->Appointments_model->checkSlotAvailable($data['attendees'][0], $data['start'], $data['end'])) {
                echo json_encode(array(
                    "success" => false,
                    "msg" => "The attendee hasn't any slot available for the date selected between the start and the end dates"
                ));
                exit(0);
            }

            //check the postcode
            if ($data['postcode'] === NULL||$data['access_postcode'] === NULL) {
                echo json_encode(array(
                    "success" => false,
                    "msg" => "You must set a valid UK Postcode"
                ));
                exit;
            }

            $data['start'] = to_mysql_datetime($data['start']);
            $data['end'] = to_mysql_datetime($data['end']);

            //check the date
            if (strtotime($data['start']) < strtotime('now') || strtotime($data['end']) < strtotime('now')) {
                echo json_encode(array(
                    "success" => false,
                    "msg" => "Appointment date must be in the future"
                ));
                exit;
            }

            $state = false;
            if (empty($data['appointment_id'])) {
                $id = $this->Records_model->save_appointment($data);
                $data['appointment_id'] = $id;
                $this->Audit_model->log_appointment_insert($data);
                $state = 'inserted';
            } else {
                $this->Audit_model->log_appointment_update($data);
                $id = $this->Records_model->save_appointment($data);
                $state = 'updated';
            }
			if(!empty($data['contact_id'])){
			$data['contact_email'] = $this->db->get_where("contacts",array("contact_id"=>$data['contact_id']))->row()->email;
			}
			if(!empty($data['attendees'])){
			$data['attendee_email'] = $this->db->get_where("users",array("user_id"=>$data['attendees'][0]))->row()->user_email;
			}
            log_message('info', 'Appointment added to 121sys:' . $id . ":" . $state);
            $response = array(
                "success" => true,
                "appointment_id" => $id,
                "add_to_planner" => false,
                "state" => $state,
                "data" => $data
            );

            if (in_array("apps to planner", $_SESSION['permissions'])) {
                $response['add_to_planner'] = true;
            }
			if(!isset($data['data_id'])){
			//check if a custom data panel items needs creating for this appointment
			$data['data_id'] = $this->Records_model->create_custom_data_with_linked_appointments($id);
			$linked=false;
			$response['data']['job_id'] = $data['data_id'];
			} else {
			$this->Records_model->link_appointment_to_custom_data($data['data_id'],$id);
			$linked=true;
			$response['data']['job_id'] = $data['data_id'];
			}

            echo json_encode($response);

            $this->load->model('Locations_model');
            //set the location id on the appointment
            $this->Locations_model->set_location_id($data['postcode']);
        } else {
            echo "Denied";
            exit;
        }
    }

    public function delete_appointment()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            $data = $this->input->post();
            $this->Audit_model->log_appointment_delete($data['appointment_id']);
            $this->Records_model->delete_appointment($data);

            //return success to page
            echo json_encode(array(
                "success" => true,
                "add_to_planner"=>(in_array("apps to planner",$_SESSION['permissions'])),
                "appointment" => $this->Records_model->get_appointment($data['appointment_id'])
            ));
        } else {
            echo "Denied";
            exit;
        }
    }

    //this function is triggered during the update if the question trigger is below the answer or if an option on a multiple choise is set as a trigger. It sends the email with a list of all teh answers that triggered it. The email is sent to the recipients in the trigger_recipients table for the trigger with the survey outcome. If it does not exist then it does not get sent.
    public function survey_alert($survey, $survey_triggers)
    {
        $this->load->model('Contacts_model');
        $this->load->model('Email_model');
        $data = $this->Email_model->survey_email($survey);
        foreach ($data as $row) {
            if ($row['nps_question'] == 1) {
                $urn         = $row['urn'];
                $campaign_id = $row['campaign_id'];
                $nps         = $row['answer'];
                $contact_id  = $row['contact_id'];
                $survey_name = $row['survey_name'];
                $notes       = $row['notes'];
                $urgent      = $row['urgent'];
            }
        }
        $outcome_id   = 60; //the survey outcome_id
        $recipients   = $this->Email_model->get_recipients($campaign_id, $outcome_id);
        $contact      = $this->Contacts_model->get_contact($contact_id);
        $contact_name = $contact['general']['fullname'];
        $comments     = $this->Records_model->get_last_comment($urn);

        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);

        $this->email->from("noreply@leadcontrol.co.uk", '121 Systems');
        $this->email->subject('Survey Response');
        $this->email->to($recipients['main']);
        $this->email->cc($recipients['cc']);
		$this->email->bcc($recipients['bcc']);
        $msg = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Survey Email</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
</html><body>';
        $msg .= "<span style='font-family: Arial, Helvetica, sans-serif;font-size: 12px;'><h4>A survey was completed with one of more answers triggering an alert</h4>" . "<table width='100%' style='text-align: center;font-family: Arial, Helvetica, sans-serif; border-spacing: 0;border-collapse: collapse;'>" . "<tr>" . "<th style='border-bottom: 2px solid #ddd;'>URN</th>" . "<th style='border-bottom: 2px solid #ddd;'>Survey</th>" . "<th style='border-bottom: 2px solid #ddd;'>NPS</th>" . "<th style='border-bottom: 2px solid #ddd;'>Notes</th>" . "<th style='border-bottom: 2px solid #ddd;'>Urgent</th>" . "</tr>";
        $i       = 0;
        $bgColor = $i % 2 === 0 ? '#F3F3F3' : '#FFFFFF';
        $msg .= "<tr style='background-color: $bgColor'>" . "<td style='border-bottom: 1px solid #ddd;'>" . $urn . "</td>" . "<td style='border-bottom: 1px solid #ddd;'>" . $survey_name . "</td>" . "<td style='border-bottom: 1px solid #ddd;'>" . (!empty($nps) ? $nps : "-") . "</td>" . "<td style='border-bottom: 1px solid #ddd;'>" . (!empty($notes) ? $notes : "-") . "</td>" . "<td style='border-bottom: 1px solid #ddd;" . (!empty($urgent) ? "color:red" : "") . "'>" . (!empty($urgent) ? "Yes" : "No") . "</td>" . "</tr>";

        $msg .= "</table>";
        $msg .= "<h4>Answers that triggered this alert</h4>";
        $msg .= "<table width='100%' style='text-align: center;font-family: Arial, Helvetica, sans-serif; border-spacing: 0;border-collapse: collapse;'>" . "<tr>" . "<th style='border-bottom: 2px solid #ddd;'>Question</th>" . "<th style='border-bottom: 2px solid #ddd;'>Answer</th>" . "<th style='border-bottom: 2px solid #ddd;'>Notes</th>" . "</tr>";

        foreach ($survey_triggers as $row) {
            $msg .= "<tr style='background-color: $bgColor'>" . "<td style='border-bottom: 1px solid #ddd;'>" . $row['question'] . "</td>" . "<td style='border-bottom: 1px solid #ddd;'>" . $row['answer'] . "</td>" . "<td style='border-bottom: 1px solid #ddd;'>" . (!empty($row['notes']) ? $row['notes'] : "-") . "</td>" . "</tr>";
        }
        $msg .= "</table>";
        $msg .= "<p>Contact Name: <b>" . $contact_name . "<b>";
        //only include comments if there are any
        if (!empty($comments)) {
            $msg .= "<p>Comments<br>" . $comments . "</p>";
        }



        $msg .= "</span><p style='font-family: Arial, Helvetica, sans-serif;font-size: 12px;'>Please click the link below to view the record details<br>
					<a href='" . base_url() . "records/detail/$urn'>" . base_url() . "records/detail/$urn</a>
					</p></body></html>";

        $i++;

        $this->email->message($msg);

        //If the environment is different than production, send the email to the user (if exists)
        if ((ENVIRONMENT !== "production")) {
            if (isset($_SESSION['email']) && $_SESSION['email'] != '') {
                $this->email->to($_SESSION['email']);
                $this->email->cc("");
                $this->email->bcc("");
            } else {
                $this->email->clear(TRUE);
                return true;
            }
        }
        $this->email->send();
        //$this->firephp->log($this->email->print_debugger());
        $this->email->clear();


    }

    /**
     * Get the attachments
     */
    public function get_attachments()
    {

        if ($this->input->is_ajax_request()) {
			session_write_close();
            $record_urn = intval($this->input->post('urn'));
			$webform = intval($this->input->post('webform'));
            $limit      = (intval($this->input->post('limit'))) ? intval($this->input->post('limit')) : NULL;

            $attachments = $this->Records_model->get_attachments($record_urn, $limit, 0, $webform);

            echo json_encode(array(
                "success" => true,
                "data" => $attachments
            ));
        }
    }

    /**
     * Upload new attachments
     */
    public function upload_attach()
    {
        $options                   = array();
        $options['upload_dir']     = dirname(md5($_SERVER['SCRIPT_FILENAME'])) . '/upload/attachments/';
        $options['upload_url']     = base_url() . 'upload/attachments/';
        $options['image_versions'] = array();
        $upload_handler            = new Upload($options, true);
    }

    /**
     * Get the upload attachment folder path
     */
    public function get_attachment_file_path()
    {
        $file     = $this->input->post('file');
        $path     = base_url() . 'upload/attachments/';
        $fullpath = $path . $file;

        $result = array(
            "path" => $fullpath
        );

        $json = json_encode($result);
        echo $json;
    }


    /**
     * Save the upload attachment folder path
     */
    public function save_attachment()
    {
        $data = $this->input->post();

        if ($this->input->is_ajax_request()) {
            $data['date']    = date('Y-m-d H:i:s');
            $data['user_id'] = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : NULL;

            $attachment_id = $this->Records_model->save_attachment($data);

            //return success to page
            echo json_encode(array(
                "success" => true,
                "attachment_id" => $attachment_id
            ));
        } else {
            echo json_encode(array(
                "success" => false
            ));
        }
    }

    public function delete_attachment()
    {
        $attachment = $this->Records_model->get_attachment_by_id($this->input->post('attachment_id'));
        if ($this->input->is_ajax_request()) {
            $this->Records_model->delete_attachment($this->input->post('attachment_id'));

            //Delete the file from the server folder
			if(!file_exists(strstr('./' . $attachment['path'], 'upload'))){
				 echo json_encode(array(
                    "success" => true
                ));
				exit;
			}
            if (unlink(strstr('./' . $attachment['path'], 'upload'))) {
                //return success to page
                echo json_encode(array(
                    "success" => true
                ));
            } else {
                echo json_encode(array(
                    "success" => false
                ));
            }
        } else {
            echo json_encode(array(
                "success" => false
            ));
        }
    }

    public function check_attachment_version() {
        if ($this->input->is_ajax_request()) {
            $name = $this->input->post('name');
            $urn = $this->input->post('urn');

            $last_attachment_version = $this->Records_model->get_last_attachment_version($name, $urn);

            //return success to page
            echo json_encode(array(
                "success" => !empty($last_attachment_version),
                "last_attachment_version" => (isset($last_attachment_version['version'])?$last_attachment_version['version']:''),
                "last_attachment_version_id" => (isset($last_attachment_version['attachment_id'])?$last_attachment_version['attachment_id']:'')
            ));
        } else {
            echo json_encode(array(
                "success" => false
            ));
        }
    }

	public function update_custom_data_field(){
	$data_id = $this->input->post("data_id");
	$field_id = $this->input->post("field_id");
	$value = $this->input->post("value");
	$query = "replace into custom_panel_values set data_id = '$data_id',field_id = '$field_id', `value`= '$value'";
	$this->db->query($query);
	echo json_encode(array("success"=>true));
	}

    public function save_record_planner() {
        if ($this->input->is_ajax_request()) {
            $record_planner = $this->input->post();
            $record_planner['user_id'] = $_SESSION['user_id'];

            $record_planner['start_date'] = to_mysql_datetime($record_planner['start_date']);

            //Save the record planner
            $result = $this->Records_model->save_record_planner($record_planner);

            echo json_encode(array(
                "success" => ($result),
                "msg" => ($result?"Record planner save successfully!":"ERROR: Record planner NOT save successfully!!")
            ));
        }
    }

    /**
     * Update icon
     */
    public function set_icon()
    {
        if ($this->input->is_ajax_request()) {
            $record = $this->input->post();


            $record['map_icon'] = getFontAwesomeIconFromAlias($record['map_icon']);

            $record['map_icon'] = (strlen($record['map_icon']) > 0 ?$record['map_icon']: NULL);
            $result = $this->Records_model->set_icon($record);

            echo json_encode(array(
                "success" => ($result),
                "msg" => ($result?"Icon was updated!":"ERROR: Icon could not be set. Please contact support@121customerinsight.co.uk!!")
            ));
        }
    }


    /**
     *
     */
    public function export_data() {
        if ($this->input->is_ajax_request()) {

            //Get the urn list from the session
            $urnList = $_SESSION['navigation'];
            $urnsShown = array_pop($urnList);

            $visible_columns = $this->Datatables_model->get_visible_columns(1);
            //$this->firephp->log($visible_columns);

            echo json_encode(array(
                "success" => true,
                "msg" => ""
            ));
        }
    }
}

?>