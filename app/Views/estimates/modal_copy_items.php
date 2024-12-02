

<?php echo form_open(get_uri("estimates/save_copy"), array("id" => "estimate-item-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
<input type="hidden" name="estimate_id" value="<?php echo $estimate_id; ?>" />
<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

Deseja realizar a cópia dos itens?<br>
Os itens existentes serão excluidos.
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span>Copiar</button>
</div>
<?php echo form_close(); ?>

