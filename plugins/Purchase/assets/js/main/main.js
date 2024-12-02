$(".select2").select2();

// Set datatables error throw console log
$.fn.dataTable.ext.errMode = 'throw';
$.fn.dataTableExt.oStdClasses.sWrapper = 'dataTables_wrapper form-inline dt-bootstrap table-loading pt15 pl15 pr15';

// Predefined global variables
var original_top_search_val,
	table_leads,
	table_activity_log,
	table_estimates,
	table_invoices,
	table_tasks,

	side_bar = $('#sidebar-menu'),
	content_wrapper = $('#wrapper'),
	setup_menu = $('#setup-menu-wrapper'),
	menu_href_selector,
	calendar_selector = $('#calendar'),
	notifications_wrapper = $('#header').find('li.notifications-wrapper'),
	doc_initial_title = document.title,
	newsfeed_posts_page = 0,
	track_load_post_likes = 0,
	track_load_comment_likes = 0,
	post_likes_total_pages = 0,
	comment_likes_total_pages = 0,
	select_picker_validated_event = false,
	postid = 0,
	lastAddedItemKey = null,
	setup_menu_item = $('#setup-menu-item');

// General function for all datatables serverside
function initDataTable(selector, url, notsearchable, notsortable, fnserverparams, defaultorder) {
	"use strict";
	var table = typeof (selector) == 'string' ? $("body").find('table' + selector) : selector;

	if (table.length === 0) {
		return false;
	}

	fnserverparams = (fnserverparams == 'undefined' || typeof (fnserverparams) == 'undefined') ? [] : fnserverparams;

	// If not order is passed order by the first column
	if (typeof (defaultorder) == 'undefined') {
		defaultorder = [
			[0, 'asc']
		];
	} else {
		if (defaultorder.length === 1) {
			defaultorder = [defaultorder];
		}
	}

	var user_table_default_order = table.attr('data-default-order');

	if (!empty(user_table_default_order)) {
		var tmp_new_default_order = JSON.parse(user_table_default_order);
		var new_defaultorder = [];
		for (var i in tmp_new_default_order) {
			// If the order index do not exists will throw errors
			if (table.find('thead th:eq(' + tmp_new_default_order[i][0] + ')').length > 0) {
				new_defaultorder.push(tmp_new_default_order[i]);
			}
		}
		if (new_defaultorder.length > 0) {
			defaultorder = new_defaultorder;
		}
	}

	var length_options = [10, 25, 50, 100];
	var length_options_names = [10, 25, 50, 100];

	//set default display length
	var displayLength = AppHelper.settings.displayLength * 1;

	if (isNaN(displayLength) || !displayLength) {
		displayLength = 10;
	}

	if ($.inArray(displayLength, length_options) == -1) {
		length_options.push(displayLength);
		length_options_names.push(displayLength);
	}

	length_options.sort(function (a, b) {
		return a - b;
	});
	length_options_names.sort(function (a, b) {
		return a - b;
	});

	length_options.push(-1);
	length_options_names.push(AppLanugage.all);

	var dtSettings = {
		// "language": app.lang.datatables,
		"processing": true,
		"retrieve": true,
		"serverSide": true,
		'paginate': true,
		'searchDelay': 750,
		"bDeferRender": true,
		"autoWidth": false,
		dom: "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'col-md-8'p>><'row'<'#colvis'><'.dt-page-jump'>>",
		"pageLength": displayLength,
		"lengthMenu": [length_options, length_options_names],
		"columnDefs": [{
			"searchable": false,
			"targets": notsearchable,
		}, {
			"sortable": false,
			"targets": notsortable
		}],
		"fnDrawCallback": function (oSettings) {
			_table_jump_to_page(this, oSettings);
			if (oSettings.aoData.length === 0) {
				$(oSettings.nTableWrapper).addClass('app_dt_empty');
			} else {
				$(oSettings.nTableWrapper).removeClass('app_dt_empty');
			}
		},
		"fnCreatedRow": function (nRow, aData, iDataIndex) {
			// If tooltips found
			$(nRow).attr('data-title', aData.Data_Title);
			$(nRow).attr('data-toggle', aData.Data_Toggle);
		},
		"initComplete": function (settings, json) {
			var t = this;
			var $btnReload = $('.btn-dt-reload');
			$btnReload.attr('data-toggle', 'tooltip');

			$btnReload.attr('title', 'Reload');

			var $btnColVis = $('.dt-column-visibility');
			$btnColVis.attr('data-toggle', 'tooltip');
	
			$btnColVis.attr('title', 'Visibility');

			t.wrap('<div class="table-responsive"></div>');

			var dtEmpty = t.find('.dataTables_empty');
			if (dtEmpty.length) {
				dtEmpty.attr('colspan', t.find('thead th').length);
			}

			// Hide mass selection because causing issue on small devices
			if (is_mobile() && $(window).width() < 400 && t.find('tbody td:first-child input[type="checkbox"]').length > 0) {
				t.DataTable().column(0).visible(false, false).columns.adjust();
				$("a[data-target*='bulk_actions']").addClass('hide');
			}

			t.parents('.table-loading').removeClass('table-loading');
			t.removeClass('dt-table-loading');
			var th_last_child = t.find('thead th:last-child');
			var th_first_child = t.find('thead th:first-child');

			if (th_first_child.find('input[type="checkbox"]').length > 0) {
				th_first_child.addClass('not-export');
			}
			mainWrapperHeightFix();
		},
		"order": defaultorder,
		"ajax": {
			"url": url,
			"type": "POST",
			"data": function (d) {
				if (typeof (csrfData) !== 'undefined') {
					d[csrfData['token_name']] = csrfData['hash'];
				}
				for (var key in fnserverparams) {
					d[key] = $(fnserverparams[key]).val();
				}
				if (table.attr('data-last-order-identifier')) {
					d['last_order_identifier'] = table.attr('data-last-order-identifier');
				}
			}
		},
		"language": {
                lengthMenu: "_MENU_",
                zeroRecords: AppLanugage.noRecordFound,
                info: "_START_-_END_ / _TOTAL_",
                sInfo: "_START_-_END_ / _TOTAL_",
                infoFiltered: "(_MAX_)",
                search: "",
                searchPlaceholder: AppLanugage.search,
                sInfoEmpty: "0-0 / 0",
                sInfoFiltered: "(_MAX_)",
                sInfoPostFix: "",
                sInfoThousands: ",",
                sProcessing: "<div class='table-loader'><span class='loading'></span></div>",
                "oPaginate": {
                    "sPrevious": "<i data-feather='chevrons-left' class='icon-16'></i>",
                    "sNext": "<i data-feather='chevrons-right' class='icon-16'></i>"
                }

            },
		buttons: get_datatable_buttons(table),
	};

	table = table.dataTable(dtSettings);
	var tableApi = table.DataTable();

	var hiddenHeadings = table.find('th.not_visible');
	var hiddenIndexes = [];

	$.each(hiddenHeadings, function () {
		hiddenIndexes.push(this.cellIndex);
	});

	setTimeout(function () {
		for (var i in hiddenIndexes) {
			tableApi.columns(hiddenIndexes[i]).visible(false, false).columns.adjust();
		}
	}, 10);

	if (table.hasClass('customizable-table')) {
		var tableToggleAbleHeadings = table.find('th.toggleable');
		var invisible = $('#hidden-columns-' + table.attr('id'));
		try {
			invisible = JSON.parse(invisible.text());
		} catch (err) {
			invisible = [];
		}

		$.each(tableToggleAbleHeadings, function () {
			var cID = $(this).attr('id');
			if ($.inArray(cID, invisible) > -1) {
				tableApi.column('#' + cID).visible(false);
			}
		});

		// For for not blurring out when clicked on the link
		// Causing issues hidden column still to be shown as not hidden because the link is focused

	}

	// Fix for hidden tables colspan not correct if the table is empty
	if (table.is(':hidden')) {
		table.find('.dataTables_empty').attr('colspan', table.find('thead th').length);
	}

	table.on('preXhr.dt', function (e, settings, data) {
		if (settings.jqXHR) settings.jqXHR.abort();
	});

	return tableApi;
}

// Check if field is empty
function empty(data) {
	"use strict";
	if (typeof(data) == 'number' || typeof(data) == 'boolean') {
		return false;
	}
	if (typeof(data) == 'undefined' || data === null) {
		return true;
	}
	if (typeof(data.length) != 'undefined') {
		return data.length === 0;
	}
	var count = 0;
	for (var i in data) {
		if (data.hasOwnProperty(i)) {
			count++;
		}
	}
	return count === 0;
}


// Returns datatbles export button array based on settings
// Admin area only
function get_datatable_buttons(table) {
	"use strict";
	// pdfmake arabic fonts support


	var formatExport = {
		body: function(data, row, column, node) {

			// Fix for notes inline datatables
			// Causing issues because of the hidden textarea for edit and the content is duplicating
			// This logic may be extended in future for other similar fixes
			var newTmpRow = $('<div></div>', data);
			newTmpRow.append(data);

			if (newTmpRow.find('[data-note-edit-textarea]').length > 0) {
				newTmpRow.find('[data-note-edit-textarea]').remove();
				data = newTmpRow.html().trim();
			}
			// Convert e.q. two months ago to actual date
			var exportTextHasActionDate = newTmpRow.find('.text-has-action.is-date');

			if(exportTextHasActionDate.length) {
			   data = exportTextHasActionDate.attr('data-title');
			}

			if (newTmpRow.find('.row-options').length > 0) {
				newTmpRow.find('.row-options').remove();
				data = newTmpRow.html().trim();
			}

			if (newTmpRow.find('.table-export-exclude').length > 0) {
				newTmpRow.find('.table-export-exclude').remove();
				data = newTmpRow.html().trim();
			}



			// Datatables use the same implementation to strip the html.
			var div = document.createElement("div");
			div.innerHTML = data;
			var text = div.textContent || div.innerText || "";

			return text.trim();
		}
	};
	var table_buttons_options = [];

	if (typeof(table_export_button_is_hidden) != 'function' || !table_export_button_is_hidden()) {
		table_buttons_options.push({
			extend: 'collection',
			text: 'Export',
			className: 'btn btn-default-dt-options',
			buttons: [{
				extend: 'excel',
				text:AppLanugage.excel,
				footer: true,
				exportOptions: {
					columns: [':not(.not-export)'],
					rows: function(index) {
						return _dt_maybe_export_only_selected_rows(index, table);
					},
					format: formatExport,
				},
			}, {
			
				extend: 'pdfHtml5',
				text: AppLanugage.print,
				footer: true,
				exportOptions: {
					columns: [':not(.not-export)'],
					rows: function(index) {
						return _dt_maybe_export_only_selected_rows(index, table);
					},
					format: formatExport,
				},
				orientation: 'landscape',
				customize: function(doc) {
					// Fix for column widths
					var table_api = $(table).DataTable();
					var columns = table_api.columns().visible();
					var columns_total = columns.length;
					var total_visible_columns = 0;

					for (i = 0; i < columns_total; i++) {
						// Is only visible column
						if (columns[i] == true) {
							total_visible_columns++;
						}
					}

					setTimeout(function() {
						if (total_visible_columns <= 5) {
							var pdf_widths = [];
							for (i = 0; i < total_visible_columns; i++) {
								pdf_widths.push((735 / total_visible_columns));
							}

							doc.content[1].table.widths = pdf_widths;
						}
					}, 10);

					if (app.user_language.toLowerCase() == 'persian' || app.user_language.toLowerCase() == 'arabic') {
						doc.defaultStyle.font = Object.keys(pdfMake.fonts)[0];
					}

					doc.styles.tableHeader.alignment = 'left';
					doc.defaultStyle.fontSize = 10;

					doc.styles.tableHeader.fontSize = 10;
					doc.styles.tableHeader.margin = [3, 3, 3, 3];

					doc.styles.tableFooter.fontSize = 10;
					doc.styles.tableFooter.margin = [3, 0, 0, 0];

					doc.pageMargins = [2, 20, 2, 20];
				}
			}, {
				extend: 'print',
				text: AppLanugage.print,
				footer: true,
				exportOptions: {
					columns: [':not(.not-export)'],
					rows: function(index) {
						return _dt_maybe_export_only_selected_rows(index, table);
					},
					format: formatExport,
				}
			}],
		});
	}
	var tableButtons = $("body").find('.table-btn');

	$.each(tableButtons, function() {
		var b = $(this);
		if (b.length && b.attr('data-table')) {
			if ($(table).is(b.attr('data-table'))) {
				table_buttons_options.push({
					text: b.text().trim(),
					className: 'btn btn-default-dt-options',
					action: function(e, dt, node, config) {
						b.click();
					}
				});
			}
		}
	});

	if (!$(table).hasClass('dt-inline')) {
		table_buttons_options.push({
			text: '<span data-feather="refresh-cw" class="icon-16"></span>',
			className: 'btn btn-default-dt-options btn-dt-reload',
			action: function(e, dt, node, config) {
				dt.ajax.reload();
			}
		});
	}
	



	return table_buttons_options;
}

// Datatables custom job to page function
function _table_jump_to_page(table, oSettings) {
	"use strict";
	var paginationData = table.DataTable().page.info();
	var previousDtPageJump = $("body").find('#dt-page-jump-' + oSettings.sTableId);

	if (previousDtPageJump.length) {
		previousDtPageJump.remove();
	}

	if (paginationData.pages > 1) {

		var jumpToPageSelect = $("<select></select>", {
			"data-id": oSettings.sTableId,
			"class": "dt-page-jump-select form-control",
			'id': 'dt-page-jump-' + oSettings.sTableId
		});

		var paginationHtml = '';

		for (var i = 1; i <= paginationData.pages; i++) {
			var selectedCurrentPage = ((paginationData.page + 1) === i) ? 'selected' : '';
			paginationHtml += "<option value='" + i + "'" + selectedCurrentPage + ">" + i + "</option>";
		}

		if (paginationHtml != '') {
			jumpToPageSelect.append(paginationHtml);
		}

	}
}

function is_mobile() {
	"use strict";
	if($(window).width() < 800){
		return true;
	}
	return false;

}

// Fix for height on the wrapper
function mainWrapperHeightFix() {
	"use strict";
	// Get and set current height
	var headerH = 63;
	var navigationH = side_bar.height();
	var contentH = $("#wrapper").find('.content').height();
	setup_menu.css('min-height', ($(document).outerHeight(true) - (headerH * 2)) + 'px');

	content_wrapper.css('min-height', $(document).outerHeight(true) - headerH + 'px');
	// Set new height when content height is less then navigation
	if (contentH < navigationH) {
		content_wrapper.css("min-height", navigationH + 'px');
	}

	// Set new height when content height is less then navigation and navigation is less then window
	if (contentH < navigationH && navigationH < $(window).height()) {
		content_wrapper.css("min-height", $(window).height() - headerH + 'px');
	}
	// Set new height when content is higher then navigation but less then window
	if (contentH > navigationH && contentH < $(window).height()) {
		content_wrapper.css("min-height", $(window).height() - headerH + 'px');
	}
	// Fix for RTL main admin menu height
	var isRTL = false;
	if (is_mobile() && isRTL == 'true') {
		side_bar.css('min-height', $(document).outerHeight(true) - headerH + 'px');
	}
}


	// On mass_select all select all the availble rows in the tables.
	$("body").on('change', '#mass_select_all', function () {
		var to, rows, checked;
		to = $(this).data('to-table');

		rows = $('.table-' + to).find('tbody tr');
		checked = $(this).prop('checked');
		$.each(rows, function () {
			$($(this).find('td').eq(0)).find('input').prop('checked', checked);
		});
	});

	function _dt_maybe_export_only_selected_rows(index, table) {
		"use strict";
		table = $(table);
		index = index.toString();
		var bulkActionsCheckbox = table.find('thead th input[type="checkbox"]').eq(0);
		if (bulkActionsCheckbox && bulkActionsCheckbox.length > 0) {
			var rows = table.find('tbody tr');
			var anyChecked = false;
			$.each(rows, function() {
				if ($(this).find('td:first input[type="checkbox"]:checked').length) {
					anyChecked = true;
				}
			});

			if (anyChecked) {
				if (table.find('tbody tr:eq(' + (index) + ') td:first input[type="checkbox"]:checked').length > 0) {
					return index;
				} else {
					return null;
				}
			} else {
				return index;
			}
		}
		return index;
	}

	function init_ajax_search(type, selector, server_data, url) {
		"use strict";
		var ajaxSelector = $('body').find(selector);

		if (ajaxSelector.length) {
			var options = {
				ajax: {
					url: (typeof (url) == 'undefined' ? admin_url + 'misc/get_relation_data' : url),
					data: function () {
						var data = {};
						data.type = type;
						data.rel_id = '';
						data.q = '{{{q}}}';
						if (typeof (server_data) != 'undefined') {
							jQuery.extend(data, server_data);
						}
						return data;
					}
				},
				locale: {
					emptyTitle: app.lang.search_ajax_empty,
					statusInitialized: app.lang.search_ajax_initialized,
					statusSearching: app.lang.search_ajax_searching,
					statusNoResults: app.lang.not_results_found,
					searchPlaceholder: app.lang.search_ajax_placeholder,
					currentlySelected: app.lang.currently_selected
				},
				requestDelay: 500,
				cache: false,
				preprocessData: function (processData) {
					var bs_data = [];
					var len = processData.length;
					for (var i = 0; i < len; i++) {
						var tmp_data = {
							'value': processData[i].id,
							'text': processData[i].name,
						};
						if (processData[i].subtext) {
							tmp_data.data = {
								subtext: processData[i].subtext
							};
						}
						bs_data.push(tmp_data);
					}
					return bs_data;
				},
				preserveSelectedPosition: 'after',
				preserveSelected: true
			};
			if (ajaxSelector.data('empty-title')) {
				options.locale.emptyTitle = ajaxSelector.data('empty-title');
			}

			ajaxSelector.select2({data: options});
		}
	}


(function($) {
	"use strict";
    var configuredjQueryValidation = false;

    $.fn.appFormValidator = function(options) {
        var self = this;

        var defaultMessages = {
            email: {
                remote: $.fn.appFormValidator.internal_options.localization.email_exists,
            },
        }

        var defaults = {
            rules: [],
            messages: [],
            ignore: [],
            onSubmit: false,
            submitHandler: function(form) {
                var $form = $(form);

                if ($form.hasClass('disable-on-submit')) {
                    $form.find('[type="submit"]').prop('disabled', true);
                }

                var loadingBtn = $form.find('[data-loading-text]');

                if (loadingBtn.length > 0) {
                    loadingBtn.button('loading');
                }

                if (settings.onSubmit) {
                    settings.onSubmit(form);
                } else {
                    return true;
                }
            }
        };

        var settings = $.extend({}, defaults, options);

        // Just make sure that this is always configured
        if (typeof(settings.messages.email) == 'undefined') {
            settings.messages.email = defaultMessages.email;
        }


        self.configureJqueryValidationDefaults = function() {

            // Set this only 1 time before the first validation happens
            if (!configuredjQueryValidation) {
                configuredjQueryValidation = true;
            } else {
                return true;
            }

            // Jquery validate set default options
            $.validator.setDefaults({
                highlight: $.fn.appFormValidator.internal_options.error_highlight,
                unhighlight: $.fn.appFormValidator.internal_options.error_unhighlight,
                errorElement: $.fn.appFormValidator.internal_options.error_element,
                errorClass: $.fn.appFormValidator.internal_options.error_class,
                errorPlacement: $.fn.appFormValidator.internal_options.error_placement,
            });

            self.addMethodFileSize();
            self.addMethodExtension();
        }

        self.addMethodFileSize = function() {
            // New validation method filesize
            $.validator.addMethod('filesize', function(value, element, param) {
                return this.optional(element) || (element.files[0].size <= param);
            }, $.fn.appFormValidator.internal_options.localization.file_exceeds_max_filesize);
        }

        self.addMethodExtension = function() {
            // New validation method extension based on app extensions
            $.validator.addMethod("extension", function(value, element, param) {
                param = typeof param === "string" ? param.replace(/,/g, "|") : "png|jpe?g|gif";
                return this.optional(element) || value.match(new RegExp("\\.(" + param + ")$", "i"));
            }, $.fn.appFormValidator.internal_options.localization.validation_extension_not_allowed);
        }

        self.validateCustomFields = function($form) {

            $.each($form.find($.fn.appFormValidator.internal_options.required_custom_fields_selector), function() {
                // for custom fields in tr.main, do not validate those
                if (!$(this).parents('tr.main').length && !$(this).hasClass('do-not-validate')) {

                    $(this).rules("add", { required: true });
                    if ($.fn.appFormValidator.internal_options.on_required_add_symbol) {
                        var label = $(this).parents('.' + $.fn.appFormValidator.internal_options.field_wrapper_class).find('[for="' + $(this).attr('name') + '"]');
                        if (label.length > 0 && label.find('.req').length === 0) {
                            label.prepend('<small class="req text-danger">* </small>');
                        }
                    }
                }
            });
        }

        self.addRequiredFieldSymbol = function($form) {
            if ($.fn.appFormValidator.internal_options.on_required_add_symbol) {
                $.each(settings.rules, function(name, rule) {
                    if ((rule == 'required' && !jQuery.isPlainObject(rule)) ||
                        (jQuery.isPlainObject(rule) && rule.hasOwnProperty('required'))) {
                        var label = $form.find('[for="' + name + '"]');
                        if (label.length > 0 && label.find('.req').length === 0) {
                            label.prepend(' <small class="req text-danger">* </small>');
                        }
                    }
                });
            }
        }

        self.configureJqueryValidationDefaults();

        return self.each(function() {

            var $form = $(this);

            // If already validated, destroy to free up memory
            if ($form.data('validator')) {
                $form.data('validator').destroy();
            }

            $form.validate(settings);
            self.validateCustomFields($form);
            self.addRequiredFieldSymbol($form);

            $(document).trigger('app.form-validate', $form);
        });
    }
})(jQuery);

$.fn.appFormValidator.internal_options = {
    localization: {
        email_exists: typeof(app) != 'undefined' ? app.lang.email_exists : 'Please fix this field',
        file_exceeds_max_filesize: typeof(app) != 'undefined' ? app.lang.file_exceeds_max_filesize : 'File Exceeds Max Filesize',
        validation_extension_not_allowed: typeof(app) != 'undefined' ? $.validator.format(app.lang.validation_extension_not_allowed) : $.validator.format('Extension not allowed'),
    },
    on_required_add_symbol: true,
    error_class: 'text-danger',
    error_element: 'p',
    required_custom_fields_selector: '[data-custom-field-required]',
    field_wrapper_class: 'form-group',
    field_wrapper_error_class: 'has-error',
    tab_panel_wrapper: 'tab-pane',
    validated_tab_class: 'tab-validated',
    error_placement: function(error, element) {
        if (element.parent('.input-group').length || element.parents('.chk').length) {
            if (!element.parents('.chk').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element.parents('.chk'));
            }
        } else if (element.is('select') && (element.hasClass('selectpicker') || element.hasClass('ajax-search'))) {
            error.insertAfter(element.parents('.' + $.fn.appFormValidator.internal_options.field_wrapper_class + ' *').last());
        } else {
            error.insertAfter(element);
        }
    },
    error_highlight: function(element) {
        var $child_tab_in_form = $(element).parents('.' + $.fn.appFormValidator.internal_options.tab_panel_wrapper);
        if ($child_tab_in_form.length && !$child_tab_in_form.is(':visible')) {
            $('a[href="#' + $child_tab_in_form.attr('id') + '"]')
                .css('border-bottom', '1px solid red').css('color', 'red')
                .addClass($.fn.appFormValidator.internal_options.validated_tab_class);
        }

        if ($(element).is('select')) {
            // Having some issues with select, it's not aways highlighting good or too fast doing unhighlight
            delay(function() {
                $(element).closest('.' + $.fn.appFormValidator.internal_options.field_wrapper_class).addClass($.fn.appFormValidator.internal_options.field_wrapper_error_class);
            }, 400);
        } else {
            $(element).closest('.' + $.fn.appFormValidator.internal_options.field_wrapper_class).addClass($.fn.appFormValidator.internal_options.field_wrapper_error_class);
        }
    },
    error_unhighlight: function(element) {
        element = $(element);
        var $child_tab_in_form = element.parents('.' + $.fn.appFormValidator.internal_options.tab_panel_wrapper);
        if ($child_tab_in_form.length) {
            $('a[href="#' + $child_tab_in_form.attr('id') + '"]').removeAttr('style').removeClass($.fn.appFormValidator.internal_options.validated_tab_class);
        }
        element.closest('.' + $.fn.appFormValidator.internal_options.field_wrapper_class).removeClass($.fn.appFormValidator.internal_options.field_wrapper_error_class);
    },
}


function requestGet(uri, params) {
	"use strict";
    params = typeof (params) == 'undefined' ? {} : params;
    var options = {
        type: 'GET',
        url: uri
    };
    return $.ajax($.extend({}, options, params));
}

// General helper function for $.get ajax requests with dataType JSON
function requestGetJSON(uri, params) {
	"use strict";
    params = typeof (params) == 'undefined' ? {} : params;
    params.dataType = 'json';
    return requestGet(uri, params);
}

function appValidateForm(form, form_rules, submithandler, overwriteMessages) {
	"use strict";
    $(form).appFormValidator({ rules: form_rules, onSubmit: submithandler, messages: overwriteMessages });
}

function slugify(string) {
	"use strict";
	return string
	.toString()
	.trim()
	.toLowerCase()
	.replace(/\s+/g, "-")
	.replace(/[^\w\-]+/g, "")
	.replace(/\-\-+/g, "-")
	.replace(/^-+/, "")
	.replace(/-+$/, "");
}

function hidden_input(name, val) {
	"use strict";
	return '<input type="hidden" name="' + name + '" value="' + val + '">';
}