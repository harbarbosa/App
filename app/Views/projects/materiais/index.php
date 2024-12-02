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
               {title: "Entregue por", "class": "text-right", "bSortable": false},
               {title: "Retirado por", "class": "text-right", "bSortable": false},
               {title: "Data", "class": "text-right", "bSortable": false},
               
               {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100", "bSortable": false}
           ],
           order: [], // Remove qualquer ordenação inicial
        ordering: false, // Desativa a ordenação completamente
           
           
           
       });
    });
</script>