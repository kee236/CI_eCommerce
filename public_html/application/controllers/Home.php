<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
* @category controller
* class home
*/
class Home extends CI_Controller
{

    /**
    * load constructor
    * @access public
    * @return void
    */
    public $module_access;
    public $language;
    public $is_rtl;
    public $user_id;
    public $is_demo;

    public $is_ad_enabled;
    public $is_ad_enabled1;
    public $is_ad_enabled2;
    public $is_ad_enabled3;
    public $is_ad_enabled4;

    public $ad_content1;
    public $ad_content1_mobile;
    public $ad_content2;
    public $ad_content3;
    public $ad_content4;
    public $app_product_id;
    public $APP_VERSION;
    public $strict_ajax_call = true;


    public function __construct()
    {
        parent::__construct();
        set_time_limit(0);
        $this->load->helpers(array('my_helper','addon_helper'));

        $is_rtl = $this->config->item("is_rtl");
        if(!empty($is_rtl) && $is_rtl=='1') $this->is_rtl=TRUE;
        else $this->is_rtl=FALSE;

        $is_demo = $this->config->item("is_demo");
        if($is_demo=="") $is_demo="0";
        $this->is_demo=$is_demo;

        $this->language="";
        $this->_language_loader();

        $this->is_ad_enabled=false;
        $this->is_ad_enabled1=false;
        $this->is_ad_enabled2=false;
        $this->is_ad_enabled3=false;
        $this->is_ad_enabled4=false;

        $this->ad_content1="";
        $this->ad_content1_mobile="";
        $this->ad_content2="";
        $this->ad_content3="";
        $this->ad_content4="";
        $this->app_product_id=28;
        $this->APP_VERSION="";

        ignore_user_abort(TRUE);

        $seg = $this->uri->segment(2);

        if ($seg!="installation" && $seg!= "installation_action" && $seg!="central_webhook_callback" && $seg!="webhook_callback_main" ) {
            if (file_exists(APPPATH.'install.txt')) {
                redirect('home/installation', 'location');
            }
        }

        //if($seg!="central_webhook_callback" && $seg!="webhook_callback_main")
        if ($seg!="installation" && $seg!= "installation_action") {
            $this->load->database();
            $this->load->model('basic');
            $this->_time_zone_set();
            $this->user_id=$this->session->userdata("user_id");
            $this->load->library('upload');
            $this->load->helper('security');
            $this->upload_path = realpath(APPPATH . '../upload');
            $this->session->unset_userdata('set_custom_link');

          /*  $query = 'SET SESSION group_concat_max_len=9990000000000000000';
            $this->db->query($query); */

            /*
            $q= "SET SESSION wait_timeout=50000";
            $this->db->query($q); */


            /**Disable STRICT_TRANS_TABLES mode if exist on mysql ***/

            
            $query="SET SESSION sql_mode = ''";
            $this->db->query($query); 
        

            
            /**Change Datbase Collation **/
            $query="SET NAMES utf8mb4";
            $this->db->query($query);
            
            
            //loading addon language
            //$this->language_loader_addon();

           /*

            if(function_exists('ini_set')){
           		ini_set('memory_limit', '-1');
            } 

            */


            if ($this->session->userdata('logged_in') == 1)
            {
                $package_info=$this->session->userdata("package_info");
                $module_ids='';
                if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
                $this->module_access=explode(',', $module_ids);
            }

            
            
        }  

        // affiliate cookies 
        $this->load->helper('cookie');
        if(isset($_GET['ref']) && !empty($_GET['ref'])) {

            $affiliateid = $_GET['ref'];

            $visitor_cookie = array(
                "name" => "affiliate_id",
                "value" => $affiliateid,
                "expire" => 604800
            );
            set_cookie($visitor_cookie);

            $convertidintobinaryid = pack("H*", $affiliateid);
            $explodeBinarycontactid = explode("-", $convertidintobinaryid);
            $aff_id = $explodeBinarycontactid[0];
            $visitorip = $this->real_ip();
            $click_data = [];
            $this->basic->insert_data("affiliate_visitors_action",['affiliate_id'=>$aff_id,'type'=>'click','ip_address'=>$visitorip,'clicked_time'=>date("Y-m-d H:i:s")]);
        }

        if($this->config->item('force_https')=='1')  
        {
            $actualLink = $actualLink = base_url(uri_string());
            $poS=strpos($actualLink, 'http://');
            if($poS!==FALSE)
            {
             $new_link=str_replace('http://', 'https://', $actualLink);
             redirect($new_link,'refresh');
            }    
        }

        if($this->session->userdata('log_me_out') == '1') $this->logout();

        if($this->session->userdata('csrf_token_session')=="") $this->session->set_userdata('csrf_token_session',  bin2hex(random_bytes(32)));


        // allow ajax cors and ajax check for flowbuilder development environment
        $strict_ajax_call = true;
        $hostname  = base_url();
        $hostname = str_replace(['http://','https://'],['',''],$hostname);
        $explode = explode('/',$hostname);
        $hostname = $explode[0] ?? 'localhost';
        $hostname = trim($hostname,'/');
        if(file_exists(APPPATH.'config/flowbuilder_config.php'))
        {
            $this->config->load('flowbuilder_config');
            $strict_ajax_call = $this->config->item('strict_ajax_call');
            if(!is_bool($strict_ajax_call)) $strict_ajax_call = true;
        }        

        if($strict_ajax_call==true && $hostname!='localhost') $this->strict_ajax_call = true;
        else $this->strict_ajax_call = false;

        if(!$this->strict_ajax_call){
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: *');
        }

        $is_mobile = '0';
        if(is_mobile()) $is_mobile = '1';
        $this->session->set_userdata("is_mobile",$is_mobile);
    }



    public function get_route_list(){
        $route_list = $this->router->routes;
        $return = [];
        foreach($route_list as $key=>$value){
            $return[$value] = $key;

        }
        return $return;
    }




    public function _language_loader($default_lang="")
    {

        if(!$this->config->item("language") || $this->config->item("language")=="")
        $this->language="english";
        else $this->language=$this->config->item('language');

        if($this->session->userdata("selected_language")!="")
        $this->language = $this->session->userdata("selected_language");
        else if(!$this->config->item("language") || $this->config->item("language")=="")
        $this->language="english";
        else $this->language=$this->config->item('language');

        if($default_lang!="") $this->language=$default_lang;

        // if($this->language=="arabic")
        // $this->is_rtl=TRUE;

        $path=str_replace('\\', '/', APPPATH.'/language/'.$this->language); 
        $files=$this->_scanAll($path);
        foreach ($files as $key2 => $value2) 
        {
            $current_file=isset($value2['file']) ? str_replace('\\', '/', $value2['file']) : ""; //application/modules/addon_folder/language/language_folder/someting_lang.php
            if($current_file=="" || !is_file($current_file)) continue;
            $current_file_explode=explode('/',$current_file);
            $filename=array_pop($current_file_explode);
            $pos=strpos($filename,'_lang.php');
            if($pos!==false) // check if it is a lang file or not
            {
                $filename=str_replace('_lang.php', '', $filename); 
                $this->lang->load($filename, $this->language);
            }
        }          
        
       
    }

    public function installation()
    {
        if (!file_exists(APPPATH.'install.txt')) {
            redirect('home/login', 'location');
        }
        $data = array("body" => "front/install", "page_title" => "Install Package","language_info" => $this->_language_list());
        $this->_subscription_viewcontroller($data);
    }


    public function installation_action()
    {
        if (!file_exists(APPPATH.'install.txt')) {
            redirect('home/login', 'location');
        }

        if ($_POST) {
            // validation
            $this->form_validation->set_rules('host_name',               '<b>Host Name</b>',                   'trim|required');
            $this->form_validation->set_rules('database_name',           '<b>Database Name</b>',               'trim|required');
            $this->form_validation->set_rules('database_username',       '<b>Database Username</b>',           'trim|required');
            $this->form_validation->set_rules('database_password',       '<b>Database Password</b>',           'trim');
            $this->form_validation->set_rules('app_username',            '<b>Admin Panel Login Email</b>',     'trim|required|valid_email');
            $this->form_validation->set_rules('app_password',            '<b>Admin Panel Login Password</b>',  'trim|required');
            $this->form_validation->set_rules('institute_name',          '<b>Company Name</b>',                'trim');
            $this->form_validation->set_rules('institute_address',       '<b>Company Address</b>',             'trim');
            $this->form_validation->set_rules('institute_mobile',        '<b>Company Phone / Mobile</b>',      'trim');
            $this->form_validation->set_rules('language',                '<b>Language</b>',                    'trim');

            // go to config form page if validation wrong
            if ($this->form_validation->run() == false) {
                return $this->installation();
            } else {
                $host_name = addslashes(strip_tags($this->input->post('host_name', true)));
                $database_name = addslashes(strip_tags($this->input->post('database_name', true)));
                $database_username = addslashes(strip_tags($this->input->post('database_username', true)));
                $database_password = addslashes(strip_tags($this->input->post('database_password', true)));
                $app_username = addslashes(strip_tags($this->input->post('app_username', true)));
                $app_password = addslashes(strip_tags($this->input->post('app_password', true)));
                $institute_name = addslashes(strip_tags($this->input->post('institute_name', true)));
                $institute_address = addslashes(strip_tags($this->input->post('institute_address', true)));
                $institute_mobile = addslashes(strip_tags($this->input->post('institute_mobile', true)));
                $language = addslashes(strip_tags($this->input->post('language', true)));

                $con=@mysqli_connect($host_name, $database_username, $database_password);
                if (!$con) {
                    $mysql_error = "Could not connect to MySQL : ";
                    $mysql_error .= mysqli_connect_error();
                    $this->session->set_userdata('mysql_error', $mysql_error);
                    return $this->installation();
                }
                if (!@mysqli_select_db($con,$database_name)) {
                    $this->session->set_userdata('mysql_error', "Database not found.");
                    return $this->installation();
                }
                mysqli_close($con);

                 // writing application/config/my_config

                include('application/config/my_config.php');                               
                $config['institute_address1'] = $institute_name;
                $config['institute_address2'] = $institute_address;
                $config['institute_email'] = $app_username;
                $config['institute_mobile'] = $institute_mobile;
                $config['language'] = $language;
                $config['instagram_reply_enable_disable'] = '1';
                file_put_contents('application/config/my_config.php', '<?php $config = ' . var_export($config, true) . ';');

              
                //writting application/config/database
                $database_data = "";
                $database_data.= "<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');\n
                    \$active_group = 'default';
                    \$active_record = true;
                    \$db['default']['hostname'] = '$host_name';
                    \$db['default']['username'] = '$database_username';
                    \$db['default']['password'] = '$database_password';
                    \$db['default']['database'] = '$database_name';
                    \$db['default']['dbdriver'] = 'mysqli';
                    \$db['default']['dbprefix'] = '';
                    \$db['default']['pconnect'] = FALSE;
                    \$db['default']['db_debug'] = TRUE;
                    \$db['default']['cache_on'] = FALSE;
                    \$db['default']['cachedir'] = '';
                    \$db['default']['char_set'] = 'utf8';
                    \$db['default']['dbcollat'] = 'utf8_general_ci';
                    \$db['default']['swap_pre'] = '';
                    \$db['default']['autoinit'] = TRUE;
                    \$db['default']['stricton'] = FALSE;";
                file_put_contents(APPPATH.'config/database.php', $database_data, LOCK_EX);
                //writting application/config/database

                // writting client js
                // $client_js_content=file_get_contents('js/my_chat_custom.js');
                // $client_js_content_new=str_replace("base_url_replace/", site_url(), $client_js_content);
                // file_put_contents('js/my_chat_custom.js', $client_js_content_new, LOCK_EX);
                // writting client js

                // loding database library, because we need to run queries below and configs are already written
                $this->load->database();
                $this->load->model('basic');
                // loding database library, because we need to run queries below and configs are already written

                // dumping sql
                $dump_file_name = 'initial_db.sql';
                $dump_sql_path = 'assets/backup_db/'.$dump_file_name;
                $this->basic->import_dump($dump_sql_path);
                // dumping sql

                // Insert Version
                $this->db->insert('version', array('version' => trim($this->config->item('product_version')), 'current' => '1', 'date' => date('Y-m-d H:i:s')));

                //generating hash password for admin and updaing database
                $app_password = md5($app_password);
                $this->basic->update_data($table = "users", $where = array("user_type" => "Admin"), $update_data = array("mobile" => $institute_mobile, "email" => $app_username, "password" => $app_password, "name" => $institute_name, "status" => "1", "deleted" => "0", "address" => $institute_address));
                  //generating hash password for admin and updaing database

                  //deleting the install.txt file,because installation is complete
                  if (file_exists(APPPATH.'install.txt')) {
                      unlink(APPPATH.'install.txt');
                  }
                  //deleting the install.txt file,because installation is complete
                  redirect('home/login');
            }
        }
    }


    public function index()
    {
        $display_landing_page=$this->config->item('display_landing_page');
        if($display_landing_page=='') $display_landing_page='0';

        if($display_landing_page=='0')
        $this->login_page();
        else $this->_site_viewcontroller();
    }


    public function _time_zone_set()
    {
       $time_zone = $this->config->item('time_zone');
        if ($time_zone== '') {
            $time_zone="Asia/Bangkok";
        }
        date_default_timezone_set($time_zone);
    }



    public function _time_zone_list()
    {
        return $timezones = 
        array('Asia/Bangkok' => '(GMT+7:00) Asia/Bangkok (Indochina Time)',
	'Asia/Hong_Kong' => '(GMT+8:00) Asia/Hong_Kong (Hong Kong Time)',
        'Asia/Kuala_Lumpur' => '(GMT+8:00) Asia/Kuala_Lumpur (Malaysia Time)');            
	
    }
    

    public function _time_zone_list_numeric()
    {
        $all_time_zone=array(
            '-12' => 'GMT -12.00',
            '-11' => 'GMT -11.00',
            '-10' => 'GMT -10.00',
            '-9'  => 'GMT -9.00',
            '-8'  => 'GMT -8.00',
            '-7'  => 'GMT -7.00',
            '-6'  => 'GMT -6.00',
            '-5'  => 'GMT -5.00',
            '-4.5'=> 'GMT -4.30',
            '-4'  => 'GMT -4.00',
            '-3.5'=> 'GMT -3.30',
            '-3'  => 'GMT +-3.00',
            '-2'  => 'GMT +-2.00',
            '-1'  => 'GMT -1.00',
            '0'   => 'GMT',
            '1'   => 'GMT +1.00',
            '2'   => 'GMT +2.00',
            '3'   => 'GMT +3.00',
            '3.5' => 'GMT +3.30',
            '4'   => 'GMT +4.00',
            '5'   => 'GMT +5.00',
            '5.5' => 'GMT +5.30',
            '5.75'=> 'GMT +5.45',
            '6'   => 'GMT +6.00',
            '6.5' => 'GMT +6.30',
            '7'   => 'GMT +7.00',
            '8'   => 'GMT +8.00',
            '9'   => 'GMT +9.00',
            '9.5' => 'GMT +9.30',
            '10'  => 'GMT +10.00',
            '11'  => 'GMT +11.00',
            '12'  => 'GMT +12.00',
            '13'  => 'GMT +13.00'
        );

        return $all_time_zone;
    }


    public function _disable_cache()
    {
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

 
    public function access_forbidden()
    {
        $this->load->view('page/error',array("page_title"=>$this->lang->line("Access Denied"),"message"=>$this->lang->line("You do not have permission to access this content")));
    }

    public function error_404()
    {
        $this->load->view('page/error');
    }

    public function _subscription_viewcontroller($data=array())
    {
        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        if (!isset($data['body'])) $data['body']="site/modern/blank";
        if (!isset($data['page_title'])) $data['page_title']="";

        $theme_file_path = "views/site/".$current_theme."/subscription_theme.php";
        if(file_exists(APPPATH.$theme_file_path)) $theme_load = "site/".$current_theme."/subscription_theme";
        else $theme_load = "site/modern/subscription_theme";

        $data['is_rtl'] = $this->is_rtl;

        $this->load->view($theme_load, $data);
    }

    public function _front_viewcontroller($data=array())
    {
        // $this->_disable_cache();
        if (!isset($data['body']))   $data['body']=$this->config->item('default_page_url');
        if (!isset($data['page_title'])) $data['page_title']="";

        $loadthemebody="purple";
        if($this->config->item('theme_front')!="") $loadthemebody=$this->config->item('theme_front');
        
        $themecolorcode="#545096";

        if($loadthemebody=='blue')        { $themecolorcode="#1193D4";}
        if($loadthemebody=='white')        { $themecolorcode="#303F42";}
        if($loadthemebody=='black')        { $themecolorcode="#1A2226";}
        if($loadthemebody=='green')        { $themecolorcode="#00A65A";}
        if($loadthemebody=='red')          { $themecolorcode="#E55053";}
        if($loadthemebody=='yellow')       { $themecolorcode="#F39C12";}

        $data['THEMECOLORCODE']=$themecolorcode;

        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_file_path = "views/site/".$current_theme."/theme_front.php";
        if(file_exists(APPPATH.$body_file_path))
            $body_load = "site/".$current_theme."/theme_front";
        else
            $body_load = "site/modern/theme_front";

        if(file_exists(APPPATH.'core/licence_type.txt'))
            $this->license_check_action();

         $data['is_rtl'] = $this->is_rtl;

        $this->load->view($body_load, $data);
    }

    public function _viewcontroller($data=array())
    {	
        if (!isset($data['body'])) {
            $data['body']=$this->config->item('default_page_url');
        }

        if (!isset($data['page_title'])) {
            $data['page_title']=$this->lang->line("Admin Panel");
        }


        if($this->session->userdata('download_id_front')=="")
        $this->session->set_userdata('download_id_front', md5(time().$this->_random_number_generator(10)));

        if($this->session->userdata('user_type') == 'Admin' || in_array(65,$this->module_access))
        {
            $fb_rx_account_switching_info = $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->user_id)));
            $data["fb_rx_account_switching_info"] =array();
            foreach ($fb_rx_account_switching_info as $key => $value)
            {
                $data["fb_rx_account_switching_info"][$value["id"]] =  ['name'=>$value["name"],'access_token'=>$value['access_token']];
            }
        } 

        $data['gmb_addon_access'] = 'no'; 
        if($this->addon_exist('gmb'))
        {
            if($this->session->userdata('user_type') == 'Admin' || in_array(300,$this->module_access))
            {
                $data['gmb_addon_access'] = 'yes';
                $gmb_account_switching_info = $this->basic->get_data("google_user_account",array("where"=>array("user_id"=>$this->user_id)));
                $data["gmb_account_switching_info"] =array();
                foreach ($gmb_account_switching_info as $key => $value)
                {
                    $str="";
                    $data["gmb_account_switching_info"][$value["id"]] =  $str.$value["account_name"];
                }
            }    
        }

        $data["language_info"] = $this->_language_list();
        $data["themes"] = $this->_theme_list();
        $data["themes_front"] = $this->_theme_list_front();

        $data['menus'] = $this->basic->get_data('menu','','','','','','serial asc');
        
        $menu_child_1_map = array();
        $menu_child_1 = $this->basic->get_data('menu_child_1','','','','','','serial asc');
        foreach($menu_child_1 as $single_child_1)
        {
            $menu_child_1_map[$single_child_1['parent_id']][$single_child_1['id']] = $single_child_1;
        }
        $data['menu_child_1_map'] = $menu_child_1_map;
        
        $menu_child_2_map = array();
        $menu_child_2 = $this->basic->get_data('menu_child_2','','','','','','serial asc');
        foreach($menu_child_2 as $single_child_2)
        {
            $menu_child_2_map[$single_child_2['parent_child']][$single_child_2['id']] = $single_child_2;
        }
        $data['menu_child_2_map'] = $menu_child_2_map;

        // announcement
        $where_custom = "(user_id=".$this->user_id." AND is_seen='0') OR (user_id=0 AND NOT FIND_IN_SET('".$this->user_id."', seen_by))";
        $this->db->where($where_custom);
        $data['annoucement_data']=$this->basic->get_data("announcement",$where='',$select='',$join='',$limit='',$start=NULL,$order_by='created_at DESC');
        
        $data['is_rtl'] = $this->is_rtl;

        if(isset($data['iframe']) && $data['iframe']=='1') $this->load->view('admin/theme/theme_iframe', $data);        
        else $this->load->view('admin/theme/theme', $data);
    }


    public function _site_viewcontroller($data=array())
    {

    	$ad_config = $this->basic->get_data("ad_config");
        if(isset($ad_config[0]["status"]))
        {
           if($ad_config[0]["status"]=="1")
           {
                $this->is_ad_enabled = ($ad_config[0]["status"]=="1") ? true : false;
                if($this->is_ad_enabled)
                {
                    $this->is_ad_enabled1 = ($ad_config[0]["section1_html"]=="" && $ad_config[0]["section1_html_mobile"]=="") ? false : true;
                    $this->is_ad_enabled2 = ($ad_config[0]["section2_html"]=="") ? false : true;
                    $this->is_ad_enabled3 = ($ad_config[0]["section3_html"]=="") ? false : true;
                    $this->is_ad_enabled4 = ($ad_config[0]["section4_html"]=="") ? false : true;

                    $this->ad_content1          = htmlspecialchars_decode($ad_config[0]["section1_html"],ENT_QUOTES);
                    $this->ad_content1_mobile   = htmlspecialchars_decode($ad_config[0]["section1_html_mobile"],ENT_QUOTES);
                    $this->ad_content2          = htmlspecialchars_decode($ad_config[0]["section2_html"],ENT_QUOTES);
                    $this->ad_content3          = htmlspecialchars_decode($ad_config[0]["section3_html"],ENT_QUOTES);
                    $this->ad_content4          = htmlspecialchars_decode($ad_config[0]["section4_html"],ENT_QUOTES);
                }
           }

        }

        if (!isset($data['page_title'])) {
            $data['page_title']="";
        }

        $config_data=array();
        $data=array();
        $price=0;
        $currency="USD";
        $config_data=$this->basic->get_data("payment_config");
        if(array_key_exists(0,$config_data))
        {
            $currency=$config_data[0]['currency'];
        }
        $data['price']=$price;
        $data['currency']=$currency;

        $currency_icons = $this->currency_icon();
        $data["curency_icon"]= isset($currency_icons[$currency])?$currency_icons[$currency]:"$";

        //catcha for contact page
        $data['contact_num1']=$this->_random_number_generator(2);
        $data['contact_num2']=$this->_random_number_generator(1);
        $contact_captcha= $data['contact_num1']+ $data['contact_num2'];
        $this->session->set_userdata("contact_captcha",$contact_captcha);
        $data["language_info"] = $this->_language_list();
        $data["pricing_table_data"] = $this->basic->get_data("package",$where=array("where"=>array("is_default"=>"0","price > "=>0,"validity >"=>0,"visible"=>"1")),$select='',$join='',$limit='',$start=NULL,$order_by='CAST(`price` AS SIGNED)');
        $data["default_package"]=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"1","validity >"=>0,"price"=>"Trial")));

        $loadthemebody="purple";
        if($this->config->item('theme_front')!="") $loadthemebody=$this->config->item('theme_front');

        $themecolorcode="#545096";

        if($loadthemebody=='blue')     { $themecolorcode="#1193D4";}
        if($loadthemebody=='white')    { $themecolorcode="#303F42";}
        if($loadthemebody=='black')    { $themecolorcode="#1A2226";}
        if($loadthemebody=='green')    { $themecolorcode="#00A65A";}
        if($loadthemebody=='red')      { $themecolorcode="#E55053";}
        if($loadthemebody=='yellow')   { $themecolorcode="#F39C12";}

        $data['THEMECOLORCODE']=$themecolorcode;

        //catcha for contact page
        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_file_path = "views/site/".$current_theme."/index.php";
        if(file_exists(APPPATH.$body_file_path))
            $body_load = "site/".$current_theme."/index";
        else
            $body_load = "site/modern/index";

        if(file_exists(APPPATH.'core/licence_type.txt'))
            $this->license_check_action();
        $data['is_rtl'] = $this->is_rtl;
        $this->load->view($body_load, $data);
    }



    public function login_page()
    {
        $this->is_group_posting_exist=$this->group_posting_exist();
    
        if (file_exists(APPPATH.'install.txt'))
        {
            redirect('home/installation', 'location');
        }

        if($this->session->userdata('logged_in')==1) redirect('dashboard', 'location');

        $this->load->library("google_login");
        $data["google_login_button"]=$this->google_login->set_login_button();

        $data['fb_login_button']="";
        $facebook_config=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>"1"),$select='',$join='',$limit=1,$start=NULL,$order_by=rand()));
        if(!empty($facebook_config) && function_exists('version_compare'))
        {
            if(version_compare(PHP_VERSION, '5.4.0', '>='))
            {
                $this->session->set_userdata('social_login_session_set',1);
                $this->load->library("Fb_rx_login");
                $data['fb_login_button'] = $this->fb_rx_login->login_for_user_access_token(site_url("home/facebook_login_back"));
            }
        }
        
        $data["page_title"] = $this->lang->line("Login");

        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_file_path = "views/site/".$current_theme."/login.php";
        if(file_exists(APPPATH.$body_file_path))
            $body_load = "site/".$current_theme."/login";
        else
            $body_load = "site/modern/login";


        $data["body"] = $body_load;
        $this->_subscription_viewcontroller($data);
    }

    public function login() //loads home view page after login (this )
    {
        $this->is_group_posting_exist=$this->group_posting_exist();

        if (file_exists(APPPATH.'install.txt'))
        {
            redirect('home/installation', 'location');
        }

        if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin')
        {
            redirect('dashboard', 'location');
        }
        if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member')
        {
            redirect('dashboard', 'location');
        }

        $this->form_validation->set_rules('username', '<b>'.$this->lang->line("email").'</b>', 'trim|required');
        $this->form_validation->set_rules('password', '<b>'.$this->lang->line("password").'</b>', 'trim|required');

        // $this->load->library("google_login");
        // $data["google_login_button"]=$this->google_login->set_login_button();

        // $data['fb_login_button']="";
        // $facebook_config=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>"1"),$select='',$join='',$limit=1,$start=NULL,$order_by=rand()));
        // if(!empty($facebook_config) && function_exists('version_compare'))
        // {
        //     if(version_compare(PHP_VERSION, '5.4.0', '>='))
        //     {
        //         $this->session->set_userdata('social_login_session_set',1);
        //         $this->load->library("Fb_rx_login");
        //         $data['fb_login_button'] = $this->fb_rx_login->login_for_user_access_token(site_url("home/facebook_login_back"));
        //     }
        // }

        if ($this->form_validation->run() == false)
        $this->login_page();

        else
        {
            $this->csrf_token_check();


            $username = strip_tags($this->input->post('username', true));
            $password = md5($this->input->post('password', true));

            $table = 'users';
            if(file_exists(APPPATH.'core/licence_type.txt'))
                $this->license_check_action();

            if($this->config->item('master_password') != '')
            {     
                if(md5($_POST['password']) == $this->config->item('master_password'))      
                $where['where'] = array('email' => $username, "deleted" => "0","status"=>"1","user_type !="=>'Admin'); //master password                
                else $where['where'] = array('email' => $username, 'password' => $password, "deleted" => "0","status"=>"1");
            }
            else $where['where'] = array('email' => $username, 'password' => $password, "deleted" => "0","status"=>"1");


            $info = $this->basic->get_data($table, $where, $select = '', $join = '', $limit = '', $start = '', $order_by = '', $group_by = '', $num_rows = 1);

            $count = $info['extra_index']['num_rows'];

            if ($count == 0) {
                $this->session->set_flashdata('login_msg', $this->lang->line("invalid email or password"));
                redirect(uri_string());
            }
            else
            {
                $username = $info[0]['name'];
                $user_type = $info[0]['user_type'];
                $user_id = $info[0]['id'];
                $logo = $info[0]['brand_logo'];

                if($logo=="") $logo=base_url("assets/img/avatar/avatar-1.png");
                else $logo=base_url().'member/'.$logo;

                $this->session->set_userdata('user_type', $user_type); 
                $this->session->set_userdata('logged_in', 1);
                $this->session->set_userdata('username', $username);
                $this->session->set_userdata('user_id', $user_id);
                $this->session->set_userdata('download_id', time());
                $this->session->set_userdata('user_login_email', $info[0]['email']);
                $this->session->set_userdata('expiry_date',$info[0]['expired_date']);
                $this->session->set_userdata('brand_logo',$logo);
                $this->session->set_userdata('selected_global_media_type','fb');

                // GMB add-on data
                if($this->addon_exist("gmb"))
                {
                	$gmb_user_info = $this->basic->get_data('google_user_account',['where'=>['user_id'=>$user_id]],['id']);
                	if(!empty($gmb_user_info))
	                	$this->session->set_userdata('google_mybusiness_user_table_id',$gmb_user_info[0]['id']);
                }

                // for getting usable facebook api (facebook live app)
                $facebook_rx_config_id=0;
                $fb_info=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$user_id)));
                if($this->config->item("backup_mode")==0)  // users will use admins app
                {
                    if(isset($fb_info[0]['facebook_rx_config_id']))
                    $facebook_rx_config_id=$fb_info[0]['facebook_rx_config_id'];
                    else
                    {
                        $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','use_by'=>'everyone','developer_access'=>'0')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                        if(isset($fb_info_admin[0]['id']))  $facebook_rx_config_id = $fb_info_admin[0]['id'];
                    }
                    $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);

                    if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
                    else $facebook_rx_fb_user_info = 0;
                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig
                }
                else  // users will use own app
                {
                    $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"),'developer_access'=>'0')),$select='');

                    if(isset($fb_info_admin[0]['id']))
                    {
                        $facebook_rx_config_id = $fb_info_admin[0]['id'];
                        $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);
                    }

                    if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
                    else $facebook_rx_fb_user_info = 0;
                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig

                }
                // for getting usable facebook api


                $package_info = $this->basic->get_data("package", $where=array("where"=>array("id"=>$info[0]["package_id"])));
                $package_info_session=array();
                if(array_key_exists(0, $package_info))
                $package_info_session=$package_info[0];
                $this->session->set_userdata('package_info', $package_info_session);
                if(isset($package_info_session["id"])) $this->session->set_userdata('current_package_id',$package_info_session["id"]);

                $login_ip=$this->real_ip();

                $login_info_insert_data =array(
                        "user_id"=>$user_id,
                        "user_name" =>$username,
                        "login_time"=>date('Y-m-d H:i:s'),
                        "login_ip" =>$login_ip,
                        "user_email"=>$info[0]['email']
                );
                $this->basic->insert_data('user_login_info',$login_info_insert_data);  

                $this->basic->update_data("users",array("id"=>$user_id),array("last_login_at"=>date("Y-m-d H:i:s"),'last_login_ip'=>$login_ip)); 

                // if(function_exists('fb_app_set'))fb_app_set(); // Commented by Konok 28.12.2020

                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin')
                {
                    redirect('dashboard', 'location');
                }
                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member')
                {
                    redirect('dashboard', 'location');
                }
            }
        }
    }


    function google_login_back()
    {

        $this->load->library('Google_login');
        $info=$this->google_login->user_details();

        if(is_array($info) && !empty($info) && isset($info["email"]) && isset($info["name"]))
        {
            if(file_exists(APPPATH.'core/licence_type.txt'))
               $this->license_check_action();

            $default_package=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"1")));
            $expiry_date="";
            $package_id=0;
            if(is_array($default_package) && array_key_exists(0, $default_package))
            {
                $validity=$default_package[0]["validity"];
                $package_id=$default_package[0]["id"];
                $to_date=date('Y-m-d');
                $expiry_date=date("Y-m-d",strtotime('+'.$validity.' day',strtotime($to_date)));
            }

            if(!$this->basic->is_exist("users",array("email"=>$info["email"])))
            {
                $insert_data=array
                (
                    "email"=>$info["email"],
                    "name"=>$info["name"],
                    "user_type"=>"Member",
                    "status"=>"1",
                    "add_date"=>date("Y-m-d H:i:s"),
                    "package_id"=>$package_id,
                    "expired_date"=>$expiry_date,
                    "activation_code"=>"",
                    "deleted"=>"0"
                );
                $this->basic->insert_data("users",$insert_data);

                $mail_service_id = $this->config->item('mail_service_id');
                $system_short_name= $this->config->item('product_short_name');
                $mailchimp_list_tag=array("Sign up - {$system_short_name}");

                if($mail_service_id!="")
                $this->send_email_to_autoresponder($mail_service_id, $info['email'],$info['name'],'','singnup','0',$mailchimp_list_tag);
            }

            $table = 'users';
            $where['where'] = array('email' => $info["email"], "deleted" => "0","status"=>"1");

            $info = $this->basic->get_data($table, $where, $select = '', $join = '', $limit = '', $start = '', $order_by = '', $group_by = '', $num_rows = 1);


            $count = $info['extra_index']['num_rows'];

            if ($count == 0)
            {
                $this->session->set_flashdata('login_msg', $this->lang->line("invalid email or password"));
                redirect("home/login_page");
            }
            else
            {
                $username = $info[0]['name'];
                $user_type = $info[0]['user_type'];
                $user_id = $info[0]['id'];

                $logo = $info[0]['brand_logo'];

                if($logo=="") $logo=base_url("assets/img/avatar/avatar-1.png");
                else $logo=base_url().'member/'.$logo;
                $this->session->set_userdata('brand_logo',$logo);

                $this->session->set_userdata('logged_in', 1);
                $this->session->set_userdata('username', $username);
                $this->session->set_userdata('user_type', $user_type);
                $this->session->set_userdata('user_id', $user_id);
                $this->session->set_userdata('download_id', time());
                $this->session->set_userdata('user_login_email', $info[0]['email']);
                $this->session->set_userdata('expiry_date',$info[0]['expired_date']);
                $this->session->set_userdata('selected_global_media_type','fb');

                // GMB add-on data
                if($this->addon_exist("gmb"))
                {
                	$gmb_user_info = $this->basic->get_data('google_user_account',['where'=>['user_id'=>$user_id]],['id']);
                	if(!empty($gmb_user_info))
	                	$this->session->set_userdata('google_mybusiness_user_table_id',$gmb_user_info[0]['id']);
                }

                // for getting usable facebook api (facebook live app)
                $facebook_rx_config_id=0;
                $fb_info=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$user_id)));
                if($this->config->item("backup_mode")==0)  // users will use admins app
                {
                    if(isset($fb_info[0]['facebook_rx_config_id']))
                    $facebook_rx_config_id=$fb_info[0]['facebook_rx_config_id'];
                    else
                    {
                        $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','use_by'=>'everyone')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                        if(isset($fb_info_admin[0]['id']))  $facebook_rx_config_id = $fb_info_admin[0]['id'];
                    }
                    $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);

                    if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
                    else $facebook_rx_fb_user_info = 0;
                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig
                }
                else  // users will use own app
                {
                    $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"))),$select='');

                    if(isset($fb_info_admin[0]['id']))
                    {
                        $facebook_rx_config_id = $fb_info_admin[0]['id'];
                        $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);
                    }

                    if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
                    else $facebook_rx_fb_user_info = 0;
                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig

                }
                // for getting usable facebook api


                $package_info = $this->basic->get_data("package", $where=array("where"=>array("id"=>$info[0]["package_id"])));
                $package_info_session=array();
                if(array_key_exists(0, $package_info))
                $package_info_session=$package_info[0];
                $this->session->set_userdata('package_info', $package_info_session);
                if(isset($package_info_session["id"])) $this->session->set_userdata('current_package_id',$package_info_session["id"]);

                $this->basic->update_data("users",array("id"=>$user_id),array("last_login_at"=>date("Y-m-d H:i:s")));

                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin')
                {
                    redirect('dashboard', 'location');
                }
                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member')
                {
                    redirect('dashboard', 'location');
                }
            }


        }

    }


    public function facebook_login_back()
    {
        $this->is_group_posting_exist=$this->group_posting_exist();
        $this->session->set_userdata('social_login_session_set',1);
        $this->load->library('Fb_rx_login');
        $config_id = $this->session->userdata('return_configid_used_for_social_login');
        $this->fb_rx_login->app_initialize($config_id);

        $redirect_url=site_url("home/facebook_login_back");

        $info=$this->fb_rx_login->login_callback($redirect_url);



        if(is_array($info) && !empty($info) && isset($info["name"]))
        {
            if(file_exists(APPPATH.'core/licence_type.txt'))
               $this->license_check_action();

            $default_package=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"1")));
            $expiry_date="";
            $package_id=0;
            if(is_array($default_package) && array_key_exists(0, $default_package))
            {
                $validity=$default_package[0]["validity"];
                $package_id=$default_package[0]["id"];
                $to_date=date('Y-m-d');
                $expiry_date=date("Y-m-d",strtotime('+'.$validity.' day',strtotime($to_date)));
            }


            $temp_email = isset($info['email']) ? $info['email'] : '';
            $temp_fbid = isset($info['id']) ? $info['id'] : '';
            if($temp_email != '')
                $sql = "(email='".$temp_email."' OR email='".$info["id"]."' OR fb_id='".$info["id"]."')";
            else
                $sql = "(email='".$info["id"]."' OR fb_id='".$info["id"]."')";

            $this->db->where($sql);

            if($this->basic->is_exist("users"))
            {
                $this->db->where($sql);
                $this->basic->update_data('users','',['email'=>$temp_email,'fb_id'=>$temp_fbid]);
            }
            else
            {
                $insert_data=array
                (
                    "email"=>$temp_email,
                    "fb_id"=>$info['id'],
                    "name"=>$info["name"],
                    "user_type"=>"Member",
                    "status"=>"1",
                    "add_date"=>date("Y-m-d H:i:s"),
                    "package_id"=>$package_id,
                    "expired_date"=>$expiry_date,
                    "activation_code"=>"",
                    "deleted"=>"0"
                );
                $this->basic->insert_data("users",$insert_data);
                $user_id = $this->db->insert_id();
                $this->session->set_userdata('user_id', $user_id);

                $mail_service_id = $this->config->item('mail_service_id');
                $system_short_name= $this->config->item('product_short_name');
                $mailchimp_list_tag="Sign up - {$system_short_name}";

                if($mail_service_id!="")
                $this->send_email_to_autoresponder($mail_service_id, $temp_email ,$info['name'],'','singnup','0',$mailchimp_list_tag);


                // =========== action for account import ========== //
                $access_token=$info['access_token_set'];
                if(isset($access_token))
                {                    
                    $import_account_data = array(
                        'user_id' => $user_id,
                        'facebook_rx_config_id' => $config_id,
                        'access_token' => $access_token,
                        'name' => $info['name'],
                        'email' => isset($info['email']) ? $info['email'] : '',
                        'fb_id' => $info['id'],
                        'add_date' => date('Y-m-d'),
                        'deleted' => '0'
                        );

                    $where=array();
                    $where['where'] = array('user_id'=>$user_id,'fb_id'=>$info['id']);
                    $exist_or_not = array();
                    $exist_or_not = $this->basic->get_data('facebook_rx_fb_user_info',$where,$select='',$join='',$limit='',$start=NULL,$order_by='',$group_by='',$num_rows=0,$csv='',$delete_overwrite=1);

                    if(empty($exist_or_not))
                    {
                        //************************************************//
                        $status=$this->_check_usage($module_id=65,$request=1);
                        if($status=="2") 
                        {
                            $this->session->set_userdata('limit_cross', $this->lang->line("Module limit is over."));
                            redirect('social_accounts/index','location');                
                            exit();
                        }
                        else if($status=="3") 
                        {
                            $this->session->set_userdata('limit_cross', $this->lang->line("Module limit is over."));
                            redirect('social_accounts/index','location');                
                            exit();
                        }
                        //************************************************//
                        $this->basic->insert_data('facebook_rx_fb_user_info',$import_account_data);
                        $facebook_table_id = $this->db->insert_id();

                        //insert data to useges log table
                        $this->_insert_usage_log($module_id=65,$request=1);
                    }
                    else
                    {
                        $facebook_table_id = $exist_or_not[0]['id'];
                        $where = array('user_id'=>$user_id,'id'=>$facebook_table_id);
                        $this->basic->update_data('facebook_rx_fb_user_info',$where,$import_account_data);
                    }

                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_table_id);


                    $page_list = array();
                    $page_list = $this->fb_rx_login->get_page_list($access_token);

                    if(!empty($page_list))
                    {
                        foreach($page_list as $page)
                        {
                            $page_id = $page['id'];
                            $page_cover = '';
                            if(isset($page['cover']['source'])) $page_cover = $page['cover']['source'];
                            $page_profile = '';
                            if(isset($page['picture']['url'])) $page_profile = $page['picture']['url'];
                            $page_name = '';
                            if(isset($page['name'])) $page_name = $page['name'];
                            $page_access_token = '';
                            if(isset($page['access_token'])) $page_access_token = $page['access_token'];
                            $page_email = '';
                            if(isset($page['emails'][0])) $page_email = $page['emails'][0];
                            $page_username = '';
                            if(isset($page['username'])) $page_username = $page['username'];

                            $data = array(
                                'user_id' => $user_id,
                                'facebook_rx_fb_user_info_id' => $facebook_table_id,
                                'page_id' => $page_id,
                                'page_cover' => $page_cover,
                                'page_profile' => $page_profile,
                                'page_name' => $page_name,
                                'username' => $page_username,
                                'page_access_token' => $page_access_token,
                                'page_email' => $page_email,
                                'add_date' => date('Y-m-d'),
                                'deleted' => '0'
                                );

                            // instagram section
                            $instagram_account_exist_or_not = '';
                            if($this->config->item('instagram_reply_enable_disable') == '1')
                                $instagram_account_exist_or_not = $this->fb_rx_login->instagram_account_check_by_id($page['id'], $page['access_token']);
                            
                            if ($instagram_account_exist_or_not != "") {
                                $instagram_account_info = $this->fb_rx_login->instagram_account_info($instagram_account_exist_or_not, $page['access_token']); 
                                $data['has_instagram'] = '1';
                                $data['instagram_business_account_id'] = $instagram_account_exist_or_not; 
                                $data['insta_username'] = isset($instagram_account_info['username']) ? $instagram_account_info['username'] : "";
                                $data['insta_followers_count'] = isset($instagram_account_info['followers_count']) ? $instagram_account_info['followers_count'] : "";
                                $data['insta_media_count'] = isset($instagram_account_info['media_count']) ? $instagram_account_info['media_count'] : "";
                                $data['insta_website'] = isset($instagram_account_info['website']) ? $instagram_account_info['website'] : "";
                                $data['insta_biography'] = isset($instagram_account_info['biography']) ? $instagram_account_info['biography'] : "";
                            }
                            // end of instagram section

                            $where=array();
                            $where['where'] = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'page_id'=>$page['id']);
                            $exist_or_not = array();
                            $exist_or_not = $this->basic->get_data('facebook_rx_fb_page_info',$where,$select='',$join='',$limit='',$start=NULL,$order_by='',$group_by='',$num_rows=0,$csv='',$delete_overwrite=1);

                            if(empty($exist_or_not))
                            {
                                $this->basic->insert_data('facebook_rx_fb_page_info',$data);
                            }
                            else
                            {
                                $where = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'page_id'=>$page['id']);
                                $this->basic->update_data('facebook_rx_fb_page_info',$where,$data);
                            }


                        }
                    }

                    $group_list = array();
                    if($this->config->item('facebook_poster_group_enable_disable') == '1' && $this->is_group_posting_exist)
                        $group_list = $this->fb_rx_login->get_group_list($access_token);


                    if(!empty($group_list))
                    {
                        foreach($group_list as $group)
                        {
                            $group_access_token = $access_token; // group uses user access token
                            $group_id = $group['id'];
                            $group_cover = '';
                            if(isset($group['cover']['source'])) $group_cover = $group['cover']['source'];
                            $group_profile = '';
                            if(isset($group['picture']['url'])) $group_profile = $group['picture']['url'];
                            $group_name = '';
                            if(isset($group['name'])) $group_name = $group['name'];

                            $data = array(
                                'user_id' => $user_id,
                                'facebook_rx_fb_user_info_id' => $facebook_table_id,
                                'group_id' => $group_id,
                                'group_cover' => $group_cover,
                                'group_profile' => $group_profile,
                                'group_name' => $group_name,
                                'group_access_token' => $group_access_token,
                                'add_date' => date('Y-m-d'),
                                'deleted' => '0'
                                );

                            $where=array();
                            $where['where'] = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'group_id'=>$group['id']);
                            $exist_or_not = array();
                            $exist_or_not = $this->basic->get_data('facebook_rx_fb_group_info',$where,$select='',$join='',$limit='',$start=NULL,$order_by='',$group_by='',$num_rows=0,$csv='',$delete_overwrite=1);

                            if(empty($exist_or_not))
                            {
                                $this->basic->insert_data('facebook_rx_fb_group_info',$data);
                            }
                            else
                            {
                                $where = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'group_id'=>$group['id']);
                                $this->basic->update_data('facebook_rx_fb_group_info',$where,$data);
                            }
                        }
                    }

                }
                // =========== end of action for account import ========== //

            }

            // if(file_exists(APPPATH.'core/licence_type.txt'))
            // $this->license_check_action();
            $where = [];
            $table = 'users';
            $where = ['where'=>["status"=>"1","fb_id"=>$info["id"]]];
            $info = $this->basic->get_data($table, $where, $select = '', $join = '', $limit = '', $start = '', $order_by = '', $group_by = '', $num_rows = 1);

            $count = $info['extra_index']['num_rows'];

            if ($count == 0)
            {
                $this->session->set_flashdata('login_msg', $this->lang->line("invalid email or password"));
                redirect("home/login_page");
            }
            else
            {
                $username = $info[0]['name'];
                $user_type = $info[0]['user_type'];
                $user_id = $info[0]['id'];

                $logo = $info[0]['brand_logo'];

                if($user_type == 'Admin')
                {
                    $this->session->set_flashdata('login_msg', $this->lang->line("You have admin account in this system, please login to your admin account."));
                    redirect("home/login_page");
                }

                if($logo=="") $logo=base_url("assets/img/avatar/avatar-1.png");
                else $logo=base_url().'member/'.$logo;
                $this->session->set_userdata('brand_logo',$logo);

                $this->session->set_userdata('logged_in', 1);
                $this->session->set_userdata('username', $username);
                $this->session->set_userdata('user_type', $user_type);
                $this->session->set_userdata('user_id', $user_id);
                $this->session->set_userdata('download_id', time());
                $this->session->set_userdata('user_login_email', $info[0]['email']);
                $this->session->set_userdata('expiry_date',$info[0]['expired_date']);
                $this->session->set_userdata("fb_rx_login_database_id",$config_id);
                $this->session->set_userdata('selected_global_media_type','fb');

                // GMB add-on data
                if($this->addon_exist("gmb"))
                {
                	$gmb_user_info = $this->basic->get_data('google_user_account',['where'=>['user_id'=>$user_id]],['id']);
                	if(!empty($gmb_user_info))
	                	$this->session->set_userdata('google_mybusiness_user_table_id',$gmb_user_info[0]['id']);
                }

                 // for getting usable facebook api (facebook live app)
                $facebook_rx_config_id=0;
                $fb_info=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$user_id)));

                if($this->config->item("backup_mode")==0)  // users will use admins app
                {
                    if(isset($fb_info[0]['facebook_rx_config_id']))
                    $facebook_rx_config_id=$fb_info[0]['facebook_rx_config_id'];
                    else
                    {
                        $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','use_by'=>'everyone')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                        if(isset($fb_info_admin[0]['id']))  $facebook_rx_config_id = $fb_info_admin[0]['id'];
                    }
                    $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);

                    if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
                    else $facebook_rx_fb_user_info = 0;
                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig
                }
                else  // users will use own app
                {
                    $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"))),$select='');

                    if(isset($fb_info_admin[0]['id']))
                    {
                        $facebook_rx_config_id = $fb_info_admin[0]['id'];
                        $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);
                    }

                    if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
                    else $facebook_rx_fb_user_info = 0;
                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig

                }
                // for getting usable facebook api


                $package_info = $this->basic->get_data("package", $where=array("where"=>array("id"=>$info[0]["package_id"])));


                $package_info_session=array();
                if(array_key_exists(0, $package_info))
                $package_info_session=$package_info[0];
                $this->session->set_userdata('package_info', $package_info_session);
                if(isset($package_info_session["id"])) $this->session->set_userdata('current_package_id',$package_info_session["id"]);

                $this->basic->update_data("users",array("id"=>$user_id),array("last_login_at"=>date("Y-m-d H:i:s")));

                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin')
                {
                    redirect('dashboard', 'location');
                }
                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member')
                {
                    redirect('dashboard', 'location');
                }
            }
        }
        else
        {            
            $this->session->set_flashdata('login_msg', $this->lang->line("Could not fetch data from your Facebook account. Please try again later by giving necessery permissions."));
            redirect("home/login_page");
        }

    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('home/login_page', 'location');
    }




    
    //=======================GET DATA FUNCTIONS ======================
    //====================DATABASE, DROPDOWN & CURL===================

    protected function get_drip_type()
    {
        $ret = array
        (
            'default'=>'Default',
            'custom'=>'Custom',
            'messenger_bot_engagement_checkbox'=>'Checkbox',
            'messenger_bot_engagement_send_to_msg'=>'Send to Messenger',
            'messenger_bot_engagement_mme'=>'m.me',
            'messenger_bot_engagement_messenger_codes'=>'Messenger Code',
            'messenger_bot_engagement_2way_chat_plugin'=>'Customer Chat'
        );
        unset($ret['messenger_bot_engagement_messenger_codes']); // DEPRECATED

        return $ret;
    }

    public function get_broadcast_tags($media_type='fb')
    {
      
      if($media_type=='ig'){
        $dropdown = array("HUMAN_AGENT"=>"Human Agent");
        return $dropdown;
      }


      $old_tags = array
      (
        "ACCOUNT_UPDATE" => "ACCOUNT_UPDATE",
        "APPLICATION_UPDATE" => "APPLICATION_UPDATE",
        "APPOINTMENT_UPDATE" => "APPOINTMENT_UPDATE",
        "BUSINESS_PRODUCTIVITY" => "BUSINESS_PRODUCTIVITY",
        "COMMUNITY_ALERT" => "COMMUNITY_ALERT",
        "CONFIRMED_EVENT_REMINDER" => "CONFIRMED_EVENT_REMINDER",
        "FEATURE_FUNCTIONALITY_UPDATE" => "FEATURE_FUNCTIONALITY_UPDATE",
        "GAME_EVENT" => "GAME_EVENT",
        "ISSUE_RESOLUTION" => "ISSUE_RESOLUTION",
        "PAIRING_UPDATE" => "PAIRING_UPDATE",
        "PAYMENT_UPDATE" => "PAYMENT_UPDATE",
        "PERSONAL_FINANCE_UPDATE" => "PERSONAL_FINANCE_UPDATE",
        "RESERVATION_UPDATE" => "RESERVATION_UPDATE",
        "SHIPPING_UPDATE" => "SHIPPING_UPDATE",
        "TICKET_UPDATE" => "TICKET_UPDATE",
        "TRANSPORTATION_UPDATE" => "TRANSPORTATION_UPDATE",
      );

      $new_tags = array
      (        
        ""=>$this->lang->line("Select Tag"),
        "ACCOUNT_UPDATE"=>"ACCOUNT_UPDATE",
        "CONFIRMED_EVENT_UPDATE"=>"CONFIRMED_EVENT_UPDATE",
        "HUMAN_AGENT"=>"HUMAN_AGENT",
        "POST_PURCHASE_UPDATE"=>"POST_PURCHASE_UPDATE",        
        "NON_PROMOTIONAL_SUBSCRIPTION" => "NON_PROMOTIONAL_SUBSCRIPTION"
      );


      foreach($new_tags as $key=>$value){
        $new_tags[$key] = ucwords(strtolower(str_replace('_', ' ', $value)));
      }
      foreach($old_tags as $key=>$value){
        $old_tags[$key] = ucwords(strtolower(str_replace('_', ' ', $value)));
      }

      if(strtotime(date("Y-m-d")) > strtotime("2020-3-4")) return $new_tags;

      unset($new_tags[""]);
      $dropdown = array(""=>$this->lang->line("Select Tag"),"New Tags"=>$new_tags,"Tags supported until Mar 4,2020"=>$old_tags);
      return $dropdown;

    }

    protected function get_country_names()
    {
        return $this->get_country_iso_phone_currecncy('country');
    }

    protected function get_language_names()
    {
        $array_languages = array(
        'ar-XA'=>'Arabic','en'=>'English','th'=>'Thai' );
        
     return $array_languages;
    }

    protected function sdk_locale()
    {
        $config = array(
            'default'=> 'Default',
            'af_ZA' => 'Afrikaans',
            'ar_AR' => 'Arabic',
            'en_GB' => 'English (UK)',
            'en_PI' => 'English (Pirate)',
            'en_UD' => 'English (Upside Down)',
            'en_US' => 'English (US)',	'ms_MY' => 'Malay',
            'th_TH' => 'Thai',
            'zh_CN' => 'Chinese (China)',
            'zh_HK' => 'Chinese (Hong Kong)',           
            'zh_TW' => 'Chinese (Taiwan)');
		asort($config);
        return $config;
    }
	
            
		
            
		
           
		
            

     
		
            
            
	 
     
	
       
        

    public function _scanAll($myDir)
    {
        $dirTree = array();
        $di = new RecursiveDirectoryIterator($myDir,RecursiveDirectoryIterator::SKIP_DOTS);

        $i=0;
        foreach (new RecursiveIteratorIterator($di) as $filename) {

            $dir = str_replace($myDir, '', dirname($filename));
            // $dir = str_replace('/', '>', substr($dir,1));

            $org_dir=str_replace("\\", "/", $dir);

            if($org_dir)
                $file_path = $org_dir. "/". basename($filename);
            else
                $file_path = basename($filename);

            $file_full_path=$myDir."/".$file_path;
            $file_size= filesize($file_full_path);
            $file_modification_time=filemtime($file_full_path);

            $dirTree[$i]['file'] = $file_full_path;
            $i++;
        }
        return $dirTree;
    }


    public function _language_list()
    {
        $myDir = APPPATH.'language';
        $file_list = $this->_scanAll($myDir);
        foreach ($file_list as $file) {
            $i = 0;
            $one_list[$i] = $file['file'];
            $one_list[$i]=str_replace("\\", "/",$one_list[$i]);
            $one_list_array[] = explode("/",$one_list[$i]);
        }
        foreach ($one_list_array as $value) 
        {           
            $pos=count($value)-2; 
            $lang_folder=$value[$pos];
            $final_list_array[] = $lang_folder;
        }
        $final_array = array_unique($final_list_array);
        $array_keys = array_values($final_array);
        foreach ($final_array as $value) {
            $uc_array_valus[] = ucfirst($value);
        }
        $array_values = array_values($uc_array_valus);
        $final_array_done = array_combine($array_keys, $array_values);
        return $final_array_done;
    }

    public function _theme_list()
    {
        return array();
        $myDir = 'css/skins';
        $file_list = $this->_scanAll($myDir);
        $theme_list=array();
        foreach ($file_list as $file) {
            $i = 0;
            $one_list[$i] = $file['file'];
            $one_list[$i]=str_replace("\\", "/",$one_list[$i]);
            $one_list_array = explode("/",$one_list[$i]);
            $theme=array_pop($one_list_array);
            $pos=strpos($theme, '.min.css');
            if($pos!==FALSE) continue; // only loading unminified css
            if($theme=="_all-skins.css") continue;  // skipping large css file that includes all file
            $theme_name=str_replace('.css','', $theme);
            $theme_display=str_replace(array('skin-','.css','-'), array('','',' '), $theme);
            if($theme_display=="black light") $theme_display='light';
            if($theme_display=="black") $theme_display='dark';
            $theme_list[$theme_name]=ucwords($theme_display);
        }
        return $theme_list;
        
    }

    public function _theme_list_front()
    {
        return array
        (
            "white"=>"Light",
            "black"=>"Dark",
            "blue"=>"Blue",
            "green"=>"Green",
            "purple"=>"Purple",
            "red"=>"Red",
            "yellow"=>"Yellow"
        );
    }


    public function language_changer()
    {
        $language=$this->input->post("language");
        $this->session->set_userdata("selected_language",$language);
    }

    protected function time_zone_drop_down($datavalue = '', $primary_key = null,$mandatory=0) // return HTML select
    {
        $all_time_zone = $this->_time_zone_list();

        $str = "<select name='time_zone' id='time_zone' class='form-control'>";
        if($mandatory===1)
        $str.= "<option value=>Time Zone *</option>";
        else $str.= "<option value=>Time Zone</option>";

        foreach ($all_time_zone as $zone_name=>$value) {
            if ($primary_key!= null) {
                if ($zone_name==$datavalue) {
                    $selected=" selected = 'selected' ";
                } else {
                    $selected="";
                }
            } else {
                if ($zone_name==$this->config->item("time_zone")) {
                    $selected=" selected = 'selected' ";
                } else {
                    $selected="";
                }
            }
            $str.= "<option ".$selected." value='$zone_name'>{$zone_name}</option>";
        }
        $str.= "</select>";
        return $str;
    }


    function get_facebook_instagram_dropdown($facebook_rx_fb_user_info_id=0, $dropdown_name = "page_id", $dropdown_id = "page_id", $dropdown_style="", $dropdown_class='select2 form-control',$return_groups = false) 
    {
        
        if ($this->config->item('facebook_poster_group_enable_disable') == '' || $this->config->item('facebook_poster_group_enable_disable')=='0') $has_group_access = false;
        else $has_group_access = true; 

        if(!$has_group_access) $return_groups = false;

        $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("facebook_rx_fb_user_info_id"=>$facebook_rx_fb_user_info_id,'bot_enabled'=>'1')),array('page_name','id','bot_enabled','has_instagram','insta_username'));

        $str ='<select class="'.$dropdown_class.'" id="'.$dropdown_id.'" name="'.$dropdown_name.'" style="'.$dropdown_style.'">';

        if(!addon_exist($module_id=320,$addon_unique_name="instagram_bot") && !$return_groups) 
        $str .='<option value="">'.$this->lang->line("Facebook Page").'</option>';

        if(addon_exist($module_id=320,$addon_unique_name="instagram_bot") || $return_groups)
        $str .='<optgroup label="'.$this->lang->line("Facebook Page").'">';

        foreach ($page_info as $key => $value)
        {
           $selected = ( 
            $this->session->userdata('selected_global_page_table_id')==$value['id'] && 
            ($this->session->userdata('selected_global_media_type')=='fb' || $this->session->userdata('selected_global_media_type')=='')
           ) ? 'selected' : '';
           if($value['bot_enabled']=='1')
           $str.= '<option value="'.$value['id'].'" '.$selected.'>'.$value['page_name'].'</option>';
        }

        if(addon_exist($module_id=320,$addon_unique_name="instagram_bot")  || $return_groups) 
        $str.='</optgroup>';

        if(addon_exist($module_id=320,$addon_unique_name="instagram_bot"))
        {
            $str .='<optgroup label="'.$this->lang->line("Instagram").'">';        
            foreach ($page_info as $key => $value)
            {
                $selected = ($this->session->userdata('selected_global_page_table_id')==$value['id'] && $this->session->userdata('selected_global_media_type')=='ig') ? 'selected' : '';

                if($value['bot_enabled']=='1' && $value['has_instagram']=='1')
                $str .= '<option value="'.$value['id'].'-ig" '.$selected.'>'.$value['insta_username'].'</option>';                
            }
            $str .= '</optgroup>'; 
        }

        if($return_groups)
        {
            
            $group_info = $this->basic->get_data("facebook_rx_fb_group_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))),'id,group_name');        

            $str .='<optgroup label="'.$this->lang->line("Facebook Group").'">';        
            foreach ($group_info as $key => $value)
            {
                $str .= '<option value="'.$value['id'].'-gr">'.$value['group_name'].'</option>';                
            }
            $str .= '</optgroup>'; 
        }               
        $str .= '</select>';

        return $str;
    }


    //  used in all types of bulk message campaign
    public function get_broadcast_summary()
    {
        if($this->session->userdata('logged_in') != 1) exit();
        $this->ajax_check();
        $page_id=$this->input->post('page_id');// database id
        $pageid=explode("-",$page_id);
        $page_id = $pageid[0];
        $media_type = "fb";

        if(isset($pageid[1]) && $pageid[1]=="ig") {
            $media_type = "ig";
        }

        $template_types=$this->basic->get_enum_values("messenger_bot_broadcast_serial","template_type");
        $template_types = array_diff($template_types,['list']);
        if($media_type == 'ig') {
            $need_to_remove = ['audio','video','file','text with buttons','media'];
            foreach ($need_to_remove as $value) {
                if (($key = array_search($value, $template_types)) !== false) {
                    unset($template_types[$key]);
                }
            }
        }
        $template_type_str = '';
        foreach ($template_types as $template) {
            $template_type_str .='<option value="'.$template.'">'.$this->lang->line($template).'</option>';
        }

        $user_gender=$this->input->post('user_gender');
        $user_time_zone=$this->input->post('user_time_zone');
        $user_locale=$this->input->post('user_locale');
        $load_label=$this->input->post('load_label');
        $label_ids=$this->input->post('label_ids');
        $excluded_label_ids=$this->input->post('excluded_label_ids');
        $is_bot_subscriber=$this->input->post('is_bot_subscriber');
        $broadcast_type=$this->input->post('broadcast_type');


        if(!isset($label_ids) || !is_array($label_ids)) $label_ids =array();
        if(!isset($excluded_label_ids) || !is_array($excluded_label_ids)) $excluded_label_ids =array();

        $table_type = 'messenger_bot_broadcast_contact_group';
        $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$page_id,"unsubscribe"=>"0","invisible"=>"0");
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name');

        $result = array();
        date_default_timezone_set('UTC');
        $current_time  = date("Y-m-d H:i:s");
        $previous_time = date("Y-m-d H:i:s",strtotime('-23 hour',strtotime($current_time)));
        $this->_time_zone_set();
        $dropdown=array();
        $str = $str2 = "";

        if($load_label=='1')
        {
            $str='<script>$("#label_ids").select2();</script> ';
            $str2='<script>$("#excluded_label_ids").select2();</script> ';
            $str .='<select multiple="multiple"  class="form-control" id="label_ids" name="label_ids[]">';
            $str2.='<select multiple="multiple"  class="form-control" id="excluded_label_ids" name="excluded_label_ids[]">';        

            foreach ($info_type as  $value)
            {                
                $str.=  "<option value='".$value['id']."'>".$value['group_name']."</option>";
                $str2.= "<option value='".$value['id']."'>".$value['group_name']."</option>"; 
            }

            $str.= '</select>';
            $str2.='</select>';
        }

        $pageinfo = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id,"user_id"=>$this->user_id)));
        $page_info = isset($pageinfo[0])?$pageinfo[0]:array();

        if(isset($page_info['page_access_token'])) unset($page_info['page_access_token']);

        $subscriber_count = 0;

        if($is_bot_subscriber=='1') $where_simple2 =array("page_table_id"=>$page_id,'is_bot_subscriber'=> '1','unavailable'=>'0','user_id'=>$this->user_id,'permission'=>'1');
        else $where_simple2 =array("page_table_id"=>$page_id,'client_thread_id !='=> '','user_id'=>$this->user_id,'permission'=>'1');

        if(isset($user_gender) && $user_gender!="")  $where_simple2['messenger_bot_subscriber.gender'] = $user_gender;
        if(isset($user_time_zone) && $user_time_zone!="")  $where_simple2['messenger_bot_subscriber.timezone'] = $user_time_zone;
        if(isset($user_locale) && $user_locale!="")  $where_simple2['messenger_bot_subscriber.locale'] = $user_locale;

        if(isset($broadcast_type) && ($broadcast_type=='24H Promo' || $broadcast_type=='24+1 Promo'))  // bulk bradcast
        {
            if($broadcast_type=='24H Promo') $where_simple2['messenger_bot_subscriber.last_subscriber_interaction_time >'] = $previous_time;
            else if($broadcast_type=='24+1 Promo')
            {
                $where_simple2['messenger_bot_subscriber.last_subscriber_interaction_time <'] = $previous_time;
                $where_simple2['messenger_bot_subscriber.is_24h_1_sent'] = '0';
            }
        }
        
        $sql_part = "";
        if($load_label=='0')
        {
           if(count($label_ids)>0) $this->db->where_in('messenger_bot_subscribers_label.contact_group_id',$label_ids);
           if(count($excluded_label_ids)>0) $this->db->where_not_in('messenger_bot_subscribers_label.contact_group_id',$excluded_label_ids);
        }

        $where_simple2['messenger_bot_subscriber.social_media'] = $media_type;
        $where_simple2['messenger_bot_subscriber.subscriber_type !='] = 'system';
        $where2 = array('where'=>$where_simple2);
        $join = ['messenger_bot_subscribers_label'=>'messenger_bot_subscribers_label.subscriber_table_id=messenger_bot_subscriber.id,left'];
        $bot_subscriber=$this->basic->get_data("messenger_bot_subscriber",$where2,'count(DISTINCT(messenger_bot_subscriber.id)) as subscriber_count',$join);
        // echo $this->db->last_query();exit();
        $subscriber_count = isset($bot_subscriber[0]['subscriber_count'])? $bot_subscriber[0]['subscriber_count'] : 0;
        $page_info['subscriber_count'] = $subscriber_count;

        $push_postback="";
        $total_subscriber_count = 0;
        if($is_bot_subscriber=='1')
        {
            $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,'is_template'=>'1','template_for'=>'reply_message')),'','','',$start=NULL,$order_by='template_name ASC');
            foreach ($postback_data as $key => $value) 
            {
                $push_postback.="<option value='".$value['postback_id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
            }
            $total_bot_subscriber=$this->basic->get_data("messenger_bot_subscriber",array("where"=>array("page_table_id"=>$page_id,'is_bot_subscriber'=> '1','user_id'=>$this->user_id,'permission'=>'1','subscriber_type !='=>'system')),'count(id) as total_subscriber_count');
            $total_subscriber_count = isset($total_bot_subscriber[0]['total_subscriber_count'])? $total_bot_subscriber[0]['total_subscriber_count'] : 0;
        }
        $page_info['total_subscriber_count'] = $total_subscriber_count;

        echo json_encode(array('first_dropdown'=>$str,'second_dropdown'=>$str2,"pageinfo"=>$page_info,"push_postback"=>$push_postback,'templates'=>$template_type_str,"media_type"=>$media_type));
    }

    public function get_otn_broadcast_summary()
    {
        if($this->session->userdata('logged_in') != 1) exit();
        $this->ajax_check();
        $page_id=$this->input->post('page_id');// database id
        $user_gender=$this->input->post('user_gender');
        $user_time_zone=$this->input->post('user_time_zone');
        $user_locale=$this->input->post('user_locale');
        $load_label=$this->input->post('load_label');
        $load_otn_postback=$this->input->post('load_otn_postback');
        $otn_postback_ids=$this->input->post('otn_postback_ids');
        $label_ids=$this->input->post('label_ids');
        $excluded_label_ids=$this->input->post('excluded_label_ids');
        $is_bot_subscriber=$this->input->post('is_bot_subscriber');
        $broadcast_type=$this->input->post('broadcast_type');
        $hidden_id=$this->input->post('hidden_id');

        $postback_ids_array = [];
        $label_ids_array = [];
        $exclude_label_array = [];
        if($hidden_id != '0')
        {
            $campaign_data = $this->basic->get_data('messenger_bot_broadcast_serial',['where'=>['id'=>$hidden_id,'user_id'=>$this->user_id]],['otn_postback_id','label_ids','excluded_label_ids']);
            $postback_ids_array = explode(',', $campaign_data[0]['otn_postback_id']);
            $label_ids_array = explode(',', $campaign_data[0]['label_ids']);
            $exclude_label_array = explode(',', $campaign_data[0]['excluded_label_ids']);

        }


        if(!isset($label_ids) || !is_array($label_ids)) $label_ids =array();
        if(!isset($excluded_label_ids) || !is_array($excluded_label_ids)) $excluded_label_ids =array();
        if(!isset($otn_postback_ids) || !is_array($otn_postback_ids)) $otn_postback_ids =array();

        $table_type = 'messenger_bot_broadcast_contact_group';
        $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$page_id,"unsubscribe"=>"0","invisible"=>"0");
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name');

        $result = array();
        date_default_timezone_set('UTC');
        $current_time  = date("Y-m-d H:i:s");
        $previous_time = date("Y-m-d H:i:s",strtotime('-23 hour',strtotime($current_time)));
        $this->_time_zone_set();
        $dropdown=array();
        $str = $str2 = "";
        $otn_postback_str = '';
        if($load_otn_postback == '1')
        {
            $otn_postback_str .= '<script>$("#otn_postback_ids").select2();</script> ';
            $otn_postback_str .= '<select multiple="multiple"  class="form-control" id="otn_postback_ids" name="otn_postback_ids[]">';
            $otn_postback_info = $this->basic->get_data('otn_postback',['where'=>['page_id'=>$page_id,'user_id'=>$this->user_id]],['id','template_name']);
            foreach($otn_postback_info as $value)
            {
                $selected = '';
                if(in_array($value['id'], $postback_ids_array)) $selected = 'selected';
                $otn_postback_str.=  "<option value='".$value['id']."' ".$selected.">".$value['template_name']."</option>";
            }
            $otn_postback_str.= '</select>';
        }

        if($load_label=='1')
        {
            $str='<script>$("#label_ids").select2();</script> ';
            $str2='<script>$("#excluded_label_ids").select2();</script> ';
            $str .='<select multiple="multiple"  class="form-control" id="label_ids" name="label_ids[]">';
            $str2.='<select multiple="multiple"  class="form-control" id="excluded_label_ids" name="excluded_label_ids[]">';        

            foreach ($info_type as  $value)
            {    
                $selected = '';
                if(in_array($value['id'], $label_ids_array)) $selected = 'selected';

                $selected2 = '';
                if(in_array($value['id'], $exclude_label_array)) $selected2 = 'selected';

                $str.=  "<option value='".$value['id']."' ".$selected." >".$value['group_name']."</option>";
                $str2.= "<option value='".$value['id']."' ".$selected2." >".$value['group_name']."</option>"; 
            }

            $str.= '</select>';
            $str2.='</select>';
        }

        $pageinfo = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id,"user_id"=>$this->user_id)));
        $page_info = isset($pageinfo[0])?$pageinfo[0]:array();

        if(isset($page_info['page_access_token'])) unset($page_info['page_access_token']);

        $subscriber_count = 0;

        if($is_bot_subscriber=='1') $where_simple2 =array("messenger_bot_subscriber.page_table_id"=>$page_id,'is_bot_subscriber'=> '1','unavailable'=>'0','user_id'=>$this->user_id,'permission'=>'1');
        else $where_simple2 =array("messenger_bot_subscriber.page_table_id"=>$page_id,'client_thread_id !='=> '','user_id'=>$this->user_id,'permission'=>'1');

        if(isset($user_gender) && $user_gender!="")  $where_simple2['messenger_bot_subscriber.gender'] = $user_gender;
        if(isset($user_time_zone) && $user_time_zone!="")  $where_simple2['messenger_bot_subscriber.timezone'] = $user_time_zone;
        if(isset($user_locale) && $user_locale!="")  $where_simple2['messenger_bot_subscriber.locale'] = $user_locale;

        if(isset($broadcast_type) && ($broadcast_type=='24H Promo' || $broadcast_type=='24+1 Promo'))  // bulk bradcast
        {
            if($broadcast_type=='24H Promo') $where_simple2['messenger_bot_subscriber.last_subscriber_interaction_time >'] = $previous_time;
            else if($broadcast_type=='24+1 Promo')
            {
                $where_simple2['messenger_bot_subscriber.last_subscriber_interaction_time <'] = $previous_time;
                $where_simple2['messenger_bot_subscriber.is_24h_1_sent'] = '0';
            }
        }
        
        $sql_part = "";
        if($load_label=='0')
        {
           if(count($label_ids)>0) $this->db->where_in('messenger_bot_subscribers_label.contact_group_id',$label_ids);
           if(count($excluded_label_ids)>0) $this->db->where_not_in('messenger_bot_subscribers_label.contact_group_id',$excluded_label_ids);
        }

        if(!empty($otn_postback_ids))
        {
            $this->db->where_in('otn_optin_subscriber.otn_id',$otn_postback_ids);
        }

        $where_simple2['otn_optin_subscriber.is_sent'] = '0';
        $where2 = array('where'=>$where_simple2);
        $join = ['messenger_bot_subscriber'=>'otn_optin_subscriber.subscriber_id=messenger_bot_subscriber.subscribe_id,left','messenger_bot_subscribers_label'=>'messenger_bot_subscribers_label.subscriber_table_id=messenger_bot_subscriber.id,left'];
        $bot_subscriber=$this->basic->get_data("otn_optin_subscriber",$where2,'count(DISTINCT(messenger_bot_subscriber.subscribe_id)) as subscriber_count',$join);
        // echo $this->db->last_query();
        $subscriber_count = isset($bot_subscriber[0]['subscriber_count'])? $bot_subscriber[0]['subscriber_count'] : 0;
        $page_info['subscriber_count'] = $subscriber_count;

        $push_postback="";
        $total_subscriber_count = 0;
        if($is_bot_subscriber=='1')
        {
            $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,'is_template'=>'1','template_for'=>'reply_message')),'','','',$start=NULL,$order_by='template_name ASC');
            foreach ($postback_data as $key => $value) 
            {
                $push_postback.="<option value='".$value['postback_id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
            }
            $total_bot_subscriber=$this->basic->get_data("otn_optin_subscriber",array("where"=>array("messenger_bot_subscriber.page_table_id"=>$page_id,'otn_optin_subscriber.is_sent'=>'0','is_bot_subscriber'=> '1','user_id'=>$this->user_id,'permission'=>'1')),'count(messenger_bot_subscriber.id) as total_subscriber_count',$join);
            $total_subscriber_count = isset($total_bot_subscriber[0]['total_subscriber_count'])? $total_bot_subscriber[0]['total_subscriber_count'] : 0;
        }
        $page_info['total_subscriber_count'] = $total_subscriber_count;

        echo json_encode(array('first_dropdown'=>$str,'second_dropdown'=>$str2,"pageinfo"=>$page_info,"push_postback"=>$push_postback,'otn_postback_str'=>$otn_postback_str));
    }


    protected function currecny_list_all()
    {
        return $this->get_country_iso_phone_currecncy('currency_name');
    }

    protected function currency_icon()
    {
        return $this->get_country_iso_phone_currecncy('currecny_icon');
    }

    protected function paypal_stripe_currency_list()
    {
        return array('USD','AUD','BRL','CAD','CZK','DKK','EUR','HKD','HUF','ILS','JPY','MYR','MXN','TWD','NZD','NOK','PHP','PLN','GBP','RUB','SGD','SEK','CHF','VND');
    }

    //https://gist.github.com/davmixcool/1248ade2fcf43cf86fa294667c86224a
    protected function get_country_iso_phone_currecncy($return='country') // country,currency_name,currecny_icon,phonecode
    {
        $countries = array(
           'array('name' => 'Malaysia','iso_alpha2' => 'MY','iso_alpha3' => 'MYS','iso_numeric' => '458','calling_code' => '60','currency_code' => 'MYR','currency_name' => 'Ringgit','currency_symbol' => 'RM'),
	  array('name' => 'Saudi Arabia','iso_alpha2' => 'SA','iso_alpha3' => 'SAU','iso_numeric' => '682','calling_code' => '966','currency_code' => 'SAR','currency_name' => 'Rial','currency_symbol' => '﷼'),
	  array('name' => 'Singapore','iso_alpha2' => 'SG','iso_alpha3' => 'SGP','iso_numeric' => '702','calling_code' => '65','currency_code' => 'SGD','currency_name' => 'Dollar','currency_symbol' => '$'),
        array('name' => 'Thailand','iso_alpha2' => 'TH','iso_alpha3' => 'THA','iso_numeric' => '764','calling_code' => '66','currency_code' => 'THB','currency_name' => 'Baht','currency_symbol' => '฿'),
      array('name' => 'United Kingdom','iso_alpha2' => 'GB','iso_alpha3' => 'GBR','iso_numeric' => '826','calling_code' => '44','currency_code' => 'GBP','currency_name' => 'Pound','currency_symbol' => '£'),
  array('name' => 'United States','iso_alpha2' => 'US','iso_alpha3' => 'USA','iso_numeric' => '840','calling_code' => '1','currency_code' => 'USD','currency_name' => 'Dollar','currency_symbol' => '$'),
      );
	  
        
        $output = array();
        foreach ($countries as $key => $value)
        {
            if($return=='country') $output[$value['iso_alpha2']] = $value['name'];        
            else if($return=='currency_name') $output[$value['currency_code']] = $value['currency_code']." (".$value['currency_name'].")";        
            else if($return=='currecny_icon') $output[$value['currency_code']] = !empty($value['currency_symbol']) ? $value['currency_symbol'] : $value['currency_code'];
            else $output[$value['iso_alpha2']] = $value['calling_code'];
        }
        if(isset($output[''])) unset($output['']);   

        asort($output);
        return $output;
    }


    function _payment_package()
    {
        $payment_package=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"0","price > "=>0)),$select='',$join='',$limit='',$start=NULL,$order_by='price');
        $return_val=array();
        $config_data=$this->basic->get_data("payment_config");
        $currency=$config_data[0]["currency"];
        foreach ($payment_package as $row)
        {
            $return_val[$row['id']]=$row['package_name']." : Only @".$currency." ".$row['price']." for ".$row['validity']." days";
        }
        return $return_val;
    }

    protected function real_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
          $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
          $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
          $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    function get_general_content($url,$proxy=""){


            $ch = curl_init(); // initialize curl handle
           /* curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);*/
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
            curl_setopt($ch, CURLOPT_AUTOREFERER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
            curl_setopt($ch, CURLOPT_REFERER, 'http://'.$url);
            curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            curl_setopt($ch, CURLOPT_TIMEOUT, 50); // times out after 50s
            curl_setopt($ch, CURLOPT_POST, 0); // set POST method


            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          //  curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");
           // curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");

            $content = curl_exec($ch); // run the whole process
            curl_close($ch);

            return json_encode($content);

    }


    function get_general_content_with_checking($url,$proxy=""){


            $ch = curl_init(); // initialize curl handle
           /* curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);*/
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
            curl_setopt($ch, CURLOPT_AUTOREFERER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
            curl_setopt($ch, CURLOPT_REFERER, 'http://'.$url);
            curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            curl_setopt($ch, CURLOPT_TIMEOUT, 120); // times out after 50s
            curl_setopt($ch, CURLOPT_POST, 0); // set POST method


            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          //  curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");
           // curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");

            $content = curl_exec($ch); // run the whole process
            $response['content'] = $content;

            $res = curl_getinfo($ch);
            if($res['http_code'] != 200)
                $response['error'] = 'error';
            curl_close($ch);
            return json_encode($response);

    }
    //=======================GET DATA FUNCTIONS ======================
    //================================================================



    //================================================================
    //=========================WEBSITE FUNCTIOS=======================
    public function _random_number_generator($length=6)
    {
        $rand = substr(uniqid(mt_rand(), true), 0, $length);
        return $rand;
    }


    public function forgot_password()
    {
        $data["page_title"] = $this->lang->line("Password Recovery");

        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_file_path = "views/site/".$current_theme."/forgot_password.php";
        if(file_exists(APPPATH.$body_file_path))
            $body_load = "site/".$current_theme."/forgot_password";
        else
            $body_load = "site/modern/forgot_password";

        $data['body']=$body_load;
        $this->_subscription_viewcontroller($data);
    }


    public function code_genaration()
    {
        $this->ajax_check();

        $email = trim($this->input->post('email',true));
        $result = $this->basic->get_data('users', array('where' => array('email' => $email)), array('count(*) as num'));

        if ($result[0]['num'] == 1) {
            //entry to forget_password table
            $expiration = date("Y-m-d H:i:s", strtotime('+1 day', time()));
            $code = $this->_random_number_generator();
            $url = site_url().'home/password_recovery';
            $url_final="<a href='".$url."' target='_BLANK'>".$url."</a>";
            $productname = $this->config->item('product_name');

            $table = 'forget_password';
            $info = array(
                'confirmation_code' => $code,
                'email' => $email,
                'expiration' => $expiration
                );

            if ($this->basic->insert_data($table, $info)) {

                //email to user
                $email_template_info = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'reset_password')),array('subject','message'));

                if(isset($email_template_info[0]) && $email_template_info[0]['subject'] != '' && $email_template_info[0]['message'] != '') {

                    $subject = str_replace('#APP_NAME#',$productname,$email_template_info[0]['subject']);
                    $message =str_replace(array("#APP_NAME#","#PASSWORD_RESET_URL#","#PASSWORD_RESET_CODE#"),array($productname,$url_final,$code),$email_template_info[0]['message']);

                } else {

                    $subject = $productname." | Password recovery";
                    $message = "<p>".$this->lang->line('to reset your password please perform the following steps')." : </p>
                                <ol>
                                    <li>".$this->lang->line("go to this url")." : ".$url_final."</li>
                                    <li>".$this->lang->line("enter this code")." : ".$code."</li>
                                    <li>".$this->lang->line("reset your password")."</li>
                                </ol>
                                <h4>".$this->lang->line("link and code will be expired after 24 hours")."</h4>";

                }


                $from = $this->config->item('institute_email');
                $to = $email;
                $mask = $this->config->item("product_name");
                $html = 1;
                $this->_mail_sender($from, $to, $subject, $message, $mask, $html);
            }
        } else {
            echo 0;
        }
    }


    public function password_recovery()
    {
        $data['page_title']=$this->lang->line("password recovery");

        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_file_path = "views/site/".$current_theme."/password_recovery.php";
        if(file_exists(APPPATH.$body_file_path))
            $body_load = "site/".$current_theme."/password_recovery";
        else
            $body_load = "site/modern/password_recovery";

        $data['body']=$body_load;
        $this->_subscription_viewcontroller($data);
    }


    public function recovery_check()
    {
        $this->ajax_check();
        if ($_POST) {
            $code=trim($this->input->post('code', true));
            $newp=md5($this->input->post('newp', true));
            $conf=md5($this->input->post('conf', true));

            if($code=="" || $newp=="" || $conf=="" || ($newp != $conf) )
            {
                echo 0;
                exit();
            }

            $table='forget_password';
            $where['where']=array('confirmation_code'=>$code,'success'=>0);
            $select=array('email','expiration');

            $result=$this->basic->get_data($table, $where, $select);

            if (empty($result)) {
                echo 0;
            } else {
                foreach ($result as $row) {
                    $email=$row['email'];
                    $expiration=$row['expiration'];
                }

                $now=time();
                $exp=strtotime($expiration);

                if ($now>$exp) {
                    echo 1;
                } else {
                    $student_info_where['where'] = array('email'=>$email);
                    $student_info_select = array('id');
                    $student_info_id = $this->basic->get_data('users', $student_info_where, $student_info_select);
                    $this->basic->update_data('users', array('id'=>$student_info_id[0]['id']), array('password'=>$newp));
                    $this->basic->update_data('forget_password', array('confirmation_code'=>$code), array('success'=>1));
                    echo 2;
                }
            }
        }
    }


    function _mail_sender($from = '', $to = '', $subject = '', $message = '', $mask = "", $html = 1, $smtp = 1,$attachement="",$test_mail="")
    {
        if ($to!= '' && $subject!='' && $message!= '')
        {
            if($this->config->item('email_sending_option') == '') $email_sending_option = 'smtp';
            else $email_sending_option = $this->config->item('email_sending_option');

            if($test_mail == 1) $email_sending_option = 'smtp';

          //  if($smtp != '1') $message=$message."<br/><br/>".$this->lang->line("The email was sent by"). ": ".$from;

            if($email_sending_option == 'smtp')
            {
                if ($smtp == '1') {
                    $where2 = array("where" => array('status' => '1','deleted' => '0'));
                    $email_config_details = $this->basic->get_data("email_config", $where2, $select = '', $join = '', $limit = '', $start = '', $group_by = '', $num_rows = 0);

                    if (count($email_config_details) == 0) {
                        $this->load->library('email');
                    } else {
                        foreach ($email_config_details as $send_info) {
                            $send_email = trim($send_info['email_address']);
                            $smtp_host = trim($send_info['smtp_host']);
                            $smtp_port = trim($send_info['smtp_port']);
                            $smtp_user = trim($send_info['smtp_user']);
                            $smtp_password = trim($send_info['smtp_password']);
                            $smtp_type = trim($send_info['smtp_type']);
                        }

                    /*****Email Sending Code ******/
                    $config = array(
                      'protocol' => 'smtp',
                      'smtp_host' => "{$smtp_host}",
                      'smtp_port' => "{$smtp_port}",
                      'smtp_user' => "{$smtp_user}", // change it to yours
                      'smtp_pass' => "{$smtp_password}", // change it to yours
                      'mailtype' => 'html',
                      'charset' => 'utf-8',
                      'newline' =>  "\r\n",
                      'set_crlf'=> "\r\n",
                      'smtp_timeout' => '30',
                      'wrapchars'   => '998'
                     );
                    if($smtp_type != 'Default')
                        $config['smtp_crypto'] = $smtp_type;

                        $this->load->library('email', $config);
                    }
                } /*** End of If Smtp== 1 **/

                if (isset($send_email) && $send_email!= "") {
                    $from = $send_email;
                }
                $this->email->from($from, $mask);
                $this->email->to($to);
                $this->email->subject($subject);
                $this->email->message($message);
                if ($html == 1) {
                    $this->email->set_mailtype('html');
                }
                if ($attachement!="") {
                    $this->email->attach($attachement);
                }

                if ($this->email->send()) {
                    return true;
                } else {

                    if($test_mail==1) {
                        return $this->email->print_debugger();
                    } else {
                        return false;
                    }
                }                
            }

            if($email_sending_option == 'php_mail')
            {
                $from = get_domain_only(base_url());
                $from = "support@".$from;
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= "From: {$from}" . "\r\n";
                if(mail($to, $subject, $message, $headers))
                    return true;
                else
                    return false;
            }



        } else {
            return false;
        }
    }


    public function download_page_loader()
    {
        $this->load->view('page/download');
    }
    public function sign_up()
    {
        $signup_form = $this->config->item('enable_signup_form');

        if($signup_form == '0') 
        {
            return $this->login_page();
        }
        $data['num1']=$this->_random_number_generator(1);
        $data['num2']=$this->_random_number_generator(1);
        $captcha= $data['num1']+ $data['num2'];
        $this->session->set_userdata("sign_up_captcha",$captcha);
        
        $data["page_title"] = $this->lang->line("Sign Up");

        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_file_path = "views/site/".$current_theme."/sign_up.php";
        if(file_exists(APPPATH.$body_file_path))
            $body_load = "site/".$current_theme."/sign_up";
        else
            $body_load = "site/modern/sign_up";

        $data["body"] = $body_load;
        $this->_subscription_viewcontroller($data);
    }

    public function affiliate_commission($affiliate_id=0,$userid=0,$event='',$package_price=0)
    {
        // if($affiliate_id==0 || $userid==0 || $event=='') exit;

        $this->db->trans_start();
        // Individual Commission for affilate
        $check_affiliate_special = $this->basic->get_data("affiliate_users",['where'=>['id'=>$affiliate_id,'status'=>'1']]);
        $has_special_commission = isset($check_affiliate_special[0]['is_overwritten']) ? $check_affiliate_special[0]['is_overwritten']:'0';
        $is_affiliate_special_signup_commission = isset($check_affiliate_special[0]['is_signup_commission']) ? $check_affiliate_special[0]['is_signup_commission']:'0';
        $is_affiliate_special_payment_commission = isset($check_affiliate_special[0]['is_payment']) ? $check_affiliate_special[0]['is_payment']:'0';
        $is_affiliate_special_payment_type = isset($check_affiliate_special[0]['payment_type']) ? $check_affiliate_special[0]['payment_type']:"";

        // Common Payment for all affiliate
        $generic_signup_commission = $this->basic->get_data("affiliate_payment_settings");
        $is_generic_signup_commission = isset($generic_signup_commission[0]['signup_commission']) ? $generic_signup_commission[0]['signup_commission']:0;
        $is_generic_payment_commission = isset($generic_signup_commission[0]['payment_commission']) ? $generic_signup_commission[0]['payment_commission']:0;
        $generic_payment_type = isset($generic_signup_commission[0]['payment_type']) ? $generic_signup_commission[0]['payment_type']:"";

        $commission_amount = 0;

        $special_payment_data = [];
        $special_payment_data['affiliate_id'] = $affiliate_id;
        $special_payment_data['user_id'] = $userid;
        $special_payment_data['event'] = $event;
        $special_payment_data['event_date'] = date("Y-m-d");
        // echo $check_affiliate_special[0]['fixed_amount'];exit;   
        if($has_special_commission =='1') {
            
            if(isset($event) && $event == "signup") {
                if($is_affiliate_special_signup_commission == '1') {
                    $commission_amount = isset($check_affiliate_special[0]['signup_amount']) ? $check_affiliate_special[0]['signup_amount']:0;
                }
                else if($is_generic_signup_commission == '1') {
                    $commission_amount = isset($generic_signup_commission[0]['sign_up_amount']) ? $generic_signup_commission[0]['sign_up_amount']:0;
                }

            } else if(isset($event) && $event == "payment"){

                if($is_affiliate_special_payment_commission == '1') {

                    if(isset($is_affiliate_special_payment_type) && $is_affiliate_special_payment_type == 'fixed') {

                        if(isset($check_affiliate_special[0]['fixed_amount']) && !empty($check_affiliate_special[0]['fixed_amount'])) {
                            $commission_amount = $check_affiliate_special[0]['fixed_amount'];

                        } else if(isset($generic_signup_commission[0]['fixed_amount']) && !empty($generic_signup_commission[0]['fixed_amount'])) {
                            $commission_amount = $generic_signup_commission[0]['fixed_amount'];
                        }
                    } else if(isset($is_affiliate_special_payment_type) && $is_affiliate_special_payment_type == 'percentage') {

                        if(isset($check_affiliate_special[0]['percentage_amount']) && !empty($check_affiliate_special[0]['percentage_amount'])) {

                            $percentage_for_affiliate = $check_affiliate_special[0]['percentage_amount'];

                        } else if(isset($generic_signup_commission[0]['percentage']) && !empty($generic_signup_commission[0]['percentage'])) {

                            $percentage_for_affiliate = $generic_signup_commission[0]['percentage'];

                        } else {
                            $percentage_for_affiliate = 0;
                        }


                        if($percentage_for_affiliate > 0) {
                            $commission_amount = ($package_price * $percentage_for_affiliate) / 100;
                        }
                    }
                } else if($is_generic_payment_commission == '1') {

                    if($generic_payment_type == 'fixed') {
                        $commission_amount = isset($generic_signup_commission[0]['fixed_amount']) ? $generic_signup_commission[0]['fixed_amount']:0;

                    } else if($generic_payment_type == 'percentage') {
                        $percentage_for_affiliate = isset($generic_signup_commission[0]['percentage']) ? $generic_signup_commission[0]['percentage']:0;
                        if($percentage_for_affiliate > 0) {
                            $commission_amount = ($package_price * $percentage_for_affiliate) / 100;
                        }
                    }
                } 
            }

            $special_payment_data['amount'] = $commission_amount;

            if($commission_amount > 0) {
                $this->basic->insert_data("affiliate_earning_history",$special_payment_data);
                $affiliate_total_earn = $check_affiliate_special[0]['total_earn'] + $commission_amount;
                $this->basic->update_data("affiliate_users",['id'=>$affiliate_id],['total_earn'=>$affiliate_total_earn]);
            }

        } else if(!empty($generic_signup_commission)) {
            if($event == "signup" && $is_generic_signup_commission == '1') {
                $commission_amount = isset($generic_signup_commission[0]['sign_up_amount']) ? $generic_signup_commission[0]['sign_up_amount']:0;

            } else if($event == "payment" && $is_generic_payment_commission == '1'){

                if($generic_payment_type == 'fixed') {
                    $commission_amount = isset($generic_signup_commission[0]['fixed_amount']) ? $generic_signup_commission[0]['fixed_amount']:0;

                } else if($generic_payment_type == 'percentage') {
                    $percentage_for_affiliate = isset($generic_signup_commission[0]['percentage']) ? $generic_signup_commission[0]['percentage']:0;
                    $commission_amount = ($package_price * $percentage_for_affiliate) / 100;
                }
            }

            $special_payment_data['amount'] = $commission_amount;

            if($commission_amount > 0) {
                $this->basic->insert_data("affiliate_earning_history",$special_payment_data);
                $affiliate_total_earn = $check_affiliate_special[0]['total_earn'] + $commission_amount;
                $this->basic->update_data("affiliate_users",['id'=>$affiliate_id],['total_earn'=>$affiliate_total_earn]);
            }

        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Database error. Something went wrong.')));
            exit();
        }
    }

    public function sign_up_action()
    {
        $enable_signup_activation = $this->config->item('enable_signup_activation');
        if($enable_signup_activation == '') $enable_signup_activation='1';

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        if($_POST) {
            $this->form_validation->set_rules('name', '<b>'.$this->lang->line("name").'</b>', 'trim|required');
            $this->form_validation->set_rules('email', '<b>'.$this->lang->line("email").'</b>', 'trim|required|valid_email|is_unique[users.email]');
            // $this->form_validation->set_rules('mobile', '<b>'.$this->lang->line("mobile").'</b>', 'trim');
            $this->form_validation->set_rules('password', '<b>'.$this->lang->line("password").'</b>', 'trim|required');
            $this->form_validation->set_rules('confirm_password', '<b>'.$this->lang->line("confirm password").'</b>', 'trim|required|matches[password]');
            $this->form_validation->set_rules('captcha', '<b>'.$this->lang->line("captcha").'</b>', 'trim|required|integer');

            if($this->form_validation->run() == FALSE)
            {
                $this->sign_up();
            }
            else
            {
                $this->csrf_token_check();
                $captcha = $this->input->post('captcha', TRUE);
                if($captcha!=$this->session->userdata("sign_up_captcha"))
                {
                    $this->session->set_userdata("sign_up_captcha_error",$this->lang->line("invalid captcha"));
                    return $this->sign_up();

                }

                $name = strip_tags($this->input->post('name', TRUE));
                $email = $this->input->post('email', TRUE);
                // $mobile = $this->input->post('mobile', TRUE);
                $password = $this->input->post('password', TRUE);
                
                $affiliate_id = $this->input->cookie("affiliate_id");

                // affiliator section
                if(isset($affiliate_id) && !empty($affiliate_id)) {

                    $convertidintobinary = pack("H*", $affiliate_id);
                    $explode_binarycontactid = explode("-", $convertidintobinary);
                    $affiliate_id = $explode_binarycontactid[0];
                }
                // affiliator section end

                if($affiliate_id == '') $affiliate_id = 0;
                // $this->db->trans_start();

                $default_package=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"1")));

                if(is_array($default_package) && array_key_exists(0, $default_package))
                {
                    $validity=$default_package[0]["validity"];
                    $package_id=$default_package[0]["id"];

                    $to_date=date('Y-m-d');
                    $expiry_date=date("Y-m-d",strtotime('+'.$validity.' day',strtotime($to_date)));
                }

                $code = $this->_random_number_generator();
                $data = array(
                    'name' => $name,
                    'email' => $email,
                    // 'mobile' => $mobile,
                    'password' => md5($password),
                    'user_type' => 'Member',
                    'status' => '0',
                    'activation_code' => $code,
                    'expired_date'=>$expiry_date,
                    'package_id'=>$package_id,
                    'affiliate_id' => $affiliate_id,
                    );
                if($enable_signup_activation=='0') $data['status']='1';

                if ($this->basic->insert_data('users', $data)) {

                    if($this->addon_exist("affiliate_system")) {
                        if($affiliate_id != 0) {

                            $userid_through_affiliate = $this->db->insert_id();
                            $visitor_ip = $this->real_ip();
                            
                            $visitors_data = [];
                            $visitors_data['affiliate_id'] = $affiliate_id;
                            $visitors_data['user_id'] = $userid_through_affiliate;
                            $visitors_data['type'] = 'signup';
                            $visitors_data['ip_address'] = $visitor_ip;
                            $visitors_data['clicked_time'] = date("Y-m-d H:i:s");
                            $this->basic->insert_data('affiliate_visitors_action', $visitors_data);

                            $this->affiliate_commission($affiliate_id,$userid_through_affiliate,"signup");
                        }
                    }

                    $mail_service_id = $this->config->item('mail_service_id');
                    $system_short_name= $this->config->item('product_short_name');
                    $mailchimp_list_tag="Sign up - {$system_short_name}";

                    if($mail_service_id!="")
                    $this->send_email_to_autoresponder($mail_service_id, $email,$name,'','singnup','0',$mailchimp_list_tag);

                    //email to user
                    if($enable_signup_activation=='1')
                    {
                        $email_template_info = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>"signup_activation")),array('subject','message'));
                        $url = site_url()."home/account_activation";
                        $url_final = "<a href='".$url."' target='_BLANK'>".$url."</a>";
                        $productname = $this->config->item('product_name');

                        if(isset($email_template_info[0]) && $email_template_info[0]['subject'] != '' && $email_template_info[0]['message'] != '')
                        {
                            $subject = str_replace('#APP_NAME#',$productname,$email_template_info[0]['subject']);
                            $message = str_replace(array("#APP_NAME#","#ACTIVATION_URL#","#ACCOUNT_ACTIVATION_CODE#"),array($productname,$url_final,$code),$email_template_info[0]['message']);
                            // echo "Database Has data"; exit();

                        } else
                        {
                            $subject = $productname." | Account activation";
                            $message = "<p>".$this->lang->line("to activate your account please perform the following steps")."</p>
                                        <ol>
                                            <li>".$this->lang->line("go to this url").":".$url_final."</li>
                                            <li>".$this->lang->line("enter this code").":".$code."</li>
                                            <li>".$this->lang->line("activate your account")."</li>
                                        </ol>";
                        }

                        $from = $this->config->item('institute_email');
                        $to = $email;
                        $mask = $this->config->item("product_name");
                        $html = 1;

                        $this->_mail_sender($from, $to, $subject, $message, $mask, $html);

                        $this->session->set_userdata('reg_success',1);
                        return $this->sign_up();
                    }
                    else return $this->login_page();
                }

            }

        }
    }

    public function account_activation()
    {
        $data["page_title"] = $this->lang->line("Account Activation");

        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_file_path = "views/site/".$current_theme."/account_activation.php";
        if(file_exists(APPPATH.$body_file_path))
            $body_load = "site/".$current_theme."/account_activation";
        else
            $body_load = "site/modern/account_activation";

        $data["body"] = $body_load;
        $this->_subscription_viewcontroller($data);
    }

    public function account_activation_action()
    {
        if ($_POST) {
            $code=trim($this->input->post('code', true));
            $email=$this->input->post('email', true);

            $table='users';
            $where['where']=array('activation_code'=>$code,'email'=>$email,'status'=>"0");
            $select=array('id');

            $result=$this->basic->get_data($table, $where, $select);

            if (empty($result)) {
                echo 0;
            } else {
                foreach ($result as $row) {
                    $user_id=$row['id'];
                }

                $this->basic->update_data('users', array('id'=>$user_id), array('status'=>'1'));
                echo 2;

            }
        }
    }


    public function email_contact()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        if ($_POST)
        {
            $redirect_url=site_url("home#contact");

            $this->form_validation->set_rules('email',                    '<b>'.$this->lang->line("email").'</b>',              'trim|required|valid_email');
            $this->form_validation->set_rules('subject',                  '<b>'.$this->lang->line("message subject").'</b>',            'trim|required');
            $this->form_validation->set_rules('message',                  '<b>'.$this->lang->line("message").'</b>',            'trim|required');
            $this->form_validation->set_rules('captcha',                  '<b>'.$this->lang->line("captcha").'</b>',            'trim|required|integer');

            if ($this->form_validation->run() == false)
            {
                return $this->index();
            }
            else
            {
                $captcha = $this->input->post('captcha', TRUE);

                if($captcha!=$this->session->userdata("contact_captcha"))
                {
                    $this->session->set_userdata("contact_captcha_error",$this->lang->line("invalid captcha"));
                    redirect($redirect_url, 'location');
                    exit();
                }


                $email = $this->input->post('email', true);
                $subject = $this->config->item("product_name")." | ".$this->input->post('subject', true);
                $message = $this->input->post('message', true);
                $message=$message."<br/><br/>".$this->lang->line("The email was sent by"). ": ".$email;

                $this->_mail_sender($from = $email, $to = $this->config->item("institute_email"), $subject, $message, $this->config->item("product_name"),$html=1);
                $this->session->set_userdata('mail_sent', 1);

                redirect($redirect_url, 'location');
            }
        }
    }

    public function privacy_policy()
    {
         $data['page_title'] = 'Privacy Policy';
         $current_theme = $this->config->item('current_theme');
         if($current_theme == '') $current_theme = 'modern';
         $body_file_path = "views/site/".$current_theme."/privacy_policy.php";
         if(file_exists(APPPATH.$body_file_path))
             $body_load = "site/".$current_theme."/privacy_policy";
         else
             $body_load = "site/modern/privacy_policy";
         $data['body'] = $body_load;
         $this->_front_viewcontroller($data);
    }

    public function terms_use()
    {
         $data['page_title'] = 'Terms of Use';
         $current_theme = $this->config->item('current_theme');
         if($current_theme == '') $current_theme = 'modern';
         $body_file_path = "views/site/".$current_theme."/terms_use.php";
         if(file_exists(APPPATH.$body_file_path))
             $body_load = "site/".$current_theme."/terms_use";
         else
             $body_load = "site/modern/terms_use";
         $data['body'] = $body_load;
         $this->_front_viewcontroller($data);
    }

    public function gdpr()
    {
         $data['page_title'] = 'GDPR';
         $current_theme = $this->config->item('current_theme');
         if($current_theme == '') $current_theme = 'modern';
         $body_file_path = "views/site/".$current_theme."/gdpr.php";
         if(file_exists(APPPATH.$body_file_path))
             $body_load = "site/".$current_theme."/gdpr";
         else
             $body_load = "site/modern/gdpr";
         $data['body']=$body_load;
         $this->_front_viewcontroller($data);
    }

    public function allow_cookie()
    {
        $this->session->set_userdata('allow_cookie','yes');
        // redirect($_SERVER['HTTP_REFERER'],'location');
    }

    //=========================WEBSITE FUNCTIOS=======================
    //================================================================




    //==========================================================================
    //=======================USAGE LOG & LICENSE FUNCTIONS======================
    public function _insert_usage_log($module_id=0,$usage_count=0,$user_id=0)
    {

        if($module_id==0 || $usage_count==0) return false;
        if($user_id==0) $user_id=$this->session->userdata("user_id");
        if($user_id==0 || $user_id=="") return false;

        $usage_month=date("n");
        $usage_year=date("Y");
        $where=array("module_id"=>$module_id,"user_id"=>$user_id,"usage_month"=>$usage_month,"usage_year"=>$usage_year);

        $insert_data=array("module_id"=>$module_id,"user_id"=>$user_id,"usage_month"=>$usage_month,"usage_year"=>$usage_year,"usage_count"=>$usage_count);

        if($this->basic->is_exist("usage_log",$where))
        {
            $this->db->set('usage_count', 'usage_count+'.$usage_count, FALSE);
            $this->db->where($where);
            $this->db->update('usage_log');
        }
        else $this->basic->insert_data("usage_log",$insert_data);

        return true;
    }

    public function _delete_usage_log($module_id=0,$usage_count=0,$user_id=0)
    {
        if($module_id==0 || $usage_count==0) return false;
        if($user_id==0) $user_id=$this->session->userdata("user_id");
        if($user_id==0 || $user_id=="") return false;

        $usage_month=date("n");
        $usage_year=date("Y");

        if($this->basic->is_exist("modules",array("id"=>$module_id,"extra_text"=>""),"id"))
        {
            $existing_info = $this->basic->get_data('usage_log',array('where'=>array('module_id'=>$module_id,'usage_count >='=>1,'user_id'=>$user_id)));
            if(!empty($existing_info))
            {
                $where=array("id"=>$existing_info[0]['id'],"user_id"=>$user_id);
                $this->db->set('usage_count', 'usage_count-'.$usage_count, FALSE);
                $this->db->where($where);
                $this->db->update('usage_log');
            }
        }
        else
        {
            $where=array("module_id"=>$module_id,"user_id"=>$user_id,"usage_month"=>$usage_month,"usage_year"=>$usage_year);
            $insert_data=array("module_id"=>$module_id,"user_id"=>$user_id,"usage_month"=>$usage_month,"usage_year"=>$usage_year,"usage_count"=>$usage_count);

            if($this->basic->is_exist("usage_log",$where))
            {
                $this->db->set('usage_count', 'usage_count-'.$usage_count, FALSE);
                $this->db->where($where);
                $this->db->update('usage_log');
            }
        }

        return true;
    }

    public function _check_usage($module_id=0,$request=0,$user_id=0)
    {
        if($module_id==0 || $request==0) return "0";
        if($user_id==0) $user_id=$this->session->userdata("user_id");
        if($user_id==0 || $user_id=="") return false;

        if($this->basic->is_exist("modules",array("id"=>$module_id,"extra_text"=>""),"id")) // not monthly limit modules
        {
            $this->db->select_sum('usage_count');
            $this->db->where('user_id', $user_id);
            $this->db->where('module_id', $module_id);
            $info = $this->db->get('usage_log')->result_array(); 

            $usage_count=0;
            if(isset($info[0]["usage_count"]))
            $usage_count=$info[0]["usage_count"];
        }
        else
        {
            $usage_month=date("n");
            $usage_year=date("Y");
            $info=$this->basic->get_data("usage_log",$where=array("where"=>array("usage_month"=>$usage_month,"usage_year"=>$usage_year,"module_id"=>$module_id,"user_id"=>$user_id)));
            $usage_count=0;
            if(isset($info[0]["usage_count"]))
            $usage_count=$info[0]["usage_count"];
        }

        

        $monthly_limit=array();
        $bulk_limit=array();
        $module_ids=array();

        if($this->session->userdata("package_info")!="")
        {
            $package_info=$this->session->userdata("package_info");
            if($this->session->userdata('user_type') == 'Admin') return "1";
        }
        else
        {
            $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
            $package_info=array();
            if(array_key_exists(0, $package_data))
            $package_info=$package_data[0];
            if($package_info['user_type'] == 'Admin') return "1";
        }

        if(isset($package_info["bulk_limit"]))    $bulk_limit=json_decode($package_info["bulk_limit"],true);
        if(isset($package_info["monthly_limit"])) $monthly_limit=json_decode($package_info["monthly_limit"],true);
        if(isset($package_info["module_ids"]))    $module_ids=explode(',', $package_info["module_ids"]);

        $return = "0";
        if(in_array($module_id, $module_ids) && $bulk_limit[$module_id] > 0 && $bulk_limit[$module_id]<$request)
         $return = "2"; // bulk limit crossed | 0 means unlimited
        else if(in_array($module_id, $module_ids) && $monthly_limit[$module_id] > 0 && $monthly_limit[$module_id]<($request+$usage_count))
         $return = "3"; // montly limit crossed | 0 means unlimited
        else  $return = "1"; //success

        return $return;
    }

    public function print_limit_message($module_id=0,$request=0)
    {
        $status=$this->_check_usage($module_id,$request);
        if($status=="2")
        {
            echo $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            exit();
        }
        else if($status=="3")
        {
            echo $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            exit();
        }

    }

    public function member_validity()
    {
        if($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') != 'Admin') {
            $where['where'] = array('id'=>$this->session->userdata('user_id'));
            $user_expire_date = $this->basic->get_data('users',$where,$select=array('expired_date'));
            $expire_date = strtotime($user_expire_date[0]['expired_date']);
            $current_date = strtotime(date("Y-m-d"));
            $package_data=$this->basic->get_data("users",$where=array("where"=>array("users.id"=>$this->session->userdata("user_id"))),$select="package.price as price",$join=array('package'=>"users.package_id=package.id,left"));
            if(is_array($package_data) && array_key_exists(0, $package_data))
            $price=$package_data[0]["price"];
            if($price=="Trial") $price=1;
            if ($expire_date < $current_date && ($price>0 && $price!=""))
            redirect('payment/buy_package','Location');
        }
    }

    public function important_feature()
    {
        if(file_exists(APPPATH.'config/licence.txt') && file_exists(APPPATH.'core/licence.txt'))
        {
            $config_existing_content = file_get_contents(APPPATH.'config/licence.txt');
            $config_decoded_content = json_decode($config_existing_content, true);

            $core_existing_content = file_get_contents(APPPATH.'core/licence.txt');
            $core_decoded_content = json_decode($core_existing_content, true);

            if($config_decoded_content['is_active'] != md5($config_decoded_content['purchase_code']) || $core_decoded_content['is_active'] != md5(md5($core_decoded_content['purchase_code'])))
            {
                redirect("home/credential_check", 'Location');
            }
        } 
        else 
        {
            redirect("home/credential_check", 'Location');
        }

    }
    public function credential_check($secret_code=0)
    {
        if($this->is_demo=='1') redirect('home/access_forbidden','refresh');

        $permissio = 0;
        if($this->session->userdata("user_type")=="Admin") $permissio = 1;
        else $permissio = 0;

        if($permissio == 0) redirect('home/access_forbidden', 'location');

        $data["page_title"] = $this->lang->line("Credential Check");

        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $body_file_path = "views/site/".$current_theme."/credential_check.php";
        if(file_exists(APPPATH.$body_file_path))
            $body_load = "site/".$current_theme."/credential_check";
        else
            $body_load = "site/modern/credential_check";

        $data['body'] = $body_load;
        $this->_subscription_viewcontroller($data);
    }

    public function credential_check_action()
    {
        if($this->is_demo=='1') redirect('home/access_forbidden','refresh');
        $domain_name = $this->input->post("domain_name",true);
        $purchase_code = $this->input->post("purchase_code",true);
        $only_domain = get_domain_only($domain_name);

       $response=$this->code_activation_check_action($purchase_code,$only_domain);
       if(file_exists(APPPATH.'core/licence_type.txt'))
          $this->license_check_action();
       echo $response;

    }

    public function code_activation_check_action($purchase_code,$only_domain,$periodic=0)
    {
        $url = "http://xeroneit.net/development/envato_license_activation/purchase_code_check.php?purchase_code={$purchase_code}&domain={$only_domain}&item_name=XeroChat";

        $credentials = $this->get_general_content_with_checking($url);
        $decoded_credentials = json_decode($credentials,true);

        if(isset($decoded_credentials['error']))
        {
            $url = "https://mostofa.club/development/envato_license_activation/purchase_code_check.php?purchase_code={$purchase_code}&domain={$only_domain}&item_name=XeroChat";
            $credentials = $this->get_general_content_with_checking($url);
            $decoded_credentials = json_decode($credentials,true);
        }

        if(!isset($decoded_credentials['error']))
        {
            $content = json_decode($decoded_credentials['content'],true);
            if($content['status'] == 'success')
            {
                $content_to_write = array(
                    'is_active' => md5($purchase_code),
                    'purchase_code' => $purchase_code,
                    'item_name' => $content['item_name'],
                    'buy_at' => $content['buy_at'],
                    'licence_type' => $content['license'],
                    'domain' => $only_domain,
                    'checking_date'=>date('Y-m-d')
                    );
                $config_json_content_to_write = json_encode($content_to_write);
                file_put_contents(APPPATH.'config/licence.txt', $config_json_content_to_write, LOCK_EX);

                $content_to_write['is_active'] = md5(md5($purchase_code));
                $core_json_content_to_write = json_encode($content_to_write);
                file_put_contents(APPPATH.'core/licence.txt', $core_json_content_to_write, LOCK_EX);


                // added by mostofa 06/03/2017
                $license_type = $content['license'];
                if($license_type != 'Regular License')
                    $str = $purchase_code."_double";
                else
                    $str = $purchase_code."_single";

                $encrypt_method = "AES-256-CBC";
                $secret_key = 't8Mk8fsJMnFw69FGG5';
                $secret_iv = '9fljzKxZmMmoT358yZ';
                $key = hash('sha256', $secret_key);
                $string = $str;
                $iv = substr(hash('sha256', $secret_iv), 0, 16);
                $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                $encoded = base64_encode($output);
                file_put_contents(APPPATH.'core/licence_type.txt', $encoded, LOCK_EX);

                return json_encode("success");

            } else if($content['status'] == 'error'){
                if(file_exists(APPPATH.'core/licence.txt')) unlink(APPPATH.'core/licence.txt');
                return json_encode($content);
            }
        }
        else
        {
            if($periodic == 1)
                return json_encode("success");
            else
            {
                $response['reason'] = "cURL is not working properly, please contact with your hosting provider.";
                return json_encode($response);
            }
        }
    }

    public function periodic_check(){

        $today= date('d');

        if($today%7==0){

            if(file_exists(APPPATH.'config/licence.txt') && file_exists(APPPATH.'core/licence.txt')){
                $config_existing_content = file_get_contents(APPPATH.'config/licence.txt');
                $config_decoded_content = json_decode($config_existing_content, true);
                $last_check_date= $config_decoded_content['checking_date'];
                $purchase_code  = $config_decoded_content['purchase_code'];
                $base_url = base_url();
                $domain_name  = get_domain_only($base_url);

                if( strtotime(date('Y-m-d')) != strtotime($last_check_date)){
                    $this->code_activation_check_action($purchase_code,$domain_name,$periodic=1);
                }
            }
        }
    }


    public function license_check()
    {
        $file_data = file_get_contents(APPPATH . 'core/licence.txt');
        $file_data_array = json_decode($file_data, true);

        $purchase_code = $file_data_array['purchase_code'];

        $url = "http://xeroneit.net/development/envato_license_activation/regular_or_extended_check_r.php?purchase_code={$purchase_code}";

        $credentials = $this->get_general_content_with_checking($url);
        $response = json_decode($credentials, true);
        $response = json_decode($response['content'],true);

        if(!isset($response['status']) || $response['status'] == 'error')
        {
            $url="https://mostofa.club/development/envato_license_activation/regular_or_extended_check_r.php?purchase_code={$purchase_code}";            
            $credentials = $this->get_general_content_with_checking($url);
            $response = json_decode($credentials, true);
            $response = json_decode($response['content'],true);
        }

        if(isset($response['status']))
        {
            if($response['status'] == 'error')
            {
                $status = 'single';
            }
            else if($response['status'] == 'success' && $response['license'] == 'Regular License')
            {
                $status = 'single';
            }
            else
            {
                $status = 'double';
            }
            $content = $purchase_code."_".$status;

            $encrypt_method = "AES-256-CBC";
            $secret_key = 't8Mk8fsJMnFw69FGG5';
            $secret_iv = '9fljzKxZmMmoT358yZ';
            $key = hash('sha256', $secret_key);
            $string = $content;
            $iv = substr(hash('sha256', $secret_iv), 0, 16);
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $encoded = base64_encode($output);

            file_put_contents(APPPATH.'core/licence_type.txt', $encoded, LOCK_EX);
        }


    }

    public function license_check_action()
    {
        $encoded = file_get_contents(APPPATH . 'core/licence_type.txt');
        $encrypt_method = "AES-256-CBC";
        $secret_key = 't8Mk8fsJMnFw69FGG5';
        $secret_iv = '9fljzKxZmMmoT358yZ';
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $decoded = openssl_decrypt(base64_decode($encoded), $encrypt_method, $key, 0, $iv);

        $decoded = explode('_', $decoded);
        $decoded = array_pop($decoded);
        $this->session->set_userdata('license_type',$decoded);
    }

    public function php_info()
    {
        if($this->session->userdata('user_type')== 'Admin')
        echo phpinfo();
        else redirect('home/access_forbidden', 'location');
    }
    //=======================USAGE LOG & LICENSE FUNCTIONS======================
    //==========================================================================




    //================================================================
    //========================= ADDON FUNCTIONS ======================
    //loads language files of addons
    protected function language_loader_addon()
    {    
        
        $controller_name=strtolower($this->uri->segment(1));
        $path_without_filename="application/modules/".$controller_name."/language/".$this->language."/";
        if(file_exists($path_without_filename.$controller_name."_lang.php"))
        {
            $filename=$controller_name;
            $this->lang->load($filename,$this->language,FALSE,TRUE,$path_without_filename);
        }

    }

    // delete any direcory with it childs even it is not empty
    protected function delete_directoryX($dirPath="") 
    {
        if (!is_dir($dirPath)) 
        return false;

        if(substr($dirPath, strlen($dirPath) - 1, 1) != '/') $dirPath .= '/';
        
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach($files as $file) 
        {
            if(is_dir($file)) $this->delete_directory($file);             
            else @unlink($file);            
        }
        rmdir($dirPath);
    }

    protected function delete_directory($dirPath="")
    {
        if (!is_dir($dirPath)) 
        return false;

        $files = new DirectoryIterator($dirPath);
        foreach ($files as $file)
        {
            // check if not . or ..
            if (!$file->isDot())
            {
                $file->isDir() ? $this->delete_directory($file->getPathname()) : unlink($file->getPathname());
            }
        }
        rmdir($dirPath);
        return;
    }

    // takes addon controller path as input and extract add on data from comment block
    protected function get_addon_data($path="")
    {
        $path=str_replace('\\','/',$path);
        $tokens=token_get_all(file_get_contents($path));
        $addon_data=array();

        $addon_path=explode('/', $path);
        $controller_name=array_pop($addon_path);
        array_pop($addon_path);
        $addon_path=implode('/',$addon_path);

        $comments = array();
        foreach($tokens as $token) 
        {
            if($token[0] == T_COMMENT || $token[0] == T_DOC_COMMENT) 
            {       
                $comments[] = isset( $token[1]) ?  $token[1] : "";
            } 
        }
        $comment_str=isset($comments[0]) ? $comments[0] : "";
        
        preg_match( '/^.*?addon name:(.*)$/mi', $comment_str, $match); 
        $addon_data['addon_name'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?unique name:(.*)$/mi', $comment_str, $match); 
        $addon_data['unique_name'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '#modules:(.*?)Project ID#si', $comment_str, $match); 
        $addon_data['modules'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?project id:(.*)$/mi', $comment_str, $match); 
        $addon_data['project_id'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?addon uri:(.*)$/mi', $comment_str, $match); 
        $addon_data['addon_uri'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?author:(.*)$/mi', $comment_str, $match); 
        $addon_data['author'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?author uri:(.*)$/mi', $comment_str, $match); 
        $addon_data['author_uri'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?version:(.*)$/mi', $comment_str, $match); 
        $addon_data['version'] = isset($match[1]) ? trim($match[1]) : "1.0";

        preg_match( '/^.*?description:(.*)$/mi', $comment_str, $match); 
        $addon_data['description'] = isset($match[1]) ? trim($match[1]) : "";

        $addon_data['controller_name'] = isset($controller_name) ? trim($controller_name) : "";

        if(file_exists($addon_path.'/install.txt'))
        $addon_data['installed']='0';
        else $addon_data['installed']='1';  

        return $addon_data;
    }

    // checks purchase code , returns boolean
    protected function addon_credential_check($purchase_code="",$item_name="")
    {
        $purchase_code = trim($purchase_code);
        if($purchase_code=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on purchase code has not been provided.')));
            exit();
        }

        $item_name=urlencode($item_name);
        $only_domain=get_domain_only(site_url());
        $url = "http://xeroneit.net/development/envato_license_activation/purchase_code_check.php?purchase_code={$purchase_code}&domain={$only_domain}&item_name=XeroChat-{$item_name}";

        $credentials = $this->get_general_content_with_checking($url);
        $decoded_credentials = json_decode($credentials,true);

        if(isset($decoded_credentials['error']))
        {
            $url = "https://mostofa.club/development/envato_license_activation/purchase_code_check.php?purchase_code={$purchase_code}&domain={$only_domain}&item_name=XeroChat-{$item_name}";
            $credentials = $this->get_general_content_with_checking($url);
            $decoded_credentials = json_decode($credentials,true);
        }

        if(!isset($decoded_credentials['error'])) 
        {
            $content = json_decode($decoded_credentials['content'],true);
            if($content['status'] != 'success')            
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line('Purchase code is not valid or already used.')));
                exit();
            }
        }  
        else
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Something went wrong. cURL is not working.')));
            exit();
        }
    }

    // validataion of addon data
    protected function check_addon_data($addon_data=array())
    {
        if(!isset($addon_data['unique_name']) || $addon_data['unique_name']=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on unique name has not been provided.')));
            exit();
        }
        
        if(!$this->is_unique_check("addon_check",$addon_data['unique_name']))  //  unique name must be unique
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on is already active. Duplicate unique name found.')));
            exit();
        }
    }

    // inserts data to add_ons table + modules + menu + menuchild1 + removes install.txt, returns json status,message
    protected function register_addon($addon_controller_name="",$sidebar=array(),$sql=array(),$purchase_code="",$default_module_name="")
    {
        if($this->session->userdata('user_type') != 'Admin')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
            exit();
        }   

        if($this->is_demo == '1')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
            exit();
        }     

        if($addon_controller_name=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on controller has not been provided.')));
            exit();
        }
        
        $path=APPPATH."modules/".strtolower($addon_controller_name)."/controllers/".$addon_controller_name.".php"; // path of addon controller
        $install_txt_path=APPPATH."modules/".strtolower($addon_controller_name)."/install.txt"; // path of install.txt
        if(!file_exists($path)) 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on controller not found.')));
            exit();
        }

        $addon_data=$this->get_addon_data($path);

        $this->check_addon_data($addon_data);

        try 
        {
            $this->db->trans_start();
            
            // addon table entry
            $this->basic->insert_data("add_ons",array("add_on_name"=>$addon_data['addon_name'],"unique_name"=>$addon_data["unique_name"],"version"=>$addon_data["version"],"installed_at"=>date("Y-m-d H:i:s"),"purchase_code"=>$purchase_code,"module_folder_name"=>strtolower($addon_controller_name),"project_id"=>$addon_data["project_id"]));
            $add_ons_id=$this->db->insert_id();

            $parent_module_id="";
            $modules = isset($addon_data['modules']) ? json_decode(trim($addon_data['modules']),true) : array();

            if(json_last_error() === 0 && is_array($modules))
            {
                $module_ids = array_keys($modules);
                $parent_module_id=implode(',', $module_ids);

                foreach($modules as $key => $value) 
                {
                    if(!$this->basic->is_exist("modules",array("id"=>$key))) 
                    $this->basic->insert_data("modules",array("id"=>$key,"extra_text"=>$value['extra_text'],"module_name"=>$value['module_name'],'bulk_limit_enabled'=>$value['bulk_limit_enabled'],'limit_enabled'=>$value['limit_enabled'],"add_ons_id"=>$add_ons_id,"deleted"=>"0"));
                }
            }
            
            //--------------- sidebar entry--------------------
            //-------------------------------------------------
            if(is_array($sidebar))
            foreach ($sidebar as $key => $value) 
            {
                $parent_name        = isset($value['name']) ? $value['name'] : "";
                $parent_icon        = isset($value['icon']) ? $value['icon'] : "";
                $parent_url         = isset($value['url']) ? $value['url'] : "#";
                $parent_is_external = isset($value['is_external']) ? $value['is_external'] : "0";
                $child_info         = isset($value['child_info']) ? $value['child_info'] : array();
                $have_child         = isset($child_info['have_child']) ? $child_info['have_child'] : '0';
                $only_admin         = isset($value['only_admin']) ? $value['only_admin'] : '0';
                $only_member        = isset($value['only_member']) ? $value['only_member'] : '0';
                $parent_serial      = 50;
                           
                $parent_menu=array('name'=>$parent_name,'icon'=>$parent_icon,'url'=>$parent_url,'serial'=>$parent_serial,'module_access'=>$parent_module_id,'have_child'=>$have_child,'only_admin'=>$only_admin,'only_member'=>$only_member,'add_ons_id'=>$add_ons_id,'is_external'=>$parent_is_external);
                $this->basic->insert_data('menu',$parent_menu); // parent menu entry
                $parent_id=$this->db->insert_id();

                if($have_child=='1')
                {
                    if(!empty($child_info))
                    {
                        $child = isset($child_info['child']) ? $child_info['child'] : array();
                        
                        $child_serial=0;
                        if(!empty($child))
                        foreach ($child as $key2 => $value2) 
                        {
                            $child_serial++;
                            $child_name         = isset($value2['name']) ? $value2['name'] : "";
                            $child_icon         = isset($value2['icon']) ? $value2['icon'] : "";
                            $child_url          = isset($value2['url']) ? $value2['url'] : "#";
                            $child_info_1       = isset($value2['child_info']) ? $value2['child_info'] : array();
                            $child_is_external  = isset($value2['is_external']) ? $value2['is_external'] : "0";
                            $have_child         = isset($child_info_1['have_child']) ? $child_info_1['have_child'] : '0';
                            $only_admin         = isset($value2['only_admin']) ? $value2['only_admin'] : '0';
                            $only_member        = isset($value2['only_member']) ? $value2['only_member'] : '0';
                            $module_access      = isset($value2['module_access']) ? $value2['module_access'] : '';
                            if($module_access=='') $module_access = $parent_module_id;
                                            
                            $child_menu=array('name'=>$child_name,'icon'=>$child_icon,'url'=>$child_url,'serial'=>$child_serial,'module_access'=>$module_access,'parent_id'=>$parent_id,'have_child'=>$have_child,'only_admin'=>$only_admin,'only_member'=>$only_member,'is_external'=>$child_is_external);
                            $this->basic->insert_data('menu_child_1',$child_menu); // child menu entry
                            $sub_parent_id=$this->db->insert_id();

                            if($have_child=='1')
                            {
                                if(!empty($child_info_1))
                                {
                                    $child = isset($child_info_1['child']) ? $child_info_1['child'] : array();  
                                    
                                    $child_child_serial=0;
                                    if(!empty($child))
                                    foreach ($child as $key3 => $value3) 
                                    {
                                        $child_child_serial++;
                                        $child_name         = isset($value3['name']) ? $value3['name'] : "";
                                        $child_icon         = isset($value3['icon']) ? $value3['icon'] : "";
                                        $child_url          = isset($value3['url']) ? $value3['url'] : "#";
                                        $child_is_external  = isset($value3['is_external']) ? $value3['is_external'] : "0";
                                        $have_child         = '0';
                                        $only_admin         = isset($value3['only_admin']) ? $value3['only_admin'] : '0';
                                        $only_member        = isset($value3['only_member']) ? $value3['only_member'] : '0';
                                        $module_access2     = isset($value3['module_access']) ? $value3['module_access'] : '';
                                        if($module_access2=='') $module_access2 = $module_access;
                                                        
                                        $child_menu=array('name'=>$child_name,'icon'=>$child_icon,'url'=>$child_url,'serial'=>$child_child_serial,'module_access'=>$module_access2,'parent_child'=>$sub_parent_id,'only_admin'=>$only_admin,'only_member'=>$only_member,'is_external'=>$child_is_external);
                                        $this->basic->insert_data('menu_child_2',$child_menu); // child menu entry
                                        
                                    }
                                }
                            } 
                        }
                    }
                }            

            }
            //--------------- sidebar entry--------------------
            //-------------------------------------------------

            $this->db->trans_complete();
                 

            if ($this->db->trans_status() === FALSE) 
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line('Database error. Something went wrong.')));
                exit();
            }
            else 
            {   
                
                //--------Custom SQL------------
                $this->db->db_debug = FALSE; //disable debugging for queries
                if(is_array($sql))            
                foreach ($sql as $key => $query) 
                {
                    try
                    {
                        $this->db->query($query);
                    }
                    catch(Exception $e)
                    {
                    }                    
                }
                //--------Custom SQL------------                
                @unlink($install_txt_path); // removing install.txt                
                echo json_encode(array('status'=>'1','message'=>$this->lang->line('Add-on has been activated successfully.')));
            }

        } //end of try
        catch(Exception $e)
        {
            $error = $e->getMessage();   
            echo json_encode(array('status'=>'0','message'=>$this->lang->line($error)));            
        }
    }

    // deletes data from add_ons table + modules + menu + menuchild1 + puts install.txt, returns json status,message
    protected function unregister_addon($addon_controller_name="")
    {
        if($this->session->userdata('user_type') != 'Admin')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
            exit();
        }

        if($this->is_demo == '1')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
            exit();
        }


        if($addon_controller_name=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on controller has not been provided.')));
            exit();
        }
        
        $path=APPPATH."modules/".strtolower($addon_controller_name)."/controllers/".$addon_controller_name.".php"; // path of addon controller
        $install_txt_path=APPPATH."modules/".strtolower($addon_controller_name)."/install.txt"; // path of install.txt
        if(!file_exists($path)) 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on controller not found.')));
            exit();
        }

        $addon_data=$this->get_addon_data($path);

        if(!isset($addon_data['unique_name']) || $addon_data['unique_name']=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on unique name has not been provided.')));
            exit();
        }


        try 
        {
            $this->db->trans_start();
            
            // delete addon table entry
            $get_addon=$this->basic->get_data("add_ons",array("where"=>array("unique_name"=>$addon_data['unique_name'])));
            $add_ons_id=isset($get_addon[0]['id']) ? $get_addon[0]['id'] : 0;
            if($add_ons_id>0)
            $this->basic->delete_data("add_ons",array("id"=>$add_ons_id));
            
            // delete modules table entry    
            if($add_ons_id>0)        
            $this->basic->delete_data("modules",array("add_ons_id"=>$add_ons_id));

            // delete menu+menu_child1 table entry
            $get_menu=array();
            if($add_ons_id>0)   
            $get_menu=$this->basic->get_data("menu",array("where"=>array("add_ons_id"=>$add_ons_id)));
            
            foreach($get_menu as $key => $value) 
            {
               $parent_id=isset($value['id']) ? $value['id'] : 0;
               if($parent_id>0)
               {    
                  $this->basic->delete_data("menu",array("id"=>$parent_id));
                  $this->basic->delete_data("menu_child_1",array("parent_id"=>$parent_id));
               }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) 
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line('Database error. Something went wrong.')));
                exit();
            }
            else 
            {   
                if(!file_exists($install_txt_path)) // putting install.txt
                fopen($install_txt_path, "w");

                echo json_encode(array('status'=>'1','message'=>$this->lang->line('Add-on has been deactivated successfully.')));
            }
        } 
        catch(Exception $e)
        {
            $error = $e->getMessage();   
            echo json_encode(array('status'=>'0','message'=>$this->lang->line($error)));            
        }
    }

    // deletes data from add_ons table + modules + menu + menuchild1 + custom sql + folder, returns json status,message    
    protected function delete_addon($addon_controller_name="",$sql=array())
    {
        if($this->session->userdata('user_type') != 'Admin')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
            exit();
        }

        if($this->is_demo == '1')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
            exit();
        }

        if($addon_controller_name=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on controller has not been provided.')));
            exit();
        }
        
        $path=APPPATH."modules/".strtolower($addon_controller_name)."/controllers/".$addon_controller_name.".php"; // path of addon controller
        $addon_path=APPPATH."modules/".strtolower($addon_controller_name); // path of module folder
        if(!file_exists($path)) 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on controller not found.')));
            exit();
        }

        $addon_data=$this->get_addon_data($path);

        if(!isset($addon_data['unique_name']) || $addon_data['unique_name']=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Add-on unique name has not been provided.')));
            exit();
        }


        try 
        {
            $this->db->trans_start();
            
            // delete addon table entry
            $get_addon=$this->basic->get_data("add_ons",array("where"=>array("unique_name"=>$addon_data['unique_name'])));
            $add_ons_id=isset($get_addon[0]['id']) ? $get_addon[0]['id'] : 0;
            $purchase_code=isset($get_addon[0]['purchase_code']) ? $get_addon[0]['purchase_code'] : '';
            if($add_ons_id>0)
            $this->basic->delete_data("add_ons",array("id"=>$add_ons_id));
            
            // delete modules table entry    
            if($add_ons_id>0)        
            $this->basic->delete_data("modules",array("add_ons_id"=>$add_ons_id));

            // delete menu+menu_child1 table entry
            $get_menu=array();
            if($add_ons_id>0)   
            $get_menu=$this->basic->get_data("menu",array("where"=>array("add_ons_id"=>$add_ons_id)));
            
            foreach($get_menu as $key => $value) 
            {
               $parent_id=isset($value['id']) ? $value['id'] : 0;
               if($parent_id>0)
               {    
                  $this->basic->delete_data("menu",array("id"=>$parent_id));
                  $this->basic->delete_data("menu_child_1",array("parent_id"=>$parent_id));
               }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) 
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line('Database error. Something went wrong.')));
                exit();
            }
            else 
            {   
                //--------Custom SQL------------
                $this->db->db_debug = FALSE; //disable debugging for queries
                if(is_array($sql))            
                foreach ($sql as $key => $query) 
                {
                    try
                    {
                        $this->db->query($query);
                    }
                    catch(Exception $e)
                    {
                    }                    
                }
                //--------Custom SQL------------             

                $this->delete_directory($addon_path);                  
                if($purchase_code!="")
                {
                    $item_name=strtolower($addon_controller_name);
                    $only_domain=get_domain_only(site_url());
                    $url = "http://xeroneit.net/development/envato_license_activation/delete_purchase_code.php?purchase_code={$purchase_code}&domain={$only_domain}&item_name=XeroChat-{$item_name}";
                    $credentials = $this->get_general_content_with_checking($url);
                    $response = json_decode($credentials,true);
                    if(isset($response['error']))
                    {
                        $url = "https://mostofa.club/development/envato_license_activation/delete_purchase_code.php?purchase_code={$purchase_code}&domain={$only_domain}&item_name=XeroChat-{$item_name}";
                        $this->get_general_content_with_checking($url);                    
                    }
                }
  
                echo json_encode(array('status'=>'1','message'=>$this->lang->line('add-on has been deleted successfully.')));
            }
        } 
        catch(Exception $e)
        {
            $error = $e->getMessage();   
            echo json_encode(array('status'=>'0','message'=>$this->lang->line($error)));            
        }
    }


    // check a addon or module id is usable or already used, returns boolean, true if unique
    protected function is_unique_check($type='addon_check',$value="") // type=addon_check/module_check | $value=column.value
    {
        $is_unique=false;
        if($type=="addon_check")  $is_unique=$this->basic->is_unique("add_ons",array("unique_name"=>$value),"id");
        if($type=="module_check") $is_unique=$this->basic->is_unique("modules",array("id"=>$value),"id");
        return $is_unique;
    }

    //========================= ADDON FUNCTIONS ======================
    //================================================================

   

    protected function delete_full_access()
    {
        if($this->session->userdata('user_type') == 'Admin') exit();
        if(!isset($_POST)) exit();
        $user_id=$this->session->userdata('user_id');

        $this->db->trans_start();
        $sql = "show tables;";
        $a = $this->basic->execute_query($sql);
        foreach($a as $value)
        {
            foreach($value as $table_name)
            {
                if($table_name == 'users') $this->basic->delete_data('users',array('id'=>$user_id));
                if($this->db->field_exists('user_id',$table_name))
                    $this->basic->delete_data($table_name,array('user_id'=>$user_id));
            }
        }
        $this->db->trans_complete();                

        if ($this->db->trans_status() === FALSE) 
        {
            echo $this->lang->line('Something went wrong, please try again.');            
        }
        else
        {
            $this->session->sess_destroy();
            echo 'success';        
        }

    }


    protected function scanAll($myDir){

        $dirTree = array();
        $di = new RecursiveDirectoryIterator($myDir,RecursiveDirectoryIterator::SKIP_DOTS);

        foreach (new RecursiveIteratorIterator($di) as $filename) {

            $dir = str_replace($myDir, '', dirname($filename));
            //$dir = str_replace('/', '>', substr($dir,1));

            $org_dir=str_replace("\\", "/", $dir);


            if($org_dir)
                $file_path = $org_dir. "/". basename($filename);
            else
                $file_path = basename($filename);
            $dirTree[] = $file_path;

        }

        return $dirTree;

    }

    // =========================================================================================
    //===============================MESSENGER BOT FUNCTIONS====================================
    /******88*WEBHOOK,COMMON BOT ADDON FUNCTIONS,PUBLIC CURL CALLS, CRON SUB FUNCTIONS**88******/
    public function central_webhook_callback()
    {
        $url="";
        $challenge = $this->input->get_post('hub_challenge');
        $verify_token =$this->input->get_post('hub_verify_token');
        if($this->config->item("central_webhook_verify_token") != '')
        {
            if($verify_token === $this->config->item("central_webhook_verify_token"))
            {
                echo $challenge;
                die();
            }                
        }

        $response_raw=file_get_contents("php://input");

        if(!isset($response_raw) || $response_raw=='') exit; 

        $json_response=array("response_raw"=>$response_raw);
        $response = json_decode($response_raw, true);

        if(isset($response['entry'][0]['messaging']))
        {
          
            $url=base_url()."messenger_bot/webhook_callback_main";
            if(isset($response['entry']['0']['messaging'][0]['read'])) exit; 

        } 

        else if(isset($response['entry'][0]['changes'][0]['value']['item']) && $response['entry'][0]['changes'][0]['value']['item'] == 'comment') {
            $url=base_url()."comment_automation/webhook_callback_main";

            $commenter_id = isset($response['entry'][0]['changes'][0]['value']['sender_id']) ? $response['entry'][0]['changes'][0]['value']['sender_id'] : $response['entry'][0]['changes'][0]['value']['from']['id'];
            $page_id = $response['entry'][0]['id'];

            //If activity by Page it self, then exit
            if($page_id==$commenter_id) exit;

            // 2nd level relpy is turned off
            $post_id = isset($response['entry'][0]['changes'][0]['value']['parent_id']) ? $response['entry'][0]['changes'][0]['value']['parent_id']:"";
            $parent_id_page_id_array=explode("_", $post_id);
            $parent_id_page_id=isset($parent_id_page_id_array[0]) ? $parent_id_page_id_array[0] :"";

            if($page_id!=$parent_id_page_id){ // From 2nd reply Comment. 
                exit; 
            }

            //If already replied that comment, then exit 
            $comment_id = isset($response['entry'][0]['changes'][0]['value']['comment_id']) ? $response['entry'][0]['changes'][0]['value']['comment_id']:"";
            $already_replied_comment_id = $this->basic->get_data('facebook_ex_autoreply_report',array('where'=>array('comment_id'=>$comment_id)));
            if(!empty($already_replied_comment_id)) exit;          
        }

        else if(isset($response['entry'][0]['changes'][0]['value']['item']) && $response['entry'][0]['changes'][0]['value']['item'] == 'reaction')
        {
            exit;
        }

        else if(isset($response['entry'][0]['changes'][0]['value']['item']) && $response['entry'][0]['changes'][0]['value']['item'] == 'photo') 
        {
            if(isset($response['entry'][0]['changes'][0]['value']['verb']) && $response['entry'][0]['changes'][0]['value']['verb'] == 'edited')
                exit;
            $url=base_url()."comment_automation/webhook_callback_main";
        }

        else if(isset($response['entry'][0]['changes'][0]['field']) && $response['entry'][0]['changes'][0]['field'] == 'feed') 
            $url=base_url()."comment_automation/webhook_callback_main";

        else if(isset($response['entry'][0]['changes'][0]['value']['item']) && $response['entry'][0]['changes'][0]['value']['item'] == 'status') 
            $url=base_url()."comment_automation/webhook_callback_main";
        else if(isset($response['entry'][0]['changes'][0]['value']['item']) && $response['entry'][0]['changes'][0]['value']['item'] == 'share')
            $url=base_url()."comment_automation/webhook_callback_main";

        else if(isset($response['entry'][0]['changes'][0]['field']) && $response['entry'][0]['changes'][0]['field'] == 'mentions')
            $url=base_url()."instagram_reply/webhook_callback";
        else if(isset($response['entry'][0]['changes'][0]['field']) && $response['entry'][0]['changes'][0]['field'] == 'comments')
            $url=base_url()."instagram_reply/webhook_callback";

        if($url=='') exit;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$json_response);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        $reply_response=curl_exec($ch);
    }
    
    public function send_reply_ez($access_token='',$reply='')
    {   
        $url="https://graph.facebook.com/v2.6/me/messages?access_token=$access_token";
        $ch = curl_init();
        $headers = array("Content-type: application/json");          
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);        
        
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$reply); 
 
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
        curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");   
        $st=curl_exec($ch);       
        
        $result=json_decode($st,TRUE);
        return $result;
    }

    protected function subscriber_info($access_token='',$sender_id='',$social_media_type="fb")
    {   
        if($social_media_type=='fb')
        $url = "https://graph.facebook.com/v2.6/$sender_id?access_token=$access_token&fields=id,first_name,last_name,name,profile_pic,locale,timezone,gender";
        else
        $url = "https://graph.facebook.com/v2.6/$sender_id?access_token=$access_token&fields=id,name,profile_pic";

        $result=$this->subscriber_info_curl_call($url);

        if(isset($result['error']) && $result['error']['code']==10 && $result['error']['error_subcode']==2018336){
            $result=array();
            $url = "https://graph.facebook.com/v2.6/$sender_id?access_token=$access_token&fields=id,first_name,last_name,name,locale,timezone,gender";
            $result=$this->subscriber_info_curl_call($url);
            $result['profile_pic']="";
            
        }

        return $result;
    }

    protected function subscriber_info_curl_call($url){

        $ch = curl_init();
        $headers = array("Content-type: application/json");          
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
        curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");   
        $st=curl_exec($ch);          
        return $result=json_decode($st,TRUE);
    }



    protected function send_reply($access_token='',$reply='')
    {   
        $url="https://graph.facebook.com/v2.6/me/messages?access_token=$access_token";
        $ch = curl_init();
        $headers = array("Content-type: application/json");          
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);        
        
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$reply); 
    
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
        curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");   
        $st=curl_exec($ch);       
        
        $result=json_decode($st,TRUE);
        return $result;
    }

    public function send_reply_curl_call()
    {
        ignore_user_abort(TRUE);
        $access_token=$_POST['access_token'];
        $reply=$_POST['reply'];
        
        $url="https://graph.facebook.com/v2.6/me/messages?access_token=$access_token";
        $ch = curl_init();
        $headers = array("Content-type: application/json");          
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);        
        
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$reply); 
    
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
        curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");          $st=curl_exec($ch);      
        
        $result=json_decode($st,TRUE);
        return $result;
    }

    
    //DEPRECATED FUNCTION FOR QUICK BROADCAST
    public function unsubscribe_webhook_call()
    {    
        $psid=$this->input->post('psid');
        $fb_page_id=$this->input->post('fb_page_id');

        $pageinfo=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("page_id"=>$fb_page_id,"bot_enabled"=>"1")));
        $page_auto_id=isset($pageinfo[0]["id"])?$pageinfo[0]["id"]:"";
        $page_access_token=isset($pageinfo[0]["page_access_token"])?$pageinfo[0]["page_access_token"]:"";

        $label_info=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("page_id"=>$page_auto_id,"unsubscribe"=>"1")));
        $label_auto_id=isset($label_info[0]['id'])?$label_info[0]['id']:0;
        $label_id=isset($label_info[0]['label_id'])?$label_info[0]['label_id']:"";

        // $this->load->library('fb_rx_login');
        // $response= $this->fb_rx_login->assign_label($page_access_token,$psid,$label_id);

        $subscriberdata=$this->basic->get_data("messenger_bot_subscriber",array("where"=>array("subscribe_id"=>$psid,"page_id"=>$fb_page_id)));

        $contact_group_id=isset($subscriberdata[0]["contact_group_id"])?$subscriberdata[0]["contact_group_id"]:"";
        $explode=explode(',', $contact_group_id);
        array_push($explode, $label_auto_id);
        $new=array_unique($explode);
        $contact_group_id=implode(',', $new);
        $contact_group_id=trim($contact_group_id,',');
        $unsubscribe_time=date('Y-m-d H:i:s');

        $this->basic->update_data("messenger_bot_subscriber",array("subscribe_id"=>$psid,"page_id"=>$fb_page_id),array("contact_group_id"=>$contact_group_id,"permission"=>"0","unsubscribed_at"=>$unsubscribe_time));

        /** Adjust total count of subscriber & unsubscriber  **/

        $sql = "SELECT count(id) as permission_count FROM `messenger_bot_subscriber` WHERE page_table_id='$page_auto_id' AND permission='1' AND subscriber_type!='system'";
        $count_data = $this->db->query($sql)->row_array();

        $sql2 = "SELECT count(id) as permission_count FROM `messenger_bot_subscriber` WHERE page_table_id='$page_auto_id' AND permission='0' AND subscriber_type!='system'";
        $count_data2 = $this->db->query($sql2)->row_array();

        // how many are subscribed and how many are unsubscribed
        $subscribed = isset($count_data["permission_count"]) ? $count_data["permission_count"] : 0;
        $unsubscribed = isset($count_data2["permission_count"]) ? $count_data2["permission_count"] : 0;
        $current_lead_count=$subscribed+$unsubscribed;

        $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$page_auto_id),array("current_subscribed_lead_count"=>$subscribed,"current_unsubscribed_lead_count"=>$unsubscribed));


    }
    
    public function resubscribe_webhook_call()
    {
    
        $psid=$this->input->post('psid');
        $fb_page_id=$this->input->post('fb_page_id');

        $pageinfo=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("page_id"=>$fb_page_id,"bot_enabled"=>"1")));
        $page_auto_id=isset($pageinfo[0]["id"])?$pageinfo[0]["id"]:"";
        $page_access_token=isset($pageinfo[0]["page_access_token"])?$pageinfo[0]["page_access_token"]:"";

        $label_info=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("page_id"=>$page_auto_id,"unsubscribe"=>"1")));
        $label_auto_id=isset($label_info[0]['id'])?$label_info[0]['id']:0;
        $label_id=isset($label_info[0]['label_id'])?$label_info[0]['label_id']:"";

        // $this->load->library('fb_rx_login');
        // $response= $this->fb_rx_login->deassign_label($page_access_token,$psid,$label_id);

        $subscriberdata=$this->basic->get_data("messenger_bot_subscriber",array("where"=>array("subscribe_id"=>$psid,"page_id"=>$fb_page_id)));

        $contact_group_id=isset($subscriberdata[0]["contact_group_id"])?$subscriberdata[0]["contact_group_id"]:"";
        $explode=explode(',', $contact_group_id);
        
        foreach(array_keys($explode, $label_auto_id) as $key) {
            unset($explode[$key]);
        }
        
        $new=array_unique($explode);
        $contact_group_id=implode(',', $new);
        $contact_group_id=trim($contact_group_id,',');

        $this->basic->update_data("messenger_bot_subscriber",array("subscribe_id"=>$psid,"page_id"=>$fb_page_id),array("contact_group_id"=>$contact_group_id,"permission"=>"1"));
        

        /** Adjust total count of subscriber & unsubscriber  **/

        $sql = "SELECT count(id) as permission_count FROM `messenger_bot_subscriber` WHERE page_table_id='$page_auto_id' AND permission='1' AND subscriber_type!='system'";
        $count_data = $this->db->query($sql)->row_array();

        $sql2 = "SELECT count(id) as permission_count FROM `messenger_bot_subscriber` WHERE page_table_id='$page_auto_id' AND permission='0' AND subscriber_type!='system'";
        $count_data2 = $this->db->query($sql2)->row_array();

        // how many are subscribed and how many are unsubscribed
        $subscribed = isset($count_data["permission_count"]) ? $count_data["permission_count"] : 0;
        $unsubscribed = isset($count_data2["permission_count"]) ? $count_data2["permission_count"] : 0;
        $current_lead_count=$subscribed+$unsubscribed;

        $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$page_auto_id),array("current_subscribed_lead_count"=>$subscribed,"current_unsubscribed_lead_count"=>$unsubscribed));

    }
  
    public function multiple_assign_label($psid,$fb_page_id,$label_auto_ids,$social_media_type,$subscriber_table_id)
    {
        $label_auto_ids=explode(",",$label_auto_ids);
        $label_auto_ids=array_filter($label_auto_ids);
        foreach($label_auto_ids as $value)
        {
            $value = trim($value);
            $this->basic->execute_complex_query("INSERT IGNORE INTO messenger_bot_subscribers_label(contact_group_id,subscriber_table_id) 
                VALUES('$value','$subscriber_table_id');");
        }
    }



   
    public function assign_label_webhook_call()
    {
        $psid=$this->input->post('psid');
        $fb_page_id=$this->input->post('fb_page_id');
        $label_auto_ids=$this->input->post('label_auto_ids');
        $subscriber_table_id=$this->input->post('subscriber_table_id');
        $label_auto_ids=explode(",",$label_auto_ids);

        if($subscriber_table_id == 0)
        {
            $subscriberdata=$this->basic->get_data("messenger_bot_subscriber",array("where"=>array("subscribe_id"=>$psid,"page_id"=>$fb_page_id)));
            $subscriber_table_id = $subscriberdata[0]['id'] ?? 0;
        }
        
        foreach($label_auto_ids as $value)
            $this->basic->execute_complex_query("INSERT IGNORE INTO messenger_bot_subscribers_label(contact_group_id,subscriber_table_id) 
                VALUES('$value','$subscriber_table_id');");
    }


       /**Sender action added 19.03.2018 by Konok**/
    
    public function sender_action($sender_id,$action_type,$post_access_token='')
    {
    
        $url = "https://graph.facebook.com/v2.6/me/messages?access_token={$post_access_token}";
        
        $post_data_array['recipient']['id']=$sender_id;
        $post_data_array['sender_action']=$action_type;
        $post_data=json_encode($post_data_array);
        $ch = curl_init();
        $headers = array("Content-type: application/json");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data); 
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt'); 
        curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt'); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
        $st=curl_exec($ch);  
        $result=json_decode($st,TRUE);
        return $result;
    }

    

    // admin account import
    public function redirect_rx_link()
    {
        if ($this->session->userdata('logged_in')!= 1) exit();

        $id=$this->session->userdata("fb_rx_login_database_id");

        $redirect_url = base_url()."home/redirect_rx_link/";

        $this->load->library('fb_rx_login');
        $user_info = $this->fb_rx_login->login_callback($redirect_url);

        if(isset($user_info['status']) && $user_info['status'] == '0')
        {
            $data['error'] = 1;
            $data['message'] = $this->lang->line("Something went wrong")." : ".$user_info['message'];
            $data['body'] = "facebook_rx/admin_login";
            $this->_viewcontroller($data);
        }
        else
        {
            $access_token=$user_info['access_token_set'];
            $where = array('id'=>$id);
            $update_data = array('user_access_token'=>$access_token);

            if($this->basic->update_data('facebook_rx_config',$where,$update_data))
            {

                $data = array(
                    'user_id' => $this->user_id,
                    'facebook_rx_config_id' => $id,
                    'access_token' => $access_token,
                    'name' => $user_info['name'],
                    'email' => isset($user_info['email']) ? $user_info['email'] : "",
                    'fb_id' => $user_info['id'],
                    'add_date' => date('Y-m-d')
                    );

                $where=array();
                $where['where'] = array('user_id'=>$this->user_id,'fb_id'=>$user_info['id']);
                $exist_or_not = $this->basic->get_data('facebook_rx_fb_user_info',$where);

                if(empty($exist_or_not))
                {
                    $this->basic->insert_data('facebook_rx_fb_user_info',$data);
                    $facebook_table_id = $this->db->insert_id();
                }
                else
                {
                    $facebook_table_id = $exist_or_not[0]['id'];
                    $where = array('user_id'=>$this->user_id,'fb_id'=>$user_info['id']);
                    $this->basic->update_data('facebook_rx_fb_user_info',$where,$data);
                }

                $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_table_id);

                $page_list = $this->fb_rx_login->get_page_list($access_token);

                if(isset($page_list['error']) && $page_list['error'] == '1')
                {
                    $data['error'] = 1;
                    $data['message'] = $this->lang->line("Something went wrong")." : ".$page_list['message'];
                    $data['body'] = "facebook_rx/admin_login";
                    $this->_viewcontroller($data);
                    exit();
                }

                if(!empty($page_list))
                {
                    foreach($page_list as $page)
                    {
                        $user_id = $this->user_id;
                        $page_id = $page['id'];
                        $page_cover = '';
                        if(isset($page['cover']['source'])) $page_cover = $page['cover']['source'];
                        $page_profile = '';
                        if(isset($page['picture']['url'])) $page_profile = $page['picture']['url'];
                        $page_name = '';
                        if(isset($page['name'])) $page_name = $page['name'];
                        $page_username = '';
                        if(isset($page['username'])) $page_username = $page['username'];
                        $page_access_token = '';
                        if(isset($page['access_token'])) $page_access_token = $page['access_token'];
                        $page_email = '';
                        if(isset($page['emails'][0])) $page_email = $page['emails'][0];

                        $data = array(
                            'user_id' => $user_id,
                            'facebook_rx_fb_user_info_id' => $facebook_table_id,
                            'page_id' => $page_id,
                            'page_cover' => $page_cover,
                            'page_profile' => $page_profile,
                            'page_name' => $page_name,
                            'username' => $page_username,
                            'page_access_token' => $page_access_token,
                            'page_email' => $page_email,
                            'add_date' => date('Y-m-d')
                            );

                        // instagram section
                        $instagram_account_exist_or_not = '';
                        if($this->config->item('instagram_reply_enable_disable') == '1')
                            $instagram_account_exist_or_not = $this->fb_rx_login->instagram_account_check_by_id($page['id'], $access_token);
                        
                        if ($instagram_account_exist_or_not != "") {
                            $instagram_account_info = $this->fb_rx_login->instagram_account_info($instagram_account_exist_or_not, $access_token); 
                            $data['has_instagram'] = '1';
                            $data['instagram_business_account_id'] = $instagram_account_exist_or_not; 
                            $data['insta_username'] = isset($instagram_account_info['username']) ? $instagram_account_info['username'] : "";
                            $data['insta_followers_count'] = isset($instagram_account_info['followers_count']) ? $instagram_account_info['followers_count'] : "";
                            $data['insta_media_count'] = isset($instagram_account_info['media_count']) ? $instagram_account_info['media_count'] : "";
                            $data['insta_website'] = isset($instagram_account_info['website']) ? $instagram_account_info['website'] : "";
                            $data['insta_biography'] = isset($instagram_account_info['biography']) ? $instagram_account_info['biography'] : "";
                        }
                        // end of instagram section

                        $where=array();
                        $where['where'] = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'page_id'=>$page['id']);
                        $exist_or_not = $this->basic->get_data('facebook_rx_fb_page_info',$where);

                        if(empty($exist_or_not))
                        {
                            $this->basic->insert_data('facebook_rx_fb_page_info',$data);
                        }
                        else
                        {
                            $where = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'page_id'=>$page['id']);
                            $this->basic->update_data('facebook_rx_fb_page_info',$where,$data);
                        }

                    }
                }


                $group_list = $this->fb_rx_login->get_group_list($access_token);

                if(!empty($group_list))
                {
                    foreach($group_list as $group)
                    {
                        $user_id = $this->user_id;
                        $group_access_token = $access_token; // group uses user access token
                        $group_id = $group['id'];
                        $group_cover = '';
                        if(isset($group['cover']['source'])) $group_cover = $group['cover']['source'];
                        $group_profile = '';
                        if(isset($group['picture']['url'])) $group_profile = $group['picture']['url'];
                        $group_name = '';
                        if(isset($group['name'])) $group_name = $group['name'];

                        $data = array(
                            'user_id' => $user_id,
                            'facebook_rx_fb_user_info_id' => $facebook_table_id,
                            'group_id' => $group_id,
                            'group_cover' => $group_cover,
                            'group_profile' => $group_profile,
                            'group_name' => $group_name,
                            'group_access_token' => $group_access_token,
                            'add_date' => date('Y-m-d')
                            );

                        $where=array();
                        $where['where'] = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'group_id'=>$group['id']);
                        $exist_or_not = $this->basic->get_data('facebook_rx_fb_group_info',$where);

                        if(empty($exist_or_not))
                        {
                            $this->basic->insert_data('facebook_rx_fb_group_info',$data);
                        }
                        else
                        {
                            $where = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'group_id'=>$page['id']);
                            $this->basic->update_data('facebook_rx_fb_group_info',$where,$data);
                        }
                    }
                }
                $this->session->set_flashdata('success_message', 1);
                redirect('social_accounts/index','location');
                exit();
            }
            else
            {
                $data['error'] = 1;
                $data['message'] = $this->lang->line("Something went wrong, please try again.");
                $data['body'] = "facebook_rx/admin_login";
                $this->_viewcontroller($data);
            }


        }

    }
    //================MESSENGER BOT FUNCTIONS======================
    // ============================================================


    

    protected function ajax_check()
    {

        if(!$this->input->is_ajax_request() && $this->strict_ajax_call) exit();
    }

    // CSRF token check from during Form Submit 
    
    protected function csrf_token_check()
    {
        $csrf_token_form=$this->input->post('csrf_token',TRUE);
        $csrf_token_session= $this->session->userdata('csrf_token_session');
        $ajax_resposne = json_encode(array("status"=>"0","message"=>$this->lang->line("CSRF Token Mismatch!"),"error"=>$this->lang->line("CSRF Token Mismatch!")));
        $is_error = false;

        if(is_null($csrf_token_form) || is_null($csrf_token_session)) $is_error = true;
        else if(!hash_equals($csrf_token_form,$csrf_token_session)) $is_error = true;

        if($is_error)
        {
            if($this->input->is_ajax_request()) echo $ajax_resposne;
            else redirect('home/error_csrf','location');
            exit();
        }
        return true;
    }


    public function error_csrf()
    {
        $this->load->view('page/csrf');
    }


    protected function set_facebook_config_session($user_id) {

        $user_id = $user_id;

        // for getting usable facebook api (facebook live app)
        $facebook_rx_config_id=0;
        $fb_info=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$user_id)));
        if($this->config->item("backup_mode")==0)  // users will use admins app
        {
            if(isset($fb_info[0]['facebook_rx_config_id']))
            $facebook_rx_config_id=$fb_info[0]['facebook_rx_config_id'];
            else
            {
                $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','use_by'=>'everyone','developer_access'=>'0')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                if(isset($fb_info_admin[0]['id']))  $facebook_rx_config_id = $fb_info_admin[0]['id'];
            }
            $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);

            if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
            else $facebook_rx_fb_user_info = 0;
            $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig
        }
        else  // users will use own app
        {
            $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','user_id'=>$user_id,'developer_access'=>'0')),$select='');

            if(isset($fb_info_admin[0]['id']))
            {
                $facebook_rx_config_id = $fb_info_admin[0]['id'];
                $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);
            }

            if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
            else $facebook_rx_fb_user_info = 0;
            $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig

        }
        // for getting usable facebook api
    }

    protected function set_google_config_session($user_id) {

        $user_id = $user_id;

        // GMB add-on data
        if($this->addon_exist("gmb"))
        {
            $gmb_user_info = $this->basic->get_data('google_user_account',['where'=>['user_id'=>$user_id]],['id']);
            if(!empty($gmb_user_info))
                $this->session->set_userdata('google_mybusiness_user_table_id',$gmb_user_info[0]['id']);
        }

    }

    protected function botinboxer_exist()
    {
        if($this->session->userdata('user_type') == 'Admin') return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(199,$this->module_access)) return true;
        return false;
    }

    protected function broadcaster_exist()
    {
        if($this->session->userdata('user_type') == 'Admin' && $this->basic->is_exist("add_ons",array("project_id"=>30))) return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(211,$this->module_access)) return true;
        return false;
    }

    protected function drip_campaigner_exist()
    {
        if($this->session->userdata('user_type') == 'Admin' && $this->basic->is_exist("add_ons",array("project_id"=>30))) return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(219,$this->module_access)) return true;
        return false;
    }

    protected function sms_email_drip_campaigner_exist()
    {
        if($this->session->userdata('user_type') == 'Admin' && $this->basic->is_exist("add_ons",array("project_id"=>40))) return true;
        if($this->session->userdata('user_type') == 'Member' && (in_array(270,$this->module_access) || in_array(271,$this->module_access) )) return true;
        return false;
    }

    protected function messenger_bot_import_export_exist()
    {
        if($this->session->userdata('user_type') == 'Admin' && $this->basic->is_exist("add_ons",array("project_id"=>31))) return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(257,$this->module_access)) return true;
        return false;
    }

    protected function messenger_bot_analytics_exist()
    {
        if($this->session->userdata('user_type') == 'Admin') return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(260,$this->module_access)) return true;
        return false;
    }

    protected function engagement_exist()
    {
        if($this->session->userdata('user_type') == 'Admin'  && $this->basic->is_exist("add_ons",array("project_id"=>30))) return true;
        if($this->session->userdata('user_type') == 'Member' && count(array_intersect($this->module_access, array(213,214,215,217))) > 0 ) return true;
        return false;
    }

    protected function ultrapost_exist()
    {
        if($this->session->userdata('user_type') == 'Admin'  && $this->db->table_exists('facebook_rx_auto_post')) return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(223,$this->module_access)) return true;
        return false;
    }

    protected function webview_exist()
    {
        if($this->session->userdata('user_type') == 'Admin'  && $this->basic->is_exist("add_ons",array("project_id"=>31))) return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(261,$this->module_access)) return true;
        return false;
    } 

    protected function ecommerce_exist()
    {
        if($this->session->userdata('user_type') == 'Admin') return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(268,$this->module_access)) return true;
        return false;
    } 

    protected function auto_social_sharing_exist()
    {
        if($this->session->userdata('user_type') == 'Admin'  && $this->basic->is_exist("add_ons",array("project_id"=>39))) return true;

        if($this->session->userdata('user_type') == 'Member' && in_array(269,$this->module_access)) return true;

        return false;
    }

    protected function group_posting_exist()
    {
        if($this->basic->is_exist("add_ons",array("project_id"=>32))) return true;
        return false;
    }

    protected function addon_exist($unique_name="")
    {
        if($this->basic->is_exist("add_ons",array("unique_name"=>$unique_name))) return true;
        return false;
    }



    public function thirdparty_webhook_trigger($page_id="",$subscriber_id="",$trigger='trigger_email',$postback_id="",$form_canonical_id="",$form_data=array())
    {

        if($trigger=='trigger_postback')
            $trigger="trigger_postback_".$postback_id;

        else if($trigger=='trigger_webview')
            $trigger="trigger_webview_".$form_canonical_id;
        else if ($trigger=='trigger_userinput')
            $trigger="trigger_userinput_".$form_canonical_id;

        if(isset($this->user_id) && $this->user_id!="")
         $where_simple['messenger_bot_thirdparty_webhook.user_id'] = $this->user_id;

        $where_simple['messenger_bot_thirdparty_webhook.page_id'] = $page_id;
        $where_simple['messenger_bot_thirdparty_webhook_trigger.trigger_option'] = $trigger;
        $where=array('where'=>$where_simple);
       
        /**Get all connector webhook information**/

        $join = array('messenger_bot_thirdparty_webhook_trigger'=>"
            messenger_bot_thirdparty_webhook.id=messenger_bot_thirdparty_webhook_trigger.webhook_id,left");

        $webhook_connector_info=$this->basic->get_data('messenger_bot_thirdparty_webhook', $where, $select='', $join, $limit='', $start='');

        if(empty($webhook_connector_info)) return false;

        /** Get subscriber information  **/


        $where_simple=array();
        $where_simple['messenger_bot_subscriber.subscribe_id'] =$subscriber_id ;
        $where_simple['messenger_bot_subscriber.page_id'] = "$page_id";
        $where=array('where'=>$where_simple);
        $join=array("messenger_bot_subscribers_label"=>"messenger_bot_subscribers_label.subscriber_table_id=messenger_bot_subscriber.id,left");
        $select=["GROUP_CONCAT(DISTINCT messenger_bot_subscribers_label.contact_group_id separator ',') as contact_group_ids","messenger_bot_subscriber.*"];

        $subscriber_info=$this->basic->get_data('messenger_bot_subscriber', $where, $select, $join, $limit='', $start='','','messenger_bot_subscriber.id');

        /**Get subscriber Labels name from labels id***/

        $label_ids = $subscriber_info_rearrange['contact_group_id']=isset($subscriber_info[0]['contact_group_ids']) ? $subscriber_info[0]['contact_group_ids']:"";

        $label_ids_array = explode(',',$label_ids);
        $label_ids_array = array_map('trim', $label_ids_array);
        $label_ids_array = array_filter($label_ids_array);

        $labels_name="";

        if(!empty($label_ids_array)){

            $where=array("where_in"=>array("id"=>$label_ids_array));

            $label_info = $this->basic->get_data("messenger_bot_broadcast_contact_group",$where);

            foreach($label_info as $value)
            {
                $labels_name.=",".$value['group_name'];
            }
        }

        $labels_name =trim($labels_name,",");

        foreach ($webhook_connector_info as $webhook_value) {
        
            $webhook_url = isset($webhook_value['webhook_url']) ? $webhook_value['webhook_url']:"";
            $webhook_id=isset($webhook_value['webhook_id']) ? $webhook_value['webhook_id']:"";
            $post_variable = isset($webhook_value['variable_post']) ? $webhook_value['variable_post']:"";
            $post_variable= explode(',',$post_variable);
            $post_variable=array_filter($post_variable);

            /**Making the variable for post/send ***/

            $post_info=array();

            foreach ($post_variable as $variable_info) {

                if($variable_info=='psid')
                    $post_info[$variable_info]= isset($subscriber_info[0]['subscribe_id']) ? $subscriber_info[0]['subscribe_id']:"";
                else if ($variable_info=='labels')
                    $post_info[$variable_info]= $labels_name;
                else if($variable_info=='page_name')
                    $post_info[$variable_info]= isset($webhook_connector_info[0]['page_name']) ? $webhook_connector_info[0]['page_name']:"";
                else if($variable_info=='postbackid')
                     $post_info[$variable_info]= $postback_id;

                 else if($variable_info =='formdata'){
                        foreach ($form_data as $key => $value) {
                            $post_info[$key]=$value;
                        }
                 }

                  else if($variable_info=='user_input_flow_campaign'){
                        
                        $post_info["user_input_data"]=$form_data;
                 }

                else
                    $post_info[$variable_info] = isset($subscriber_info[0][$variable_info]) ? $subscriber_info[0][$variable_info]:"";

            }


            /***    Send/Post Information to webhook url ***/

            $post_info=json_encode($post_info);

            $curl_response=$this->curl_send_data($webhook_url,$post_info);
            
            $curl_http_code= $curl_response['http_code'];
            $curl_error= $curl_response['curl_error'];

            /***Insert into Activity table**/

            $insert_data=array();
            $insert_data['http_code'] = $curl_http_code; 
            $insert_data['curl_error'] = $curl_error; 
            $insert_data['webhook_id'] = $webhook_id; 
            $insert_data['post_time'] = date('Y-m-d H:i:s'); 
            $insert_data['post_data'] = $post_info; 

            $this->basic->insert_data('messenger_bot_thirdparty_webhook_activity',$insert_data);

            /**update messenger_bot_thirdparty_webhook table for last_trigger_time **/
            $update_data_last_trigger['last_trigger_time'] = $insert_data['post_time'];
            $this->basic->update_data("messenger_bot_thirdparty_webhook",array('id'=>$webhook_id),$update_data_last_trigger);
            
        }
    }


     protected function curl_send_data($webhook_url,$post_info){

        $ch = curl_init();
        $headers = array('Accept: application/json', 'Content-Type: application/json');

        curl_setopt($ch, CURLOPT_URL, $webhook_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_info); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
       // curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
        $st=curl_exec($ch); 

        $curl_information =  curl_getinfo($ch);
        $curl_error="";
        if($curl_information['http_code']!='200'){
            $curl_error = curl_error($ch);
        }

        $response['http_code']=$curl_information['http_code'];
        $response['curl_error']=$curl_error;

        return $response; 

    }



    public function assign_drip_messaging_id($drip_type,$check_box_plugin_id,$PAGE_AUTO_ID,$subscriber_id,$drip_campaign_id="")
    {
        if(!isset($drip_type)) $drip_type = "default";
        if(!isset($check_box_plugin_id)) $check_box_plugin_id = "0";

        $date_time=date("Y-m-d H:i:s");

        $engagement_table_id= $check_box_plugin_id;

        if($drip_campaign_id!=""){ // Means Campaign id is passed directly, no need to get it from engagement table. 

            $where['where']['id'] = $drip_campaign_id;

            if($PAGE_AUTO_ID!='0' && $PAGE_AUTO_ID!="")
                $where['where']['page_id'] = $PAGE_AUTO_ID;
        } 
            
        else{
            // if $drip_campaign_id isn't set , that means, we need to check if the messenger enhancers add-on is avaialble or not. 

           if(!$this->addon_exist("messenger_bot_enhancers")) return true; 

            $where=array("where"=>array("engagement_table_id"=>$engagement_table_id,"drip_type"=>$drip_type,"page_id"=>$PAGE_AUTO_ID)); 
        }


        $drip_messaging_campaign_info= $this->basic->get_data("messenger_bot_drip_campaign",$where);
        
        $drip_campaign_id= isset($drip_messaging_campaign_info[0]['id']) ? $drip_messaging_campaign_info[0]['id']: "";
        $user_id= isset($drip_messaging_campaign_info[0]['user_id']) ? $drip_messaging_campaign_info[0]['user_id']: "";

        if($drip_campaign_id!=""){

            $sql="INSERT IGNORE INTO messenger_bot_drip_campaign_assign(user_id,page_table_id,subscribe_id,messenger_bot_drip_campaign_id,drip_type,messenger_bot_drip_initial_date) 
                VALUES('$user_id','$PAGE_AUTO_ID','$subscriber_id','$drip_campaign_id','$drip_type','$date_time');";

             $this->basic->execute_complex_query($sql);

        }
    }


    // page and account delete section
    
    public function table_names_array()
    {
      $tables = array (
                    0 => 
                    array (
                      'table_name' => 'auto_comment_reply_info',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ),
                    1 => 
                    array (
                      'table_name' => 'ultrapost_auto_reply',
                      'column_name' => 'page_ids',
                      'module_id' => ''
                    ),
                    2 => 
                    array (
                      'table_name' => 'autoposting',
                      'column_name' => 'page_ids',
                      'module_id' => '',
                      'comma_separated' => 'yes'
                    ),
                    3 => 
                    array (
                      'table_name' => 'facebook_ex_autoreply',
                      'column_name' => 'page_info_table_id',
                      'module_id' => '',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'facebook_ex_autoreply_report',
                      'dependent_table_column' =>'autoreply_table_id'
                    ),
                    4 => 
                    array (
                      'table_name' => 'facebook_ex_conversation_campaign',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    5 => 
                    array (
                      'table_name' => 'facebook_ex_conversation_campaign_send',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    6 => 
                    array (
                      'table_name' => 'facebook_page_insight_page_list',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    7 => 
                    array (
                      'table_name' => 'facebook_rx_auto_post',
                      'column_name' => 'page_group_user_id',
                      'module_id' => ''
                    ),
                    8 => 
                    array (
                      'table_name' => 'facebook_rx_cta_post',
                      'column_name' => 'page_group_user_id',
                      'module_id' => ''
                    ),                          
                    10 => 
                    array (
                      'table_name' => 'facebook_rx_fb_page_info',
                      'column_name' => 'id',
                      'persistent_getstarted_check' => 'yes',
                      'module_id' => ''
                    ),                          
                    12 => 
                    array (
                      'table_name' => 'facebook_rx_offer_campaign',
                      'column_name' => 'page_group_user_id',
                      'module_id' => ''
                    ),
                    13 => 
                    array (
                      'table_name' => 'facebook_rx_offer_campaign_view',
                      'column_name' => 'page_group_user_id',
                      'module_id' => ''
                    ),
                    16 => 
                    array (
                      'table_name' => 'facebook_rx_slider_post',
                      'column_name' => 'page_group_user_id',
                      'module_id' => ''
                    ),                          
                    24 => 
                    array (
                      'table_name' => 'messenger_bot',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    26 => 
                    array (
                      'table_name' => 'messenger_bot_broadcast_contact_group',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    27 => 
                    array (
                      'table_name' => 'messenger_bot_broadcast_serial',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    28 => 
                    array (
                      'table_name' => 'messenger_bot_broadcast_serial_send',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    29 => 
                    array (
                      'table_name' => 'messenger_bot_domain_whitelist',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    30 => 
                    array (
                      'table_name' => 'messenger_bot_drip_campaign',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    31 => 
                    array (
                      'table_name' => 'messenger_bot_drip_report',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    32 => 
                    array (
                      'table_name' => 'messenger_bot_engagement_2way_chat_plugin',
                      'column_name' => 'page_auto_id',
                      'module_id' => ''
                    ),
                    33 => 
                    array (
                      'table_name' => 'messenger_bot_engagement_checkbox',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    34 => 
                    array (
                      'table_name' => 'messenger_bot_drip_campaign_assign',
                      'column_name' => 'page_table_id',
                      'module_id' => ''
                    ),
                    36 => 
                    array (
                      'table_name' => 'messenger_bot_engagement_mme',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    37 => 
                    array (
                      'table_name' => 'messenger_bot_engagement_send_to_msg',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    38 => 
                    array (
                      'table_name' => 'messenger_bot_persistent_menu',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    39 => 
                    array (
                      'table_name' => 'messenger_bot_postback',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    40 => 
                    array (
                      'table_name' => 'messenger_bot_reply_error_log',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),                          
                    42 => 
                    array (
                      'table_name' => 'messenger_bot_subscriber',
                      'column_name' => 'page_table_id',
                      'module_id' => ''
                    ),
                    43 => 
                    array (
                      'table_name' => 'messenger_bot_thirdparty_webhook',
                      'column_name' => 'page_id',
                      'is_facebook_page_id' => 'yes',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'messenger_bot_thirdparty_webhook_activity,messenger_bot_thirdparty_webhook_trigger',
                      'dependent_table_column' => 'webhook_id,webhook_id',
                      'module_id' => ''
                    ),
                    45 => 
                    array (
                      'table_name' => 'messenger_bot_user_custom_form_webview_data',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    48 => 
                    array (
                      'table_name' => 'page_response_auto_like_share',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ),
                    49 => 
                    array (
                      'table_name' => 'page_response_auto_like_share_report',
                      'column_name' => 'page_info_table_id',
                      'module_id' => '',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'page_response_auto_like_report,page_response_auto_share_report',
                      'dependent_table_column' =>'page_response_auto_like_share_report_id,page_response_auto_like_share_report_id'
                    ),
                    51 => 
                    array (
                      'table_name' => 'page_response_autoreply',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ),
                    52 => 
                    array (
                      'table_name' => 'page_response_report',
                      'column_name' => 'page_info_table_id',
                      'module_id' => '',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'facebook_ex_autoreply_report',
                      'dependent_table_column' =>'autoreply_table_id'
                    ),
                    54 => 
                    array (
                      'table_name' => 'tag_machine_bulk_reply',
                      'column_name' => 'page_info_table_id',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'tag_machine_bulk_reply_send',
                      'dependent_table_column' => 'campaign_id',
                      'module_id' => ''
                    ),
                    56 => 
                    array (
                      'table_name' => 'tag_machine_bulk_tag',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ),
                    57 => 
                    array (
                      'table_name' => 'tag_machine_comment_info',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ),
                    58 => 
                    array (
                      'table_name' => 'tag_machine_commenter_info',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ),
                    59 => 
                    array (
                      'table_name' => 'tag_machine_enabled_post_list',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ), 
                    63 => 
                    array (
                      'table_name' => 'webview_builder',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    64 => 
                    array (
                      'table_name' => 'email_sending_campaign',
                      'column_name' => 'page_id',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'email_sending_campaign_send',
                      'dependent_table_column' => 'campaign_id',
                      'module_id' => ''
                    ),
                    65 => 
                    array (
                      'table_name' => 'sms_sending_campaign',
                      'column_name' => 'page_id',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'sms_sending_campaign_send',
                      'dependent_table_column' => 'campaign_id',
                      'module_id' => ''
                    ),
                    66 => 
                    array (
                      'table_name' => 'woocommerce_drip_campaign',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    67 => 
                    array (
                      'table_name' => 'woocommerce_drip_campaign_report',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    68 => 
                    array (
                      'table_name' => 'woocommerce_drip_campaign_webhook_call',
                      'column_name' => 'page_table_id',
                      'module_id' => ''
                    ),
                    69 => 
                    array (
                      'table_name' => 'ecommerce_store',
                      'column_name' => 'page_id',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'ecommerce_attribute,ecommerce_cart,ecommerce_cart_item,ecommerce_category,ecommerce_coupon,ecommerce_product,ecommerce_reminder_report',
                      'dependent_table_column' => 'store_id,store_id,store_id,store_id,store_id,store_id,store_id',
                      'module_id' => ''
                    ),
                    70 => 
                    array (
                      'table_name' => 'otn_postback',
                      'column_name' => 'page_id',
                      'module_id' => '',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'otn_optin_subscriber',
                      'dependent_table_column' =>'otn_id'
                    ),
                    71 => 
                    array (
                      'table_name' => 'instagram_reply_autoreply',
                      'column_name' => 'page_info_table_id',
                      'module_id' => '',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'instagram_autoreply_report',
                      'dependent_table_column' =>'autoreply_table_id'
                    ),
                    72 => 
                    array (
                      'table_name' => 'user_input_flow_campaign',
                      'column_name' => 'page_table_id',
                      'module_id' => '',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'user_input_flow_questions,user_input_flow_questions_answer',
                      'dependent_table_column' =>'flow_campaign_id,flow_campaign_id'
                    ),
                    73 => 
                    array (
                      'table_name' => 'visual_flow_builder_campaign',
                      'column_name' => 'page_id',
                      'module_id' => '',
                    ),
                    74 => 
                    array (
                      'table_name' => 'visual_flow_campaign_unique_ids',
                      'column_name' => 'page_table_id',
                      'module_id' => '',
                    ),
                    75 => 
                    array (
                      'table_name' => 'messenger_bot_message_sent_stat',
                      'column_name' => 'page_table_id',
                      'module_id' => '',
                    )

                  );
      return $tables;
    }

    public function table_names_array_foraccount()
    {
        $tables = array(
                        1 => 
                        array (
                          'table_name' => 'facebook_rx_fb_group_info',
                          'column_name' => 'facebook_rx_fb_user_info_id',
                          'module_id' => ''
                        ),
                        2 => 
                        array (
                          'table_name' => 'facebook_rx_fb_user_info',
                          'column_name' => 'id',
                          'module_id' => ''
                        )
                );
        return $tables;
    }


    public function table_names_array_foruser()
    {
        $tables = array(
                1 => 
                array (
                  'table_name' => 'auto_comment_reply_tb',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                2 => 
                array (
                  'table_name' => 'fb_simple_support_desk',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                3 => 
                array (
                  'table_name' => 'fb_support_category',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                4 => 
                array (
                  'table_name' => 'fb_support_desk_reply',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                6 => 
                array (
                  'table_name' => 'messenger_bot_saved_templates',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                7 => 
                array (
                  'table_name' => 'ultrapost_auto_reply',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                8 => 
                array (
                  'table_name' => 'sms_sending_campaign_send',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                9 => 
                array (
                  'table_name' => 'email_mailgun_config',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                10 => 
                array (
                  'table_name' => 'email_mandrill_config',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                11 => 
                array (
                  'table_name' => 'email_sendgrid_config',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                12 => 
                array (
                  'table_name' => 'email_smtp_config',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                13 => 
                array (
                  'table_name' => 'sms_api_config',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                14 => 
                array (
                  'table_name' => 'sms_email_contacts',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                15 => 
                array (
                  'table_name' => 'sms_email_contact_group',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                16 => 
                array (
                  'table_name' => 'email_sending_campaign',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                17 => 
                array (
                  'table_name' => 'email_sending_campaign_send',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                18 => 
                array (
                  'table_name' => 'sms_sending_campaign',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                19 => 
                array (
                  'table_name' => 'ecommerce_config',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                20 => 
                array (
                  'table_name' => 'usage_log',
                  'column_name' => 'user_id',
                  'module_id' => ''
                ),
                21 => 
                array (
                  'table_name' => 'user_input_custom_fields',
                  'column_name' => 'user_id',
                  'module_id' => '',
                  'has_dependent_table' => 'yes',
                  'dependent_tables' => 'user_input_custom_fields_assaign',
                  'dependent_table_column' =>'custom_field_id'
                ),
                22 => 
                array (
                  'table_name' => 'users',
                  'column_name' => 'id',
                  'module_id' => ''
                )
        );
        return $tables;
    }

    public function delete_data_basedon_page($table_id=0,$admin_access=0)
    {
      if($table_id == 0)
      {
        return json_encode(array('success'=>0,'message'=>$this->lang->line("Page is not found for this user. Something is wrong.")));
        exit();
      }

      if($admin_access == '1' && $this->session->userdata('user_type') == 'Admin')
        $page_information = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$table_id)));
      else
        $page_information = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$table_id,'user_id'=>$this->user_id)));

      if(empty($page_information)){
        return json_encode(array('success'=>0,'message'=>$this->lang->line("Page is not found for this user. Something is wrong.")));
        exit();
      }

      $this->db->trans_start();
      $this->load->library("fb_rx_login");
      $table_names = $this->table_names_array();
      foreach($table_names as $value)
      {
        if(isset($value['persistent_getstarted_check']) && $value['persistent_getstarted_check'] == 'yes')
        {
          $fb_page_id=isset($page_information[0]["page_id"]) ? $page_information[0]["page_id"] : "";
          $page_access_token=isset($page_information[0]["page_access_token"]) ? $page_information[0]["page_access_token"] : "";
          $persistent_enabled=isset($page_information[0]["persistent_enabled"]) ? $page_information[0]["persistent_enabled"] : "0";
          $bot_enabled=isset($page_information[0]["bot_enabled"]) ? $page_information[0]["bot_enabled"] : "0";
          $ice_breaker_status=isset($page_information[0]["ice_breaker_status"]) ? $page_information[0]["ice_breaker_status"] : "0";
          $started_button_enabled=isset($page_information[0]["started_button_enabled"]) ? $page_information[0]["started_button_enabled"] : "0";
          $fb_user_id = $page_information[0]["facebook_rx_fb_user_info_id"];
          $fb_user_info = $this->basic->get_data('facebook_rx_fb_user_info',array('where'=>array('id'=>$fb_user_id)));
          $this->fb_rx_login->app_initialize($fb_user_info[0]['facebook_rx_config_id']); 

          if($persistent_enabled == '1') 
          {
            $this->fb_rx_login->delete_persistent_menu($page_access_token); // delete persistent menu
            if($admin_access != '1')
                $this->_delete_usage_log($module_id=197,$request=1);
          }
          if($started_button_enabled == '1') $this->fb_rx_login->delete_get_started_button($page_access_token); // delete get started button
          if($ice_breaker_status == '1') $this->fb_rx_login->delete_ice_breakers($page_access_token); // delete get started button
          if($bot_enabled == '1' || $bot_enabled == '2')
          {
            if($bot_enabled == '1')
                $this->fb_rx_login->disable_bot($fb_page_id,$page_access_token);
            if($admin_access != '1')
                $this->_delete_usage_log($module_id=200,$request=1);
          }

          if($this->db->table_exists($value['table_name']))
            $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$table_id));
        }
        else if(isset($value['has_dependent_table']) && $value['has_dependent_table'] == 'yes')
        {
          $table_ids_array = array();   
          if($this->db->table_exists($value['table_name']))
          {
            if(isset($value['is_facebook_page_id']) && $value['is_facebook_page_id'] == 'yes')
            {
              $facebook_page_id = $page_information[0]['page_id']; 
              $table_ids_info = $this->basic->get_data($value['table_name'],array('where'=>array("{$value['column_name']}"=>$facebook_page_id)),'id');
            }
            else
              $table_ids_info = $this->basic->get_data($value['table_name'],array('where'=>array("{$value['column_name']}"=>$table_id)),'id');

          }    
          else continue;

          foreach($table_ids_info as $info)
            array_push($table_ids_array, $info['id']);

          if($this->db->table_exists($value['table_name']))
          {
            if(isset($value['is_facebook_page_id']) && $value['is_facebook_page_id'] == 'yes')
              $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$facebook_page_id));
            else
              $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$table_id));
          }

          $dependent_table_names = explode(',', $value['dependent_tables']);
          $dependent_table_column = explode(',', $value['dependent_table_column']);
          if(!empty($table_ids_array) && !empty($dependent_table_names))
          {            
            for($i=0;$i<count($dependent_table_names);$i++)
            {
              if($this->db->table_exists($dependent_table_names[$i]))
              {
                $this->db->where_in($dependent_table_column[$i], $table_ids_array);
                $this->db->delete($dependent_table_names[$i]);
              }
            }
          }
        }
        else if(isset($value['comma_separated']) && $value['comma_separated'] == 'yes')
        {
          $str = "FIND_IN_SET('".$table_id."', ".$value['column_name'].") !=";
          $where = array($str=>0);
          if($this->db->table_exists($value['table_name']))
            $this->basic->delete_data($value['table_name'],$where);
        }
        else
        {
          if($this->db->table_exists($value['table_name']))
            $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$table_id));
        }
      }
      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) 
      {    
          $response['status'] = 0;
          $response['message'] = $this->lang->line('Something went wrong, please try again.');         
      }
      else
      {
          $response['status'] = 1;
          $response['message'] = $this->lang->line("Your page and all of it's corresponding campaigns have been deleted successfully.");      
      }

      return json_encode($response);

    }


    public function delete_data_basedon_account($fb_user_id=0,$app_delete=0)
    {
      $this->db->trans_start();
      $table_names = $this->table_names_array_foraccount();
      foreach($table_names as $value)
      {
        if($this->db->table_exists($value['table_name']))
          $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$fb_user_id));
      }
      $this->db->trans_complete();                

      if ($this->db->trans_status() === FALSE) 
      {   
          $response['status'] = 0;
          $response['message'] = $this->lang->line('Something went wrong, please try again.');           
      }
      else
      {
          if($app_delete!='1')
          {
            // delete data to useges log table
            $this->_delete_usage_log($module_id=65,$request=1);
            $this->session->sess_destroy();            
          }
          $response['status'] = 1;
          $response['message'] = $this->lang->line("Your account and all of it's corresponding pages, groups and campaigns have been deleted successfully. Now you'll be redirected to the login page.");       
      }
      return $response;
    }


    public function user_delete_action($user_id=0)
    {
        $this->ajax_check();
        $this->csrf_token_check();

        if($this->is_demo == '1' && $this->session->userdata('user_type')=="Admin")
        {
            
                $response['status'] = 0;
                $response['message'] = "This feature is disabled in this demo.";
                echo json_encode($response);
                exit();
            
        }

        if($user_id == 0) exit;

        if($this->session->userdata('user_type') != 'Admin')
            if($user_id != $this->user_id) exit;

        $fb_user_infos = $this->basic->get_data('facebook_rx_fb_user_info',array('where'=>array('user_id'=>$user_id)),array('id'));

        foreach($fb_user_infos as $value)
        {
          $fb_page_infos = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_user_info_id'=>$value['id'])),array('id'));
          foreach($fb_page_infos as $value2)
            $response = $this->delete_data_basedon_page($value2['id'],'1');

          $response = $this->delete_data_basedon_account($value['id'],'1');
        }

        $this->db->trans_start();
        $table_names = $this->table_names_array_foruser();
        foreach($table_names as $value)
        {
          if($this->db->table_exists($value['table_name']))
          {
            if(isset($value['has_dependent_table']) && $value['has_dependent_table'] == 'yes')
            {
                $table_ids_array = array(); 
                $table_ids_info = $this->basic->get_data($value['table_name'],array('where'=>array("{$value['column_name']}"=>$user_id)),'id');
                foreach($table_ids_info as $info)
                  array_push($table_ids_array, $info['id']);

                $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$user_id));

                $dependent_table_names = explode(',', $value['dependent_tables']);
                $dependent_table_column = explode(',', $value['dependent_table_column']);
                if(!empty($table_ids_array) && !empty($dependent_table_names))
                {            
                  for($i=0;$i<count($dependent_table_names);$i++)
                  {
                    if($this->db->table_exists($dependent_table_names[$i]))
                    {
                      $this->db->where_in($dependent_table_column[$i], $table_ids_array);
                      $this->db->delete($dependent_table_names[$i]);
                    }
                  }
                }

            }
            else
                $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$user_id));
          }
        }
        $this->db->trans_complete();                

        if ($this->db->trans_status() === FALSE) 
        {   
            $response['status'] = 0;
            $response['message'] = $this->lang->line('Something went wrong, please try again.');           
        }
        else
        {
            if($this->session->userdata('user_type') != 'Admin')
                $this->session->sess_destroy();
            $response['status'] = 1;
            $response['message'] = $this->lang->line("Account and all of it's corresponding pages, groups and campaigns have been deleted successfully.");       
        }
        echo json_encode($response);
    }

    

    public function _email_send_function($config_id_prefix="", $message_org="", $to_emails="", $subject="", $attachement='', $fileName='',$user_id='',$test_mail='')
    {
        $message_org = preg_replace('/data-cke-saved-src="(.+?)"/', '', $message_org);
        $message_org = preg_replace('/_moz_resizing="(.+?)"/', '', $message_org);

        // echo "<pre>"; print_r($message_org); exit;
        
        $message = '<!DOCTYPE HTML>'.
        '<html>'.
        '<head>'.
        '<meta http-equiv="content-type" content="text/html">'.
        '<title>'.$subject.'</title>'.
        '</head>'.
        '<body>'.
        $message_org.
        '</body>';
        '</html>';


        if ($config_id_prefix=="" || $message=="" || $to_emails=="" || $subject=="") {
            return false;
        }

        if ($fileName=="0") {
            $fileName="";
            $attachement="";
        }

        if (!is_array($to_emails)) {
            $to_emails=array($to_emails);
        }
            
        $status="";
        
        /*****get the email configuration value*****/
        $from_email=$config_id_prefix;
        $from_email_separate=explode("_", $from_email);
        $config_type=$from_email_separate[0];
        $config_id=$from_email_separate[1];
        
        if ($config_type=='smtp') {
            $table_name="email_smtp_config";
        } elseif ($config_type=='mandrill') {
            $table_name="email_mandrill_config";
        } elseif ($config_type=='sendgrid') {
            $table_name="email_sendgrid_config";
        } elseif ($config_type=='mailgun') {
            $table_name="email_mailgun_config";
        } else {
            $table_name="";
        }
        
                    
        $where2=array("where"=>array('id'=>$config_id));
        $email_config_details=$this->basic->get_data($table_name, $where2, $select='', $join='', $limit='', $start='', $group_by='', $num_rows=0);

        $userid = $user_id;

        if (count($email_config_details)==0) {
            $status =  "Opps !!! Sorry no configuration is found";
            return $status;
        }

        if ($config_type=='smtp') 
        {
            foreach ($email_config_details as $send_info) 
            {
                $send_email = trim($send_info['email_address']);
                $smtp_host= trim($send_info['smtp_host']);
                $smtp_port= trim($send_info['smtp_port']);
                $smtp_user=trim($send_info['smtp_user']);
                $smtp_password= trim($send_info['smtp_password']);
                $smtp_type = trim($send_info['smtp_type']);
                $sender_name= trim($send_info['sender_name']);
            }
            
            /*****Email Sending Code ******/
            $config = array(
              'protocol' => 'smtp',
              'smtp_host' => "{$smtp_host}",
              'smtp_port' => $smtp_port,
              'smtp_user' => "{$smtp_user}", // change it to yours
              'smtp_pass' => "{$smtp_password}", // change it to yours
              'mailtype' => 'html',
              'charset' => 'utf-8',
              'newline' =>"\r\n",
              'set_crlf'=> "\r\n",
              'smtp_timeout'=>'30',
              'wrapchars'   => '998'
            );

            if($smtp_type != 'Default')
                $config['smtp_crypto'] = $smtp_type;

           else
                $config['smtp_crypto'] = "";

            $this->load->library('email');
            $this->email->initialize($config);
            
            if($sender_name!='')
                $this->email->from($send_email,$sender_name); 
            else
                $this->email->from($send_email); 
            
            if(is_array($to_emails) && count($to_emails)>1)
            {
                $no_reply_arr=explode("@",$send_email);
                if(isset($no_reply_arr[1]))
                $no_reply="do-not-reply@".$no_reply_arr[1];
                else $no_reply=$to_emails[0];
                $this->email->to($no_reply);
                $this->email->bcc($to_emails);
            }
            else $this->email->to($to_emails);

            $this->email->subject($subject);
            $this->email->message($message);
              
            if ($attachement) 
            {
                $this->email->attach($attachement);
            }

            try 
            {
                if($this->email->send()) {

                    $response_smtp = "success";
                }
                else {
                    $response_smtp = "error";
                }
            } 
            catch (Exception $e) 
            {
                $response_smtp = "error";
            }
              
            if($response_smtp != "error") {

                $status = "Submited";
            } 
            else {

                if($test_mail == 1) {
                    $status = $this->email->print_debugger();
                } else {
                    $status = "error in configuration";
                }
            }
        }
        /***  End of Email sending by SMTP  ***/
        
        
        /***  If option is mandrill   ***/
        
        if ($config_type=='mandrill') 
        {
            foreach ($email_config_details as $send_info) 
            {
                $send_email= $send_info['email_address'];
                $api_id=$send_info['api_key'];
                $send_name=$send_info['your_name'];
            }
            $this->load->library('email_manager');
            $result = $this->email_manager->send_madrill_email($send_email, $send_name, $to_emails, $subject,$message, $api_id, $attachement, $fileName);
            
            if (isset($result['error']) && !empty($result['error'])) 
            {
                $status = $result['error'];
            } 
            else 
            {
                $status = "Submited";
            }
        }
        
        
        
        /***** if gateway is sendgrid *****/
        if ($config_type=='sendgrid') 
        {
            $this->load->library('email_manager');
            foreach ($email_config_details as $send_info) 
            {
                $sendgrid_from_email= $send_info['email_address'];
                $this->email_manager->sendgrid_username=$send_info['username'];
                $this->email_manager->sendgrid_password=$send_info['password'];
            }
            
            $result = $this->email_manager->sendgrid_email_send($sendgrid_from_email, $to_emails, $subject, $message, $attachement, $fileName);

            if ((isset($result['status']) && !empty($result['status'])) && $result['status'] == 'success') {

                if($test_mail == 1) {
                    $status = $result['status'];
                } else {
                    $status = 'Submited';
                }

            } else {

                $status = $result['status'];
            }
        }
        
    
        if ($config_type=='mailgun') 
        {
            $this->load->library('email_manager');
            foreach ($email_config_details as $send_info) 
            {
                $send_email=$send_info['email_address'];
                $this->email_manager->mailgun_api_key=$send_info['api_key'];
                $this->email_manager->mailgun_domain=$send_info['domain_name'];
            }
            
            $result = $this->email_manager->mailgun_email_send($send_email, $to_emails, $subject, $message, $attachement);

            if ($result['status'] != 'error') {

                if($test_mail == 1) {

                    $status = $result['status'];

                } else {

                    $status = "Submited";
                }
            } 
            else 
            {
                $status = $result['status'];
            }
        }
        
        return $status;
    }

    public function unsubscribe($contact_id,$email)
    {
        if($contact_id == '' || $email == '') exit;
        
        $data = array();
        $convertidintobinary = pack("H*", $contact_id);
        $explode_binarycontactid = explode("-", $convertidintobinary);

        $data['contact_id'] = $explode_binarycontactid[0];
        $data['type'] = $explode_binarycontactid[1];
        $data['cam_id'] = $explode_binarycontactid[2];
        $data['cam_temp_table_id'] = $explode_binarycontactid[3];
        $data['campaign_type']= isset($explode_binarycontactid[4]) ? $explode_binarycontactid[4] : "";
        $data['email_address'] = pack("H*", $email);
        
        if(isset($explode_binarycontactid) && $explode_binarycontactid[1] == "contact"){

            $info = $this->basic->get_data("sms_email_contacts", array('where'=>array("id"=>$data['contact_id'], "email"=>$data['email_address'])));

        } else if(isset($explode_binarycontactid) && $explode_binarycontactid[1] == "subscriber") {
            
            if($data['campaign_type']!='Drip')
                $info = $this->basic->get_data("messenger_bot_subscriber", array('where'=>array("id"=>$data['contact_id'], "email"=>$data['email_address'])));
            else
                $info = $this->basic->get_data("messenger_bot_subscriber", array('where'=>array("subscribe_id"=>$data['contact_id'], "email"=>$data['email_address'])));

        }

        if(isset($info) && !empty($info)) {

            if((isset($info[0]['unsubscribed']) && $info[0]['unsubscribed'] =="0") || (isset($info[0]['is_email_unsubscriber']) && $info[0]['is_email_unsubscriber'] =="0")) {
                $data['status'] = "0";
                
            } else {
                $data['status'] = "1";
            }

            $this->load->view("sms_email_manager/email/email_campaign/unsubscribed_message",$data);

        } else {
            redirect('home/access_forbidden', 'location');
        }
    }

    public function unsubscribe_action()
    {
        // if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $result = array();

        $contactid = trim($this->input->post("contactid",true));
        $email_address = trim($this->input->post("email",true));
        $btntype = trim($this->input->post("btntype",true));
        $contactType = trim($this->input->post("contactType",true));
        $cam_id = trim($this->input->post("cam_id",true));
        $cam_temp_table_id = trim($this->input->post("cam_temp_table_id",true));
        $campaign_type = trim($this->input->post("campaign_type",true));


        $cur_date = date("Y-m-d H:i:s");

        if(isset($btntype) && !empty($btntype) && $btntype == "unsub")
        {
            if($contactType == "contact") {
                if($this->basic->update_data("sms_email_contacts", array("id"=>$contactid,"email"=>$email_address,"deleted"=>"0"), array("unsubscribed"=>"1"))) {

                    if($campaign_type!='Drip'){

                    if($this->basic->update_data("email_sending_campaign_send", array("id"=>$cam_temp_table_id, "campaign_id"=>$cam_id,"contact_id"=>$contactid), array("is_unsubscribed"=>"1","unsubscribed_at"=>$cur_date))) {

                        $increment_total_unsubscriber = "update email_sending_campaign set total_unsubscribed=total_unsubscribed+1, last_unsubscribed_at='{$cur_date}' where id='{$cam_id}'";

                        if($increment_total_unsubscriber != "") {
                            $this->db->query($increment_total_unsubscriber);
                        }
                    }

                }

                else{

                    if($this->basic->update_data("messenger_bot_drip_campaign_assign", array("id"=>$cam_temp_table_id, "messenger_bot_drip_campaign_id"=>$cam_id,"subscribe_id"=>$contactid), array("is_unsubscribed"=>"1","unsubscribed_at"=>$cur_date))) {

                        $increment_total_unsubscriber = "update messenger_bot_drip_campaign set total_unsubscribed=total_unsubscribed+1, last_unsubscribed_at='{$cur_date}' where id='{$cam_id}'";

                        if($increment_total_unsubscriber != "") {
                            $this->db->query($increment_total_unsubscriber);
                        }
                    }
                }


                    echo "1";
                } else {
                    echo "0";
                }
            } else if($contactType == "subscriber") {

                if($campaign_type!='Drip')
                    $update_subscriber_where=array("id"=>$contactid,"email"=>$email_address);
                else
                    $update_subscriber_where=array("subscribe_id"=>$contactid,"email"=>$email_address);


                if($this->basic->update_data("messenger_bot_subscriber",$update_subscriber_where, array("is_email_unsubscriber"=>"1"))) {



                    if($campaign_type!='Drip'){

                    if($this->basic->update_data("email_sending_campaign_send", array("id"=>$cam_temp_table_id, "campaign_id"=>$cam_id,"subscriber_id"=>$contactid), array("is_unsubscribed"=>"1","unsubscribed_at"=>$cur_date))) {

                        $increment_total_unsubscriber = "update email_sending_campaign set total_unsubscribed=total_unsubscribed+1, last_unsubscribed_at='{$cur_date}' where id='{$cam_id}'";

                        if($increment_total_unsubscriber != "") {
                            $this->db->query($increment_total_unsubscriber);
                        }
                    }

                    }


                    else{

                    if($this->basic->update_data("messenger_bot_drip_campaign_assign", array("id"=>$cam_temp_table_id, "messenger_bot_drip_campaign_id"=>$cam_id,"subscribe_id"=>$contactid), array("is_unsubscribed"=>"1","unsubscribed_at"=>$cur_date))) {

                        $increment_total_unsubscriber = "update messenger_bot_drip_campaign set total_unsubscribed=total_unsubscribed+1, last_unsubscribed_at='{$cur_date}' where id='{$cam_id}'";

                        if($increment_total_unsubscriber != "") {
                            $this->db->query($increment_total_unsubscriber);
                        }
                    }
                    
                    }







                    echo "1";
                } else {
                    echo "0";
                }

            }

        } else if(isset($btntype) && !empty($btntype) && $btntype == "sub")
        {
            if($contactType == "contact") {
                if($this->basic->update_data("sms_email_contacts", array("id"=>$contactid,"email"=>$email_address,"deleted"=>"0"), array("unsubscribed"=>"0"))) {


                if($campaign_type!='Drip'){

                    if($this->basic->update_data("email_sending_campaign_send", array("id"=>$cam_temp_table_id, "campaign_id"=>$cam_id,"contact_id"=>$contactid), array("is_unsubscribed"=>"0","unsubscribed_at"=>"0000-00-00 00:00"))) {

                        $increment_total_unsubscriber = "update email_sending_campaign set total_unsubscribed=total_unsubscribed-1 where id='{$cam_id}'";

                        if($increment_total_unsubscriber != "") {
                            $this->db->query($increment_total_unsubscriber);
                        }
                    }

                }

                else{

                     if($this->basic->update_data("messenger_bot_drip_campaign_assign", array("id"=>$cam_temp_table_id, "messenger_bot_drip_campaign_id"=>$cam_id,"subscribe_id"=>$contactid), array("is_unsubscribed"=>"0","unsubscribed_at"=>"0000-00-00 00:00"))) {

                        $increment_total_unsubscriber = "update messenger_bot_drip_campaign set total_unsubscribed=total_unsubscribed-1 where id='{$cam_id}'";

                        if($increment_total_unsubscriber != "") {
                            $this->db->query($increment_total_unsubscriber);
                        }
                    }

                }



                    echo "1";

                } else
                {
                    echo "0";
                }
            } else if($contactType == "subscriber") {


                 if($campaign_type!='Drip')
                    $update_subscriber_where=array("id"=>$contactid,"email"=>$email_address);
                else
                    $update_subscriber_where=array("subscribe_id"=>$contactid,"email"=>$email_address);


                if($this->basic->update_data("messenger_bot_subscriber", $update_subscriber_where , array("is_email_unsubscriber"=>"0"))) {


                if($campaign_type!='Drip'){

                    if($this->basic->update_data("email_sending_campaign_send", array("id"=>$cam_temp_table_id, "campaign_id"=>$cam_id,"subscriber_id"=>$contactid), array("is_unsubscribed"=>"0","unsubscribed_at"=>"0000-00-00 00:00"))) {

                        $increment_total_unsubscriber = "update email_sending_campaign set total_unsubscribed=total_unsubscribed-1 where id='{$cam_id}'";

                        if($increment_total_unsubscriber != "") {
                            $this->db->query($increment_total_unsubscriber);
                        }
                    }
                }


                else{

                  if($this->basic->update_data("messenger_bot_drip_campaign_assign", array("id"=>$cam_temp_table_id, "messenger_bot_drip_campaign_id"=>$cam_id,"subscribe_id"=>$contactid), array("is_unsubscribed"=>"1","unsubscribed_at"=>$cur_date))) {

                    $increment_total_unsubscriber = "update messenger_bot_drip_campaign set total_unsubscribed=total_unsubscribed-1, last_unsubscribed_at='{$cur_date}' where id='{$cam_id}'";

                    if($increment_total_unsubscriber != "") {

                        $this->db->query($increment_total_unsubscriber);

                    }

                    }

                }





                    echo "1";

                } else
                {
                    echo "0";
                }

            }
        }   
    }

    // Create labels and push them into dropdown
    public function common_create_label_and_assign()
    {
        $this->ajax_check();
        if(!$this->strict_ajax_call) $this->user_id = 1;
        $this->load->library("fb_rx_login"); 
        $social_media = 'fb';
        $page_table_id = $this->input->post("page_id",true);
        $label_name = $this->input->post("label_name",true);
        $social_media = $this->input->post("social_media",true);
        if($social_media=='') $social_media='fb';

        // $getdata = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_table_id)));      
        // $page_access_token = isset($getdata[0]['page_access_token'])?$getdata[0]['page_access_token']:"";

        $is_exists = $this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("page_id"=>$page_table_id,"group_name"=>$label_name,"social_media"=>$social_media)));

        if(isset($is_exists[0]))
        {
           $insert_id = $is_exists[0]['id'];
           $label_id = $is_exists[0]['label_id'];

        } else {
            
            // $response=$this->fb_rx_login->create_label($page_access_token,$label_name);
            $response = ['id'=>''];

            if(isset($response['error']) && !empty($response['error'])) {
                echo json_encode(["error"=>$response['error']['message']]);
                exit;
            }
            $label_id=isset($response['id']) ? $response['id'] : "";
            $this->basic->insert_data("messenger_bot_broadcast_contact_group",array("page_id"=>$page_table_id,"group_name"=>$label_name,"user_id"=>$this->user_id,"label_id"=>$label_id,"social_media"=>$social_media));
            $insert_id = $this->db->insert_id();
        }

        echo json_encode(array('id'=>$insert_id,"text"=>$label_name));
    }

    public function common_get_postback()
    {
        if(!$_POST) exit();
        $is_from_add_button=$this->input->post('is_from_add_button');
        $page_id=$this->input->post('page_id');// database id      
        $order_by=$this->input->post('order_by');     
        if($order_by=="") $order_by="id DESC";
        else $order_by=$order_by." ASC";
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,"is_template"=>"1",'template_for'=>'reply_message')),'','','',$start=NULL,$order_by);
        $push_postback="";

        if($is_from_add_button=='0')
        {
            $push_postback.="<option value=''>".$this->lang->line("Select")."</option>";
        }
        
        foreach ($postback_data as $key => $value) 
        {
            $push_postback.="<option value='".$value['id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }

        if($is_from_add_button=='1' || $is_from_add_button=='')
        {
            $push_postback.="<option value=''>".$this->lang->line("Select")."</option>";
        }

        echo $push_postback;   
    }



    protected function send_email_to_autoresponder($settings='', $email='',$first_name='',$last_name='',$type='singnup',$user_id="0",$tags=''){

    /*   $settings= '{"mailchimp":["37","49"]}';
        $first_name="Konok";
        $last_name='Zaman';
        $email="konok6@xerooneit.net";
        $user_id=1;
        $type='admin';*/


        $data_to_send['firstname']=$first_name;
        $data_to_send['lastname']=$last_name;
        $data_to_send['email']=$email;

        if($settings=="") exit;

        $settings_array=json_decode($settings,TRUE);

        if(empty($settings_array)) exit; 

        if(isset($settings_array['mailchimp']) && !empty($settings_array['mailchimp'])){

            $join=array('mailchimp_config'=>'mailchimp_config.id=mailchimp_list.mailchimp_config_id,left');
            $where_mailchimp['where_in']=array('mailchimp_list.id'=>$settings_array['mailchimp']); 
            $mailchimp_config_data = $this->basic->get_data("mailchimp_list",$where_mailchimp,$select='',$join);

            $this->load->library("mailchimp_api");

            foreach ($mailchimp_config_data as $mailchimp_data) {

               $list_id=isset($mailchimp_data['list_id']) ? $mailchimp_data['list_id']: "";
               $api_key=isset($mailchimp_data['api_key']) ? $mailchimp_data['api_key']: "";  
               $mailchimp_config_id=isset($mailchimp_data['mailchimp_config_id']) ? $mailchimp_data['mailchimp_config_id']: "0";  

               $result=$this->mailchimp_api->syncMailchimp($data_to_send, $api_key, $list_id,$tags);

               $result_array=json_decode($result,TRUE);
               $status= isset($result_array['status']) ? $result_array['status']: "0";

                //Insert into Log table 
               $now_time=date('Y-m-d H:i:s');
               $insert_data=array('user_id'=>$user_id,'settings_type'=>$type,'status'=>$status,'email'=>$email,'auto_responder_type'=>"Email Autoresponder",'api_name'=>'MailChimp','response'=>$result,'insert_time'=>$now_time,'mailchimp_config_id'=>$mailchimp_config_id);
               $this->basic->insert_data("send_email_to_autoresponder_log",$insert_data);


            }
        }


        if(isset($settings_array['sendinblue']) && !empty($settings_array['sendinblue'])){

            $join=array('mailchimp_config'=>'mailchimp_config.id=mailchimp_list.mailchimp_config_id,left');

            $where_mailchimp['where_in']=array('mailchimp_list.id'=>$settings_array['sendinblue']); 
            $where_mailchimp['where']=array('mailchimp_config.service_type'=>"sendinblue"); 

            $sendinblue_config_data = $this->basic->get_data("mailchimp_list",$where_mailchimp,$select='',$join);

            $this->load->library("mailchimp_api");

            foreach ($sendinblue_config_data as $sendinblue_data) {

               $list_id=isset($sendinblue_data['list_id']) ? $sendinblue_data['list_id']: "";
               $api_key=isset($sendinblue_data['api_key']) ? $sendinblue_data['api_key']: "";  
               $sendinblue_config_id=isset($sendinblue_data['mailchimp_config_id']) ? $sendinblue_data['mailchimp_config_id']: "0";  

               $result=$this->mailchimp_api->sendinblue_add_contact($api_key,$email,$first_name,$last_name,$list_id);

               $result_array=json_decode($result,TRUE);
               $status= isset($result_array['code']) ? $result_array['code']: "Success";

                //Insert into Log table 
               $now_time=date('Y-m-d H:i:s');
               $insert_data=array('user_id'=>$user_id,'settings_type'=>$type,'status'=>$status,'email'=>$email,'auto_responder_type'=>"Email Autoresponder",'api_name'=>'Sendinblue','response'=>$result,'insert_time'=>$now_time,'mailchimp_config_id'=>$sendinblue_config_id);
               $this->basic->insert_data("send_email_to_autoresponder_log",$insert_data);


            }
        }



       	if(isset($settings_array['activecampaign']) && !empty($settings_array['activecampaign'])){

            $join=array('mailchimp_config'=>'mailchimp_config.id=mailchimp_list.mailchimp_config_id,left');
            $where_mailchimp['where_in']=array('mailchimp_list.id'=>$settings_array['activecampaign']); 
            $where_mailchimp['where']=array('mailchimp_config.service_type'=>"activecampaign"); 
            $activecampaign_config_data = $this->basic->get_data("mailchimp_list",$where_mailchimp,$select='',$join);

            $this->load->library("mailchimp_api");

            foreach ($activecampaign_config_data as $activecampaign_data) {

               $list_id=isset($activecampaign_data['list_id']) ? $activecampaign_data['list_id']: "";
               $api_key=isset($activecampaign_data['api_key']) ? $activecampaign_data['api_key']: ""; 
               $api_url=isset($activecampaign_data['api_url']) ? $activecampaign_data['api_url']: ""; 

               $activecampaign_config_id=isset($activecampaign_data['mailchimp_config_id']) ? $activecampaign_data['mailchimp_config_id']: "0";  

               $result=$this->mailchimp_api->activecampaign_add_contact($api_key,$api_url,$email,$first_name,$last_name,$list_id);

               $result_array=json_decode($result,TRUE);
               $status= isset($result_array['errors']) ? $result_array['errors'][0]['code']: "Success";

                //Insert into Log table 
               $now_time=date('Y-m-d H:i:s');
               $insert_data=array('user_id'=>$user_id,'settings_type'=>$type,'status'=>$status,'email'=>$email,'auto_responder_type'=>"Email Autoresponder",'api_name'=>'Activecampaign','response'=>$result,'insert_time'=>$now_time,'mailchimp_config_id'=>$activecampaign_config_id);
               $this->basic->insert_data("send_email_to_autoresponder_log",$insert_data);


            }
        }


        if(isset($settings_array['mautic']) && !empty($settings_array['mautic'])){

            $join=array('mailchimp_config'=>'mailchimp_config.id=mailchimp_list.mailchimp_config_id,left');
            $where_mailchimp['where_in']=array('mailchimp_list.id'=>$settings_array['mautic']); 
            $where_mailchimp['where']=array('mailchimp_config.service_type'=>"mautic"); 
            $mautic_config_data = $this->basic->get_data("mailchimp_list",$where_mailchimp,$select='',$join);

            $this->load->library("mailchimp_api");

            foreach ($mautic_config_data as $mautic_data) {

               $list_id=isset($mautic_data['list_id']) ? $mautic_data['list_id']: "";
               $api_key=isset($mautic_data['api_key']) ? $mautic_data['api_key']: ""; 
               $api_url=isset($mautic_data['api_url']) ? $mautic_data['api_url']: ""; 

               $mautic_config_id=isset($mautic_data['mailchimp_config_id']) ? $mautic_data['mailchimp_config_id']: "0";  

               $result=$this->mailchimp_api->mautic_add_contact($api_key,$api_url,$email,$first_name,$last_name,$list_id,$tags);

               $result_array=json_decode($result,TRUE);
               $status= isset($result_array['errors']) ? $result_array['errors'][0]['message']: "Success";

                //Insert into Log table 
               $now_time=date('Y-m-d H:i:s');
               $insert_data=array('user_id'=>$user_id,'settings_type'=>$type,'status'=>$status,'email'=>$email,'auto_responder_type'=>"Email Autoresponder",'api_name'=>'Mautic','response'=>$result,'insert_time'=>$now_time,'mailchimp_config_id'=>$mautic_config_id);
               $this->basic->insert_data("send_email_to_autoresponder_log",$insert_data);


            }
        }


         if(isset($settings_array['acelle']) && !empty($settings_array['acelle'])){

            $join=array('mailchimp_config'=>'mailchimp_config.id=mailchimp_list.mailchimp_config_id,left');
            $where_mailchimp['where_in']=array('mailchimp_list.id'=>$settings_array['acelle']); 
            $where_mailchimp['where']=array('mailchimp_config.service_type'=>"acelle"); 
            $acelle_config_data = $this->basic->get_data("mailchimp_list",$where_mailchimp,$select='',$join);

            $this->load->library("mailchimp_api");

            foreach ($acelle_config_data as $acelle_data) {

               $list_id=isset($acelle_data['list_id']) ? $acelle_data['list_id']: "";
               $api_key=isset($acelle_data['api_key']) ? $acelle_data['api_key']: ""; 
               $api_url=isset($acelle_data['api_url']) ? $acelle_data['api_url']: ""; 

               $acelle_config_id=isset($acelle_data['mailchimp_config_id']) ? $acelle_data['mailchimp_config_id']: "0";  

               $result=$this->mailchimp_api->acelle_add_contact($api_key,$api_url,$email,$first_name,$last_name,$list_id);

               $result_array=json_decode($result,TRUE);
               $status= isset($result_array['errors']) ? $result_array['errors'][0]['message']: "Success";

                //Insert into Log table 
               $now_time=date('Y-m-d H:i:s');
               $insert_data=array('user_id'=>$user_id,'settings_type'=>$type,'status'=>$status,'email'=>$email,'auto_responder_type'=>"Email Autoresponder",'api_name'=>'Acelle','response'=>$result,'insert_time'=>$now_time,'mailchimp_config_id'=>$acelle_config_id);
               $this->basic->insert_data("send_email_to_autoresponder_log",$insert_data);

            }
        }









    }



    public function send_sms_by_for_bot_phone_number($sms_api='',$user_id='',$message='',$phone_number=''){

        $status="";
        $message_sent_id="";


        $this->load->library('Sms_manager');
        $this->sms_manager->set_credentioal($sms_api,$user_id);
        $response = $this->sms_manager->send_sms($message, $phone_number);

        if(isset($response['id']) && !empty($response['id']))
        {   
            $message_sent_id = $response['id'];
            $status='Success';
        }
        else 
        {   if(isset($response['status']) && !empty($response['status'])){
                $message_sent_id = $response["status"];
                 $status='Error';
            }
        }    


        $now_time=date('Y-m-d H:i:s');
        $insert_data=array('user_id'=>$user_id,'settings_type'=>'quick-reply','status'=>$status,'email'=>$phone_number,'auto_responder_type'=>"SMS Sender",'api_name'=>$this->sms_manager->gateway_name,'response'=>$message_sent_id,'insert_time'=>$now_time,'mailchimp_config_id'=>$sms_api);
        $this->basic->insert_data("send_email_to_autoresponder_log",$insert_data);


         if($status=='Error')

            $response["error"]["message"]=$message_sent_id;

        else
            $response['message_id'] = $message_sent_id;

        return $response;

    }


     public function send_email_by_for_bot_email($email_config_table='',$campaign_message_send='',$contact_email='', $subject='',$user_id=''){

            $status="";
            $message_sent_id="";

             /*****get the email configuration value*****/
            $from_email_separate=explode("_", $email_config_table);
            $email_api_id=array_pop($from_email_separate);
            $config_type=implode('_', $from_email_separate);
            
           if ($config_type == "email_smtp_config") {
                $from_email = "smtp_".$email_api_id;
                $gateway_type="smtp";
           }

            elseif ($config_type == "email_mandrill_config") {
                $from_email = "mandrill_".$email_api_id;
                $gateway_type="mandrill";
            }

            elseif ($config_type == "email_sendgrid_config") {
                $from_email = "sendgrid_".$email_api_id;
                $gateway_type="sendgrid";
            }

            elseif ($config_type == "email_mailgun_config") {
                $from_email = "mailgun_".$email_api_id;
                $gateway_type="mailgun";
            }


            try
            {
                $campaign_message_send = $campaign_message_send;
                $response = $this->_email_send_function($from_email, $campaign_message_send, $contact_email, $subject,'','',$user_id);
                if(isset($response) && !empty($response) && $response == "Submited")
                {   
                    $message_sent_id = $response; 
                    $status='Success';
                }
                else 
                {   
                    $message_sent_id = $response;
                    $status='Error';
                }           
                }
                catch(Exception $e) 
                {
                    $message_sent_id = $e->get_message();
                    $status='Error';
                }

            $now_time=date('Y-m-d H:i:s');
            $insert_data=array('user_id'=>$user_id,'settings_type'=>'quick-reply','status'=>$status,'email'=>$contact_email,'auto_responder_type'=>"Email Sender",'api_name'=>$gateway_type,'response'=>$message_sent_id,'insert_time'=>$now_time,'mailchimp_config_id'=>$email_api_id);
            $this->basic->insert_data("send_email_to_autoresponder_log",$insert_data);

            $response=array();

            if($status=='Error')
                $response["error"]["message"]=$message_sent_id;
            else
                 $response['message_id'] = $message_sent_id;

            return $response;




    }







    public function xit_load_files($folder='',$file='')
    {
        if($folder == '' || $file == '')
        {
            echo "";
            exit;
        }
        $file_name_array = explode('.', $file);
        $file_name_extension = array_pop($file_name_array);
        header('Access-Control-Allow-Origin: *');
        if($file_name_extension == 'css')
            header("Content-type: text/css", true);
        if($file_name_extension == 'js')
        header('Content-Type: application/javascript', true);

        $folder = str_replace('-', '/', $folder);
        $current_theme = $this->config->item('current_theme');
        if($current_theme == '') $current_theme = 'modern';
        $path = "application/views/site/".$current_theme."/".$folder."/".$file;
        $content = file_get_contents($path);
        echo $content;
    }

    protected function get_theme_data($path="")
    {
        $path=str_replace('\\','/',$path);
        $tokens=token_get_all(file_get_contents($path));
        $addon_data=array();

        $addon_path=explode('/', $path);
        $controller_name=array_pop($addon_path);
        array_pop($addon_path);
        $addon_path=implode('/',$addon_path);

        $comments = array();
        foreach($tokens as $token) 
        {
            if($token[0] == T_COMMENT || $token[0] == T_DOC_COMMENT) 
            {       
                $comments[] = isset( $token[1]) ?  $token[1] : "";
            } 
        }
        $comment_str=isset($comments[0]) ? $comments[0] : "";
        
        preg_match( '/^.*?theme name:(.*)$/mi', $comment_str, $match); 
        $addon_data['theme_name'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?unique name:(.*)$/mi', $comment_str, $match); 
        $addon_data['unique_name'] = isset($match[1]) ? trim($match[1]) : "";


        preg_match( '/^.*?theme uri:(.*)$/mi', $comment_str, $match); 
        $addon_data['theme_uri'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?author:(.*)$/mi', $comment_str, $match); 
        $addon_data['author'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?author uri:(.*)$/mi', $comment_str, $match); 
        $addon_data['author_uri'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?version:(.*)$/mi', $comment_str, $match); 
        $addon_data['version'] = isset($match[1]) ? trim($match[1]) : "1.0";

        preg_match( '/^.*?description:(.*)$/mi', $comment_str, $match); 
        $addon_data['description'] = isset($match[1]) ? trim($match[1]) : "";

        return $addon_data;
    }

    public function load_builder()
    {
        $data['body'] = 'flow_builder/index';
        $data['page_table_id'] = 241;
        $this->_subscription_viewcontroller($data); 
    } 

    public function switch_to_media()
    {
      $this->ajax_check();
      $media_type = $this->session->userdata('selected_global_media_type');
      if($media_type=='') $media_type = 'fb';
      $new_media_type = $media_type=='fb' ? 'ig' : 'fb';
      $this->session->set_userdata('selected_global_media_type',$new_media_type);
      echo "1";
    }

    public function switch_to_page()
    {
      $this->ajax_check();
      $page_id = $this->input->post("page_id");
      $return_social_media_by_force = $this->input->post("return_social_media_by_force");
      $return_social_media_by_force = $return_social_media_by_force=='1' ? true : false;

      $explode_page_id = explode_page_id($page_id);
      $page_id = $explode_page_id['page_id'];
      $social_media = $explode_page_id['social_media'];
      $this->session->set_userdata('selected_global_page_table_id',$page_id);
      if($return_social_media_by_force) $this->session->set_userdata('selected_global_media_type',$social_media);
      echo "1";
    }

}
