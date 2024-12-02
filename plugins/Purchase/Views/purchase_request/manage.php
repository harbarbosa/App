

<div id="page-content" class="page-wrapper clearfix">
  <div class="card clearfix">
      <div class="page-title clearfix">
        <h4 class="no-margin font-bold"><i class="fa fa-shopping-basket" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
        <div class="title-button-group">
          <?php if($user_type == 'staff'){ ?>
            <a href="<?php echo get_uri('purchase/pur_request'); ?>"class="btn btn-default">
              <i data-feather='plus-circle' class='icon-16'></i>  <?php echo _l('add_pur_request'); ?>
            </a>
          <?php } ?>     
        </div>
      </div>

      <div class="row ml2 mr5 mt-3 general-form">    
          <div class="col-md-3">
              <label for="from_date"><?php echo app_lang('from_date'); ?></label>
              <?php echo render_date_input1('from_date','','',array('placeholder' => _l('from_date') )); ?>
          </div>
          <div class="col-md-3">
            <label for="to_date"><?php echo app_lang('to_date'); ?></label>
              <?php echo render_date_input1('to_date','','',array('placeholder' => _l('to_date') )); ?>
          </div>

          <?php if($user_type == 'staff'){ ?>
            <div class="col-md-3">
              <label for="to_date"><?php echo app_lang('team'); ?></label>
              <select name="department" id="department" class="select2 validate-hidden" onchange="department_change(this); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                <option value="">-</option>
                <?php foreach($teams as $s) { ?>
                  <option value="<?php echo html_entity_decode($s['id']); ?>" <?php if(isset($pur_request) && $s['id'] == $pur_request->department){ echo 'selected'; } ?>><?php echo html_entity_decode($s['title']); ?></option>
                  <?php } ?>
              </select>
            </div>
          <?php } ?>
      </div>
      <div class="row ml2 mr2"> 
        <?php render_datatable1(array(
            _l('pur_rq_code'),
            _l('pur_rq_name'),
            _l('requester'),
            _l('team'),
            _l('pur_request_date'),
            _l('status'),
            _l('options'),
            ),'table_pur_request'); ?>
      </div>
  </div>
</div>
<?php require('plugins/Purchase/assets/js/purchase_request/manage_purchase_request_js.php'); ?>

