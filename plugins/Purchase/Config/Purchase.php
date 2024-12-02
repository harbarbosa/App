<?php

namespace Purchase\Config;

use CodeIgniter\Config\BaseConfig;
use Purchase\Models\Purchase_model;

class Purchase extends BaseConfig {

    public $app_settings_array = array(
        "purchase_file_path" => PLUGIN_URL_PATH . "Purchase/files/purchase_files/"
    );

    public function __construct() {
        
    }

}
