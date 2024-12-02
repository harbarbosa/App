<?php

namespace Purchase\Config;

use CodeIgniter\Events\Events;

Events::on('pre_system', function () {
    helper("purchase_general");
    helper("purchase_convert_field");
    helper("purchase_datatables");
});