<?



<?php

// ... (ส่วนอื่นๆ ของโค้ดใน Controller)

public function line_settings()
{
    // ตรวจสอบสิทธิ์การเข้าถึง (ถ้าจำเป็น)

    $data['page_title'] = $this->lang->line('Line Settings');
    $data['title'] = $this->lang->line('Line Settings');
    $data['body'] = 'admin/social_apps/line_settings'; // ปรับ path ของ view ให้ถูกต้อง

    $this->_viewcontroller($data);
}

public function edit_line_settings($id = 0)
{
    // ตรวจสอบสิทธิ์การเข้าถึง (ถ้าจำเป็น)

    if ($id == 0) {
        redirect('social_apps/line_settings', 'refresh');
    }

    $data['page_title'] = $this->lang->line('Edit Line Settings');
    $data['title'] = $this->lang->line('Edit Line Settings');
    $data['body'] = 'admin/social_apps/line_settings'; // ปรับ path ของ view ให้ถูกต้อง

    $data['line_settings'] = $this->basic->get_data('line_accounts', array('where' => array('id' => $id, 'user_id' => $this->user_id)));
    if (empty($data['line_settings'])) {
        redirect('social_apps/line_settings', 'refresh');
    }

    $this->_viewcontroller($data);
}

public function line_settings_update_action()
{
    // ตรวจสอบสิทธิ์การเข้าถึง (ถ้าจำเป็น)

    if (!isset($_POST)) exit;

    $this->form_validation->set_rules('channel_id', $this->lang->line("Channel ID"), 'trim|required');
    $this->form_validation->set_rules('channel_secret', $this->lang->line("Channel Secret"), 'trim|required');
    $this->form_validation->set_rules('channel_access_token', $this->lang->line("Channel Access Token"), 'trim|required');

    if ($this->form_validation->run() == FALSE) {
        if ($this->input->post('table_id')) {
            $this->edit_line_settings($this->input->post('table_id'));
        } else {
            $this->line_settings();
        }
    } else {

        $this->csrf_token_check();

        $insert_data['channel_id'] = $this->input->post('channel_id', true);
        $insert_data['channel_secret'] = $this->input->post('channel_secret', true);
        $insert_data['channel_access_token'] = $this->input->post('channel_access_token', true);
        $insert_data['user_id'] = $this->user_id;
        $insert_data['updated_at'] = date('Y-m-d H:i:s');

        $table_id = $this->input->post('table_id');

        if ($table_id == 0) {
            $insert_data['created_at'] = date('Y-m-d H:i:s');
            $this->basic->insert_data('line_accounts', $insert_data);
        } else {
            $this->basic->update_data('line_accounts', array('id' => $table_id), $insert_data);
        }

        $this->session->set_flashdata('success_message', 1);
        redirect(base_url('social_apps/line_settings'), 'location');
    }
}

public function add_line_notify_token()
{
    // ตรวจสอบสิทธิ์การเข้าถึง (ถ้าจำเป็น)

    $data['page_title'] = $this->lang->line('Add Line Notify Token');
    $data['title'] = $this->lang->line('Add Line Notify Token');
    $data['body'] = 'admin/social_apps/line_notify_token'; // ปรับ path ของ view ให้ถูกต้อง

    $this->_viewcontroller($data);
}

public function edit_line_notify_token($id = 0)
{
    // ตรวจสอบสิทธิ์การเข้าถึง (ถ้าจำเป็น)

    if ($id == 0) {
        redirect('social_apps/settings', 'refresh');
    }

    $data['page_title'] = $this->lang->line('Edit Line Notify Token');
    $data['title'] = $this->lang->line('Edit Line Notify Token');
    $data['body'] = 'admin/social_apps/line_notify_token'; // ปรับ path ของ view ให้ถูกต้อง

    $data['line_notify_token'] = $this->basic->get_data('line_notify_tokens', array('where' => array('id' => $id, 'user_id' => $this->user_id)));
    if (empty($data['line_notify_token'])) {
        redirect('social_apps/settings', 'refresh');
    }

    $this->_viewcontroller($data);
}

public function line_notify_token_update_action()
{
    // ตรวจสอบสิทธิ์การเข้าถึง (ถ้าจำเป็น)

    if (!isset($_POST)) exit;

    $this->form_validation->set_rules('token', $this->lang->line("Token"), 'trim|required');

    if ($this->form_validation->run() == FALSE) {
        if ($this->input->post('table_id')) {
            $this->edit_line_notify_token($this->input->post('table_id'));
        } else {
            $this->add_line_notify_token();
        }
    } else {

        $this->csrf_token_check();

        $insert_data['token'] = $this->input->post('token', true);
        $insert_data['user_id'] = $this->user_id;
        $insert_data['updated_at'] = date('Y-m-d H:i:s');

        $table_id = $this->input->post('table_id');

        if ($table_id == 0) {
            $insert_data['created_at'] = date('Y-m-d H:i:s');
            $this->basic->insert_data('line_notify_tokens', $insert_data);
        } else {
            $this->basic->update_data('line_notify_tokens', array('id' => $table_id), $insert_data);
        }

        $this->session->set_flashdata('success_message', 1);
        redirect(base_url('social_apps/settings'), 'location');
    }
}

// ... (ส่วนอื่นๆ ของโค้ดใน Controller)

public function ai_model_update_action()
{
    // ... (ตรวจสอบ CSRF token และอื่นๆ)

    $insert_data['model_name'] = $this->input->post('model_name', true);
    $insert_data['provider'] = $this->input->post('provider', true);
    $insert_data['api_key'] = $this->input->post('api_key', true);
    // ... (ข้อมูลอื่นๆ ของโมเดล AI)

    if ($insert_data['provider'] == 'Line') {
        $insert_data['line_channel_id'] = $this->input->post('line_channel_id', true);
        $insert_data['line_channel_secret'] = $this->input->post('line_channel_secret', true);
        $insert_data['line_channel_access_token'] = $this->input->post('line_channel_access_token', true);
    } else if ($insert_data['provider'] == 'Line Notify') {
        $insert_data['line_notify_token'] = $this->input->post('line_notify_token', true);
    }

    $status = $this->input->post('status');
    if($status=='') $status='0';
    $insert_data['status'] = $status;

    $table_id = $this->input->post('table_id');

    if ($table_id == 0) {
        $insert_data['created_at'] = date('Y-m-d H:i:s');
        $this->basic->insert_data('ai_models', $insert_data);
    } else {
        $this->basic->update_data('ai_models', array('id' => $table_id), $insert_data);
    }

    $this->session->set_flashdata('success_message', 1);
    redirect(base_url('social_apps/ai_settings'), 'location');
}