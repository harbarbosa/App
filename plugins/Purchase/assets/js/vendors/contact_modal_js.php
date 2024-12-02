<script>
(function($) {
  "use strict";    

$('input[name="email"]').on('change', function(){
	var data = {};
	data.email = $(this).val();
	data.contact_id = $('input[name="contactid"]').val();
	if(data.email != ''){
		$.post("<?php echo get_uri('purchase/contact_email_exists'); ?>", data).done(function(reponse){
			reponse = JSON.parse(reponse);
			if(reponse == false || reponse == 'false'){
				$('input[name="email"]').val('').change();
				appAlert.warning('<?php echo _l('contact_email_exists'); ?>');
			}

		});
	}
});


})(jQuery);  
</script>