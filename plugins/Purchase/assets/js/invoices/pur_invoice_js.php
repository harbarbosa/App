<script>
$(document).ready(function () {
  "use strict"; 
  $(".select2").select2();
  setDatePicker("#invoice_date");
  setDatePicker("#duedate");
  setDatePicker("#transaction_date");
});


(function($) {
  "use strict";


  var vendor = $('select[name="vendor"]').val();

  <?php if(get_setting('item_by_vendor') != 1){ ?>
    init_ajax_search('items','#item_select.ajax-search',undefined,"<?php echo get_uri('purchase/pur_commodity_code_search'); ?>" );
  <?php } ?>

  <?php if(is_numeric($vendor_id) && !isset($pur_invoice)){ ?>
      $('select[name="vendor"]').val("<?php echo html_entity_decode($vendor_id); ?>").change();
    <?php } ?>

  $("body").on('change', 'select[name="item_select"]', function () {
    var itemid = $(this).val();
    if (itemid != '') {
      pur_add_item_to_preview(itemid);
    }
  });

  $("body").on('change', 'select.taxes', function () {
      pur_calculate_total();
    });

    $("body").on('change', 'select[name="currency"]', function () {

      var currency_id = $(this).val();
      if(currency_id != ''){
        $.post("<?php echo get_uri('purchase/get_currency_rate/'); ?>"+currency_id).done(function(response){
          response = JSON.parse(response);
          if(response.currency_rate != 1){
            $('#currency_rate_div').removeClass('hide');

            $('input[name="currency_rate"]').val(response.currency_rate).change();

            $('#convert_str').html(response.convert_str);
            $('.th_currency').html(response.currency_name);
          }else{
            $('input[name="currency_rate"]').val(response.currency_rate).change();
            $('#currency_rate_div').addClass('hide');
            $('#convert_str').html(response.convert_str);
            $('.th_currency').html(response.currency_name);

          }

          pur_calculate_total();
        });
      }else{
        alert_float('warning', "<?php echo _l('please_select_currency'); ?>" )
      }

    });

    $("input[name='currency_rate']").on('change', function () { 
      if (AppHelper.settings.noOfDecimals == "0") {
          var decimal_places  = 0; /*round it and the add static 2 decimals*/
        } else {
          var decimal_places  = 2;
        }
      
        var currency_rate = $(this).val();
        var rows = $('.table.has-calculations tbody tr.item');
        $.each(rows, function () { 
          var old_price = $(this).find('td.rate input[name="og_price"]').val();
          var new_price = currency_rate*old_price;
          $(this).find('td.rate input[type="number"]').val(new_price.toFixed(decimal_places)).change();

        });
    });

    pur_calculate_total();

    $('.save_detail_inv').on('click', function(){
      $('#pur_invoice-form').submit();
    });

})(jQuery);

var lastAddedItemKey = null;
 

function pur_order_change(el){
  "use strict";
  if(el.value != ''){
    $.post("<?php echo get_uri('purchase/pur_order_change/'); ?>"+el.value).done(function(response) {
      response = JSON.parse(response);
        if(response){ 
          $('select[name="currency"]').val(response.currency).change();
          $('input[name="currency_rate"]').val(response.currency_rate).change();
          $('input[name="shipping_fee"]').val(response.shipping_fee);
          $('input[name="order_discount"]').val(response.order_discount);
          $('select[name="add_discount_type"]').val('amount');
          
          $('.invoice-item table.invoice-items-table.items tbody').html('');
          $('.invoice-item table.invoice-items-table.items tbody').append(response.list_item);

          $('input[name="discount_percent"]').val(response.discount_percent).change();

          setTimeout(function () {
            pur_calculate_total();
          }, 15);

          $('.refresh_tax1 .select2').select2('destroy');
          $('.refresh_tax1 .select2').select2();

          $('.refresh_tax2 .select2').select2('destroy');
          $('.refresh_tax2 .select2').select2();

          pur_reorder_items('.invoice-item');
          pur_clear_item_preview_values('.invoice-item');
          $('body').find('#items-warning').remove();
          $("body").find('.dt-loader').remove();
          $('#item_select').val('');
        }
    });

    $('#recurring_div').addClass('hide');
    $('select[name="recurring"]').val(0).change();
  }else{
    $('#recurring_div').removeClass('hide');
    $('select[name="recurring"]').val(0).change();
  }
}

function pur_vendor_change(el){
  "use strict";
  if(el.value != ''){
    $.post("<?php echo get_uri('purchase/pur_vendors_change/'); ?>"+el.value).done(function(response) {
      response = JSON.parse(response);
      $('select[name="currency"]').val(response.currency_id).change();
      
        $('select[name="pur_order"]').html('');
        $('select[name="pur_order"]').append(response.po_html);
        $('select[name="pur_order"]').select2();
      
        $('select[name="contract"]').html(response.html);
        $('select[name="contract"]').select2();

        <?php if(get_setting('item_by_vendor') == 1){ ?>
        if(response.option_html != ''){
         $('#item_select').html(response.option_html);
         $('.selectpicker').select2();
        }else if(response.option_html == ''){
          init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'purchase/pur_commodity_code_search/purchase_price/can_be_purchased/'+invoker.value);
        }
        
       <?php } ?>
      
    });
  }
}

function subtotal_change(el){
  "use strict";
  var tax = $('#tax').val();
  if(tax == ''){
    tax = '0';
  }
  var total_value =  parseFloat(removeCommas(el.value)) + parseFloat(removeCommas(tax));
  $('#total').val( numberWithCommas(total_value) );
}

function tax_rate_change(el){
  "use strict";
  var subtotal = $('#subtotal').val();
  var tax = $('#tax').val();
  var total = $('#total').val();
  if(el.value != ''){
    $.post("<?php echo get_uri('purchase/tax_rate_change/'); ?>"+el.value).done(function(response) {
      response = JSON.parse(response);
      var tax_value = parseFloat(removeCommas(subtotal)*response.rate)/100;
      var total_value =  parseFloat(removeCommas(subtotal)) +  tax_value;
      $('#tax').val( numberWithCommas(tax_value));
      $('#total').val( numberWithCommas(total_value));
    });
  }
}

function pur_calculate_total(from_discount_money){
  "use strict";
  if ($('body').hasClass('no-calculate-total')) {
    return false;
  }

  if (AppHelper.settings.noOfDecimals == "0") {
    var decimal_places  = 0; /*round it and the add static 2 decimals*/
  } else {
    var decimal_places  = 2;
  }

  var currency = $('select[name="currency"]').val();
  if(currency == "<?php echo get_setting('default_currency'); ?>"){
    currency = "<?php echo get_setting('currency_symbol'); ?>";
  }

  var calculated_tax,
    taxrate,
    item_taxes,
    row,
    _amount,
    _tax_name,
    taxes = {},
    taxes_rows = [],
    subtotal = 0,
    total = 0,
    total_money = 0,
    total_tax_money = 0,
    quantity = 1,
    total_discount_calculated = 0,
    item_total_payment,
    rows = $('.table.has-calculations tbody tr.item'),
    subtotal_area = $('#subtotal'),
    discount_area = $('#discount_area'),
    adjustment = $('input[name="adjustment"]').val(),

    discount_percent = 'before_tax',
    discount_fixed = $('input[name="discount_total"]').val(),
    discount_total_type = $('.discount-total-type.selected'),
    discount_type = $('select[name="discount_type"]').val(),
    additional_discount = $('input[name="additional_discount"]').val(),
    add_discount_type = $('select[name="add_discount_type"]').val();

    var shipping_fee = $('input[name="shipping_fee"]').val();
    if(shipping_fee == ''){
      shipping_fee = 0;
      $('input[name="shipping_fee"]').val(0);
    }

  $('.wh-tax-area').remove();

    $.each(rows, function () {
    var item_discount = 0;
    var item_discount_money = 0;
    var item_discount_from_percent = 0;
    var item_discount_percent = 0;
    var item_tax = 0,
        item_amount  = 0;

    quantity = $(this).find('[data-quantity]').val();
    if (quantity === '') {
      quantity = 1;
      $(this).find('[data-quantity]').val(1);
    }
    item_discount_percent = $(this).find('td.discount input').val();
    item_discount_money = $(this).find('td.discount_money input').val();

    if (isNaN(item_discount_percent) || item_discount_percent == '') {
      item_discount_percent = 0;
    }

    if (isNaN(item_discount_money) || item_discount_money == '') {
      item_discount_money = 0;
    }

    if(from_discount_money == 1 && item_discount_money > 0){
      $(this).find('td.discount input').val('');
    }

    _amount = parseFloat($(this).find('td.rate input').val() * quantity).toFixed(decimal_places);
    item_amount = _amount;
    _amount = parseFloat(_amount);

    $(this).find('td.into_money').html(toCurrency(_amount, currency));
    $(this).find('td._into_money input').val(_amount);

    subtotal += _amount;
    row = $(this);
    item_taxes = $(this).find('select.taxes').val();

    if (item_taxes) {
      $.each(item_taxes, function (i, taxname) {
        taxrate = row.find('select.taxes [value="' + taxname + '"]').data('taxrate');
        calculated_tax = (_amount / 100 * taxrate);
        item_tax += calculated_tax;
        if (!taxes.hasOwnProperty(taxname)) {
          if (taxrate != 0) {
            _tax_name = taxname.split('|');
            var tax_row = '<tr class="wh-tax-area"><td>' + _tax_name[0] + '(' + taxrate + '%)</td><td id="tax_id_' + slugify(taxname) + '"></td></tr>';
            $(subtotal_area).after(tax_row);
            taxes[taxname] = calculated_tax;
          }
        } else {
                    // Increment total from this tax
                    taxes[taxname] = taxes[taxname] += calculated_tax;
                }
            });
    }
    var after_tax = _amount + item_tax;

    $(this).find('td._total').html(toCurrency(after_tax , currency));
    $(this).find('td._total_after_tax input').val(after_tax);

    $(this).find('td.tax_value input').val(item_tax);
      //Discount of item
      if( item_discount_percent > 0 && from_discount_money != 1){
        item_discount_from_percent = (parseFloat(item_amount) + parseFloat(item_tax) ) * parseFloat(item_discount_percent) / 100;
        if(item_discount_from_percent != item_discount_money){
          item_discount_money = item_discount_from_percent;
        }
      }

      if( item_discount_money > 0){
        item_discount = parseFloat(item_discount_money);
      }

      item_total_payment = parseFloat(item_amount) + parseFloat(item_tax) - parseFloat(item_discount);
      // Append value to item
      total_discount_calculated += item_discount;
      $(this).find('td.discount_money input').val(item_discount);
      $(this).find('td.total_after_discount input').val(item_total_payment);

      $(this).find('td.label_total_after_discount').html(toCurrency(item_total_payment, currency));

  });

  // Discount by percent
  if ((discount_percent !== '' && discount_percent != 0) && discount_type == 'before_tax' && discount_total_type.hasClass('discount-type-percent')) {
    total_discount_calculated = (subtotal * discount_percent) / 100;
  } else if ((discount_fixed !== '' && discount_fixed != 0) && discount_type == 'before_tax' && discount_total_type.hasClass('discount-type-fixed')) {
    total_discount_calculated = discount_fixed;
  }

  var tds_tax = 0;
  $.each(taxes, function (taxname, total_tax) {
    if ((discount_percent !== '' && discount_percent != 0) && discount_type == 'before_tax' && discount_total_type.hasClass('discount-type-percent')) {
      total_tax_calculated = (total_tax * discount_percent) / 100;
      total_tax = (total_tax - total_tax_calculated);
    } else if ((discount_fixed !== '' && discount_fixed != 0) && discount_type == 'before_tax' && discount_total_type.hasClass('discount-type-fixed')) {
      var t = (discount_fixed / subtotal) * 100;
      total_tax = (total_tax - (total_tax * t) / 100);
    }

    if(taxname.indexOf("(TDS)") === -1){
      total += total_tax;
    }else{
      tds_tax += total_tax;
    }

    total_tax_money += total_tax;
    total_tax = toCurrency(total_tax, currency);
    $('#tax_id_' + slugify(taxname)).html(total_tax);
  });


  total = (total + subtotal)  - tds_tax;
  total_money = total;
  // Discount by percent
  if ((discount_percent !== '' && discount_percent != 0) && discount_type == 'after_tax' && discount_total_type.hasClass('discount-type-percent')) {
    total_discount_calculated = (total * discount_percent) / 100;
  } else if ((discount_fixed !== '' && discount_fixed != 0) && discount_type == 'after_tax' && discount_total_type.hasClass('discount-type-fixed')) {
    total_discount_calculated = discount_fixed;
  }

  var order_discount_percent = $('input[name="order_discount"]').val();
  var order_discount_percent_val = 0;
  if(order_discount_percent != ''){
    if(add_discount_type == 'percent'){
      order_discount_percent_val = (total * order_discount_percent) / 100;
    }else if(add_discount_type == 'amount'){
      order_discount_percent_val = parseFloat(order_discount_percent);
    }
  }

  total_discount_calculated = total_discount_calculated + order_discount_percent_val;

  total = total - total_discount_calculated - parseFloat(additional_discount);
  adjustment = parseFloat(adjustment);

  // Check if adjustment not empty
  if (!isNaN(adjustment)) {
    total = total + adjustment;
  }

  total+= parseFloat(shipping_fee);

  var discount_html = '-' + toCurrency((parseFloat(total_discount_calculated)+ parseFloat(additional_discount)), currency);
    $('input[name="discount_total"]').val(total_discount_calculated.toFixed(decimal_places));
    
  // Append, format to html and display
  $('.shiping_fee').html(toCurrency(shipping_fee, currency));
  $('.order_discount_value').html(toCurrency(order_discount_percent_val, currency));
  $('.wh-total_discount').html(discount_html + hidden_input('dc_total', order_discount_percent_val.toFixed(decimal_places)));
  $('.adjustment').html(toCurrency(adjustment, currency));
  $('.wh-subtotal').html(toCurrency(subtotal, currency) + hidden_input('total_mn', subtotal.toFixed(decimal_places)));
  $('.wh-total').html(toCurrency(total, currency) + hidden_input('grand_total', total.toFixed(decimal_places)));

  $(document).trigger('purchase-quotation-total-calculated');

}

function pur_add_item_to_preview(id) {
  "use strict";
  var currency_rate = $('input[name="currency_rate"]').val();
  requestGetJSON("<?php echo get_uri('purchase/get_item_by_id/'); ?>" + id +'/'+currency_rate).done(function (response) {
    pur_clear_item_preview_values();

    $('.main input[name="item_code"]').val(response.itemid);
    $('.main textarea[name="item_name"]').val(response.code_description);
    $('.main textarea[name="description"]').val(response.long_description);
    $('.main input[name="unit_price"]').val(response.purchase_price);
    $('.main input[name="unit_name"]').val(response.unit_name);
    $('.main input[name="unit_id"]').val(response.unit_id);
    $('.main input[name="quantity"]').val(1);

    var taxSelectedArray = [];
    if (response.taxname && response.taxrate) {
      taxSelectedArray.push(response.taxname + '|' + response.taxrate);
    }
    if (response.taxname_2 && response.taxrate_2) {
      taxSelectedArray.push(response.taxname_2 + '|' + response.taxrate_2);
    }

    $('.main select.taxes').val(taxSelectedArray).change();
    $('.main input[name="unit"]').val(response.unit_name);

    var $currency = $("body").find('.accounting-template select[name="currency"]');
    var baseCurency = $currency.attr('data-base');
    var selectedCurrency = $currency.find('option:selected').val();
    var $rateInputPreview = $('.main input[name="rate"]');

    if (baseCurency == selectedCurrency) {
      $rateInputPreview.val(response.purchase_price);
    } else {
      var itemCurrencyRate = response['rate_currency_' + selectedCurrency];
      if (!itemCurrencyRate || parseFloat(itemCurrencyRate) === 0) {
        $rateInputPreview.val(response.purchase_price);
      } else {
        $rateInputPreview.val(itemCurrencyRate);
      }
    }

    $(document).trigger({
      type: "item-added-to-preview",
      item: response,
      item_type: 'item',
    });
  });
}

function pur_add_item_to_table(data, itemid) {
  "use strict";

  data = typeof (data) == 'undefined' || data == 'undefined' ? pur_get_item_preview_values() : data;

  if (data.quantity == ""  ) {
    
    return;
  }
  var currency_rate = $('input[name="currency_rate"]').val();
  var to_currency = $('select[name="currency"]').val();
  var table_row = '';
  var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.invoice-items-table tbody .item').length + 1;
  lastAddedItemKey = item_key;
  $("body").append('<div class="dt-loader"></div>');
  pur_get_item_row_template('newitems[' + item_key + ']',data.item_name, data.description, data.quantity, data.unit_name, data.unit_price, data.taxname, data.item_code, data.unit_id, data.tax_rate, data.discount, itemid, currency_rate, to_currency).done(function(output){
    table_row += output;

    $('.invoice-item table.invoice-items-table.items tbody').append(table_row);

    setTimeout(function () {
      pur_calculate_total();
    }, 15);

    $('.refresh_tax2 .select2').select2('destroy');
    $('.refresh_tax2 .select2').select2();

    pur_reorder_items('.invoice-item');
    pur_clear_item_preview_values('.invoice-item');
    $('body').find('#items-warning').remove();
    $("body").find('.dt-loader').remove();
    $('#item_select').val('').change();

    return true;
  });
  return false;
}

function pur_get_item_preview_values() {
  "use strict";

  var response = {};
  response.item_name = $('.invoice-item .main textarea[name="item_name"]').val();
  response.description = $('.invoice-item .main textarea[name="description"]').val();
  response.quantity = $('.invoice-item .main input[name="quantity"]').val();
  response.unit_name = $('.invoice-item .main input[name="unit_name"]').val();
  response.unit_price = $('.invoice-item .main input[name="unit_price"]').val();
  response.taxname = $('.main select.taxes').val();
  response.item_code = $('.invoice-item .main input[name="item_code"]').val();
  response.unit_id = $('.invoice-item .main input[name="unit_id"]').val();
  response.tax_rate = $('.invoice-item .main input[name="tax_rate"]').val();
  response.discount = $('.invoice-item .main input[name="discount"]').val();


  return response;
}


function pur_clear_item_preview_values(parent) {
  "use strict";
    var taxSelectedArray = [];
    
    $('.main input').val('');
    $('.main textarea').val('');
    $('.main .select2').val(taxSelectedArray).change();
}
function pur_reorder_items(parent) {
  "use strict";

  var rows = $(parent + ' .table.has-calculations tbody tr.item');
  var i = 1;
  $.each(rows, function () {
    $(this).find('input.order').val(i);
    i++;
  });
}

function pur_delete_item(row, itemid,parent) {
  "use strict";
    $(row).parents('tr').remove();
    pur_calculate_total();

    if (itemid && $('input[name="isedit"]').length > 0) {
      $(parent+' #removed-items').append(hidden_input('removed_items[]', itemid));
    }
}

function pur_get_item_row_template(name, item_name, description, quantity, unit_name, unit_price, taxname,  item_code, unit_id, tax_rate, discount, item_key, currency_rate, to_currency)  {
  "use strict";

  jQuery.ajaxSetup({
    async: false
  });

  var d = $.post( "<?php echo get_uri('purchase/get_purchase_invoice_row_template'); ?>", {
    name: name,
    item_name : item_name,
    item_description : description,
    quantity : quantity,
    unit_name : unit_name,
    unit_price : unit_price,
    taxname : taxname,
    item_code : item_code,
    unit_id : unit_id,
    tax_rate : tax_rate,
    discount : discount,
    item_key : item_key,
    currency_rate: currency_rate,
    to_currency: to_currency
  });
  jQuery.ajaxSetup({
    async: true
  });
  return d;
}
</script>