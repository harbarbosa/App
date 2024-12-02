<div id="page-content" class="page-wrapper clearfix">
   <div class="card clearfix">

    <?php if($pur_request->currency != ''){
      
      $base_currency = $pur_request->currency;
    }else{
      $base_currency = $base_currency;
    }

    if($base_currency == get_setting('default_currency')){
      $base_currency = get_setting('currency_symbol');
    }

     ?>
    <?php if($pur_request->status == 1){ ?>
        <div class="ribbon info"><span class="fontz9" ><?php echo _l('purchase_draft'); ?></span></div>
    <?php }elseif($pur_request->status == 2){ ?>
      <div class="ribbon success"><span><?php echo _l('purchase_approved'); ?></span></div>
    <?php }elseif($pur_request->status == 3){ ?>  
      <div class="ribbon danger"><span><?php echo _l('purchase_reject'); ?></span></div>
    <?php } ?>

      <div class="page-title clearfix">
        <h4 class="no-margin font-bold"><?php echo html_entity_decode($title); ?></h4>
      </div>

       <ul data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
          <li>
             <a href="#information" class="<?php if($tab == 'information'){ echo 'active'; } ?>" data-bs-target="#information" role="presentation">
             <?php echo _l('pur_information'); ?>
             </a>
          </li>

          <li>
             <a href="#attachment" class="<?php if($tab == 'attachment'){ echo 'active'; } ?> " data-bs-target="#attachment" role="presentation" >
             <?php echo _l('attachment'); ?>
             </a>
          </li>  
       </ul>
          
   
       <div class="tab-content">
        <div role="tabpanel" class="tab-pane ptop10 <?php if($tab == 'information'){ echo 'active'; } ?>" id="information">

        <div class="row ml15 mr15">
          <div class="col-md-12">
            <div class="row">
              <p class="bold col-md-9 p_style"><?php echo _l('information'); ?></p>
              <div class="col-md-3 pull-right">
                  <div class="task-info task-status task-info-status pull-right">
                    <?php if($user_type == 'staff' && $pur_request->status == 2){
                    echo modal_anchor(get_uri("purchase/send_pr_modal_form/" . $pur_request->id), "<i data-feather='mail' class='icon-16'></i> ", array("title" => app_lang('email_pr_to_vendor'), "data-post-id" => $pur_request->id, "class" => "btn btn-default btn-with-tooltip pull-right")); 
                   } ?>
                      
                   </div>


              </div>
            </div>
          <div class=" col-md-12">
            <hr class="hr_style" />
          </div>
        </div>
    </div>

      <div class=" row ml15 mr15">
          <table class="table border table-striped martop0">
        <tbody>
           <tr class="project-overview">
              <td class="bold" width="30%"><?php echo _l('pur_rq_code'); ?></td>
              <td><?php echo html_entity_decode($pur_request->pur_rq_code); ?></td>
           </tr>
           <tr class="project-overview">
              <td class="bold"><?php echo _l('pur_rq_name'); ?></td>
              <td><?php echo html_entity_decode($pur_request->pur_rq_name); ?></td>
           </tr>
           <tr class="project-overview">
              <td class="bold"><?php echo _l('purchase_requestor'); ?></td>
              <td><?php 
              $_data =  get_staff_full_name($pur_request->requester); 
              echo html_entity_decode($_data);
              ?></td>
           </tr>
           
           <tr class="project-overview">
              <td class="bold"><?php echo _l('request_date'); ?></td>
              <td><?php echo format_to_date($pur_request->request_date); ?></td>
           </tr>
           <?php if($user_type == 'staff'){ ?>
           <tr>
            <td class="bold"><?php echo _l('pdf'); ?></td>
            <td>

              
              <span class="dropdown inline-block">
                <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
                <i data-feather="file-text" class="icon-16"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" role="menu">
                  <li role="presentation"><a href="<?php echo admin_url('purchase/pur_request_pdf/'.$pur_request->id.'?output_type=I'); ?>" class="dropdown-item"><?php echo _l('view_pdf'); ?></a></li>
                  <li role="presentation"><a href="<?php echo admin_url('purchase/pur_request_pdf/'.$pur_request->id.'?output_type=I'); ?>" class="dropdown-item" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                  <li role="presentation"><a href="<?php echo admin_url('purchase/pur_request_pdf/'.$pur_request->id); ?>" class="dropdown-item"><?php echo _l('download'); ?></a></li>
                </ul>
              </span>    
                  
            </td>
          </tr>
          <?php } ?>
       
           <tr class="project-overview">
              <td class="bold"><?php echo _l('rq_description'); ?></td>
              <td><?php echo html_entity_decode($pur_request->rq_description); ?></td>
           </tr>

        </tbody>
    </table>
  </div>

      <div class="row ml15 mr15">
        <p class=" p_style"><?php echo _l('pur_detail'); ?></p>
        <hr class="hr_style" />
        
        <div class="table-responsive">
               <table class="table items items-preview estimate-items-preview" data-type="estimate">
                  <thead>
                     <tr>
                      <th width="25%" align="left"><?php echo _l('debit_note_table_item_heading'); ?></th>
                      <th width="10%" align="right" class="text-right qty"><?php echo _l('purchase_quantity'); ?></th>
                      <th width="10%" align="right" class="text-right"><?php echo _l('unit_price'); ?></th>
                      
                      <th width="10%" align="right" class="text-right"><?php echo _l('subtotal_before_tax'); ?></th>
                      <th width="15%" align="right" class="text-right"><?php echo _l('debit_note_table_tax_heading'); ?></th>
                      <th width="10%" align="right" class="text-right"><?php echo _l('tax_value'); ?></th>
                      <th width="10%" align="right" class="text-right"><?php echo _l('debit_note_total'); ?></th>
                     </tr>
                  </thead>
                  <tbody class="ui-sortable">
                     <?php $_subtotal = 0;
                     $_total = 0;
                     if(count($pur_request_detail) > 0){
                        $count = 1;
                        $t_mn = 0;
                     foreach($pur_request_detail as $es) { 
                        $_subtotal += $es['into_money'];
                        $_total += $es['total'];
                      ?>
                     <tr nobr="true" class="sortable">
                        
                        <td class="description" align="left;"><span><strong><?php 
                        $item = get_item_hp($es['item_code']); 
                        if(isset($item) && isset($item->commodity_code) && isset($item->title)){
                           echo html_entity_decode($item->commodity_code.' - '.$item->title);
                        }else{
                           echo html_entity_decode($es['item_text']);
                        }
                        ?></strong></td>
                        <td align="right"  width="12%"><?php echo to_decimal_format($es['quantity'], 0); ?></td>
                        <td align="right"><?php echo to_currency($es['unit_price'],$base_currency); ?></td>
                        <td align="right"><?php echo to_currency($es['into_money'],$base_currency); ?></td>
                        <td align="right"><?php 
                        if($es['tax_name'] != ''){
                          echo html_entity_decode($es['tax_name']); 
                        }else{
                          $purchase_model = model('Purchase\Models\Purchase_model');

                          if($es['tax'] != ''){
                            $tax_arr =  $es['tax'] != '' ? explode('|', $es['tax']) : [];
                            $tax_str = '';
                            if(count($tax_arr) > 0){
                              foreach($tax_arr as $key => $tax_id){
                                if(($key + 1) < count($tax_arr) ){
                                  $tax_str .= $purchase_model->get_tax_name($tax_id).'|';
                                }else{
                                  $tax_str .= $purchase_model->get_tax_name($tax_id);
                                }
                              }
                            }

                            echo html_entity_decode($tax_str); 
                          }
                        }
                        ?></td>
                        <td align="right"><?php echo to_currency($es['tax_value'], $base_currency); ?></td>
                    
                        <td class="amount" align="right"><?php echo to_currency($es['total'],$base_currency); ?></td>
                     </tr>
                  <?php 
                  
                  } } ?>
                  </tbody>
               </table>
            </div>


      </div>
        <div class="row ml15 mr15" >
          <div class="col-md-6"></div>
           <div class="col-md-6 col-md-offset-6">
             <table class="table text-right mbot0">
               <tbody>
                  <tr id="subtotal">
                     <td class="td_style"><span class="bold"><?php echo _l('subtotal'); ?></span>
                     </td>
                     <td width="65%" id="total_td">
                      
                       <?php echo to_currency($_subtotal, $base_currency); ?>
                     </td>
                  </tr>
                </tbody>
              </table>

              <table class="table text-right">
               <tbody id="tax_area_body">
                  <?php if(isset($pur_request)){ 
                    echo html_entity_decode($taxes_data['html']);
                    ?>
                  <?php } ?>
               </tbody>
              </table>

              <table class="table text-right">
               <tbody id="tax_area_body">
                  <tr id="total">
                     <td class="td_style"><span class="bold"><?php echo _l('total'); ?></span>
                     </td>
                     <td width="65%" id="total_td">
                       <?php echo to_currency($pur_request->total, $base_currency); ?>
                     </td>
                  </tr>
               </tbody>
              </table>

          </div>
      </div>
      <?php echo form_hidden('request_detail'); ?>

      <?php if($user_type == 'staff'){ ?>
      <div class="row ml15 mr15">
         <?php if(count($list_approve_status) > 0 ){ ?>
        <p class=" p_style"><?php echo _l('pur_approval_infor'); ?></p>
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
                      <img src="<?php echo base_url('plugins/Purchase/Uploads/pur_request/signature/'.$pur_request->id.'/signature_'.$value['id'].'.png'); ?>" class="img_style">
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
            <div class="pull-right mb15">
                <?php 
                if($check_appr && $check_appr != false){
                if($pur_request->status == 1 && ($check_approve_status == false || $check_approve_status == 'reject')){ ?>
            <a data-toggle="tooltip" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_request_approve(<?php echo html_entity_decode($pur_request->id); ?>); return false;"><?php echo _l('send_request_approve_pur'); ?></a>
          <?php } }
            if(isset($check_approve_status['staffid'])){
                ?>
                <?php 
            if(in_array(get_staff_user_id1(), $check_approve_status['staffid']) && !in_array(get_staff_user_id1(), $get_staff_sign) && $pur_request->status == 1){ ?>
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

                            <a href="#" class="btn btn-success pull-left display-block  mr-4 button-margin-r-b" data-loading-text="<?php echo app_lang('wait_text'); ?>" onclick="approve_request(<?php echo html_entity_decode($pur_request->id); ?>); return false;"><span data-feather="upload" class="icon-16"></span>
                              <?php echo app_lang('approve'); ?>
                            </a>

                            <a href="#" data-loading-text="<?php echo app_lang('wait_text'); ?>" onclick="deny_request(<?php echo html_entity_decode($pur_request->id); ?>); return false;" class="btn btn-warning text-white"><span data-feather="x" class="icon-16"></span><?php echo app_lang('deny'); ?>
                          </a>

                        </div>
                      </div>
                    </div>
                  </div>
              <?php }
                ?>
                
              <?php
               if(in_array(get_staff_user_id1(), $check_approve_status['staffid']) && in_array(get_staff_user_id1(), $get_staff_sign) && $pur_request->status == 1){ ?>
                <button onclick="accept_action();" class="btn btn-success pull-left action-button"><?php echo _l('e_signature_sign'); ?></button>
              <?php }
                ?>
                <?php 
                 }
                ?>
              </div>
      
      </div>
      <?php } ?>
    </div>

    <div role="tabpanel" class="tab-pane  <?php if($tab == 'attachment'){ echo 'active'; } ?>" id="attachment">
       <?php echo form_open_multipart(admin_url('purchase/purchase_request_attachment/'.$pur_request->id),array('id'=>'partograph-attachments-upload')); ?>
        

        <div class="row ml15 mr15 mt10">
          <?php echo render_input1('file','file','','file', ['required'=> true]); ?>
       </div>
      <div class="row">
        <div class="col-md-12">
        <button id="obgy_btn2" type="submit" class="ml15 mr15 mb15 btn btn-info pull-right text-white"><?php echo _l('submit'); ?></button>
        <?php echo form_close(); ?>
        </div>
      </div>
       
       <div class="col-md-12 " id="purrequest_pv_file">
                            <?php


                                $file_html = '';
                                if(count($pur_request_attachments) > 0){
                                    $file_html .= '<hr />';
                                    foreach ($pur_request_attachments as $f) {
                                        $href_url = base_url('plugins/Purchase/Uploads/pur_request/'.$f['rel_id'].'/'.$f['file_name']).'" download';
                                                        if(!empty($f['external'])){
                                                          $href_url = $f['external_link'];
                                                        }
                                       $file_html .= '<div class="mb15 ml15 mr15 row" data-attachment-id="'. $f['id'].'">
                                      <div class="col-md-8 d-flex">
                                       
         
                                           '.modal_anchor(get_uri("purchase/file_purrequest/".$f['id']."/".$f['rel_id']), "<i data-feather='eye' class='icon-16'></i>", array("class" => "btn btn-success text-white mr5", "title" => $f['file_name'], "data-post-id" => $f['id'])).'

                                            <div class="d-block">
                                             <div class="pull-left"><i class="'. get_mime_class($f['filetype']).'"></i></div>
                                             <a href=" '. $href_url.'" target="_blank" download>'.$f['file_name'].'</a>
                                             <br />
                                            <small class="text-muted">'.$f['filetype'].'</small>
                                            </div>
                                           
                                      </div>
                                      <div class="col-md-4 text-right">';
                                        if($f['staffid'] == get_staff_user_id1() || is_admin()){
                                        $file_html .= '<a href="#" class="text-danger" onclick="delete_purrequest_attachment('. $f['id'].'); return false;"><i data-feather="x" class="icon-16"></i></a>';
                                        } 
                                       $file_html .= '</div></div>';
                                    }
                                    
                                    echo html_entity_decode($file_html);
                                }
                             ?>
                          </div>

       <div id="purrequest_file_data"></div>
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

           <button onclick="sign_request(<?php echo html_entity_decode($pur_request->id); ?>);" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" class="btn btn-success"><?php echo _l('e_signature_sign'); ?></button>
          </div>


      </div><!-- /.modal-content -->
   </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<?php require FCPATH. PLUGIN_URL_PATH . "Purchase/assets/js/purchase_request/view_pur_request_js.php";  ?>
