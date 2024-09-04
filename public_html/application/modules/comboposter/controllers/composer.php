<?php
/* 
Addon Name: Comboposter
Unique Name: comboposter
Module ID: 220
Project ID: 19
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: http://xeroneit.net
Version: 1.0
Description: 
*/

require_once("application/controllers/Home.php"); // loading home controller

require_once("application/modules/comboposter/controllers/Login_callback_handler.php");
require_once("application/modules/comboposter/controllers/Existing_social_accounts_handler.php");
require_once("application/modules/comboposter/controllers/Text_post_handler.php");
require_once("application/modules/comboposter/controllers/Image_post_handler.php");
require_once("application/modules/comboposter/controllers/Video_post_handler.php");
require_once("application/modules/comboposter/controllers/Link_post_handler.php");
require_once("application/modules/comboposter/controllers/Html_post_handler.php");

class Comboposter extends Home
{
    public $addon_data=array();
    public function __construct()
    {
        parent::__construct();

        $function_name=$this->uri->segment(2);
        if($function_name!="post_to_all_media") 
        {
            if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');         
            $this->member_validity();
        }

        $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
        <span class="math-inline">this\-\>addon\_data\=</span>this->get_addon_data($addon_path);
        $this->user_id=$this->session->userdata('user_id'); // user_id of logged in user, we may need it 
    }


    public function index()
    {
        $this->social_accounts();
    }

    public function social_accounts()
    {
        /* load libraries */
        $this->load->library('Google_youtube_login', NULL, 'google');
        $this->load->library('Twitter');
        // $this->load->library('Tumblr');
        $this->load->library('Line'); // เพิ่ม Line library
        $this->load->library('Line_notify'); // เพิ่ม Line Notify library
        $this->load->library('Dialogflow'); // เพิ่ม Dialogflow library
        $this->load->library('Gemini'); // เพิ่ม Gemini library
        $this->load->library('Tiktok');
        $this->load->library('Linkedin');
        $this->load->library('Medium');
        $this->load->library('Pinterests', NULL, 'pinterest');
        $this->load->library('Reddit');
        $this->load->library('Wp_org_poster', NULL, 'wordpress');
        $this->load->library('Wordpress_self_hosted', NULL, 'wordpress_self_hosted');


        /* get login buttons */
        $redirect_url = base_url('comboposter/login_callback');

        $data['youtube_login_button'] = $this->google->youtube_login_button($redirect_url. '/youtube');
        $data['blogger_login_button'] = $this->google->blogger_login_button($redirect_url. '/blogger');
        $data['twitter_login_button'] = $this->twitter->login_button($redirect_url. '/twitter');
        // $data['tumblr_login_button'] = $this->tumblr->login_button($redirect_url. '/tumblr');
       
        $data['line_login_button'] = $this->line->login_button($redirect_url. '/line'); // เพิ่มปุ่ม login Line
        $data['line_notify_login_button'] = $this->line_notify->login_button($redirect_url. '/line_notify'); // เพิ่มปุ่ม login Line Notify
        
        $data['Tiktok_login_button'] = $this->Tiktok->login_button($redirect_url. '/tiktok');
        
        
        $data['linkedin_login_button'] = $this->linkedin->login_button($redirect_url. '/linkedin');
        // $data['medium_login_button'] = $this->medium->login_button($redirect_url. '/medium');
        $data['reddit_login_button'] = $this->reddit->login_button($redirect_url. '/reddit');
        $data['wordpress_login_button'] = $this->wordpress->login_button($redirect_url. '/wordpress');
        $data['wordpress_self_hosted_login_button'] = $this->wordpress_self_hosted->login_button();
        $data['pinterest_login_button'] = "<a href='". base_url('social_apps/pinterest_settings') ."' class='btn btn-outline-primary login_button' social_account='pinterest'><i class='fas fa-plus-circle'></i> ".$this->lang->line("Import Account")."</a>";;


        /**
         * get social accounts lists
         */
        $data['twitter_account_list'] = $this->basic->get_data('twitter_users_info', array('where' => array('user_id' => $this->user_id)));
        // $data['tumblr_account_list'] = $this->basic->get_data('tumblr_users_info', array('where' => array('user_id' => $this->user_id)));
        $data['linkedin_account_list'] = $this->basic->get_data('linkedin_users_info', array('where' => array('user_id' => $this->user_id)));

        $data['medium_account_list'] = $this->basic->get_data('medium_users_info', array('where' => array('user_id' => $this->user_id)));

        $data['reddit_account_list'] = $this->basic->get_data('reddit_users_info', array('where' => array('user_id' => $this->user_id)));
        $data['youtube_channel_list'] = $this->basic->get_data('youtube_channel_list', array('where' => array('user_id' => $this->user_id)));
        $data['blogger_account_list'] = $this->basic->get_data('blogger_users_info', array('where' => array('user_id' => $this->user_id)));
        $data['wordpress_account_list'] = $this->basic->get_data('wordpress_users_info', array('where' => array('user_id' => $this->user_id)));
        $data['wordpress_account_list_self_hosted'] = $this->basic->get_data('wordpress_config_self_hosted', array('where' => array('user_id' => $this->user_id)));
        $data['pinterest_account_list'] = $this->basic->get_data('pinterest_users_info', array('where' => array('user_id' => $this->user_id)));
        // echo "<pre>";print_r($data['pinterest_account_list']);
        

        $data['page_title'] = $this->lang->line('Social Accounts');
        $data['title'] = $this->lang->line('Social Accounts');
        $data['body'] = 'social_account_list';

        $this->_viewcontroller($data);
    }

    public function delete_social_account()
    {
        $this->ajax_check();

        $social_media = $this->input->post('social_media', true);
        $table_id = $this->input->post('table_id', true);

        $response = array();
        $response['status'] = 'success';
        $response['message'] = $this->lang->line("Your account has deleted successfully.");

        if ($social_media == 'twitter') {

            $this->basic->delete_data('twitter_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
            $this->_delete_usage_log($module_id = 102, $request = 1);
        } 
        // else if ($social_media == 'tumblr') {

        //     $this->basic->delete_data('tumblr_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
        //     $this->_delete_usage_log($module_id = 102, $request = 1);
        // }
         else if ($social_media == 'linkedin') {

            $this->basic->delete_data('linkedin_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
            $this->_delete_usage_log($module_id = 103, $request = 1);

        } else if ($social_media == 'medium') {

            $this->basic->delete_data('medium_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
            $this->_delete_usage_log($module_id = 277, $request = 1);
            
        } else if ($social_media == 'reddit') {

            $this->basic->delete_data('reddit_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
            $this->_delete_usage_log($module_id = 105, $request = 1);

        } else if ($social_media == 'pinterest') {

            $this->basic->delete_data('pinterest_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
            $this->basic->delete_data('pinterest_board_info', array('user_id' => $this->user_id, 'pinterest_table_id' => $table_id));

            $this->_delete_usage_log($module_id = 101, $request = 1);
        } else if ($social_media == 'blogger') {

            $this->basic->delete_data('blogger_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
            $this->basic->delete_data('blogger_blog_info', array('user_id' => $this->user_id, 'blogger_users_info_table_id' => $table_id));

            $this->_delete_usage_log($module_id = 107, $request = 1);
        } else if ($social_media == 'wordpress') {

            $this->basic->delete_data('wordpress_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
            $this->_delete_usage_log($module_id = 108, $request = 1);

        } else if ($social_media == 'line') { // เพิ่มการลบ Line account

            $this->basic->delete_data('line_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
            // เพิ่มการลบ usage log ของ Line หากจำเป็น

        } else if ($social_media == 'line_notify') { // เพิ่มการลบ Line Notify account

            $this->basic->delete_data('line_notify_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
            // เพิ่มการลบ usage log ของ Line Notify หากจำเป็น

        } else if ($social_media == 'tiktok') { // เพิ่มการลบ TikTok account

            $this->basic->delete_data('tiktok_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
            // เพิ่มการลบ usage log ของ TikTok หากจำเป็น

        } 

        echo json_encode($response);
    }


    public function set_empty_app_error($social_media)
    {

        $this->session->set_userdata('account_import_error', $this->lang->line("Perhaps admin has not set corresponding apps information yet."));
        redirect(base_url('comboposter/social_accounts'), 'location');
    }

    public function login_callback($social_media = '')
    {
        $login_callback_handler = new Login_callback_handler($this);

        if ($social_media == 'twitter') {

            $oauth_verifier = $_GET['oauth_verifier'];
            $login_callback_handler->twitter($oauth_verifier);

        }

        // else if ($social_media == 'tumblr') {

        //     $auth_varifier = $_GET['oauth_verifier'];
        //     $login_callback_handler->tumblr($auth_varifier);

        // }
         else if ($social_media == 'linkedin') {

            if (!isset($_GET['code'])) {

                $this->session->set_userdata('account_import_error', $this->lang->line("Something went wrong while importing your account."));
                redirect(base_url('comboposter/social_accounts'),'refresh');
            } else {
                $code = $_GET['code'];
            }

            $login_callback_handler->linkedin($code);

        } else if ($social_media == 'medium') {

            $integration_token = trim($this->input->post("integration_token",true));

            if (!isset($integration_token)) {

                $this->session->set_userdata('account_import_error', $this->lang->line("Something went wrong while importing your account."));
                redirect(base_url('comboposter/social_accounts'),'refresh');
            } else {
                $code = $integration_token;
            }

            <span class="math-inline"><1\>login\_callback\_handler\-\>medium\(</span>code);

        } else if ($social_media == 'reddit') {

            $code = $_GET["code"];
            $login_callback_handler->reddit($code);

        } else if ($social_media == 'pinterest') {

            $code = $_GET['code'];
            $login_callback_handler->pinterest($code);

        } else if ($social_media == 'wordpress') {

            $login_callback_handler->wordpress();
            
        } else if ($social_media == 'wordpress_self_hosted') {

            $login_callback_handler->wordpress_self_hosted();
            
        } else if ($social_media == 'wordpress_self_hosted_callback') {

            $login_callback_handler->wordpress_self_hosted_callback();
            
        } else if ($social_media == 'youtube') {

            $login_callback_handler->youtube();

        } else if ($social_media == 'blogger') {

            $login_callback_handler->blogger();

        } else if ($social_media == 'line') { // เพิ่ม callback สำหรับ Line

            $code = $_GET['code'];
            $login_callback_handler->line($code); 

        } else if ($social_media == 'line_notify') { // เพิ่ม callback สำหรับ Line Notify

            $code = $_GET['code'];
            $login_callback_handler->line_notify($code); 

        } else if ($social_media == 'tiktok') { // เพิ่ม callback สำหรับ TikTok

            $code = $_GET['code'];
            $login_callback_handler->tiktok($code); 

        }
    }


    public function campaigns_info($campaign_type ='')
    {
        if ($campaign_type == '') {
            redirect('404','refresh');
        }

        /* check if has module access */
        $module_id_for_this_type = 0;

        if ($campaign_type == 'text') {

            $data['icon'] = 'fa fa-file-text';
            $module_id_for_this_type = 110;
        } else if ($campaign_type == 'image') {

            $data['icon'] = 'fa fa-picture-o';
            $module_id_for_this_type = 111;
        } else if ($campaign_type == 'video') {

            $data['icon'] = 'fas fa-video';
            $module_id_for_this_type = 112;
        } else if ($campaign_type == 'link') {

            $data['icon'] = 'fa fa-link ';
            $module_id_for_this_type = 113;
        } else if ($campaign_type == 'html') {

            $data['icon'] = 'fa fa-html5';
            $module_id_for_this_type = 114;
        }

        // echo "<pre>";print_r($module_id_for_this_type);exit;

        if ($this->session->userdata('user_type') == 'Member' && !in_array($module_id_for_this_type,$this->module_access)) {
           redirect('404','refresh');
        }



        $data['campaign_type'] = $campaign_type;

        $title = ucfirst($campaign_type).' post';
        $data['page_title'] = $this->lang->line($title);

$data['title'] = $this->lang->line($title);
        $data['body'] = 'posts/campaigns';

        $this->_viewcontroller($data);
    }


    public function campaigns_info_data($campaign_type)     
    {
        $this->ajax_check();

        $searching       = trim($this->input->post("searching",true));
        $post_date_range = $this->input->post("post_date_range",true);
        $display_columns = array("#",'id','campaign_name','campaign_type','posting_medium', 'action','posting_status','schedule_time');
        $search_columns = array('campaign_name');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple=array();

        if($post_date_range!="")
        {
            $exp = explode('|', $post_date_range);
            $from_date = isset($exp[0])?$exp[0]:"";
            $to_date   = isset($exp[1])?$exp[1]:"";

            if($from_date!="Invalid date" && $to_date!="Invalid date")
            {
                $from_date = date('Y-m-d', strtotime($from_date));
                $to_date   = date('Y-m-d', strtotime($to_date));
                $where_simple["Date_Format(schedule_time,'%Y-%m-%d') >="] = $from_date;
                $where_simple["Date_Format(schedule_time,'%Y-%m-%d') <="] = $to_date;
            }
        }

        if($searching !="") $where_simple['campaign_name like'] = "%".$searching."%";
        $where_simple['user_id'] = $this->user_id;

        $this->db->where("campaign_type='". $campaign_type."'");
        $this->db->where("is_child='0'");
        $where  = array('where'=>$where_simple);

        $table = "comboposter_campaigns";
        $info = $this->basic->get_data($table,$where,"","",$limit,$start,$order_by,$group_by='');

        $this->db->where("campaign_type='". $campaign_type."'");
        $total_rows_array=$this->basic->count_row($table, $where, $count=$table.".id", "", $group_by='');
        $total_result=$total_rows_array[0]['total_rows'];



        /* complete main campaign count */
        $query = $this->db->query("SELECT parent_campaign_id, COUNT(posting_status) as complete FROM `comboposter_campaigns` WHERE parent_campaign_id IS NOT NULL AND parent_campaign_id != '0' AND campaign_type = '{$campaign_type}' AND posting_status = 'completed' GROUP BY parent_campaign_id");
        $temp_complete_results = $query->result_array();
        $complete_results = array();

        foreach ($temp_complete_results as $key => $value) {
        	$complete_results[$value['parent_campaign_id']] = $value['complete'];
        }



        for($i=0;$i<count($info);$i++)
        {   
            $posting_status = $info[$i]['posting_status'];
            $schedule_type  = $info[$i]['schedule_type'];
           
            if ($posting_status == 'pending') {
                $info[$i]['action'] = '<div style="min-width:180px">';
            } else {
                $info[$i]['action'] = '<div style="min-width:130px">';
            }



            /* posting status */
            if( $posting_status == 'completed') {

                if ($info[$i]['parent_campaign_id'] == "0") {
                    
                    if ($info[$i]['full_complete'] == "1") {
                        $info[$i]['posting_status'] = '<div style="min-width:120px;" class="text-success"><i class="fas fa-check-circle"></i> '.$this->lang->line("Completed").'</div>';
                    } else {

                    	$completed = 1;
                    	if (isset($complete_results[$info[$i]['id']])) {
                    		$completed = $complete_results[$info[$i]['id']] + 1;
                    	}

                    	$total = $info[$i]['repeat_times'] + 1;
                        $info[$i]['posting_status'] = '<div style="min-width:120px;" class="text-muted"><i class="fas fa-exclamation-circle"></i> '. $completed . '/' . $total . ' '. $this->lang->line("completed").'</div>';
                    }
                    
                } else {
                    
                    $info[$i]['posting_status'] = '<div style="min-width:120px;" class="text-success"><i class="fas fa-check-circle"></i> '.$this->lang->line("Completed").'</div>';
                }
            } else if( $posting_status == 'processing') {

                $info[$i]['posting_status'] = '<div style="min-width:120px;" class="text-warning"><i class="fas fa-spinner"></i> '.$this->lang->line("Processing").'</div>';
            } else {

                $info[$i]['posting_status'] = '<div style="min-width:120px;" class="text-danger"><i class="far fa-times-circle"></i> '.$this->lang->line("Pending").'</div>';

                $info[$i]['action'] .= '<a href="'. base_url('comboposter/'.$campaign_type.'_post/edit/'.$info[$i]['id']) .'" class="btn btn-outline-warning btn-circle" data-toggle="tooltip" data-placement="top" data-title="'. $this->lang->line("Edit this campaign") .'"><i class="fas fa-edit"></i></a>';
            }
            

            /* time scheduled */
            if($info[$i]['schedule_time'] != "0000-00-00 00:00:00")
                $info[$i]['schedule_time'] = "<div style='min-width:120px !important;'>".date("M j, y H:i",strtotime($info[$i]['schedule_time']))."</div>";
            else 
                $info[$i]['schedule_time'] = "<div style='min-width:120px !important;' class='text-muted'><i class='fas fa-exclamation-circle'></i> ".$this->lang->line('Not Scheduled')."</div>";


            
            /* posting mediums */
            $posting_medium_list = json_decode($info[$i]['posting_medium'], true);
            $posting_medium = array();

            foreach ($posting_medium_list as $single_medium) {

                $temp = explode('_', <span class="math-inline">single\_medium\);
array\_push\(</span>posting_medium, ucfirst($temp[0]));
            }

            $posting_medium = array_unique($posting_medium);
            $info[$i]['posting_medium'] = implode(', ', $posting_medium);



            /* campaign actions */
            $info[$i]['action'] .= ' <a href="'. base_url('comboposter/'.$campaign_type.'_post/clone/'.$info[$i]['id']) .'" class="btn btn-outline-info btn-circle" data-toggle="tooltip" data-placement="top" data-title="'. $this->lang->line("Clone this campaign") .'"><i class="fa fa-clone"></i></a>';

            if ($info[$i]['parent_campaign_id'] == "0") {

                $info[$i]['action'] .= ' <a href="#" class="btn btn-outline-primary btn-circle main_campaign_report" data-toggle="tooltip" data-placement="top" data-title="'. $this->lang->line("View campaign report") .'"  campaign_id="'. <span class="math-inline">info\[</span>
