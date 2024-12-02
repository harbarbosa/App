<script type="text/javascript">
	$(document).ready(function () {
		"use strict";
		$("#approval_setting-table").appTable({
			source: '<?php echo get_uri("purchase/list_approval_setting_data") ?>',
			order: [[0, 'desc']],
			filterDropdown: [
			],
			columns: [
			{title: "<?php echo app_lang('_order') ?> ", "class": "w20p"},
			{title: "<?php echo app_lang('approval_name') ?>"},
			{title: "<?php echo app_lang('related_type') ?>"},
			{title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
			],
			printColumns: [0, 1, 2, 3, 4],
			xlsColumns: [0, 1, 2, 3, 4]
		});
	});

</script>