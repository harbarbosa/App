
<div class="form-group mbot25  select-placeholder">
     <select name="item_select" class="select2 no-margin<?php if($ajaxItems == true){echo ' ajax-search';} ?>" data-width="100%"  id="item_select" placeholder="<?php echo _l('select_item'); ?>" data-live-search="true">
      <option value=""></option>
      <?php foreach($items as $group_id=>$_items){ ?>
      <optgroup data-group-id="<?php echo html_entity_decode($group_id); ?>" label="<?php echo html_entity_decode($_items[0]['group_name']); ?>">
       <?php foreach($_items as $item){ ?>
       <option value="<?php echo html_entity_decode($item['id']); ?>" data-subtext="<?php echo strip_tags(mb_substr($item['description'],0,200)).'...'; ?>">(<?php echo to_decimal_format($item['purchase_price']); ; ?>) <?php echo html_entity_decode($item['item_title']); ?></option>
       <?php } ?>
     </optgroup>
     <?php } ?>
   </select>
</div>
