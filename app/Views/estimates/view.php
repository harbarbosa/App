<div id="page-content" class="clearfix">
   <div style="max-width: 90%; margin: auto;">
        <div class="page-title clearfix mt25">
            <h1><?php echo get_estimate_id($estimate_info->id); ?> - <?php echo $estimate_info->titulo; ?></h1>
            <div class="title-button-group">
                <span class="dropdown inline-block mt15">
                <button class="btn btn-info text-white dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true">
                <i data-feather="tool" class="icon-16"></i> <?php echo app_lang('actions'); ?>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li role="presentation"><?php echo anchor(get_uri("estimates/download_pdf/" . $estimate_info->id), "<i data-feather='download' class='icon-16'></i> " . app_lang('download_pdf'), array("title" => app_lang('download_pdf'), "class" => "dropdown-item")); ?> </li>
                    <li role="presentation"><?php echo anchor(get_uri("estimates/download_pdf/" . $estimate_info->id . "/view"), "<i data-feather='file-text' class='icon-16'></i> " . app_lang('view_pdf'), array("title" => app_lang('view_pdf'), "target" => "_blank", "class" => "dropdown-item")); ?> </li>
                    <li role="presentation"><?php echo anchor(get_uri("estimates/preview/" . $estimate_info->id . "/1"), "<i data-feather='search' class='icon-16'></i> " . app_lang('estimate_preview'), array("title" => app_lang('estimate_preview'), "target" => "_blank", "class" => "dropdown-item")); ?> </li>
                    <li role="presentation"><?php echo anchor(get_uri("estimate/preview/" . $estimate_info->id . "/" . $estimate_info->public_key), "<i data-feather='external-link' class='icon-16'></i> " . app_lang('estimate') . " " . app_lang("url"), array("target" => "_blank", "class" => "dropdown-item")); ?> </li>
                    <li role="presentation"><?php echo js_anchor("<i data-feather='printer' class='icon-16'></i> " . app_lang('print_estimate'), array('title' => app_lang('print_estimate'), 'id' => 'print-estimate-btn', "class" => "dropdown-item")); ?> </li>
                    <li role="presentation" class="dropdown-divider"></li>
                    <li role="presentation"><?php echo modal_anchor(get_uri("estimates/modal_form"), "<i data-feather='edit' class='icon-16'></i> " . app_lang('edit_estimate'), array("title" => app_lang('edit_estimate'), "data-post-id" => $estimate_info->id, "role" => "menuitem", "tabindex" => "-1", "class" => "dropdown-item")); ?> </li>
                    <li role="presentation"><?php echo modal_anchor(get_uri("estimates/modal_form"), "<i data-feather='copy' class='icon-16'></i> " . app_lang('clone_estimate'), array("data-post-is_clone" => true, "data-post-id" => $estimate_info->id, "title" => app_lang('clone_estimate'), "class" => "dropdown-item")); ?></li>
                    <?php
                        if ($estimate_status == "draft" || $estimate_status == "sent") {
                            ?>
                    <li role="presentation"><?php echo ajax_anchor(get_uri("estimates/update_estimate_status/" . $estimate_info->id . "/accepted"), "<i data-feather='check-circle' class='icon-16'></i> " . app_lang('mark_as_accepted'), array("data-reload-on-success" => "1", "class" => "dropdown-item")); ?> </li>
                    <li role="presentation"><?php echo ajax_anchor(get_uri("estimates/update_estimate_status/" . $estimate_info->id . "/declined"), "<i data-feather='x-circle' class='icon-16'></i> " . app_lang('mark_as_declined'), array("data-reload-on-success" => "1", "class" => "dropdown-item")); ?> </li>
                    <?php } else if ($estimate_status == "accepted") {
                        ?>
                    <li role="presentation"><?php echo ajax_anchor(get_uri("estimates/update_estimate_status/" . $estimate_info->id . "/declined"), "<i data-feather='x-circle' class='icon-16'></i> " . app_lang('mark_as_declined'), array("data-reload-on-success" => "1", "class" => "dropdown-item")); ?> </li>
                    <?php
                        } else if ($estimate_status == "declined") {
                            ?>
                    <li role="presentation"><?php echo ajax_anchor(get_uri("estimates/update_estimate_status/" . $estimate_info->id . "/accepted"), "<i data-feather='check-circle' class='icon-16'></i> " . app_lang('mark_as_accepted'), array("data-reload-on-success" => "1", "class" => "dropdown-item")); ?> </li>
                    <?php
                        }
                        ?>
                    <?php
                        if ($client_info->is_lead) {
                            if ($estimate_status == "draft" || $estimate_status == "sent") {
                                ?>
                    <li role="presentation"><?php echo modal_anchor(get_uri("estimates/send_estimate_modal_form/" . $estimate_info->id), "<i data-feather='send' class='icon-16'></i> " . app_lang('send_to_lead'), array("title" => app_lang('send_to_lead'), "data-post-id" => $estimate_info->id, "data-post-is_lead" => true, "role" => "menuitem", "tabindex" => "-1", "class" => "dropdown-item")); ?> </li>
                    <?php
                        }
                        } else {
                        if ($estimate_status == "draft" || $estimate_status == "sent") {
                            ?>
                    <li role="presentation"><?php echo modal_anchor(get_uri("estimates/send_estimate_modal_form/" . $estimate_info->id), "<i data-feather='send' class='icon-16'></i> " . app_lang('send_to_client'), array("title" => app_lang('send_to_client'), "data-post-id" => $estimate_info->id, "role" => "menuitem", "tabindex" => "-1", "class" => "dropdown-item")); ?> </li>
                    <?php
                        }
                        }
                        ?>
                    <?php if ($estimate_status == "accepted") { ?>
                    <li role="presentation" class="dropdown-divider"></li>
                    <?php if ($can_create_projects && !$estimate_info->project_id) { ?>
                    <li role="presentation"><?php echo modal_anchor(get_uri("projects/modal_form"), "<i data-feather='plus' class='icon-16'></i> " . app_lang('create_project'), array("data-post-estimate_id" => $estimate_info->id, "title" => app_lang('create_project'), "data-post-client_id" => $estimate_info->client_id, "class" => "dropdown-item")); ?> </li>
                    <?php } ?>
                    <?php if ($show_invoice_option) { ?>
                    <li role="presentation"><?php echo modal_anchor(get_uri("invoices/modal_form/"), "<i data-feather='refresh-cw' class='icon-16'></i> " . app_lang('create_invoice'), array("title" => app_lang("create_invoice"), "data-post-estimate_id" => $estimate_info->id, "class" => "dropdown-item")); ?> </li>
                    <?php } ?>
                    <?php } ?>
                </ul>
                </span>
            </div>
        </div>
      <div id="estimate-status-bar">
         <?php echo view("estimates/estimate_status_bar"); ?>
      </div>
      <ul id="project-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs rounded classic mb20 scrollable-tabs border-white" role="tablist">
         <li class="nav-item" role="presentation"><a class="nav-link active" data-bs-toggle="tab" href="#" data-bs-target="#mcalc-section" aria-selected="true" role="tab">Memória de Calculo</a></li>
         <li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab"  href="#" data-bs-target="#items-section" aria-selected="false" tabindex="-1" role="tab">Itens Orçamento</a></li>
         <li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab"  href="#" data-bs-target="#etapa-section" aria-selected="false" tabindex="-1" role="tab">Etapas</a></li>
         <li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab"  href="#" data-bs-target="#introducao-section" aria-selected="false" tabindex="-1" role="tab">Introdução</a></li>
         <li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab"  href="#" data-bs-target="#descricao-section" aria-selected="false" tabindex="-1" role="tab">Descrição</a></li>
         <li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab"  href="#" data-bs-target="#observacao-section" aria-selected="false" tabindex="-1" role="tab">Observações</a></li>
         <li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab"  href="#" data-bs-target="#pagamento-section" aria-selected="false" tabindex="-1" role="tab">Condições Comerciais</a></li>
         <li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab"  href="#" data-bs-target="#configuracao-section" aria-selected="false" tabindex="-1" role="tab">Configurações</a></li>
      </ul>

     
      
      

      <div class="tab-content">
     

      <?php echo view("estimates/itemlist"); ?>

      
      </div>




         
                
     
      
 
   <?php
      $signer_info = @unserialize($estimate_info->meta_data);
      if (!($signer_info && is_array($signer_info))) {
          $signer_info = array();
      }
      ?>
   <?php if ($estimate_status === "accepted" && ($signer_info || $estimate_info->accepted_by)) { ?>
   <div class="card mt15">
      <div class="page-title clearfix ">
         <h1><?php echo app_lang("signer_info"); ?></h1>
      </div>
      <div class="p15">
         <div><strong><?php echo app_lang("name"); ?>: </strong><?php echo $estimate_info->accepted_by ? get_client_contact_profile_link($estimate_info->accepted_by, $estimate_info->signer_name) : get_array_value($signer_info, "name"); ?></div>
         <div><strong><?php echo app_lang("email"); ?>: </strong><?php echo $estimate_info->signer_email ? $estimate_info->signer_email : get_array_value($signer_info, "email"); ?></div>
         
         <?php if (get_array_value($signer_info, "signed_date")) { ?>
         <div><strong><?php echo app_lang("signed_date"); ?>: </strong><?php echo format_to_relative_time(get_array_value($signer_info, "signed_date")); ?></div>
         <?php } ?>
         <?php
            if (get_array_value($signer_info, "signature")) {
                $signature_file = @unserialize(get_array_value($signer_info, "signature"));
                $signature_file_name = get_array_value($signature_file, "file_name");
                $signature_file = get_source_url_of_file($signature_file, get_setting("timeline_file_path"), "thumbnail");
                ?>
         <div><strong><?php echo app_lang("signature"); ?>: </strong><br /><img class="signature-image" src="<?php echo $signature_file; ?>" alt="<?php echo $signature_file_name; ?>" /></div>
         <?php } ?>
      </div>
   </div>
   <?php } ?>
</div>

<script type="text/javascript">
    
   //RELOAD_VIEW_AFTER_UPDATE = true;
   $(document).ready(function () {
    
   
    
   
    
    $("#decricao-form").appForm({
        ajaxSubmit: false,
            onSuccess: function (result) {
                appLoader.hide();      
            }
            
        });

      
        setTimeout(function () {
            $("#title").focus();
        }, 200);
        initWYSIWYGEditor(".wysiwyg-editor", {
            height: 600,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['link', 'hr', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview']],
                ['help', ['help']] // Adiciona um botão de ajuda
            ],
            fontSizes: ['8', '10', '12', '14', '16', '18', '20', '24', '28', '36', '48', '64'], // Tamanhos de fonte disponíveis
            lang: "<?php echo app_lang('language_locale_long'); ?>"
        });

    $("#step-form").appForm({
            onSuccess: function (result) {
                $("#estimate-etapa-table").appTable({reload: true}); 
            }
        });

        //TABELA ITENS ORÇAMENTO

        $("#items-orcamento-table").appTable({
    source: '<?php echo_uri("estimates/item_orcamento_list/" . $estimate_info->id . "/") ?>',
    order: [[0, "asc"]],
    hideTools: true,
    displayLength: 100,
    columns: [
        {title: "<?php echo app_lang("item") ?>", "bSortable": false},
        {title: "QTD", "class": "text-right", "bSortable": true},
        {title: "Preço", "class": "text-right", "bSortable": false},
        {title: "Preço Total", "class": "text-right", "bSortable": false},
        {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100", "bSortable": false}
    ],
    ordering: false, // Desativa a ordenação completamente

    onInitComplete: function () {
        // Itera sobre as linhas da tabela e agrupa por etapas
        var rows = $("#items-orcamento-table tbody tr");
        var currentStage = null;
        var total = 0;

        rows.each(function () {
            var stage = $(this).find('td').eq(0).text();

            var itemPriceText = $(this).find('td').eq(3).text();
            var itemPrice = parseFloat(
                itemPriceText
                    .replace("R$", "") // Remove o símbolo de Real
                    .replace(/\./g, "") // Remove separadores de milhar
                    .replace(",", ".")  // Substitui a vírgula decimal por ponto
                    .trim()
            ) || 0;

            total += itemPrice; // Soma ao total
          
            $(this).addClass("item-row");
        });
       
        // Adiciona a linha de Total no final da tabela
        var totalRow = `
            <tr class="total-row">
                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                <td class="text-right"><strong>${formatCurrencyBRL(total)}</strong></td>
                <td></td>
            </tr>`;
        $("#items-orcamento-table tbody").append(totalRow);
    }
});

function formatCurrencyBRL(value) {
    return value.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
}

   

//TABELA ETAPA

        $("#estimate-etapa-table").appTable({
    source: '<?php echo_uri("estimates/etapa_list_data/" . $estimate_id . "/") ?>',
    order: [[0, "asc"]],
    hideTools: true,
    displayLength: 100,
    columns: [
        {visible: false, searchable: false},
        {title: "<?php echo 'Etapas' ?> ", "bSortable": false},
        {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100", "bSortable": false}
    ],

    onInitComplete: function () {
        // Adiciona uma linha de título com ícone de toggle no início da tabela
       

        // Função para expandir/colapsar as linhas da tabela
        $("#toggle-icon").on("click", function () {
            var rows = $("#estimate-etapa-table tbody tr");
            if (rows.is(":visible")) {
                rows.hide();
                $(this).text("►"); // Ícone de expandir
            } else {
                rows.show();
                $(this).text("▼"); // Ícone de colapsar
            }
        });

        // Aplicar funcionalidade de classificação
        $("#estimate-etapa-table").find("tbody").attr("id", "estimate-etapa-table-sortable");
        var $selector = $("#estimate-etapa-table-sortable");

        Sortable.create($selector[0], {
            animation: 150,
            chosenClass: "sortable-chosen",
            ghostClass: "sortable-ghost",
            onUpdate: function (e) {
                appLoader.show();
                // Prepare os índices de classificação
                var data = "";
                $.each($selector.find(".item-row"), function (index, ele) {
                    if (data) {
                        data += ",";
                    }
                    data += $(ele).attr("data-id") + "-" + index;
                });

                // Atualiza os índices de classificação
               
            }
        });
    },

    onDeleteSuccess: function (result) {
        $("#estimate-total-section").html(result.estimate_total_view);
        if (typeof updateInvoiceStatusBar == 'function') {
            updateInvoiceStatusBar(result.estimate_id);
            
        }
    },
    onUndoSuccess: function (result) {
        $("#estimate-total-section").html(result.estimate_total_view);
        if (typeof updateInvoiceStatusBar == 'function') {
            updateInvoiceStatusBar(result.estimate_id);
        }
    }
});

     // Inicialize a tabela
     $("#estimate-item-table").appTable({
           source: '<?php echo_uri("estimates/item_list_data/" . $estimate_info->id . "/") ?>',
           order: [[0, "asc"]],
           hideTools: true,
           displayLength: 100,
           columns: [
               
               {title: "<?php echo 'Etapa' ?>", "bSortable": false},
               {title: "<?php echo app_lang("item") ?>", "bSortable": false},
               {title: "QTD", "class": "text-right", "bSortable": true},
               {title: "Custo", "class": "text-right", "bSortable": false},
               {title: "Custo Total", "class": "text-right", "bSortable": false},
               {title: "BDI", "class": "text-right", "bSortable": false},
               {title: "Preço Total", "class": "text-right", "bSortable": false},
               {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100", "bSortable": false}
           ],
           order: [], // Remove qualquer ordenação inicial
        ordering: false, // Desativa a ordenação completamente
           
           
           onInitComplete: function () {
               // Itera sobre as linhas da tabela e agrupa por etapas
               var rows = $("#estimate-item-table tbody tr");
               var currentStage = null;

               rows.each(function () {
                   var stage = $(this).find('td').eq(0).text();
                   var itemPrice = parseFloat($(this).find('td').eq(6).text().replace(/[^\d.-]/g, '')) || 0; // Preço total do item
                   
                   if (stage !== currentStage) {
                       // Armazena o nome da etapa sem afetar o evento de clique
                       currentStage = stage;
                       var stageRow = $('<tr class="stage-row" style="cursor: pointer;"><td colspan="9"><strong>' + stage + ' ▼</strong></tr>');
                       
                       stageRow.data("stageName", stage); // Armazena o nome da etapa

                       stageRow.on("click", function () {
                           var items = $(this).nextUntil(".stage-row");
                           if (items.is(":visible")) {
                               items.hide();
                               $(this).find("strong").html($(this).data("stageName") + " ►"); // Usa o nome armazenado
                           } else {
                               items.show();
                               $(this).find("strong").html($(this).data("stageName") + " ▼"); // Usa o nome armazenado
                           }
                       });
                       
                       $(this).before(stageRow);
                   }
                   $(this).addClass("item-row");
               });
           }
       });
   
   
   
       //print estimate
       $("#print-estimate-btn").click(function () {
           appLoader.show();
   
           $.ajax({
               url: "<?php echo get_uri('estimates/print_estimate/' . $estimate_info->id) ?>",
               dataType: 'json',
               success: function (result) {
                   if (result.success) {
                       document.body.innerHTML = result.print_view; //add estimate's print view to the page
                       $("html").css({"overflow": "visible"});
   
                       setTimeout(function () {
                           window.print();
                       }, 200);
                   } else {
                       appAlert.error(result.message);
                   }
   
                   appLoader.hide();
               }
           });
       });
   
       //reload page after finishing print action
       window.onafterprint = function () {
           location.reload();
       };

       
   
   });

   
   
   updateInvoiceStatusBar = function (estimateId) {
       $.ajax({
           url: "<?php echo get_uri("estimates/get_estimate_status_bar"); ?>/" + estimateId,
           success: function (result) {
               if (result) {
                   $("#estimate-status-bar").html(result);
               }
           }
       });
   };
   
</script>
<?php
   //required to send email 
   
   load_css(array(
       "assets/js/summernote/summernote.css",
   ));
   load_js(array(
       "assets/js/summernote/summernote.min.js",
   ));
   ?>