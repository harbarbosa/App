<?php

namespace App\Libraries;

//limitation: Can be used for only one kind of folder, for a controller. 
trait App_folders
{


    abstract private function _folder_items();

    abstract private function _folder_config();

    abstract private function _shareable_options();

    //access pattern 
    //9 = Full access (read, upload, modify, delete)
    //6 = Upload + organize (read, upload, move, rename)
    //3 = Upload only (read, upload)
    //1 = Read only (read)
    //saved in db with following:  
    //
    //9-member:1,6-all_team_members,3-member:3,1-member:4,

    private $Folders_model;
    private $controller_slag;
    private $folder_item_type = "file";
    private $show_left_menu = true;
    private $add_files_modal_url = "";
    private $add_files_modal_post_data = array();
    private $file_preview_url = "";
    private $show_file_preview_sidebar = false;
    private $permissions_value_memory = null;
    private $root_folders_default_permissions = "";
    private $global_files_path = "";

    private function init()
    {
        if (!$this->Folders_model) {
            $this->Folders_model = model('App\Models\Folders_model');
        }

        $configs = array(
            "folder_item_type",
            "show_left_menu",
            "controller_slag",
            "add_files_modal_url",
            "add_files_modal_post_data",
            "file_preview_url",
            "show_file_preview_sidebar",
            "root_folders_default_permissions",
            "global_files_path"
        );

        $this->_set_configs($configs);

        if (!$this->controller_slag) {
            die("controller_slag config is missing");
        }
    }

    private function _set_configs($configs)
    {
        $folder_config = $this->_folder_config();
        foreach ($configs as $config_name) {
            if (isset($folder_config->$config_name)) {
                $this->$config_name = $folder_config->$config_name;
            }
        }
    }

    private function init_permissions_value_memory()
    {

        $team_members_list = array();
        $team_list = array();
        $clients_list = array();
        $client_groups_list = array();

        if (is_null($this->permissions_value_memory)) {
            foreach ($this->Users_model->get_team_members_id_and_name(array("exclude_admins" => true))->getResult() as $team_member) {
                $team_members_list[$team_member->id] = $team_member->user_name;
            }

            foreach ($this->Team_model->get_id_and_title()->getResult() as $team) {
                $team_list[$team->id] = $team->title;
            }

            foreach ($this->Clients_model->get_clients_id_and_name(array("limit" => 2000))->getResult() as $client) {
                $clients_list[$client->id] = $client->name;
            }

            foreach ($this->Client_groups_model->get_id_and_title()->getResult() as $group) {
                $client_groups_list[$group->id] = $group->title;
            }

            $this->permissions_value_memory = array("member" => $team_members_list, "team" => $team_list, "client" => $clients_list, "client_group" => $client_groups_list);
        }
    }

    function _get_icon_type($item = "")
    {
        $icons = array(
            "all_team_members" => "users",
            "project_members" => "users",
            "authorized_team_members" => "users",
            "team" => "users",
            "member" => "user",
            "all_clients" => "briefcase",
            "client" => "briefcase",
            "client_group" => "layout",
        );
        if ($item) {
            return $icons[$item];
        } else {
            return $icons;
        }
    }

    function explore($folder_id = "", $tab_view = false, $view_from = "", $client_id = 0)
    {
        $this->check_module_availability("module_file_manager");
        $data = $this->_get_folder_window_data($folder_id, 0, $client_id);

        $data["view_type"] = $tab_view;
        $data["view_from"] = $view_from;

        if ($tab_view) {
            return $this->template->view("app_folders/index", $data);
        } else {
            return $this->template->rander("app_folders/index", $data);
        }
    }

    function folder_modal_form()
    {
        $id = $this->request->getPost('id');
        $parent_id = $this->request->getPost('parent_id');
        $context = $this->request->getPost('context');
        $context_id = $this->request->getPost('context_id');

        $this->init();
        $model_info = $this->Folders_model->get_one($id);
        if (!$model_info->parent_id) {
            $model_info->parent_id = $parent_id;
        }

        if (!$model_info->context) {
            $model_info->context = $context;
        }

        if (!$model_info->context_id) {
            $model_info->context_id = $context_id;
        }

        if (!$this->can_manage_folders($parent_id, $id, $model_info->context, $model_info->context_id)) {
            app_redirect("forbidden");
        }

        $view_data["model_info"] = $model_info;
        $view_data["controller_slag"] = $this->controller_slag;

        return $this->template->view('app_folders/folder_modal_form', $view_data);
    }

    function get_folder_info()
    {
        $this->validate_submitted_data(array(
            "id" => "required"
        ));
        $id = $this->request->getPost('id');
        $client_id = $this->request->getPost('client_id');

        $folder_info_content = $this->_get_folder_info($id, $client_id);
        if ($folder_info_content) {
            echo json_encode(array("success" => true, "content" => $folder_info_content));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    private function _get_folder_info($id, $client_id = 0)
    {
        $this->init();
        $this->init_permissions_value_memory();

        $data = $this->_get_folder_window_data("", $id, $client_id);

        if (!$data) {
            return false;
        }

        $view_data = $data;

        $view_data["controller_slag"] = $this->controller_slag;

        $permissions = $this->_extract_permissions_data($view_data["folder_info"]->permissions);
        $view_data["full_access_list"] = $permissions["full_access_list"];
        $view_data["upload_and_organize_list"] = $permissions["upload_and_organize_list"];
        $view_data["upload_only_list"] = $permissions["upload_only_list"];
        $view_data["read_only_list"] = $permissions["read_only_list"];

        $parent_folder_permissions = $this->_extract_permissions_data($view_data["parent_folder_permissions"]);
        $view_data["parent_full_access_list"] = $parent_folder_permissions["full_access_list"];
        $view_data["parent_upload_and_organize_list"] = $parent_folder_permissions["upload_and_organize_list"];
        $view_data["parent_upload_only_list"] = $parent_folder_permissions["upload_only_list"];
        $view_data["parent_read_only_list"] = $parent_folder_permissions["read_only_list"];


        $view_data["folder_details"] = true;

        return $this->template->view('app_folders/folder_info', $view_data, true);
    }

    function get_file_info()
    {
        $id = $this->request->getPost('id');
        $client_id = $this->request->getPost('client_id');

        $this->validate_submitted_data(array(
            "id" => "required"
        ));

        $this->init();
        $General_files_model = model('App\Models\General_files_model');
        $file_info = $General_files_model->get_details(array("id" => $id))->getRow();
        $view_data["controller_slag"] = $this->controller_slag;
        $view_data["global_files_path"] = $this->global_files_path;
        $view_data["client_files_path"] = get_general_file_path("client", $client_id);

        if ($file_info->context == "global_files") {
            $file_path = $this->global_files_path;
            $view_data["global_files_path"] = $file_path;
            $view_data["client_files_path"] = "";
        } else {
            $file_path = get_general_file_path("client", $client_id);
            $view_data["global_files_path"] = "";
            $view_data["client_files_path"] = $file_path;
        }

        //For file preview
        $view_data['can_comment_on_files'] = false;

        $file_url = get_source_url_of_file(make_array_of_file($file_info), $file_path);

        $view_data["file_url"] = $file_url;
        $view_data["is_image_file"] = is_image_file($file_info->file_name);
        $view_data["is_google_preview_available"] = is_google_preview_available($file_info->file_name);
        $view_data["is_viewable_video_file"] = is_viewable_video_file($file_info->file_name);
        $view_data["is_google_drive_file"] = ($file_info->file_id && $file_info->service_type == "google") ? true : false;
        $view_data["is_iframe_preview_available"] = is_iframe_preview_available($file_info->file_name);

        $view_data["file_info"] = $file_info;
        $view_data["client_id"] = $client_id;

        if ($view_data["file_info"]) {
            echo json_encode(array("success" => true, "content" => $this->template->view('app_folders/file_info', $view_data, true)));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function save_folder()
    {
        $this->init();

        $id = $this->request->getPost('id');
        $this->validate_submitted_data(array(
            "title" => "required"
        ));

        $parent_id = $this->request->getPost('parent_id');
        $parent_folder_info = $this->Folders_model->get_one($parent_id);
        $context = $parent_folder_info->context ? $parent_folder_info->context : $this->request->getPost('context');
        $context_id = $parent_folder_info->context_id ? $parent_folder_info->context_id : $this->request->getPost('context_id');

        if (!$this->can_manage_folders($parent_id, $id, $context, $context_id)) {
            app_redirect("forbidden");
        }

        $now = get_current_utc_time();
        $created_by = $this->login_user->id;

        $level = "";

        if ($parent_folder_info && $parent_folder_info->id) {
            if ($parent_folder_info->level) {
                $level = $parent_folder_info->level . $parent_folder_info->id . ",";
            } else {
                $level = "," . $parent_folder_info->id . ",";
            }
        }

        $permissions = $this->root_folders_default_permissions ? $this->root_folders_default_permissions : "";

        $folder_data = array(
            "title" => $this->request->getPost('title'),
            "parent_id" => $parent_id,
            "level" => $level,
            "permissions" => $permissions
        );

        if (!$id) {
            $folder_id = substr(md5($context), -7) . "-" . substr(md5($context_id ? $context_id : "0"), -5) . "-" . substr(md5($created_by), -4) . "-" . substr(md5($parent_id ? $parent_id : "root"), -5) . "-" . make_random_string(11);
            $folder_data["folder_id"] = $folder_id;
            $folder_data["context"] = $context;
            $folder_data["context_id"] = $context_id;
            $folder_data["created_by"] = $this->login_user->id;
            $folder_data["created_at"] = $now;
        }

        $save_id = $this->Folders_model->ci_save($folder_data, $id);

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => "", 'parent_folder_id' => $parent_folder_info->folder_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function folder_permissions_modal_form()
    {

        $id = $this->request->getPost('id');

        $this->validate_submitted_data(array(
            "id" => "required"
        ));

        $this->init();
        $model_info = $this->Folders_model->get_one($id);

        $view_data["model_info"] = $model_info;
        $view_data["controller_slag"] = $this->controller_slag;

        $permissions_list = $this->_get_permission_options();

        $full_access_dropdown = array();
        $upload_and_organize_dropdown = array();
        $upload_only_dropdown = array();
        $read_only_dropdown = array();

        foreach ($permissions_list as $option) {

            $full_access_dropdown[] = array("type" => $option["type"], "id" => "9-" . $option["id"], "text" => $option["text"]);
            $upload_and_organize_dropdown[] = array("type" => $option["type"], "id" => "6-" . $option["id"], "text" => $option["text"]);
            $upload_only_dropdown[] = array("type" => $option["type"], "id" => "3-" . $option["id"], "text" => $option["text"]);
            $read_only_dropdown[] = array("type" => $option["type"], "id" => "1-" . $option["id"], "text" => $option["text"]);
        }

        $view_data["full_access_dropdown"] = json_encode($full_access_dropdown);
        $view_data["upload_and_organize_dropdown"] = json_encode($upload_and_organize_dropdown);
        $view_data["upload_only_dropdown"] = json_encode($upload_only_dropdown);
        $view_data["read_only_dropdown"] = json_encode($read_only_dropdown);

        $permissions = $this->_extract_permissions_data($model_info->permissions);
        $view_data["full_access_value"] = $permissions["full_access_value"];
        $view_data["upload_and_organize_value"] = $permissions["upload_and_organize_value"];
        $view_data["upload_only_value"] = $permissions["upload_only_value"];
        $view_data["read_only_value"] = $permissions["read_only_value"];

        $view_data["format_icons"] = json_encode($this->_get_icon_type());

        return $this->template->view('app_folders/folder_permissions_modal_form', $view_data);
    }

    private function _extract_permissions_data($permissions = "")
    {
        if (!$permissions) {
            $permissions = "";
        }

        $this->init_permissions_value_memory();

        $permissions_value_memory = $this->permissions_value_memory;

        $permissions_array = explode(",", $permissions);

        $full_access_value = "";
        $upload_and_organize_value = "";
        $upload_only_value = "";
        $read_only_value = "";

        $full_access_list = array();
        $upload_and_organize_list = array();
        $upload_only_list = array();
        $read_only_list = array();

        foreach ($permissions_array as $permission) {


            $access_type = get_first_letter($permission);

            $permission_parts = explode(":", substr($permission, 2));

            $permission_identifier = get_array_value($permission_parts, 0);
            $permission_value = get_array_value($permission_parts, 1);

            $item_info = array();

            if ($permission_identifier && !$permission_value) {
                $item_info["text"] = app_lang($permission_identifier);
                $item_info["icon"] = $this->_get_icon_type($permission_identifier);
            } else if ($permission_identifier && $permission_value) {

                $values = get_array_value($permissions_value_memory, $permission_identifier);
                $text = get_array_value($values, $permission_value);

                if (!$text) {
                    continue;
                }

                $item_info["text"] = $text;
                $item_info["icon"] = $this->_get_icon_type($permission_identifier);
            }

            $permission .= ",";

            if ($access_type == 9) {

                $full_access_value .= $permission;
                $full_access_list[] = $item_info;
            } else if ($access_type == 6) {
                $upload_and_organize_value .= $permission;
                $upload_and_organize_list[] = $item_info;
            } else if ($access_type == 3) {
                $upload_only_value .= $permission;
                $upload_only_list[] = $item_info;
            } else if ($access_type == 1) {
                $read_only_value .= $permission;
                $read_only_list[] = $item_info;
            }
        }


        // Remove values from $read_only_list which are in $full_access_list, $upload_and_organize_list, or $upload_only_list
        $all_lists = array_merge($full_access_list, $upload_and_organize_list, $upload_only_list);
        $read_only_list = array_filter($read_only_list, function ($item) use ($all_lists) {
            return !in_array($item, $all_lists, true);
        });

        // Remove values from $upload_only_list which are in $full_access_list or $upload_and_organize_list
        $full_access_and_upload_and_organize_list = array_merge($full_access_list, $upload_and_organize_list);
        $upload_only_list = array_filter($upload_only_list, function ($item) use ($full_access_and_upload_and_organize_list) {
            return !in_array($item, $full_access_and_upload_and_organize_list, true);
        });

        // Remove values from $upload_and_organize_list which are in $full_access_list
        $upload_and_organize_list = array_filter($upload_and_organize_list, function ($item) use ($full_access_list) {
            return !in_array($item, $full_access_list, true);
        });


        $result = array(
            "full_access_value" => $full_access_value,
            "upload_and_organize_value" => $upload_and_organize_value,
            "upload_only_value" => $upload_only_value,
            "read_only_value" => $read_only_value,
            "full_access_list" => $full_access_list,
            "upload_and_organize_list" => $upload_and_organize_list,
            "upload_only_list" => $upload_only_list,
            "read_only_list" => $read_only_list
        );

        return $result;
    }

    private function _get_permission_options()
    {
        $shareable_options = $this->_shareable_options();

        $dropdown = array();

        if (in_array("all_team_members", $shareable_options)) {
            $dropdown[] = array("type" => "team", "id" => "all_team_members", "text" => app_lang("all_team_members"));
        }

        if (in_array("all_clients", $shareable_options)) {
            $dropdown[] = array("type" => "client", "id" => "all_clients", "text" => app_lang("all_clients"));
        }

        if (in_array("team", $shareable_options)) {
            $teams = $this->Team_model->get_id_and_title()->getResult();
            foreach ($teams as $team) {
                $dropdown[] = array("type" => "team", "id" => "team:" . $team->id, "text" => $team->title);
            }
        }

        if (in_array("client_group", $shareable_options)) {
            $client_groups = $this->Client_groups_model->get_id_and_title()->getResult();
            foreach ($client_groups as $client_group) {
                $dropdown[] = array("type" => "client_group", "id" => "client_group:" . $client_group->id, "text" => $client_group->title);
            }
        }

        if (in_array("member", $shareable_options)) {
            $team_members = $this->Users_model->get_team_members_id_and_name(array("exclude_admins" => true))->getResult();
            foreach ($team_members as $team_member) {
                $dropdown[] = array("type" => "member", "id" => "member:" . $team_member->id, "text" => $team_member->user_name);
            }
        }

        if (in_array("authorized_team_members", $shareable_options)) {
            $dropdown[] = array("type" => "team", "id" => "authorized_team_members", "text" => app_lang("authorized_team_members"));
        }

        if (in_array("project_members", $shareable_options)) {
            $dropdown[] = array("type" => "team", "id" => "project_members", "text" => app_lang("project_members"));
        }

        if (in_array("client", $shareable_options)) {
            $client_options = array("limit" => 2000);

            $client_id = get_array_value($shareable_options, "client_id");
            if ($client_id) {
                $client_options["id"] = $client_id;
            }

            $clients = $this->Clients_model->get_clients_id_and_name($client_options)->getResult();
            foreach ($clients as $client) {
                $dropdown[] = array("type" => "client", "id" => "client:" . $client->id, "text" => $client->name);
            }
        }

        return $dropdown;
    }

    function save_folder_permissions()
    {
        $this->init();

        $id = $this->request->getPost('id');
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $permissions = $this->_prepare_permissions_text("", "full_access");
        $permissions = $this->_prepare_permissions_text($permissions, "upload_and_organize");
        $permissions = $this->_prepare_permissions_text($permissions, "upload_only");
        $permissions = $this->_prepare_permissions_text($permissions, "read_only");

        $folder_data = array(
            "permissions" => $permissions //there should have a comman at the end of each permission
        );

        $save_id = $this->Folders_model->ci_save($folder_data, $id);

        $folder_info = $this->_get_folder_info($id);

        if ($save_id) {
            echo json_encode(array("success" => true, "folder_info_content" => $folder_info, 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    private function _prepare_permissions_text($permissions, $name)
    {
        $value = $this->request->getPost($name);
        if ($value) {
            if (get_last_letter($value) != ",") {
                $value .= ",";
            }

            $value = str_replace(",,", ",", $value); //remove double ,,

            $permissions .= $value;
        }
        return $permissions;
    }

    private function _get_folder_window_data($folder_id = "", $id = 0, $client_id = 0)
    {
        $this->init();

        $data = array();
        $data["has_full_access"] = false;

        $options = $this->_preapare_folder_params($client_id);

        $options["folder_id"] = $folder_id;
        $options["id"] = $id;

        if ($this->login_user->is_admin) {
            if (!$client_id) {
                $options["has_full_access"] = true;
            }

            $data["has_full_access"] = true;
            if (!$folder_id) {
                $options["show_root_folders_only"] = true;
            }
        }

        $folder_details = $this->Folders_model->get_folder_details($options);

        if (isset($folder_details->not_authorized)) {
            app_redirect("forbidden");
        }

        $folder_info = $folder_details->folder_info;
        $data["folder_info"] = $folder_info;
        $data["parent_folder_info"] = $folder_details->parent_folder_info;
        $data["folders_list"] = $folder_details->folders_list;
        $data["parent_folder_permissions"] = $folder_details->parent_folder_permissions;

        $data["can_edit_clients"] = $this->can_edit_clients($client_id);

        $data["has_write_permission"] = false;
        $data["has_upload_permission"] = false;
        $data["can_manage_folder_access_permissions"] = false;

        if ($data["has_full_access"] || ($folder_info && $folder_info->actual_permission_rank >= 6) || ($folder_info && $folder_info->context == "client" && ($this->login_user->user_type == "client" && $this->login_user->client_id == $folder_info->context_id) || $data["can_edit_clients"])) {
            $data["has_write_permission"] = true;
        }

        if ($data["has_full_access"] || ($folder_info && $folder_info->actual_permission_rank >= 3) || (!$folder_info && $this->login_user->user_type == "client" && get_setting("client_can_add_files")) || ($folder_info && $folder_info->context == "client" && ($this->login_user->user_type == "client" && get_setting("client_can_add_files") && $this->login_user->client_id == $folder_info->context_id) || ($data["can_edit_clients"] && $this->login_user->user_type == "staff"))) {
            $data["has_upload_permission"] = true;
        }

        if ($this->login_user->user_type == "staff" && ($data["has_full_access"] || ($folder_info && $folder_info->actual_permission_rank == 9))) {
            $data["can_manage_folder_access_permissions"] = true;
        }

        $folder_main_id = $folder_info ? $folder_info->id : "";

        $data["folder_items"] = $this->_folder_items($folder_main_id, $options['context'], $client_id);

        $data["folder_item_type"] = $this->folder_item_type;
        $data["controller_slag"] = $this->controller_slag;
        $data["show_left_menu"] = $this->show_left_menu;

        $data["add_files_button"] = $this->_get_add_files_button($folder_main_id, $client_id);

        $data["file_preview_url"] = $this->file_preview_url;
        $data["file_preview_link_attributes"] = $this->_get_file_preview_link_attributes();

        $data["favourite_folders"] = $this->Folders_model->get_favourite_folders($this->login_user->id, $options)->getResult();
        $data["folder_details"] = false;

        $data["client_id"] = $client_id;

        return $data;
    }

    private function _get_file_preview_link_attributes()
    {

        $file_preview_link_attr = array(
            "data-sidebar" => "0",
            "title" => "",
            "class" => "text-default file-name item-name"
        );

        if ($this->show_file_preview_sidebar) {
            $file_preview_link_attr["data-sidebar"] = "1";
        }

        return $file_preview_link_attr;
    }

    private function _get_add_files_button($folder_id, $client_id = 0)
    {
        $add_files_button_attr = array(
            "id" => "file-manager-add-files-button",
            "class" => "btn btn-default",
            "title" => app_lang('add_files'),
            "data-post-client_id" => $client_id
        );

        foreach ($this->add_files_modal_post_data as $post_data_key => $post_data_value) {
            $add_files_button_attr["data-post-" . $post_data_key] = $post_data_value;
        }

        $add_files_button_attr["data-post-folder_id"] = $folder_id;

        return modal_anchor($this->add_files_modal_url, '<i data-feather="file-plus" class="icon-16 mr5"></i>' . app_lang('add_files'), $add_files_button_attr);
    }

    function get_folder_items($folder_id = "", $client_id = 0)
    {
        $data = $this->_get_folder_window_data($folder_id, 0, $client_id);

        echo json_encode(array(
            "success" => true,
            "window_content" => $this->template->view('app_folders/window', $data, true),
            "title_bar_content" => $this->template->view('app_folders/title_bar', $data, true)
        ));
    }

    function delete_folder()
    {
        $this->init();
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');

        if (!$this->can_manage_folders($id)) {
            app_redirect("forbidden");
        }

        // Get all subfolders and subfiles
        $all_subitems = $this->_get_all_subitems($id);

        // Delete all subfolders and subfiles
        foreach ($all_subitems as $item) {
            if ($item->type == "folder") {
                $this->Folders_model->delete($item->id);
            } else {
                if ($this->General_files_model->delete($item->id)) {
                    $file_info = $this->General_files_model->get_one($item->id);
                    //delete the files
                    delete_app_files($this->_get_file_path(), array(make_array_of_file($file_info)));
                }
            }
        }

        // Delete the main folder
        if ($this->Folders_model->delete($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    private function _get_all_subitems($folder_id)
    {
        $subitems = array();
        // Get all subfolders 
        $subfolders = $this->Folders_model->get_all_where(array("parent_id" => $folder_id))->getResult();
        foreach ($subfolders as $subfolder) {
            $subitems[] = (object) array(
                "id" => $subfolder->id,
                "type" => "folder"
            );
            // Get subitems of subfolders
            $subitems = array_merge($subitems, $this->_get_all_subitems($subfolder->id));
        }

        // Get all subfiles
        $subfiles = $this->General_files_model->get_all_where(array("folder_id" => $folder_id))->getResult();
        foreach ($subfiles as $subfile) {
            $subitems[] = (object) array(
                "id" => $subfile->id,
                "type" => "file"
            );
        }

        return $subitems;
    }


    /* add-remove favorites from folder */

    function add_remove_favorites($type = "add", $folder_id = 0)
    {
        $this->init();
        if ($folder_id) {
            validate_numeric_value($folder_id);

            if ($type === "add") {
                $this->Folders_model->add_remove_favorites($folder_id, $this->login_user->id, $type = "add");
            } else {
                $this->Folders_model->add_remove_favorites($folder_id, $this->login_user->id, $type = "remove");
            }
        }
    }

    function get_favourite_folders($client_id = 0)
    {
        $this->init();
        $controller_slag = $this->controller_slag;
        $options = $this->_preapare_folder_params($client_id);

        $data = $this->Folders_model->get_favourite_folders($this->login_user->id, $options)->getResult();

        echo json_encode(array(
            "success" => true,
            "content" => $this->template->view('app_folders/favourite_folders', array("favourite_folders" => $data, "controller_slag" => $controller_slag), true)
        ));
    }


    private function _preapare_folder_params($client_id = 0)
    {
        $options = array("context" => "file_manager");
        if ($this->login_user->user_type == "staff") {
            if ($client_id) {
                $options["login_client_id"] = $client_id;
                $options["context"] = "client";
            } else {
                if ($this->login_user->is_admin) {
                    $options["has_full_access"] = true;
                } else {
                    $options["member_id"] = $this->login_user->id;
                    $options["team_ids"] = $this->login_user->team_ids;
                }
            }
        } else if ($this->login_user->user_type == "client") {
            $options["login_client_id"] = $this->login_user->client_id;
            $client_info = $this->Clients_model->get_one($this->login_user->client_id);
            $options["client_group_ids"] = $client_info->group_ids ? $client_info->group_ids : "";
            $options["context"] = "client_portal";
        }
        return $options;
    }


    function move_folder_or_file_modal_form()
    {
        $this->init();

        $view_data["folder_id"] = $this->request->getPost('folder_id');
        $view_data["file_id"] = $this->request->getPost('file_id');

        if ($view_data["file_id"]) {
            $folder_id = $this->request->getPost('parent_folder_id');
        } else {
            $folder_id = $this->request->getPost('folder_id');
        }

        if (!$this->can_manage_folders($folder_id)) {
            app_redirect("forbidden");
        }

        $client_id = $this->request->getPost('client_id');

        $options = $this->_preapare_folder_params($client_id);
        $options["get_moveable_folders"] = true;

        $folder_details = $this->Folders_model->get_folder_details($options);

        if (isset($folder_details->not_authorized)) {
            app_redirect("forbidden");
        }

        $view_data["folders_list"] = $folder_details->folders_list;
        $view_data["hierarchical_folders"] = $this->_get_hierarchical_folder($view_data["folders_list"]);

        $view_data["controller_slag"] = $this->controller_slag;

        return $this->template->view('app_folders/move_folder_or_file_modal_form', $view_data);
    }

    private function _get_hierarchical_folder($folders, $parent_id = 0)
    {
        $this->init();
        $result = array();

        foreach ($folders as $item) {
            if ($item->parent_id == $parent_id) {
                $item->subfolders = $this->_get_hierarchical_folder($folders, $item->id);
                $result[] = $item;
            }
        }

        return $result;
    }

    function move_file_or_folder()
    {
        $this->init();

        $folder_id = $this->request->getPost('folder_id');
        $file_id = $this->request->getPost('file_id');
        $parent_id = $this->request->getPost('parent_id');

        if ($file_id) {
            if (!$this->can_manage_folders($parent_id)) {
                app_redirect("forbidden");
            }
        } else {
            if (!$this->can_manage_folders($folder_id)) {
                app_redirect("forbidden");
            }
        }

        if (!$parent_id) {
            echo json_encode(array("success" => false, 'message' => app_lang("select_any_folder_for_move")));
            return false;
        }

        $parent_folder_info = $this->Folders_model->get_one($parent_id);

        $level = "";
        if ($parent_folder_info && $parent_folder_info->id) {
            if ($parent_folder_info->level) {
                $level = $parent_folder_info->level . $parent_folder_info->id . ",";
            } else {
                $level = "," . $parent_folder_info->id . ",";
            }
        }

        if ($folder_id) {
            $folder_data = array(
                "parent_id" => $parent_id,
                "level" => $level
            );

            $save_id = $this->Folders_model->ci_save($folder_data, $folder_id);
        } else {
            $file_data = array("folder_id" => $parent_id);

            $save_id = $this->General_files_model->ci_save($file_data, $file_id);
        }

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => "", 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    private function get_folder_details($folder_id)
    {
        $this->init();

        $model_info = $this->Folders_model->get_one($folder_id);

        $options = $this->_preapare_folder_params();

        $options["folder_id"] = $model_info->folder_id;

        $folder_details = $this->Folders_model->get_folder_details($options);

        if (isset($folder_details->not_authorized)) {
            return false;
        }

        $folder_info = $folder_details->folder_info;
        return $folder_info;
    }

    private function can_manage_folders($parent_folder_id = 0, $folder_id = 0, $context = "", $context_id = 0)
    {
        if ($this->login_user->is_admin) {
            return true;
        } else {

            $id = $parent_folder_id ? $parent_folder_id : $folder_id;
            if (!$id && $context !== "client") {
                return false;
            } else if (!$id && $context === "client" && $context_id) {
                //client can create folder on root for client related files 
                if ($this->login_user->user_type == "client" && $this->login_user->client_id == $context_id) {
                    return true;
                } else if ($this->login_user->user_type == "staff") {
                    return true;
                }
            } else if ($context === "client" && $this->login_user->user_type == "staff" && $this->can_edit_clients($context_id)) {
                return true;
            }

            $folder_info = $this->get_folder_details($id);

            if ($folder_info && ($folder_info->actual_permission_rank == 6 || $folder_info->actual_permission_rank == 9) || ($folder_info->context == "client" && $this->login_user->user_type == "client" && $this->login_user->client_id == $folder_info->context_id)) {
                return true;
            }
        }
    }

    function get_file_modal_form()
    {

        $view_data['model_info'] = $this->General_files_model->get_one($this->request->getPost('id'));
        $view_data['folder_id'] = $this->request->getPost('folder_id');
        $view_data['client_id'] = $this->request->getPost('client_id');

        if ($this->login_user->user_type === "client" && !get_setting("client_can_add_files")) {
            app_redirect("forbidden");
        }

        return $this->template->view('file_manager/file_modal_form', $view_data);
    }

    function save_file()
    {
        $this->init();

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        if ($this->login_user->user_type === "client" && !get_setting("client_can_add_files")) {
            app_redirect("forbidden");
        }

        $folder_id = $this->request->getPost('folder_id');
        $client_id = $this->request->getPost('client_id');

        $folder_info = $this->Folders_model->get_one($folder_id);

        $context = "global_files";
        $context_id = 0;

        if ($client_id) {
            if ($folder_id) {
                if ($folder_info->context == "file_manager") {
                    $context = "global_files";
                    $client_id = 0;
                } else {
                    $context = "client";
                    $context_id = $folder_info->context_id;
                }
            } else {
                $context = "client";
                $client_id = $client_id;
                $context_id = $client_id;
            }
        } else {
            $context = "global_files";
        }

        $files = $this->request->getPost("files");
        $success = false;
        $now = get_current_utc_time();

        if ($context == "client") {
            $target_path = getcwd() . "/" . get_general_file_path("client", $client_id);
        } else {
            $target_path = getcwd() . "/" . $this->_get_file_path();
        }
       

        //process the fiiles which has been uploaded by dropzone
        if ($files && get_array_value($files, 0)) {
            foreach ($files as $file) {
                $file_name = $this->request->getPost('file_name_' . $file);
                $file_info = move_temp_file($file_name, $target_path);
                if ($file_info) {
                    $data = array(
                        "file_name" => get_array_value($file_info, 'file_name'),
                        "vencimento" => $this->request->getPost('vencimento'. $file),
                        "file_id" => get_array_value($file_info, 'file_id'),
                        "service_type" => get_array_value($file_info, 'service_type'),
                        "description" => $this->request->getPost('description_' . $file),
                        "file_size" => $this->request->getPost('file_size_' . $file),
                        "created_at" => $now,
                        "uploaded_by" => $this->login_user->id,
                        "folder_id" => $folder_id,
                        "context" => $context,
                        "context_id" => $context_id,
                        "client_id" => $client_id
                    );

                    $success = $this->General_files_model->ci_save($data);
                } else {
                    $success = false;
                }
            }
        }


        if ($success) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function get_view_file($file_id = 0, $client_id = 0)
    {
        $file_info = $this->General_files_model->get_details(array("id" => $file_id))->getRow();

        if ($file_info) {
            $view_data['can_comment_on_files'] = false;
            $file_url = get_source_url_of_file(make_array_of_file($file_info), $this->_get_file_path($client_id, $file_info->context), $file_info->context);

            $view_data["file_url"] = $file_url;
            $view_data["is_image_file"] = is_image_file($file_info->file_name);
            $view_data["is_iframe_preview_available"] = is_iframe_preview_available($file_info->file_name);
            $view_data["is_google_preview_available"] = is_google_preview_available($file_info->file_name);
            $view_data["is_viewable_video_file"] = is_viewable_video_file($file_info->file_name);
            $view_data["is_google_drive_file"] = ($file_info->file_id && $file_info->service_type == "google") ? true : false;
            $view_data["is_iframe_preview_available"] = is_iframe_preview_available($file_info->file_name);

            $view_data["file_info"] = $file_info;
            $view_data['file_id'] = clean_data($file_id);
            return $this->template->view("file_manager/view_file", $view_data);
        } else {
            show_404();
        }
    }

    function delete_file()
    {

        $id = $this->request->getPost('id');
        $info = $this->General_files_model->get_one($id);

        if ($this->login_user->user_type === "client") {
            app_redirect("forbidden");
        }

        if ($this->General_files_model->delete($id)) {

            //delete the files
            delete_app_files($this->_get_file_path(), array(make_array_of_file($info)));

            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    /* download a file */

    function download_file($id, $client_id = 0)
    {
        $file_info = $this->General_files_model->get_one($id);

        //serilize the path
        $file_data = serialize(array(make_array_of_file($file_info)));

        return $this->download_app_files($this->_get_file_path($client_id, $file_info->context), $file_data);
    }
}
