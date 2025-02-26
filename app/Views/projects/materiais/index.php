<div class="card">
    <div class="tab-title clearfix">
        <h4>Materiais</h4>
        <div class="title-button-group">
            <?php
            echo modal_anchor(get_uri("projects/item_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i>Adicionar Material ", array("class" => "btn btn-default", "title" =>"Adicionar Material", "data-post-project_id" => $project_id));
            ?>
        </div>
    </div>

    <div class="table-responsive mt15 pl15 pr15">
               <table id="estimate-item-table" class="display" width="100%"> 
               </table>
            </div>
</div>



<script type="text/javascript">
    $(document).ready(function () {
        
        $("#estimate-item-table").appTable({
            source: '<?php echo_uri("projects/item_list_data/".$project_id."/") ?>',
            order: [[0, "asc"]],
            hideTools: false,
            displayLength: 100,
            columns: [
                {title: "<?php echo app_lang("item") ?>", "bSortable": false},
                {title: "QTD", "class": "text-right", "bSortable": true},
                {title: "Valor Unitario", "class": "text-right", "bSortable": false},
                {title: "Valor Total", "class": "text-right", "bSortable": false},
                {title: "Data", "class": "text-right", "bSortable": false},
                {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100", "bSortable": false}
            ],
            order: [], // Remove qualquer ordenação inicial
            ordering: false, // Desativa a ordenação completamente,
            onInitComplete: function () {
                calcularTotal();
            },
            onDrawCallback: function () {
                calcularTotal();
            }
        });

        function calcularTotal() {
    let total = 0;

    $("#estimate-item-table tbody tr").each(function () {
        let valor = $(this).find("td:eq(3)").text().replace("R$", "").replace(".", "").replace(",", ".").trim();
        let valorNumerico = parseFloat(valor) || 0;
        total += valorNumerico;
    });

    // Remover linha anterior se existir
    $("#total-row").remove();

    // Formatar total para padrão brasileiro
    let totalFormatado = total.toLocaleString("pt-BR", { minimumFractionDigits: 2 });

    // Adicionar linha com total ao final da tabela
    let totalRow = `<tr id="total-row">
                        <td colspan="3" class="text-right"><strong>Total Geral:</strong></td>
                        <td class="text-right"><strong>R$ ${totalFormatado}</strong></td>
                        <td colspan="2"></td>
                    </tr>`;

    $("#estimate-item-table tbody").append(totalRow);
}

    });
</script>
