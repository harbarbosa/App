<div id="page-content" class="page-wrapper clearfix">
  <div class="card clearfix">
		<div class="panel_s">
			<div class="panel-body">
				<h4><?php echo html_entity_decode($title) ?></h4>
				<hr>

				<?php echo form_open_multipart(uri_string() , array('autocomplete'=>'off', 'class' => 'general-form')) ?>

				<div class="row">
                  <div class="col-md-4">
                    <label for="title"><span class="text-danger">* </span><?php  echo _l('title'); ?></label>
                      <?php $title = isset($item) ? $item->title : '';
                      echo render_input1('title', '', $title,'text', ['required' => true]); ?>
                  </div>
                    <div class="col-md-4">
                      <label for="commodity_code"><span class="text-danger">* </span><?php  echo _l('commodity_code'); ?></label>
                        <?php $commodity_code = isset($item) ? $item->commodity_code : '';
                        echo render_input1('commodity_code', '', $commodity_code,'text', ['required' => true]); ?>
                    </div>
                    <div class="col-md-4">
                      <label for="commodity_name"><span class="text-danger">* </span><?php  echo _l('commodity_name'); ?></label>
                      <?php $description = isset($item) ? $item->description : '';
                      echo render_input1('description', '', $description,'text', ['required' => true]); ?>
                    </div>
                </div>

                <div class="row">
                   <div class="col-md-4">
                         <?php $commodity_barcode = isset($item) ? $item->commodity_barcode : '';
                         echo render_input1('commodity_barcode', 'commodity_barcode', $commodity_barcode,'text'); ?>
                    </div>
                  <div class="col-md-4">
                    <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle skucode-tooltip"  data-toggle="tooltip" title="" data-original-title="<?php echo _l('commodity_sku_code_tooltip'); ?>"></i></a>
                    <?php $sku_code = isset($item) ? $item->sku_code : '';
                    echo render_input1('sku_code', 'sku_code', $sku_code,''); ?>
                  </div>
                  <div class="col-md-4">
                    <?php $sku_name = isset($item) ? $item->sku_name : '';
                    echo render_input1('sku_name', 'sku_name', $sku_name); ?>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                        <?php $long_description = isset($item) ? $item->long_description : '';
                        echo render_textarea1('long_description', 'description', $long_description); ?>
                  </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                         <?php $commodity_group = isset($item) ? $item->group_id : '';
                         echo render_select1('group_id',$commodity_groups,array('id','title'),'commodity_group', $commodity_group); ?>
                    </div>
                     <div class="col-md-6 d-none">
                         <?php $sub_group = isset($item) ? $item->sub_group : '';
                         echo render_select1('sub_group',$sub_groups,array('id','sub_group_name'),'sub_group', $sub_group); ?>
                    </div>
                </div>

                <div class="row">
	                <div class="col-md-3">
	                    <?php $unit_id = isset($item) ? $item->unit_id : '';
	                    echo render_select1('unit_id',$units,array('unit_type_id','unit_name'),'units', $unit_id); ?>
	                </div>

	                <div class="col-md-3">
                      <label for="rate"><span class="text-danger">* </span><?php echo _l('rate'); ?></label>
                        <?php $rate = isset($item) ? $item->rate : '';
                        $attr = array();
                        $attr = ['required' => true, 'step' => 'any'];
                         echo render_input1('rate', '', $rate, 'number', $attr); ?>
                    </div>
	                    
	                <div class="col-md-3">
	                    <?php $tax = isset($item) ? $item->tax : '';
	                    echo render_select1('tax',$taxes,array('id','label'),'tax_1', $tax); ?>
	                </div>

	                <div class="col-md-3">
	                    <?php $tax2 = isset($item) ? $item->tax2 : '';
	                    echo render_select1('tax2',$taxes,array('id','label'),'tax_2', $tax2); ?>
	                </div>
                </div>

                <div class="row">
 					      <div class="col-md-12">
		              <div class="attachments">
		                <div class="attachment">
		                  <div class="mbot15">
		                    <div class="form-group">
		                      <label for="attachment" class="control-label"><?php echo _l('ticket_add_attachments'); ?></label>
		                      <div class="input-group">
		                        <input type="file" extension="jpg,png" class="form-control" name="attachments[0]" accept="image/*">
		                        <span class="input-group-btn">
		                          <button class="btn btn-success add_more_attachments p8-half" data-max="10" type="button"><i data-feather="plus" class="icon-16"></i></button>
		                        </span>
		                      </div>
		                    </div>
		                  </div>
		                </div>
		              </div>
		            </div>              	
                </div>

                <div class="footer">
                	<hr>
                	<button type="submit" class="btn btn-info pull-right text-white" ><?php echo _l('submit'); ?></button>
                </div>

				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php require('plugins/Purchase/assets/js/vendor_portal/item_js.php'); ?>