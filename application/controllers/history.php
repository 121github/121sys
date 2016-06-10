<?php
require('upload.php');

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class History extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        user_auth_check();
        $this->_campaigns = campaign_access_dropdown();
        

        $this->load->model('User_model');
		$this->load->model('Filter_model');
        $this->load->model('History_model');
        $this->load->model('Survey_model');
        $this->load->model('Form_model');
		$this->load->model('Audit_model');
        $this->load->model('Appointments_model');
        $this->load->model('Datatables_model');
        $this->_access = $this->User_model->campaign_access_check($this->input->post('urn'), true);
    }


 public function view()
    {
        //this array contains data for the visible columns in the table on the view page
		$this->load->model('Datatables_model');
		$visible_columns = $this->Datatables_model->get_visible_columns(2);
		if(!$visible_columns){
		 $this->load->model('Admin_model');
		$this->Datatables_model->set_default_columns($_SESSION['user_id']);
		$visible_columns = $this->Datatables_model->get_visible_columns(2);
		}
		$_SESSION['col_order'] = $this->Datatables_model->selected_columns(false,2);
		
		$title = "History";
		$global_filter = $this->Filter_model->build_filter_options();
        $data = array(
		'global_filter' => $global_filter,
            'campaign_access' => $this->_campaigns,
            'page' => 'list_records',
            'title' => $title,
            'columns' => $visible_columns,
			'submenu' => array("file"=>'history_list.php',"title"=>$title),
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
        $this->template->load('default', 'records/list_history.php', $data);

    }
	
		    public function mapview()
    {
        //this array contains data for the visible columns in the table on the view page
		$this->load->model('Datatables_model');
		$visible_columns = $this->Datatables_model->get_visible_columns(2);
		if(!$visible_columns){
		 $this->load->model('Admin_model');
		$this->Datatables_model->set_default_columns($_SESSION['user_id']);
		$visible_columns = $this->Datatables_model->get_visible_columns(2);
		}
		$_SESSION['col_order'] = $this->Datatables_model->selected_columns(false,2);
		$global_filter = $this->Filter_model->build_filter_options();
		$title = "History List";
        $data = array(
		'global_filter' => $global_filter,
            'campaign_access' => $this->_campaigns,
            'page' => 'list_history',
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
        $this->template->load('default', 'records/list_history_and_map.php', $data);

    }
	
	public function process_view()
    {
        if ($this->input->is_ajax_request()) {
			/* debug loading times */
			$this->benchmark->mark('code_start');
            $options = $this->input->post();
			$this->load->model('Datatables_model');
			$visible_columns = $this->Datatables_model->get_visible_columns(2);
			$this->firephp->log($visible_columns);
			$options['visible_columns'] = $visible_columns;
			//check the options
			foreach($options['columns'] as $k=>$column){
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
            $records = $this->History_model->get_history($options);

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
	
	
}