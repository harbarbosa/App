<div class="horizontal-scrollable-tabs">
   <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
   <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
   <div class="horizontal-tabs">
      <ul class="nav nav-tabs profile-tabs row customer-profile-tabs nav-tabs-horizontal" role="tablist">
         <li role="presentation" class="<?php if($tab_2 == 'general_mapping_setup'){echo 'active';}; ?>">
            <a href="<?php echo get_uri('accounting/setting?group=mapping_setup&tab=general_mapping_setup'); ?>">
              <i class="fa fa-th"></i>&nbsp;<?php echo app_lang('general'); ?>
            </a>
         </li>
     
         
         
      </ul>
   </div>
  <?php echo view($tab_2); ?>
</div>