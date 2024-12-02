<script>
(function($) {
    "use strict";    
    $(".select2").select2();


	var addMoreAttachmentsInputKey = 1;
	$("body").on('click', '.add_more_attachments', function() {

	    if ($(this).hasClass('disabled')) {
	        return false;
	    }

	    var total_attachments = $('.attachments input[name*="attachments"]').length;
	    if ($(this).data('max') && total_attachments >= $(this).data('max')) {
	        return false;
	    }

	    var newattachment = $('.attachments').find('.attachment').eq(0).clone().appendTo('.attachments');
	    newattachment.find('input').removeAttr('aria-describedby aria-invalid');
	    newattachment.find('input').attr('name', 'attachments[' + addMoreAttachmentsInputKey + ']').val('');
	    newattachment.find($.fn.appFormValidator.internal_options.error_element + '[id*="error"]').remove();
	    newattachment.find('.' + $.fn.appFormValidator.internal_options.field_wrapper_class).removeClass($.fn.appFormValidator.internal_options.field_wrapper_error_class);
	    newattachment.find('svg').removeClass('feather-plus').addClass('feather-x').html('<line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>');
	    newattachment.find('button').removeClass('add_more_attachments').addClass('remove_attachment').removeClass('btn-success').addClass('btn-danger');
	    addMoreAttachmentsInputKey++;
	});

	// Remove attachment
    $("body").on('click', '.remove_attachment', function() {
        $(this).parents('.attachment').remove();
    });
})(jQuery);  	
</script>