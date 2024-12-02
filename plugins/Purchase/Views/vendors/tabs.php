
	<ul class="list-group help-catagory">
	   <?php
	      $i = 0;
	      foreach($tab as $group){
	        ?>
	        
	        <a href="<?php echo get_uri('purchase/vendor/'.$client->userid.'?group='.$group['name']); ?>" class="list-group-item <?php if($group_tab == $group['name']){echo " active"; } ?>" data-group="<?php echo html_entity_decode($group['name']); ?>">
	         <?php echo html_entity_decode($group['icon']).' '._l($group['name']); ?></a>
	        
	        <?php $i++; } ?>
	</ul>
