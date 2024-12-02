<div class="modal-body clearfix">

                
<?php echo form_open(get_uri("estimates/save_step"), array("id" => "step-form", "class" => "general-form", "role" => "form")); ?>
  
    <input type="hidden" name="estimate_id" value="<?php echo $estimate_id; ?>" />
    
    <div class="form-group">
        <label for="step_name">Nome da Etapa</label>
        <input type="text" name="step_name" class="form-control" placeholder="Nome da Etapa" required>
    </div>

        
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><?php echo app_lang('close'); ?></button>
        <button type="submit" class="btn btn-primary"><?php echo app_lang('save'); ?></button>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript">
$(document).ready(function () {
        $("#step-form").appForm({
            onSuccess: function (result) {
                $("#estimate-etapa-table").appTable({reload: true});       
            }
        });




    });