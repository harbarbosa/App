
<div id="page-content" class="page-wrapper clearfix">
  <div class="card clearfix">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div class="page-title clearfix">
              <h4 class="no-margin font-bold"><?php echo html_entity_decode($title); ?></h4>
              <div class="title-button-group">
                <a href="<?php echo get_uri('purchase/new_vendor_items'); ?>" class="btn btn-default mbot10"><i data-feather='plus-circle' class='icon-16'></i>&nbsp;<?php echo app_lang('add_vendor_items'); ?></a>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-md-3">
                <?php echo render_select1('vendor_filter', $vendors, array('userid', 'company'), 'vendors', '', array('multiple' => true, 'data-actions-box' => true, 'data-live-search' => true), array(), '', '', false); ?>
              </div>
              <div class="col-md-3">
                <?php 
                echo render_select1('group_items_filter', $commodity_groups, array('id','title'), 'group_item', '', array('multiple' => true, 'data-actions-box' => true,  'data-live-search' => true), array(), '', '', false); ?>
              </div>
              <div class="col-md-3">
                <label for="item_select"><?php echo _l('pur_item'); ?></label>
                <?php echo view('Purchase\Views\item_include\main_item_select'); ?>
              </div>
              
              <div class="clearfix"></div>
            </div>

            <div class="modal bulk_actions" id="table_vendors_items_list_bulk_actions" tabindex="-1" role="dialog">
              <div class="modal-dialog" role="document">
                 <div class="modal-content">
                    <div class="modal-header">
                       <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                       
                       <div class="checkbox checkbox-danger">
                          <input type="checkbox" name="mass_delete" id="mass_delete">
                          <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                       </div>
                      
                      
                    </div>
                    <div class="modal-footer">
                       <button type="button" class="btn btn-default" data-bs-dismiss="modal"><?php echo _l('close'); ?></button>

                       
                       <a href="#" class="btn btn-default" onclick="purchase_delete_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                        
                    </div>
                 </div>
              </div>
            </div>

              <a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-vendor-items" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>
              <?php render_datatable1(array(
                '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="vendor-items"><label></label></div>',
                _l('vendors'),
                _l('items'),
                _l('date_create'),
                _l('pur_options')
                ),'vendor-items',[],
                  array(
                     'id'=>'table-vendor-items',
                     
                   )); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require FCPATH. PLUGIN_URL_PATH .'Purchase/assets/js/vendor_items/manage_vendor_items_js.php';?>