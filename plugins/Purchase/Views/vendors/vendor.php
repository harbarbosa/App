
		<div id="page-content" class="page-wrapper clearfix">
	      <div class="row">
	         <div class="col-md-12">

	         </div>
	         <?php if($group_tab == 'profile'){ ?>
	         <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
	            <button class="btn btn-info only-save customer-form-submiter text-white <?php if(isset($client)){ echo 'mr-5'; } ?>">
	            <?php echo _l( 'pur_save'); ?>
	            </button>
	            <?php if(!isset($client)){ ?>
	            <button class="btn btn-info save-and-add-contact customer-form-submiter text-white mr-5">
	            <?php echo _l( 'save_vendor_and_add_contact'); ?>
	            </button>
	            <?php } ?>
	         </div>
	         <?php } ?>
	         <?php if(isset($client)){ ?>
	         <div class="col-md-2">
	               <div class="padding-10">
	                  <h4 class="bold">
	                     #<?php echo html_entity_decode($client->userid . ' ' . $title); ?>
	                  </h4>
	               </div>
	        	 <?php echo view('Purchase\Views\vendors\tabs'); ?>
	         </div>
	         <?php } ?>
	         <div class="col-md-<?php if(isset($client)){echo 10;} else {echo 12;} ?>">
	            <div class="panel_s">
	               <div class="panel-body">
	                  <?php if(isset($client)){ ?>
	                  <?php echo form_hidden('isedit'); ?>
	                  <?php echo form_hidden('userid', $client->userid); ?>
	                  <div class="clearfix"></div>
	                  <?php } ?>
	                  <div>
	                     <div class="tab-content">
	                           <?php echo view((isset($tabs) ? $tabs['view'] : 'Purchase\Views\vendors\groups\profile')); ?>
	                     </div>
	                  </div>
	               </div>
	            </div>
	         </div>
	      </div>
	      <?php if($group_tab == 'profile'){ ?>
	         <div class="btn-bottom-pusher"></div>
	      <?php } ?>
	   </div>
<?php require FCPATH. PLUGIN_URL_PATH .'Purchase/assets/js/vendors/vendor_js.php';?>