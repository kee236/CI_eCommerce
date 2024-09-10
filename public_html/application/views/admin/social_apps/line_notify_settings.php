<form action="<?php echo base_url("social_apps/ai_model_update_action"); ?>" method="POST">
    <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
    <input type="hidden" name="table_id" value="<?php echo $table_id ?>"> 
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><i class="fas fa-edit"></i> <?php echo $this->lang->line("AI Model Details"); ?></h4>
        </div>
        <div class="card-body">              
            <div class="form-group">
                <label for=""><i class="fas fa-file-signature"></i> <?php echo $this->lang->line("Model Name");?> </label>
                <input name="model_name" value="<?php echo isset($ai_model['model_name']) ? $ai_model['model_name'] : set_value('model_name'); ?>"  class="form-control" type="text">              
                <span class="red"><?php echo form_error('model_name'); ?></span>
            </div>

            <div class="form-group">
                <label for=""><i class="fas fa-server"></i> <?php echo $this->lang->line("AI Provider");?> </label>
                <select name="provider" class="form-control select2" style="width:100%;">
                    <option value="OpenAI" <?php echo (isset($ai_model['provider']) && $ai_model['provider'] == 'OpenAI') ? 'selected' : ''; ?>>OpenAI</option>
                    <option value="Azure" <?php echo (isset($ai_model['provider']) && $ai_model['provider'] == 'Azure') ? 'selected' : ''; ?>>Azure</option>
                    </select>              
                <span class="red"><?php echo form_error('provider'); ?></span>
            </div>

            <div class="form-group">
                <label for=""><i class="fas fa-key"></i>  <?php echo $this->lang->line("API Key");?></label>
                <input name="api_key" value="<?php echo isset($ai_model['api_key']) ? $ai_model['api_key'] : set_value('api_key'); ?>" class="form-control" type="text">  
                <span class="red"><?php echo form_error('api_key'); ?></span>
            </div>

            <div class="row" id="line_settings" style="display: none;">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for=""><i class="far fa-id-card"></i>  <?php echo $this->lang->line("Channel ID");?></label>
                        <input name="line_channel_id" value="<?php echo isset($ai_model['line_channel_id']) ? $ai_model['line_channel_id'] : set_value('line_channel_id'); ?>" class="form-control" type="text">  
                        <span class="red"><?php echo form_error('line_channel_id'); ?></span>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for=""><i class="fas fa-key"></i>  <?php echo $this->lang->line("Channel Secret");?></label>
                        <input name="line_channel_secret" value="<?php echo isset($ai_model['line_channel_secret']) ? $ai_model['line_channel_secret'] : set_value('line_channel_secret'); ?>" class="form-control" type="text">  
                        <span class="red"><?php echo form_error('line_channel_secret'); ?></span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for=""><i class="fas fa-lock"></i>  <?php echo $this->lang->line("Channel Access Token");?></label>
                        <textarea name="line_channel_access_token" class="form-control" type="text"><?php echo isset($ai_model['line_channel_access_token']) ? $ai_model['line_channel_access_token'] : set_value('line_channel_access_token'); ?></textarea>  
                        <span class="red"><?php echo form_error('line_channel_access_token'); ?></span>
                    </div>
                </div>
            </div>

            <div class="form-group" id="line_notify_settings" style="display: none;">
    <label for=""><i class="fab fa-line"></i>  <?php echo $this->lang->line("Line Notify Token");?></label>
    <input name="line_notify_token" value="<?php echo isset($ai_model['line_notify_token']) ? $ai_model['line_notify_token'] : set_value('line_notify_token'); ?>" class="form-control" type="text">  
    <span class="red"><?php echo form_error('line_notify_token'); ?></span>
</div>

<div class="form-group">
    <label class="custom-switch mt-2">
    <input type="checkbox" name="status" value="1" class="custom-switch-input"  <?php if($status=='1') echo 'checked'; ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"><?php echo $this->lang->line('Active');?></span>
    <span class="red"><?php echo form_error('status'); ?></span>
    </label>
</div>