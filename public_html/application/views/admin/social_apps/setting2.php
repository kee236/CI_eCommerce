<section class="section">
    <div class="section-header">
        <h1><i class="fas fa-link"></i> <?php echo $this->lang->line("การเชื่อมต่อ"); ?></h1>
    </div>

    <?php $this->load->view('admin/theme/message'); ?>

    <div class="section-body">
        <div class="row">

            <div class="col-12 col-md-6">
                <div class="card card-hero">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fab fa-facebook"></i>
                        </div>
                        <h4><?php echo $this->lang->line('Facebook'); ?></h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="tickets-list">
                            <a href="<?php echo base_url("social_apps/facebook_settings"); ?>" class="ticket-item">
                                <div class="ticket-title">
                                    <h4><?php echo $this->lang->line("ตั้งค่าแอป Facebook"); ?></h4>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="card card-hero">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fab fa-line"></i>
                        </div>
                        <h4><?php echo $this->lang->line('Line'); ?></h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="tickets-list">
                            <a href="<?php echo base_url("social_apps/line_settings"); ?>" class="ticket-item">
                                <div class="ticket-title">
                                    <h4><?php echo $this->lang->line("ตั้งค่าบัญชี Line"); ?></h4>
                                </div>
                            </a>
                            <a href="<?php echo base_url("social_apps/line_notify_settings"); ?>" class="ticket-item">
                                <div class="ticket-title">
                                    <h4><?php echo $this->lang->line("ตั้งค่า Line Notify"); ?></h4>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            </div>
        </div>

        <div class="row">

            <div class="col-12 col-md-6">
                <div class="card card-hero">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-brain"></i> 
                        </div>
                        <h4><?php echo $this->lang->line('AI'); ?></h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="tickets-list">
                            <a href="<?php echo base_url("social_apps/ai_settings"); ?>" class="ticket-item">
                                <div class="ticket-title">
                                    <h4><?php echo $this->lang->line("ตั้งค่าโมเดล AI"); ?></h4>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div> 
    </div>
</section>