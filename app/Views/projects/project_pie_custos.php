<div class="card">
    <div class="card-body">
        <canvas id="custos-chart" width="512"></canvas>
    </div>
</div>

<?php
$task_title = array();
$task_data = array();
$task_status_color = array();


$task_title[] = 'Custo Produtos';
$task_data[] = $soma_itens;
$task_status_color[] = '#FFD700';

$task_title[] = 'Custo Horas';
$task_data[] = $custo_tempo;
$task_status_color[] = '#F5A623';

$task_title[] = 'Despesas';
$task_data[] = $despesas;
$task_status_color[] = '#7ED321';

$task_title[] = 'Lucro';
$task_data[] = $lucro;
$task_status_color[] = '#4A90E2';


?>
<script type="text/javascript">
    //for task status chart
    var labels = <?php echo json_encode($task_title) ?>;
    var taskData = <?php echo json_encode($task_data) ?>;
    var taskStatuscolor = <?php echo json_encode($task_status_color) ?>;
    var taskStatusChart = document.getElementById("custos-chart");

    new Chart(taskStatusChart, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [
                {
                    data: taskData,
                    backgroundColor: taskStatuscolor,
                    borderWidth: 0
                }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        // Obtém o valor do dataset
                        var value = data.datasets[0].data[tooltipItem.index];
                        
                        // Formata o valor para o formato brasileiro
                        var formattedValue = new Intl.NumberFormat('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        }).format(value);
                        
                        return formattedValue; // Exibe no tooltip
                    },
                    afterLabel: function (tooltipItem, data) {
                        var dataset = data['datasets'][0];
                        var percent = Math.round(
                            (dataset['data'][tooltipItem['index']] / 
                            dataset["_meta"][Object.keys(dataset["_meta"])[0]]['total']) * 100
                        );
                        return '(' + percent + '%)';
                    }
                }
            },
            legend: {
    display: true,
    position: 'bottom',
    labels: {
        fontColor: "#898fa9",
        generateLabels: function (chart) {
            var data = chart.data;
            var total = data.datasets[0].data.reduce((sum, value) => sum + value, 0); // Soma os valores
            var formattedTotal = new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(total); // Formata para Real

            // Gera as etiquetas normais
            var labels = data.labels.map(function (label, index) {
                var value = data.datasets[0].data[index];
                var formattedValue = new Intl.NumberFormat('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }).format(value);
                return {
                    text: `${label}: ${formattedValue}`,
                    fillStyle: data.datasets[0].backgroundColor[index],
                    hidden: chart.getDatasetMeta(0).data[index].hidden,
                    index: index
                };
            });

           

            return labels;
        }
    }
},
            animation: {
                animateScale: true
            },
            
        }
    });
</script>