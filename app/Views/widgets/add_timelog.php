<div id="js-clock-in-out" class="card dashboard-icon-widget clock-in-out-card">
    <div class="card-body">
        <div class="widget-icon  <?php echo (isset($clock_status->id)) ? 'bg-info' : 'bg-coral'; ?> ">
            <i data-feather="clock" class="icon"></i>
        </div>
        <div class="widget-details">
            <?php
                echo modal_anchor(get_uri("projects/diario_form"), "<i data-feather='log-out' class='icon-16'></i> " . app_lang('clock_out'), array("class" => "btn btn-default text-primary", "title" => app_lang('clock_out'), "id" => "timecard-clock-out", "data-post-id" => $clock_status->id, "data-post-clock_out" => 1));
                echo "<div class='mt5 bg-transparent-white'>" . app_lang('you_are_currently_clocked_out') . "</div>";
            
            ?>
        </div>
    </div>
</div>
