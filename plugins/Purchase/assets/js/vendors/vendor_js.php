<?php
/**
 * Included in application/views/admin/clients/client.php
 */
?>
<script>

$(document).ready(function () {
    "use strict";
        $(".select2").select2();
    });
<?php if(isset($client)){ ?>

   $(function(){
    "use strict";
      initDataTable('.table-vendor_contacts', "<?php echo get_uri('purchase/vendor_contacts/'); ?>"+"<?php echo html_entity_decode($client->userid); ?>");

      initDataTable('.table-table_pur_order', "<?php echo get_uri('purchase/table_vendor_pur_order/'); ?>"+"<?php echo html_entity_decode($client->userid); ?>");

      initDataTable('.table-table_pur_invoices', "<?php echo get_uri('purchase/table_vendor_pur_invoices/'); ?>"+"<?php echo html_entity_decode($client->userid); ?>");

      initDataTable('.table-pur_estimates', "<?php echo get_uri('purchase/table_vendor_quotations/'); ?>" +"<?php echo html_entity_decode($client->userid); ?>");
   });
<?php } ?>

Dropzone.options.clientAttachmentsUpload = false;
var customer_id = $('input[name="userid"]').val();
$(function() {

    "use strict"; 
    var optionsHeading = [];
      var allContactsServerParams = {
       "custom_view": "[name='custom_view']",
     }

      var _table_api = initDataTable('.table-all-contacts', window.location.href, optionsHeading, optionsHeading, allContactsServerParams, [0,'asc']);
      if(_table_api) {
       
      }

    if ($('#client-attachments-upload').length > 0) {
        new Dropzone('#client-attachments-upload', appCreateDropzoneOptions({
            paramName: "file",
            accept: function(file, done) {
                done();
            },
            success: function(file, response) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    window.location.reload();
                }
            }
        }));
    }

    // Save button not hidden if passed from url ?tab= we need to re-click again
    /*if (tab_active) {
        $('body').find('.nav-tabs [href="#' + tab_active + '"]').click();
    }*/

    $('a[href="#customer_admins"]').on('click', function() {
        $('.btn-bottom-toolbar').addClass('hide');
    });

    $('.profile-tabs a').not('a[href="#customer_admins"]').on('click', function() {
        $('.btn-bottom-toolbar').removeClass('hide');
    });

    $("input[name='tasks_related_to[]']").on('change', function() {
        var tasks_related_values = []
        $('#tasks_related_filter :checkbox:checked').each(function(i) {
            tasks_related_values[i] = $(this).val();
        });
        $('input[name="tasks_related_to"]').val(tasks_related_values.join());
        $('.table-rel-tasks').DataTable().ajax.reload();
    });

    var contact_id = get_url_param('contactid');
    if (contact_id) {
        get_url_param(customer_id, contact_id);
    }

    // consents=CONTACT_ID
    var consents = get_url_param('consents');
    if(consents){
        view_contact_consent(consents);
    }

    // If user clicked save and add new contact
    if (get_url_param('new_contact')) {
        vendor_contact(customer_id);
    }

    $('body').on('change', '.onoffswitch input.customer_file', function(event, state) {
        var invoker = $(this);
        var checked_visibility = invoker.prop('checked');
        var share_file_modal = $('#customer_file_share_file_with');
        setTimeout(function() {
            $('input[name="file_id"]').val(invoker.attr('data-id'));
            if (checked_visibility && share_file_modal.attr('data-total-contacts') > 1) {
                share_file_modal.modal('show');
            } else {
                do_share_file_contacts();
            }
        }, 200);
    });

    $('.customer-form-submiter').on('click', function() {
        var form = $('.vendor-form');
        if (form.valid()) {
            if ($(this).hasClass('save-and-add-contact')) {
                form.find('.additional').html(hidden_input('save_and_add_contact', 'true'));
            } else {
                form.find('.additional').html('');
            }
            form.submit();
        }
    });

    if (typeof(Dropbox) != 'undefined' && $('#dropbox-chooser').length > 0) {
        document.getElementById("dropbox-chooser").appendChild(Dropbox.createChooseButton({
            success: function(files) {
                saveCustomerProfileExternalFile(files, 'dropbox');
            },
            linkType: "preview",
            //extensions: app.options.allowed_files.split(','),
        }));
    }

    

    /* Custome profile contacts table */
    var contactsNotSortable = [];

    _table_api = initDataTable('.table-vendor_contacts',  'purchase/vendor_contacts/' + customer_id, contactsNotSortable, contactsNotSortable);
 

    var vRules = {};
    
        vRules = {
            company: 'required',
            vendor_code: {
               required: true,
               remote: {
                url:  "<?php echo get_uri('purchase/vendor_code_exists'); ?>",
                type: 'post',
                data: {
                    vendor_code: function() {
                        return $('input[name="vendor_code"]').val();
                    },
                    userid: function() {
                        return $('input[name="userid"]').val();
                    }
                }
            }
           }
        }
    

    appValidateForm($('.vendor-form'), vRules);


    $('.billing-same-as-customer').on('click', function(e) {
        e.preventDefault();
        $('textarea[name="billing_street"]').val($('textarea[name="address"]').val());
        $('input[name="billing_city"]').val($('input[name="city"]').val());
        $('input[name="billing_state"]').val($('input[name="state"]').val());
        $('input[name="billing_zip"]').val($('input[name="zip"]').val());
        $('input[name="billing_country"]').val($('input[name="country"]').val());
        
    });

    $('.customer-copy-billing-address').on('click', function(e) {
        e.preventDefault();
        $('textarea[name="shipping_street"]').val($('textarea[name="billing_street"]').val());
        $('input[name="shipping_city"]').val($('input[name="billing_city"]').val());
        $('input[name="shipping_state"]').val($('input[name="billing_state"]').val());
        $('input[name="shipping_zip"]').val($('input[name="billing_zip"]').val());
        $('input[name="shipping_country"]').val($('input[name="billing_country"]').val());
        
    });

    $('body').on('hidden.bs.modal', '#contact', function() {
        $('#contact_data').empty();
    });

    $('.vendor-form').on('submit', function() {
        $('select[name="default_currency"]').prop('disabled', false);
    });
    <?php if(isset($client)  && $group_tab == 'purchase_statement'){ ?>
    render_vendor_statement();
<?php } ?>

});
<?php if(isset($client)){ ?>
function render_vendor_statement(){
    "use strict";
     var $statementPeriod = $('#range');
     
     var period = new Array();
     if(value != 'period'){
        period = JSON.parse(value);
    } else {
        period[0] = $('input[name="period-from"]').val();
        period[1] = $('input[name="period-to"]').val();

        if(period[0] == '' || period[1] == ''){
            return false;
        }
    }

    var statementUrl = admin_url+'purchase/statement';
    var statementUrlParams = new Array();

    statementUrlParams['vendor_id'] = '<?php echo html_entity_decode($client->userid); ?>';
    statementUrlParams['from'] = period[0];
    statementUrlParams['to'] = period[1];
    statementUrl = buildUrl(statementUrl,statementUrlParams);

    $.get(statementUrl,function(response){
        $('#statement-html').html(response.html);

        $('#statement_pdf').attr('href',buildUrl(admin_url+'purchase/statement_pdf',statementUrlParams));
        $('#send_statement_form').attr('action',buildUrl(admin_url+'purchase/send_statement',statementUrlParams));

        statementUrlParams['print'] = true;
        $('#statement_print').attr('href',buildUrl(admin_url+'purchase/statement_pdf',statementUrlParams));
    },'json').fail(function(response){
        alert_float('danger',response.responseText);
    });
}
<?php } ?>

function delete_contact_profile_image(contact_id) {
    "use strict"; 
    requestGet('clients/delete_contact_profile_image/'+contact_id).done(function(){
        $('body').find('#contact-profile-image').removeClass('hide');
        $('body').find('#contact-remove-img').addClass('hide');
        $('body').find('#contact-img').attr('src', '<?php echo base_url('assets/images/user-placeholder.jpg'); ?>');
    });
}

function customerGoogleDriveSave(pickData) {
    "use strict"; 
    saveCustomerProfileExternalFile(pickData, 'gdrive');
}

function saveCustomerProfileExternalFile(files, externalType) {
    "use strict"; 
    $.post( 'clients/add_external_attachment', {
        files: files,
        clientid: customer_id,
        external: externalType
    }).done(function() {
        window.location.reload();
    });
}

function validate_contact_form() {
    "use strict"; 
    appValidateForm('#contact-form', {
        firstname: 'required',
        lastname: 'required',
        password: {
            required: {
                depends: function(element) {

                    var $sentSetPassword = $('input[name="send_set_password_email"]');

                    if ($('#contact input[name="contactid"]').val() == '' && $sentSetPassword.prop('checked') == false) {
                        return true;
                    }
                }
            }
        },
        email: {
           
            required: true,
       
            email: true,
            // Use this hook only if the contacts are not logging into the customers area and you are not using support tickets piping.
            
            remote: {
                url:  "purchase/contact_email_exists",
                type: 'post',
                data: {
                    email: function() {
                        return $('#contact input[name="email"]').val();
                    },
                    userid: function() {
                        return $('body').find('input[name="contactid"]').val();
                    }
                }
            }
          
        }
    }, contactFormHandler);
}

function contactFormHandler(form) {
    "use strict"; 
    $('#contact input[name="is_primary"]').prop('disabled', false);

    $("#contact input[type=file]").each(function() {
        if($(this).val() === "") {
            $(this).prop('disabled', true);
        }
    });

    var formURL = $(form).attr("action");
    var formData = new FormData($(form)[0]);

    $.ajax({
        type: 'POST',
        data: formData,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
    }).done(function(response){
           response = JSON.parse(response);
            if (response.success) {
                alert_float('success', response.message);
                if(typeof(response.is_individual) != 'undefined' && response.is_individual) {
                    $('.new-contact').addClass('disabled');
                    if(!$('.new-contact-wrapper')[0].hasAttribute('data-toggle')) {
                        $('.new-contact-wrapper').attr('data-toggle','tooltip');
                    }
                }
            }

            if ($.fn.DataTable.isDataTable('.table-vendor_contacts')) {
                $('.table-vendor_contacts').DataTable().ajax.reload(null,false);
            } else if ($.fn.DataTable.isDataTable('.table-all-vendor_contacts')) {
                $('.table-all-vendor_contacts').DataTable().ajax.reload(null,false);
            }

            if (response.proposal_warning && response.proposal_warning != false) {
                $('body').find('#contact_proposal_warning').removeClass('hide');
                $('body').find('#contact_update_proposals_emails').attr('data-original-email', response.original_email);
                $('#contact').animate({
                    scrollTop: 0
                }, 800);
            } else {
                $('#contact').modal('hide');
            }
    }).fail(function(error){
        alert_float('danger', JSON.parse(error.responseText));
    });
    return false;
}

function vendor_contact(client_id, contact_id) {
    "use strict"; 
    if (typeof(contact_id) == 'undefined') {
        contact_id = '';
    }
    requestGet('purchase/form_contact/' + client_id + '/' + contact_id).done(function(response) {
        $('#contact_data').html(response);
        $('#contact').modal({
            show: true,
            backdrop: 'static'
        });
        $('body').off('shown.bs.modal','#contact');
        $('body').on('shown.bs.modal', '#contact', function() {
            if (contact_id == '') {
                $('#contact').find('input[name="firstname"]').focus();
            }
        });

        init_datepicker();
        custom_fields_hyperlink();
        validate_contact_form();
    }).fail(function(error) {
        var response = JSON.parse(error.responseText);
        alert_float('danger', response.message);
    });
}


function update_all_proposal_emails_linked_to_contact(contact_id) {
    "use strict"; 
    var data = {};
    data.update = true;
    data.original_email = $('body').find('#contact_update_proposals_emails').data('original-email');
    $.post( 'clients/update_all_proposal_emails_linked_to_customer/' + contact_id, data).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
            alert_float('success', response.message);
        }
        $('#contact').modal('hide');
    });
}

function do_share_file_contacts(edit_contacts, file_id) {
    "use strict"; 
    var contacts_shared_ids = $('select[name="share_contacts_id[]"]');
    if (typeof(edit_contacts) == 'undefined' && typeof(file_id) == 'undefined') {
        var contacts_shared_ids_selected = $('select[name="share_contacts_id[]"]').val();
    } else {
        var _temp = edit_contacts.toString().split(',');
        for (var cshare_id in _temp) {
            contacts_shared_ids.find('option[value="' + _temp[cshare_id] + '"]').attr('selected', true);
        }

        $('input[name="file_id"]').val(file_id);
        $('#customer_file_share_file_with').modal('show');
        return;
    }
    var file_id = $('input[name="file_id"]').val();
    $.post( 'clients/update_file_share_visibility', {
        file_id: file_id,
        share_contacts_id: contacts_shared_ids_selected,
        customer_id: $('input[name="userid"]').val()
    }).done(function() {
        window.location.reload();
    });
}

function save_longitude_and_latitude(clientid) {
    "use strict"; 
    var data = {};
    data.latitude = $('#latitude').val();
    data.longitude = $('#longitude').val();
    $.post( 'clients/save_longitude_and_latitude/'+clientid, data).done(function(response) {
       if(response == 'success') {
            alert_float('success', "<?php echo _l('updated_successfully', _l('client')); ?>");
       }
        setTimeout(function(){
            window.location.reload();
        },1200);
    }).fail(function(error) {
        alert_float('danger', error.responseText);
    });
}

function fetch_lat_long_from_google_cprofile() {
    "use strict"; 
    var data = {};
    data.address = $('#long_lat_wrapper').data('address');
    data.city = $('#long_lat_wrapper').data('city');
    data.country = $('#long_lat_wrapper').data('country');
    $('#gmaps-search-icon').removeClass('fa-google').addClass('fa-spinner fa-spin');
    $.post( 'misc/fetch_address_info_gmaps', data).done(function(data) {
        data = JSON.parse(data);
        $('#gmaps-search-icon').removeClass('fa-spinner fa-spin').addClass('fa-google');
        if (data.response.status == 'OK') {
            $('input[name="latitude"]').val(data.lat);
            $('input[name="longitude"]').val(data.lng);
        } else {
            if (data.response.status == 'ZERO_RESULTS') {
                alert_float('warning', "<?php echo _l('g_search_address_not_found'); ?>");
            } else {
                alert_float('danger', data.response.status + ' - ' + data.response.error_message);
            }
        }
    });
}

function preview_ic_btn(invoker){
    "use strict";
    var id = $(invoker).attr('id');
    var rel_id = $(invoker).attr('rel_id');
    view_ic_file(id, rel_id);
}

function view_ic_file(id, rel_id) {
    "use strict";
      $('#ic_file_data').empty();
      $("#ic_file_data").load( 'purchase/file_pur_vendor/' + id + '/' + rel_id, function(response, status, xhr) {
          if (status == "error") {
              alert_float('danger', xhr.statusText);
          }
      });
}

function close_modal_preview(){
    "use strict";
 $('._project_file').modal('hide');
}

function delete_ic_attachment(id) {
    "use strict";
    if (confirm_delete()) {
        requestGet('purchase/delete_ic_attachment/' + id).done(function(success) {
            if (success == 1) {
                $("#ic_pv_file").find('[data-attachment-id="' + id + '"]').remove();
            }
        }).fail(function(error) {
            alert_float('danger', error.responseText);
        });
    }
}

function routing_init_editor(selector, settings) {

        "use strict";

      tinymce.remove(selector);

    selector = typeof(selector) == 'undefined' ? '.tinymce' : selector;
    var _editor_selector_check = $(selector);

    if (_editor_selector_check.length === 0) { return; }

    $.each(_editor_selector_check, function() {
      if ($(this).hasClass('tinymce-manual')) {
        $(this).removeClass('tinymce');
      }
    });

    // Original settings
    var _settings = {
      branding: false,
      selector: selector,
      browser_spellcheck: true,
      height: 400,
      theme: 'modern',
      skin: 'perfex',
      //language: app.tinymce_lang,
      relative_urls: false,
      inline_styles: true,
      verify_html: false,
      cleanup: false,
      autoresize_bottom_margin: 25,
      valid_elements: '+*[*]',
      valid_children: "+body[style], +style[type]",
      apply_source_formatting: false,
      remove_script_host: false,
      removed_menuitems: 'newdocument restoredraft',
      forced_root_block: false,
      autosave_restore_when_empty: false,
      fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt',
      setup: function(ed) {
            // Default fontsize is 12
            ed.on('init', function() {
              this.getDoc().body.style.fontSize = '12pt';
            });
        },
        table_default_styles: {
            // Default all tables width 100%
            width: '100%',
        },
        plugins: [
        'advlist autoresize autosave lists link image print hr codesample',
        'visualblocks code fullscreen',
        'media save table contextmenu',
        'paste textcolor colorpicker'
        ],
        toolbar1: 'fontselect fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | image link | bullist numlist | restoredraft',
        file_browser_callback: elFinderBrowser,
    };

    // Add the rtl to the settings if is true
    isRTL == 'true' ? _settings.directionality = 'rtl' : '';
    isRTL == 'true' ? _settings.plugins[0] += ' directionality' : '';

    // Possible settings passed to be overwrited or added
    if (typeof(settings) != 'undefined') {
      for (var key in settings) {
        if (key != 'append_plugins') {
          _settings[key] = settings[key];
        } else {
          _settings['plugins'].push(settings[key]);
        }
      }
    }

    // Init the editor
    var editor = tinymce.init(_settings);
    $(document).trigger('app.editor.initialized');

    return editor;
}

// Get url params like $_GET
function get_url_param(param) {
    "use strict";
    var vars = {};
    window.location.href.replace(location.hash, '').replace(
        /[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
        function(m, key, value) { // callback
            vars[key] = value !== undefined ? value : '';
        }
    );
    if (param) {
        return vars[param] ? vars[param] : null;
    }
    return vars;
}

// Show password on hidden input field
function showPassword(name) {
    "use strict";
    var target = $('input[name="' + name + '"]');
    if ($(target).attr('type') == 'password' && $(target).val() !== '') {
        $(target)
            .queue(function() {
                $(target).attr('type', 'text').dequeue();
            });
    } else {
        $(target).queue(function() {
            $(target).attr('type', 'password').dequeue();
        });
    }
}

// Generate random password
function generatePassword(field) {
    "use strict";
    var length = 12,
        charset = "abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
        retVal = "";
    for (var i = 0, n = charset.length; i < length; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }
    $(field).parents().find('input.password').val(retVal);
}

</script>
