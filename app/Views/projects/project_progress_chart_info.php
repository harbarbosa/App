<div class="card">
    <div class="bg-primary p30 rounded-top">
        <br />
    </div>
    <div class="clearfix text-center mb-1">
        <div class="mt-50 chart-circle">
            <canvas id="project-progress-chart"></canvas>
        </div>
    </div>

    <?php 

    $rate_total = $soma_itens + $custo_tempo +  $despesas;
   
    ?>

    <ul class="list-group list-group-flush">
    <li class="list-group-item border-top">
            <?php echo 'Ordem de Serviço'; ?>: <?php echo $project_info->ordem_servico; ?>
        </li>
        <li class="list-group-item border-top">
            <?php echo 'Preço'; ?>: <?php echo to_currency($project_info->price); ?>
        </li>
        <li class="list-group-item border-top">
            Custo Total: <?php echo to_currency($rate_total); ?>
        </li>
        <?php if ($login_user->user_type === "staff" && $project_info->project_type === "client_project") { ?>
            <li class="list-group-item border-top">
                <?php echo app_lang("client"); ?>: <?php echo anchor(get_uri("clients/view/" . $project_info->client_id), $project_info->company_name? $project_info->company_name: ""); ?>
            </li>
        <?php } else { ?>
            <li class="list-group-item border-top">
                <?php echo app_lang("status"); ?>: <?php echo $project_info->title_language_key ? app_lang($project_info->title_language_key) : $project_info->status_title; ?>
            </li>
        <?php } ?>
    </ul>
</div>



<script type="text/javascript">
    $(document).ready(function () {
        var project_progress = <?php echo $project_progress; ?>;
        var projectProgressChart = document.getElementById("project-progress-chart");

        var textDirection = "<?php echo app_lang("text_direction"); ?>";
        var textAlign = "";
        if (textDirection === "rtl") {
            var textAlign = "center";
        }

        new Chart(projectProgressChart, {
            type: 'doughnut',
            data: {
                datasets: [{
                        label: 'Complete',
                        percent: project_progress,
                        backgroundColor: ['#6690F4'],
                        borderWidth: 0
                    }]
            },
            plugins: [{
                    beforeInit: (chart) => {
                        const dataset = chart.data.datasets[0];
                        chart.data.labels = [dataset.label];
                        dataset.data = [dataset.percent, 100 - dataset.percent];
                    }
                },
                {
                    beforeDraw: (chart) => {
                        var width = chart.chart.width,
                                height = chart.chart.height,
                                ctx = chart.chart.ctx;
                        ctx.restore();
                        ctx.font = 1.5 + "em sans-serif";
                        ctx.fillStyle = "#9b9b9b";
                        ctx.textBaseline = "middle";
                        ctx.textAlign = textAlign;
                        var text = chart.data.datasets[0].percent + "%",
                                textX = Math.round((width - ctx.measureText(text).width) / 2),
                                textY = height / 2;
                        ctx.fillText(text, textX, textY);
                        ctx.save();
                    }
                }
            ],
            options: {
                maintainAspectRatio: false,
                cutoutPercentage: 90,
                rotation: Math.PI / 2,
                legend: {
                    display: false
                },
                tooltips: {
                    filter: tooltipItem => tooltipItem.index === 0,
                    callbacks: {
                        afterLabel: function (tooltipItem, data) {
                            var dataset = data['datasets'][0];
                            var percent = Math.round((dataset['data'][tooltipItem['index']] / dataset["_meta"][Object.keys(dataset["_meta"])[0]]['total']) * 100);
                            return '(' + percent + '%)';
                        }
                    }
                }
            }
        });
    });
</script>
