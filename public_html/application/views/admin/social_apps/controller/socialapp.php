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