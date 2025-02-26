       <!-- INICIO MCALC -->
        
<div role="tabpanel" class="tab-pane fade active show" id="mcalc-section">
         <div class="mt15">
         <div class="card p15 b-t">
            <div class="clearfix p20">
                
               <!-- small font size is required to generate the pdf, overwrite that for screen -->
               <style type="text/css">
                  .invoice-meta {
                  font-size: 100% !important;
                  }
               </style>
               <?php
                  $color = get_setting("estimate_color");
                  if (!$color) {
                      $color = get_setting("invoice_color");
                  }
                  $style = get_setting("invoice_style");
                  ?>
               <?php
                  $data = array(
                      "client_info" => $client_info,
                      "color" => $color ? $color : "#2AA384",
                      "estimate_info" => $estimate_info
                  );
                  if ($style === "style_2") {
                      echo view('estimates/estimate_parts/header_style_2.php', $data);
                  } else {
                      echo view('estimates/estimate_parts/header_style_1.php', $data);
                  }
                  ?>
            </div>
            <div class="float-start mt20 ml15">
                  <?php echo modal_anchor(get_uri("estimates/item_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_item'), array("class" => "btn btn-info text-white", "title" => app_lang('add_item'), "data-post-estimate_id" => $estimate_info->id)); ?>
                 
               </div>
            <div class="table-responsive mt15 pl15 pr15">
               <table id="estimate-item-table" class="display" width="100%"> 
               </table>
            </div>
            <div class="clearfix">
               <div class="float-start mt20 ml15">
                  <?php echo modal_anchor(get_uri("estimates/item_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_item'), array("class" => "btn btn-info text-white", "title" => app_lang('add_item'), "data-post-estimate_id" => $estimate_info->id)); ?>
                 
               </div>
               
               <div class="float-end pr15" id="estimate-total-section">
                  <?php echo view("estimates/estimate_total_section"); ?>
               </div>
            </div>
         </div>
         <p class="b-t b-info pt10 m15 pb10"><?php echo nl2br($estimate_info->note ? $estimate_info->note : ""); ?></p>
         <?php
            if (get_setting("enable_comments_on_estimates") && !($estimate_info->status === "draft")) {
                echo view("estimates/comment_form");
            }
            ?>
      </div>
         </div>

        <!-- FIM TABPANEL MCALC-->

   <!-- INICIO ITEMS -->
<div role="tabpanel" class="tab-pane fade " id="items-section">
         <div class="mt15">
         <div class="card p15 b-t">
            <div class="clearfix p20">
                
               <!-- small font size is required to generate the pdf, overwrite that for screen -->
               <style type="text/css">
                  .invoice-meta {
                  font-size: 100% !important;
                  }
               </style>
               <?php
                  $color = get_setting("estimate_color");
                  if (!$color) {
                      $color = get_setting("invoice_color");
                  }
                  $style = get_setting("invoice_style");
                  ?>
               <?php
                  $data = array(
                      "client_info" => $client_info,
                      "color" => $color ? $color : "#2AA384",
                      "estimate_info" => $estimate_info
                  );
                  if ($style === "style_2") {
                      echo view('estimates/estimate_parts/header_style_2.php', $data);
                  } else {
                      echo view('estimates/estimate_parts/header_style_1.php', $data);
                  }
                  ?>
            </div>
            
            <div class="table-responsive mt15 pl15 pr15">
                <p>Copiar itens da memória de calculo<p>
            <?php echo modal_anchor(get_uri("estimates/copy_items"), "<i data-feather='plus-circle' class='icon-16'></i>Copiar ", array("class" => "btn btn-info text-white", "title" => app_lang('add_item'), "data-post-estimate_id" => $estimate_info->id)); ?>
               <table id="items-orcamento-table" class="display" width="100%"> 
               </table>
            </div>
            <div class="clearfix">
               <div class="float-start mt20 ml15">
                  <?php echo modal_anchor(get_uri("estimates/item_orcamento_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_item'), array("class" => "btn btn-info text-white", "title" => app_lang('add_item'), "data-post-estimate_id" => $estimate_info->id)); ?>
               </div>
               
               
         </div>
         </div>
                </div>
                </div>
                

        <!-- FIM TABPANEL ITEMS-->

         <!-- INICIO TABPANEL ETAPAS-->

         <div role="tabpanel" class="tab-pane fade" id="etapa-section">
         <div class="mt15">
            <div class="card p15 b-t">
                <h2>Etapas</h2>
                <div class="clearfix p20">
                <div class="modal-body clearfix">
<div class="table-responsive mt15 pl15 pr15">
                    <table id="estimate-etapa-table" class="display" width="100%"> 
                        
                    </table>
                    <div class="float-start mt20 ml15">
                  <?php echo modal_anchor(get_uri("estimates/step_modal_form"), "<i data-feather='layers' class='icon-16'></i> " . "Adicionar Etapa", array("class" => "btn btn-primary text-white", "title" => "Adicionar Etapa", "data-post-estimate_id" => $estimate_info->id)); ?>
               </div>
                </div>
                

                </div>
            </div>
        </div>
         </div>
        </div>

    <!-- FIM TABPANEL ETAPAS-->

    <!-- INICIO TABPANEL DESCRICAO-->

    <div role="tabpanel" class="tab-pane fade" id="descricao-section">
         <div class="mt15">
            <div class="card p15 b-t">
            <h2>Descrição da Proposta</h2>
                <div class="clearfix p20">
                <div id="page-content" class="page-wrapper clearfix">
    <div class="card view-container">
        <div id="help-dropzone" class="post-dropzone">
            <?php echo form_open(get_uri("estimates/save_descricao"), array("id" => "decricao-form", "class" => "general-form", "role" => "form")); ?>

            <div>

                

                <div class="card-body">
                <input type="hidden" name="estimate_id" value="<?php echo $estimate_info->id; ?>" />

                    <div class="form-group">

                        <div class=" col-md-12">
                            <?php

                                if (empty($estimate_info->descricao)){
                                    $descricao = '
                                    <section>
                                        <div class="section-title">Descrição do Orçamento</div>
                                        <p class="section-description">Este orçamento inclui todos os serviços e produtos necessários para o desenvolvimento do projeto conforme solicitado. O escopo detalhado do trabalho é o seguinte:</p>
                                        <ul>
                                            <li>Consultoria personalizada para definição de estratégias empresariais.</li>
                                            <li>Desenvolvimento de software sob medida para as necessidades da empresa.</li>
                                            <li>Treinamento da equipe para utilização do software e otimização de processos.</li>
                                        </ul>
                                    </section>
                                    ';
                                }else{
                                    $descricao = $estimate_info->descricao;
                                }

                            echo form_textarea(array(
                                "id" => "descricao",
                                "name" => "descricao",
                                "value" => process_images_from_content($descricao, false),
                                "placeholder" => app_lang('description'),
                                "class" => "form-control wysiwyg-editor"
                            ));
                            ?>
                        </div>
                    </div>

                               

                  

                </div>
                   

               
                   
                <div>
        
        <button type="submit" class="btn btn-primary"><?php echo app_lang('save'); ?></button>
    </div>
                
            </div>

            <?php echo form_close(); ?>
        </div> 
    </div> 
</div>
</div> 
</div> 
</div>
 </div> 
                      

<!-- FIM TABPANEL DESCRIÇÃO -->

 <!-- INICIO TABPANEL INTRODUÇÃO-->

 <div role="tabpanel" class="tab-pane fade" id="introducao-section">
         <div class="mt15">
            <div class="card p15 b-t">
            <h2>Introdução da Proposta</h2>
                <div class="clearfix p20">
                <div id="page-content" class="page-wrapper clearfix">
    <div class="card view-container">
        <div id="help-dropzone" class="post-dropzone">
            <?php echo form_open(get_uri("estimates/save_introducao"), array("id" => "decricao-form", "class" => "general-form", "role" => "form")); ?>

            <div>

                

                <div class="card-body">
                <input type="hidden" name="estimate_id" value="<?php echo $estimate_info->id; ?>" />

                    <div class="form-group">

                        <div class=" col-md-12">
                            <?php
                            if (empty($estimate_info->introducao)){
                                $introducao = '
                                <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: "Arial", sans-serif;
            background-color: #f8f9fc;
            color: #333;
            line-height: 1.6;
        }
        header {
            background-color: #003f5c;
            color: white;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        header h1 {
            font-size: 2.5em;
            margin-bottom: 5px;
        }
        header p {
            font-size: 1.2em;
            margin-top: 0;
        }
        .container {
            width: 85%;
            margin: 30px auto;
        }
        section {
            margin-bottom: 40px;
        }
        .section-title {
            font-size: 1.5em;
            color: #005b8c;
            margin-bottom: 15px;
            border-bottom: 3px solid #005b8c;
            padding-bottom: 5px;
        }
        .section-description {
            font-size: 1.1em;
            color: #555;
            margin-bottom: 20px;
        }
      
   
        footer {
            background-color: #003f5c;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 0.9em;
            margin-top: 40px;
        }
    </style>

                                <div class="section-title">Alfa HP Tecnologia</div><p class="section-description" style="text-align: justify;">Somos pioneiros em energia solar na região central do Estado de São Paulo, trabalhando com soluções fotovoltaicas desde 2015.<br><br>
Buscamos em nossos projetos a compreensão das necessidades do cliente e oferecemos soluções especialmente desenhadas para satisfazer estas necessidades.<br><br>
Trabalhamos com sistemas fotovoltaicos ON-GRID, OFF-GRID e HIBRIDOS, sempre procurando o melhor custo benefício para cada cliente.<br><br>
Fazer um investimento em Energia Solar, além de proporcionar uma redução de custo significativa em sua conta de energia, também traz a vantagem de nos tornarmos colaboradores para um mundo mais sustentável ecologicamente, pois na medida em que a fonte de energia solar vai sendo mais utilizada, outras fontes de energia como a queima de carvão e gás vão sendo menos necessárias e assim todos contribuímos para a diminuição da emissão de poluentes.
<br><br>
</p><h6>VENHA CONHECER NOSSA SEDE: RUA NAPOLEÃO SELMI DEI 918 VILA HARMONIA - ARARAQUARA SP</h6><p><br></p><p><br></p>
<div class="section-title">Proposta Comercial</div>
                                ';
                            }else{
                                $introducao = $estimate_info->introducao;
                            }
                            echo form_textarea(array(
                                "id" => "introducao",
                                "name" => "introducao",
                                "value" => process_images_from_content($introducao, false),
                                "placeholder" => "Introdução",
                                "class" => "form-control wysiwyg-editor"
                            ));
                            ?>
                        </div>
                    </div>

                               

                  

                </div>
                   

               
                   
                <div>
        
        <button type="submit" class="btn btn-primary"><?php echo app_lang('save'); ?></button>
    </div>
                
            </div>

            <?php echo form_close(); ?>
        </div> 
    </div> 
</div>
</div> 
</div> 
</div>
 </div> 
                      

<!-- FIM TABPANEL INTRODUÇÃO -->

<!-- INICIO TABPANEL OBS-->

<div role="tabpanel" class="tab-pane fade" id="observacao-section">
         <div class="mt15">
            <div class="card p15 b-t">
            <h2>Observações da Proposta</h2>
                <div class="clearfix p20">
                <div id="page-content" class="page-wrapper clearfix">
    <div class="card view-container">
        <div id="help-dropzone" class="post-dropzone">
            <?php echo form_open(get_uri("estimates/save_observacao"), array("id" => "decricao-form", "class" => "general-form", "role" => "form")); ?>

            <div>

                

                <div class="card-body">
                <input type="hidden" name="estimate_id" value="<?php echo $estimate_info->id; ?>" />

                    <div class="form-group">

                        <div class=" col-md-12">
                            <?php
                            if (empty($estimate_info->observacao)){
                                $observacao = '
                                <section>
        <div class="section-title">Observações Importantes</div>
        <p class="section-description">Por favor, leia atentamente as observações abaixo para evitar qualquer mal-entendido:</p>
        <ul>
            <li>O valor total pode sofrer alterações dependendo de possíveis ajustes no escopo do projeto.</li>
            <li>Este orçamento é válido por 30 dias a partir da data de emissão.</li>
            <li>Os pagamentos deverão ser realizados em até 3 parcelas, conforme acordado previamente.</li>
        </ul>
    </section>
                                ';
                            }else{
                                $observacao = $estimate_info->observacao;
                            }
                            echo form_textarea(array(
                                "id" => "observacao",
                                "name" => "observacao",
                                "value" => process_images_from_content($observacao, false),
                                "placeholder" => "Introdução",
                                "class" => "form-control wysiwyg-editor"
                            ));
                            ?>
                        </div>
                    </div>

                               

                  

                </div>
                   

               
                   
                <div>
        
        <button type="submit" class="btn btn-primary"><?php echo app_lang('save'); ?></button>
    </div>
                
            </div>

            <?php echo form_close(); ?>
        </div> 
    </div> 
</div>
</div> 
</div> 
</div>
 </div> 
                      

<!-- FIM TABPANEL OBS -->

<!-- INICIO TABPANEL PAGAMENTO-->

<div role="tabpanel" class="tab-pane fade" id="pagamento-section">
         <div class="mt15">
            <div class="card p15 b-t">
                <div class="clearfix p20">
                <div id="page-content" class="page-wrapper clearfix">
    <div class="card view-container">
        <div id="help-dropzone" class="post-dropzone">
            <?php echo form_open(get_uri("estimates/save_pagamento"), array("id" => "decricao-form", "class" => "general-form", "role" => "form")); ?>

            <div>

                

                <div class="card-body">
                <input type="hidden" name="estimate_id" value="<?php echo $estimate_info->id; ?>" />

                    <div class="form-group">

                        <div class=" col-md-12">
                            <?php
                            if (empty($estimate_info->pagamento)){
                                $pagamento = '
       <section>
        <div class="section-title">Formas de Pagamento</div>
        <p class="section-description">A seguir, apresentamos as opções de pagamento para a proposta:</p>
        <ul>
            <li>Transferência bancária para a conta XYZ Ltda.</li>
            <li>Parcelamento em até 3x no cartão de crédito sem juros.</li>
            <li>Pagamento via boleto bancário, com vencimento de 7 dias após a aprovação.</li></ul><p><br></p><ul>
        </ul>
       
    </section>
                                ';
                            }else{
                                $pagamento = $estimate_info->pagamento;
                            }

                            echo form_textarea(array(
                                "id" => "pagamento",
                                "name" => "pagamento",
                                "value" => process_images_from_content($pagamento, false),
                                "placeholder" => "Introdução",
                                "class" => "form-control wysiwyg-editor"
                            ));
                            ?>
                        </div>
                    </div>

                               

                  

                </div>
                   

               
                   
                <div>
        
        <button type="submit" class="btn btn-primary"><?php echo app_lang('save'); ?></button>
    </div>
                
            </div>

            <?php echo form_close(); ?>
        </div> 
    </div> 
</div>
</div> 
</div> 
</div>
 </div> 
                      

<!-- FIM TABPANEL PAGAMENTO -->

<!-- INICIO TABPANEL CONFIGURACAO-->

<div role="tabpanel" class="tab-pane fade" id="configuracao-section">
         <div class="mt15">
            
            <div class="card p15 b-t">
            <h3> Configurações </h3>
                <div class="clearfix p20">
                <div id="page-content" class="page-wrapper clearfix">
                
    <div class="card view-container">
        <div id="help-dropzone" class="post-dropzone">
            <?php echo form_open(get_uri("estimates/save_config"), array("id" => "decricao-form", "class" => "general-form", "role" => "form")); ?>

            <div>

                

                <div class="card-body">
                <input type="hidden" name="estimate_id" value="<?php echo $estimate_info->id; ?>" />

                <div class="form-group">
                   <h5> Percentuais </h5>
            <div class="row">
                
                <div class="col-md-3">
                <label for="imposto_produto" >Imposto Produtos</label>
                <div class="input-group">
                        <span class="input-group-text">%</span>
                    <?php
                    echo form_input(array(
                        "id" => "imposto_produto",
                        "name" => "imposto_produto",
                        "value" => $estimate_info->imposto_produto ? to_decimal_format($estimate_info->imposto_produto) : "",
                        "class" => "form-control",
                        "placeholder" => "",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
                </div>

                
                <div class="col-md-3">
                <label for="imposto_servico">Imposto Serviços</label>
                <div class="input-group">
                        <span class="input-group-text">%</span>
                    <?php
                    echo form_input(array(
                        "id" => "imposto_servico",
                        "name" => "imposto_servico",
                        "value" => $estimate_info->imposto_servico ? to_decimal_format($estimate_info->imposto_servico) : "",
                        "class" => "form-control",
                        "placeholder" => "",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
                </div>

                                <div class="col-md-3">
                    <label for="comissao_venda">Comissão de Venda</label>
                    <div class="input-group">
                        <span class="input-group-text">%</span>
                        <?php
                        echo form_input(array(
                            "id" => "comissao_venda",
                            "name" => "comissao_venda",
                            "value" => $estimate_info->comissao_venda ? to_decimal_format($estimate_info->comissao_venda) : "",
                            "class" => "form-control",
                            "placeholder" => "",
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>

               

            </div>
            <hr>

            <div class="form-group">
                   <h5> Impressão </h5>
            <div class="row">
                
                <div class="col-md-12">
                <label for="impressao_total" ></label>
                    <?php
                    $option1 = false; // Define como false por padrão
                    $option2 = false; // Define como false por padrão
                    
                    // Determina qual opção deve estar marcada
                    if ($estimate_info->impressao_total == 0) {
                        $option2 = true; // Marcar a opção 2
                    } else {
                        $option1 = true; // Marcar a opção 1
                    }
                    
                    echo form_radio(array(
                        "id" => "option_2",
                        "name" => "impressao_total",
                        "value" => "1",
                        "checked" => $option1,
                        
                        "class" => "form-check-input",
                    ));
                    
                    echo form_label("Imprimir todos os valores", "option_2", array("class" => "form-check-label col-md-4"));

                    echo form_radio(array(
                        "id" => "option_1",
                        "name" => "impressao_total",
                        "value" => "0",
                        "checked" => $option2,
                        "class" => "form-check-input ",
                    ));
                    
                    echo form_label("Imprimir somente o valor total", "option_1", array("class" => "form-check-label col-md-4"));
                    ?>

                    
                </div>

                
               
               

            </div>
            <hr>
        </div>

                               

                  

                </div>
                   

               
                   
                <div>
        
        <button type="submit" class="btn btn-primary"><?php echo app_lang('save'); ?></button>
    </div>
                
            </div>

            <?php echo form_close(); ?>
        </div> 
    </div> 
</div>
</div> 
</div> 
</div>
 </div> 
                      

<!-- FIM TABPANEL CONFIGURACAO -->