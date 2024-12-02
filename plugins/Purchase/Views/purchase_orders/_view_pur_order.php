
<?php echo form_hidden('_attachment_sale_id',$estimate->id); ?>
<?php echo form_hidden('_attachment_sale_type','estimate'); ?>
<?php
   $base_currency = get_base_currency();
    if($estimate->currency != ''){
      $base_currency = $estimate->currency;
    }
 ?>
<div class="col-md-12 no-padding">
   <div id="page-content" class="page-wrapper clearfix">
      <div class="card clearfix">
         <?php if($estimate->approve_status == 1){ ?>
           <div class="ribbon info span_style"><span><?php echo _l('purchase_draft'); ?></span></div>
       <?php }elseif($estimate->approve_status == 2){ ?>
         <div class="ribbon success"><span><?php echo _l('purchase_approved'); ?></span></div>
       <?php }elseif($estimate->approve_status == 3){ ?>  
         <div class="ribbon warning"><span><?php echo _l('pur_rejected'); ?></span></div>
       <?php }elseif ($estimate->approve_status == 4) { ?>
         <div class="ribbon danger"><span><?php echo _l('cancelled'); ?></span></div>
      <?php  } ?> 

        <div class="page-title clearfix">
          <h4 class="no-margin font-bold"><?php echo html_entity_decode($title); ?></h4>
        </div>

         
        <ul data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
            <li>
               <a href="#tab_estimate" class="<?php if($tab == 'tab_estimate'){ echo 'active'; } ?>" data-bs-target="#tab_estimate" role="presentation">
               <?php echo _l('pur_order'); ?>
               </a>
            </li>
            <!-- <li>
               <a href="#payment_record" class="<?php if($tab == 'payment_record'){ echo 'active'; } ?>" data-bs-target="#payment_record" role="presentation">
               <?php echo _l('payment_record'); ?>
               </a>
            </li> -->   
            <li>
               <a href="#attachment" class="<?php if($tab == 'attachment'){ echo 'active'; } ?>" data-bs-target="#attachment" role="presentation">
               <?php echo _l('attachment'); ?>
               </a>
            </li>  
        </ul>
            
         <div class="row ml5 mr5 mt10">
            <div class="col-md-4">
              <p class="bold p_mar"><?php echo _l('vendor').': '?><a href="<?php echo get_uri('purchase/vendor/'.$estimate->vendor); ?>"><?php echo get_vendor_company_name($estimate->vendor); ?></a></p>
              <?php 
                $order_status_class = '';
                $order_status_text = '';
                if($estimate->order_status == 'new'){
                  $order_status_class = 'label-info';
                  $order_status_text = _l('new_order');
                }else if($estimate->order_status == 'delivered'){
                  $order_status_class = 'label-success';
                  $order_status_text = _l('delivered');
                }else if($estimate->order_status == 'confirmed'){
                  $order_status_class = 'label-warning';
                  $order_status_text = _l('confirmed');
                }else if($estimate->order_status == 'cancelled'){
                  $order_status_class = 'label-danger';
                  $order_status_text = _l('cancelled');
                }else if($estimate->order_status == 'return'){
                   $order_status_class = 'label-warning';
                   $order_status_text = _l('pur_return');
                }
               ?>

               <?php if($estimate->order_status != null){ ?>
               <p class="bold p_mar"><?php echo _l('order_status').': '; ?><span class="label <?php echo html_entity_decode($order_status_class); ?>"><?php echo html_entity_decode($order_status_text); ?></span></p>
               <?php } ?>

              
            </div>
            <div class="col-md-8">
               <div class="btn-group pull-right">
                  <span class="dropdown inline-block">
                    <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
                    <i data-feather="file-text" class="icon-16"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" role="menu">
                      <li role="presentation"><a href="<?php echo admin_url('purchase/purorder_pdf/'.$estimate->id.'?output_type=I'); ?>" class="dropdown-item"><?php echo _l('view_pdf'); ?></a></li>
                      <li role="presentation"><a href="<?php echo admin_url('purchase/purorder_pdf/'.$estimate->id.'?output_type=I'); ?>" class="dropdown-item" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                      <li role="presentation"><a href="<?php echo admin_url('purchase/purorder_pdf/'.$estimate->id); ?>" class="dropdown-item"><?php echo _l('download'); ?></a></li>
                    </ul>
                  </span>   
               </div>

               <?php if(is_admin() || $user_type == 'vendor' || $user_type == 'staff'){ ?>
                 <div class="btn-group pull-right mr5">
                     <span class="dropdown inline-block">
                    <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static"><?php echo app_lang('more'); ?></button>
                     <ul class="dropdown-menu dropdown-menu-end" role="menu">
                        <?php if((is_admin() || $user_type == 'staff') && $estimate->order_status != 'cancelled'){ ?>
                          <li role="presentation"><?php echo modal_anchor(get_uri("purchase/send_po_modal_form/" . $estimate->id), "<i data-feather='mail' class='icon-16'></i> " . app_lang('email_po_to_vendor'), array("title" => app_lang('email_po_to_vendor'), "data-post-id" => $estimate->id, "role" => "menuitem", "tabindex" => "-1", "class" => "dropdown-item")); ?> </li>
                        <?php } ?>
                        <?php if(is_admin() || $user_type == 'vendor'){ ?>
                        <?php 
                          $statuses = [
                            'new',
                            'delivered',
                            'confirmed',
                            'cancelled',

                          ]; 
                        ?>
                        <?php foreach($statuses as $status){ ?>
                          <?php if($status != $estimate->order_status){ ?>
                            <li role="presentation">
                               <a href="<?php echo get_uri('purchase/mark_pur_order_as/'.$status.'/'.$estimate->id); ?>" class="dropdown-item"><?php echo _l('pur_mark_as_'.$status); ?></a>
                            </li>
                        <?php } ?>
                      <?php } ?>    
                        <?php } ?>

                        <?php if($estimate->approve_status != 2 && is_admin()){ ?>
                          <li role="presentation">
                             <a href="<?php echo get_uri('purchase/pur_order/'.$estimate->id); ?>" class="dropdown-item"><?php echo _l('edit'); ?></a>
                          </li>
                        <?php } ?>
                        <?php if(is_admin()){ ?>
                          <li role="presentation">
                             <a href="<?php echo get_uri('purchase/delete_pur_order/'.$estimate->id); ?>" class="text-danger dropdown-item delete-text _delete"><?php echo _l('delete'); ?></a>
                          </li>
                        <?php } ?>
                     </ul>
                     </span>  
                  </div>
                <?php } ?>


               <?php if(is_admin()){ ?>
               <div class="col-md-4 pull-right mr5">     
                 <select name="status" id="status" class="select2 validate-hidden pull-right mright10" onchange="change_status_pur_order(this,<?php echo ($estimate->id); ?>); return false;" data-live-search="true" data-width="35%" data-none-selected-text="<?php echo _l('pur_change_status_to'); ?>">
                   <option value=""><?php echo _l('pur_change_status_to'); ?></option>
                   <option value="1" class="<?php if($estimate->approve_status == 1) { echo 'hide';}?>"><?php echo _l('purchase_draft'); ?></option>
                   <option value="2" class="<?php if($estimate->approve_status == 2) { echo 'hide';}?>"><?php echo _l('purchase_approved'); ?></option>
                   <option value="3" class="<?php if($estimate->approve_status == 3) { echo 'hide';}?>"><?php echo _l('pur_rejected'); ?></option>
                   <option value="4" class="<?php if($estimate->approve_status == 4) { echo 'hide';}?>"><?php echo _l('pur_canceled'); ?></option>
                 </select>
               </div>
              <?php } ?>
               
               
            </div>
         </div>

         <div class="clearfix"></div>
         <hr class="hr-panel-heading" />
         <div class="tab-content">

            <div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">
               <div id="estimate-preview">
                  <div class="row ml5 mr5">

                     <?php if($estimate->estimate != 0){ ?>
                     <div class="col-md-12">
                        <h4 class="font-medium mbot15"><?php echo _l('',array(
                          '',
                           '',
                           '<a href="'.get_uri('purchase/view_quotation/'.$estimate->estimate).'" target="_blank">' . format_pur_estimate_number($estimate->id) . '</a>',
                           )); ?></h4>
                     </div>
                     <?php } ?>
                     <div class="col-md-6 col-sm-6">
                        <h4 class="bold mbot5">
                         
                           <a href="<?php echo get_uri('purchase/purchase_order/'.$estimate->id); ?>">
                           <span id="estimate-number">
                           <?php echo html_entity_decode($estimate->pur_order_number.' - '.$estimate->pur_order_name); ?>
                           </span>
                           </a>
                        </h4>
                        <p><?php echo _l('order_date').': '. _d($estimate->order_date); ?></p>
                        <p><?php echo _l('delivery_date').': '. _d($estimate->delivery_date); ?></p>
                        <address class="mbot5">
                           <?php echo company_widget(); ?>
                        </address>

                     </div>
                     
                     
                  </div>
                  <div class="row ml5 mr5">
                     <div class="col-md-12">

                        <div class="table-responsive">
                           <table class="table items items-preview estimate-items-preview" data-type="estimate">
                              <thead>
                                 <tr>
                                    <th align="center">#</th>
                                    <th class="description" width="25%" align="left"><?php echo _l('items'); ?></th>
                                    <th align="right" class="text-right"><?php echo _l('purchase_quantity'); ?></th>
                                    <th align="right" class="text-right"><?php echo _l('purchase_unit_price'); ?></th>
                                    <th align="right" class="text-right"><?php echo _l('into_money'); ?></th>
                                    <?php if(get_setting('show_purchase_tax_column') == 1){ ?>
                                    <th align="right" class="text-right"><?php echo _l('tax'); ?></th>
                                    <?php } ?>
                                    <th align="right" class="text-right"><?php echo _l('sub_total'); ?></th>
                                    <th align="right" class="text-right"><?php echo _l('discount(%)'); ?></th>
                                    <th align="right" class="text-right"><?php echo _l('discount(money)'); ?></th>
                                    <th align="right" class="text-right"><?php echo _l('total'); ?></th>
                                 </tr>
                              </thead>
                              <tbody class="ui-sortable">

                                 <?php $item_discount = 0;
                                 if(count($estimate_detail) > 0){
                                    $count = 1;
                                    $t_mn = 0;
                                    
                                 foreach($estimate_detail as $es) { ?>
                                 <tr nobr="true" class="sortable">
                                    <td class="dragger item_no ui-sortable-handle" align="center"><?php echo html_entity_decode($count); ?></td>
                                    <td class="description" align="left;"><span><strong><?php 
                                    $item = get_item_hp($es['item_code']); 
                                    if(isset($item) && isset($item->commodity_code) && isset($item->title)){
                                       echo html_entity_decode($item->commodity_code.' - '.$item->title);
                                    }else{
                                       echo html_entity_decode($es['item_name']);
                                    }
                                    ?></strong><?php if($es['description'] != ''){ ?><br><span><?php echo html_entity_decode($es['description']); ?></span><?php } ?></td>
                                    <td align="right"  width="12%"><?php echo html_entity_decode($es['quantity']); ?></td>
                                    <td align="right"><?php echo to_currency($es['unit_price'],$base_currency); ?></td>
                                    <td align="right"><?php echo to_currency($es['into_money'],$base_currency); ?></td>
                                    <?php if(get_setting('show_purchase_tax_column') == 1){ ?>
                                    <td align="right"><?php echo to_currency(($es['total'] - $es['into_money']),$base_currency); ?></td>
                                    <?php } ?>
                                    <td class="amount" align="right"><?php echo to_currency($es['total'],$base_currency); ?></td>
                                    <td class="amount" width="12%" align="right"><?php echo ($es['discount_%'].'%'); ?></td>
                                    <td class="amount" align="right"><?php echo to_currency($es['discount_money'],$base_currency); ?></td>
                                    <td class="amount" align="right"><?php echo to_currency($es['total_money'],$base_currency); ?></td>
                                 </tr>
                              <?php 
                              $t_mn += $es['total_money'];
                              $item_discount += $es['discount_money'];
                              $count++; } } ?>
                              </tbody>
                           </table>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-7"></div>
                         <div class="col-md-5 col-md-offset-7">
                            <table class="table text-right">
                               <tbody>
                                  <tr id="subtotal">
                                     <td><span class="bold"><?php echo _l('subtotal'); ?></span>
                                     </td>
                                     <td class="subtotal">
                                        <?php echo to_currency($estimate->subtotal,$base_currency); ?>
                                     </td>
                                  </tr>

                                  <?php if($tax_data['preview_html'] != ''){
                                    echo html_entity_decode($tax_data['preview_html']);
                                  } ?>


                                  <?php if(($estimate->discount_total + $item_discount) > 0){ ?>
                                  
                                  <tr id="subtotal">
                                     <td><span class="bold"><?php echo _l('discount_total(money)'); ?></span>
                                     </td>
                                     <td class="subtotal">
                                        <?php echo '-'.to_currency(($estimate->discount_total + $item_discount), $base_currency); ?>
                                     </td>
                                  </tr>
                                  <?php } ?>

                                  <?php if($estimate->shipping_fee > 0){ ?>
                                    <tr id="subtotal">
                                      <td><span class="bold"><?php echo _l('pur_shipping_fee'); ?></span></td>
                                      <td class="subtotal">
                                        <?php echo to_currency($estimate->shipping_fee, $base_currency); ?>
                                      </td>
                                    </tr>
                                  <?php } ?>


                                  <tr id="subtotal">
                                     <td><span class="bold"><?php echo _l('total'); ?></span>
                                     </td>
                                     <td class="subtotal bold">
                                        <?php echo to_currency($estimate->total, $base_currency); ?>
                                     </td>
                                  </tr>
                               </tbody>
                            </table>
                         </div>   
                       </div>
                     <?php if($estimate->vendornote != ''){ ?>
                     <div class="col-md-12 mtop15">
                        <p class="bold text-muted"><?php echo _l('estimate_note'); ?></p>
                        <p><?php echo html_entity_decode($estimate->vendornote); ?></p>
                     </div>
                     <?php } ?>
                                                            
                     <?php if($estimate->terms != ''){ ?>
                     <div class="col-md-12 mtop15">
                        <p class="bold text-muted"><?php echo _l('terms_and_conditions'); ?></p>
                        <p><?php echo html_entity_decode($estimate->terms); ?></p>
                     </div>
                     <?php } ?>
                  </div>
                  <?php if($user_type == 'staff'){ ?>

                  <?php if(count($list_approve_status) > 0 ){ ?>
                  <div class="ml15 mr15"> <p class=" p_style"><?php echo _l('pur_approval_infor'); ?></p></div>
                  <hr class="hr_style" />
                  <div class="project-overview-right">
                     <div class="row">
                       <div class="col-md-12 project-overview-expenses-finance">
                        <div class="row">
                        <?php 
                          $users_model = model("Models\Users_model");
                          $enter_charge_code = 0;
                        foreach ($list_approve_status as $value) {
                          $value['staffid'] = explode(', ',$value['staffid']);
                          if($value['action'] == 'sign'){
                         ?>
                         <div class="col-md-3 apr_div">
                             <p class="text-uppercase text-muted no-mtop bold">
                              <?php
                              $staff_name = '';
                              $st = _l('status_0');
                              $color = 'warning';
                              foreach ($value['staffid'] as $key => $val) {
                                if($staff_name != '')
                                {
                                  $staff_name .= ' or ';
                                }

                                $options = array(
                                    "id" => $val,
                                    "user_type" => "staff",
                                );
                                $staff_name .= isset($users_model->get_details($options)->getRow()->first_name) ? $users_model->get_details($options)->getRow()->first_name : '';
                              }
                              echo html_entity_decode($staff_name); 
                              ?></p>
                             <?php if($value['approve'] == 2){ 
                              ?>
                              <img src="<?php echo base_url('plugins/Purchase/Uploads/pur_order/signature/'.$estimate->id.'/signature_'.$value['id'].'.png'); ?>" class="img_style">
                               <br><br>
                             <p class="bold text-center text-success"><?php echo _l('signed').' '.format_to_date($value['date']); ?></p>
                             <?php } ?> 
                                
                        </div>
                        <?php }else{ ?>
                        <div class="col-md-3 apr_div">
                             <p class="text-uppercase text-muted no-mtop bold">
                              <?php
                              $staff_name = '';
                              foreach ($value['staffid'] as $key => $val) {
                                if($staff_name != '')
                                {
                                  $staff_name .= ' or ';
                                }
                                $options = array(
                                    "id" => $val,
                                    "user_type" => "staff",
                                );

                                $staff_name .= isset($users_model->get_details($options)->getRow()->first_name) ? $users_model->get_details($options)->getRow()->first_name : '';
                              }
                              echo html_entity_decode($staff_name); 
                              ?></p>
                             <?php if($value['approve'] == 2){ 
                              ?>
                              <img src="<?php echo base_url('plugins/Purchase/Uploads/approval/approved.png'); ?>" class="img_style">
                             <?php }elseif($value['approve'] == 3){ ?>
                                <img src="<?php echo  base_url('plugins/Purchase/Uploads/approval/rejected.png'); ?>" class="img_style">
                            <?php } ?> 
                            <br><br>
                            <p><?php echo html_entity_decode($value['note']) ?></p>  
                            <p class="bold text-center text-<?php if($value['approve'] == 2){ echo 'success'; }elseif($value['approve'] == 3){ echo 'danger'; } ?>"><?php echo format_to_date($value['date']); ?></p> 
                        </div>
                        <?php }
                        } ?>
                       </div>
                     </div>
                    </div>
                    
                    
                    </div>
                  <?php } ?>

                  <div class=" ml15 mr15 mb15">
                      <?php if($check_appr && $check_appr != false){
                      if($estimate->approve_status == 1 && ($check_approve_status == false || $check_approve_status == 'reject')){ ?>
                  <a data-toggle="tooltip" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_request_approve(<?php echo html_entity_decode($estimate->id); ?>); return false;"><?php echo _l('send_request_approve_pur'); ?></a>
                <?php } }
                  if(isset($check_approve_status['staffid'])){
                      ?>
                      <?php 
                  if(in_array(get_staff_user_id(), $check_approve_status['staffid']) && !in_array(get_staff_user_id(), $get_staff_sign) && $estimate->approve_status == 1){
                   ?>
                      <a href="#" class="btn btn-success  show_approve" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo app_lang('approve'); ?><span class="caret"></span></a>
                                  <div class="modal fade" id="approve_modal" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h4 class="modal-title"><?php echo app_lang('approve'); ?></h4>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body general-form">
                                          <?php echo render_textarea1('reason', 'reason'); ?>
                                        </div>
                                        <div class="modal-footer">

                                          <a href="#" class="btn btn-success pull-left display-block  mr-4 button-margin-r-b" data-loading-text="<?php echo app_lang('wait_text'); ?>" onclick="approve_request(<?php echo html_entity_decode($estimate->id); ?>); return false;"><span data-feather="upload" class="icon-16"></span>
                                            <?php echo app_lang('approve'); ?>
                                          </a>

                                          <a href="#" data-loading-text="<?php echo app_lang('wait_text'); ?>" onclick="deny_request(<?php echo html_entity_decode($estimate->id); ?>); return false;" class="btn btn-warning text-white"><span data-feather="x" class="icon-16"></span><?php echo app_lang('deny'); ?>
                                        </a>

                                      </div>
                                    </div>
                                  </div>
                                </div>
                    <?php }
                      ?>
                      
                    <?php
                     if(in_array(get_staff_user_id(), $check_approve_status['staffid']) && in_array(get_staff_user_id(), $get_staff_sign) && $estimate->approve_status == 1){ ?>
                      <button onclick="accept_action();" class="btn btn-success pull-left action-button"><?php echo _l('e_signature_sign'); ?></button>
                    <?php }
                      ?>
                      <?php 
                       }
                      ?>
                    </div>
                <?php } ?>
               </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="attachment">
               <?php echo form_open_multipart(get_uri('purchase/purchase_order_attachment/'.$estimate->id),array('id'=>'partograph-attachments-upload')); ?>
                <div class="row ml15 mr15">
                  <?php echo render_input('file','file','','file'); ?>
               </div>
               <div class="modal-footer bor_top_0" >
                   <button id="obgy_btn2" type="submit" class="btn btn-info text-white"><?php echo _l('submit'); ?></button>
               </div>
                <?php echo form_close(); ?>
               
               <div class="col-md-12" id="purorder_pv_file">
                                    <?php
                                        $file_html = '';
                                        if(count($pur_order_attachments) > 0){
                                            $file_html .= '<hr />';
                                            foreach ($pur_order_attachments as $f) {
                                                $href_url = base_url('plugins/Purchase/Uploads/pur_order/'.$f['rel_id'].'/'.$f['file_name']).'" download';
                                                                if(!empty($f['external'])){
                                                                  $href_url = $f['external_link'];
                                                                }
                                               $file_html .= '<div class="mb15 ml15 mr15 row" data-attachment-id="'. $f['id'].'">
                                              <div class="col-md-8 d-flex">
                                                   '.modal_anchor(get_uri("purchase/file_pur_order/".$f['id']."/".$f['rel_id']), "<i data-feather='eye' class='icon-16'></i>", array("class" => "btn btn-success text-white mr5", "title" => $f['file_name'], "data-post-id" => $f['id'])).'

                                                    <div class="d-block">
                                                     <div class="pull-left"><i class="'. get_mime_class($f['filetype']).'"></i></div>
                                                     <a href=" '. $href_url.'" target="_blank" download>'.$f['file_name'].'</a>
                                                     <br />
                                                    <small class="text-muted">'.$f['filetype'].'</small>
                                                    </div>
                                                   
                                              </div>
                                              <div class="col-md-4 text-right">';
                                                if($f['staffid'] == get_staff_user_id1() || is_admin()){
                                               
                                                $file_html .= '<a href="#" class="text-danger" onclick="delete_purorder_attachment('. $f['id'].'); return false;"><i data-feather="x" class="icon-16"></i></a>';
                                                } 
                                               $file_html .= '</div></div>';
                                            }
                                            echo html_entity_decode($file_html);
                                        }
                                     ?>
                                  </div>

               <div id="purorder_file_data"></div>
            </div>


         </div>
      </div>
   </div>
</div>

    
<div class="modal fade" id="add_action" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         
        <div class="modal-body">
         <p class="bold" id="signatureLabel"><?php echo _l('signature'); ?></p>
            <div class="signature-pad--body border">
              <canvas id="signature" height="130" width="550"></canvas>
            </div>
            <input type="text" class="ip_style d-none" tabindex="-1" name="signature" id="signatureInput">
            <div class="dispay-block">
              <button type="button" class="btn btn-default btn-xs clear" tabindex="-1" onclick="signature_clear();"><?php echo _l('clear'); ?></button>
            
            </div>

          </div>
          <div class="modal-footer">
           <button onclick="sign_request(<?php echo html_entity_decode($estimate->id); ?>);" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" class="btn btn-success"><?php echo _l('e_signature_sign'); ?></button>
          </div>

      </div><!-- /.modal-content -->
   </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php require FCPATH. PLUGIN_URL_PATH . "Purchase/assets/js/purchase_orders/pur_order_preview_js.php";  ?>
