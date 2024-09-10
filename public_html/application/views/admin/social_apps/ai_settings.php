<section class="section">
    <div class="section-header">
        <h1><i class="fas fa-brain"></i> <?php echo $this->lang->line("AI Settings"); ?></h1>
    </div>

    <?php $this->load->view('admin/theme/message'); ?>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="card card-hero">
                                    <div class="card-header">
                                        <div class="card-icon">
                                            <i class="fas fa-robot"></i>
                                        </div>
                                        <h4><?php echo $this->lang->line('AI Models'); ?></h4>
                                        <div class="card-description"><?php echo $this->lang->line('Add or edit AI models'); ?></div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="tickets-list">
                                            <?php 
                                                $ai_models = $this->basic->get_data('ai_models');
                                                if(count($ai_models) == 0)
                                                {
                                                    echo '
                                                        <a href="'.base_url("social_apps/add_ai_model").'" class="ticket-item">
                                                            <div class="ticket-title">
                                                                <h4>'.$this->lang->line("Add AI Model").'</h4>
                                                            </div>
                                                        </a>
                                                    ';
                                                }
                                                else
                                                {
                                                    foreach($ai_models as $data)
                                                    {
                                                        $edit_url = base_url('social_apps/edit_ai_model/').$data['id'];
                                                        echo '
                                                        <a href="'.$edit_url.'" class="ticket-item">
                                                            <div class="ticket-title">
                                                                <h4>'.$data['model_name'].'</h4>
                                                            </div>
                                                            <div class="ticket-info">
                                                                <div>'.$this->lang->line("Provider").' : '.$data['provider'].'</div>
                                                                <div>'.$this->lang->line("Last Updated").' : '.date("d M, y H:i",strtotime($data['updated_at'])).'</div>
                                                            </div>
                                                        </a>
                                                        ';                                                    
                                                    }
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
</section>