
<div style=" margin: auto;">
    <?php
    $color = get_setting("estimate_color");
    if (!$color) {
        $color = get_setting("invoice_color") ? get_setting("invoice_color") : "#2AA384";
    }
    $style = get_setting("invoice_style");
    ?>
    <?php

    
  
    $data = array(
        "client_info" => $client_info,
        "color" => $color,
        "estimate_info" => $estimate_info
    );
    if ($style === "style_2") {
        echo view('estimates/estimate_parts/header_style_2.php', $data);
    } else {
        echo view('estimates/estimate_parts/header_style_1.php', $data);
    }

    if(!empty($estimate_info->introducao)){
        echo ($estimate_info->introducao);
    }
    

    $discount_row = '<tr>
                        <td colspan="3" style="text-align: right;">' . app_lang("discount") . '</td>
                        <td style="text-align: right; width: 20%; border: 1px solid #fff; background-color: #f4f4f4;">' . to_currency($estimate_total_summary->discount_total, $estimate_total_summary->currency_symbol) . '</td>
                    </tr>';

    $total_after_discount_row = '<tr>
                                    <td colspan="3" style="text-align: right;">' . app_lang("total_after_discount") . '</td>
                                    <td style="text-align: right; width: 20%; border: 1px solid #fff; background-color: #f4f4f4;">' . to_currency($estimate_total_summary->estimate_subtotal - $estimate_total_summary->discount_total, $estimate_total_summary->currency_symbol) . '</td>
                                </tr>';
    ?>
</div>

<br />



<?php
// Ordenar os itens pela etapa antes de exibir
usort($estimate_items, function ($a, $b) {
    return strcmp($a->nome_etapa, $b->nome_etapa);
});
?>
<?php if($estimate_info->impressao_total == 1) {?>
<div class="section-title">Proposta Comercial</div> 
<table class="table-responsive" style="width: 100%; color: #444;">
    <tr style="font-weight: bold; background-color: <?php echo $color; ?>; color: #fff;">
        <th style="display: none; width: 45%; border-right: 1px solid #eee;">Etapa</th>
        <th style="width: 45%; border-right: 1px solid #eee;"> <?php echo app_lang("item"); ?> </th>
        <th style="text-align: center; width: 15%; border-right: 1px solid #eee;"> <?php echo app_lang("quantity"); ?></th>
        <th style="text-align: right; width: 20%; border-right: 1px solid #eee;"> Preço Unitario</th>
        <th style="text-align: right; width: 20%;"> <?php echo app_lang("total"); ?></th>
    </tr>
    <?php
    $current_step = null; // Variável para rastrear a etapa atual
    $total = 0;
    foreach ($estimate_items as $item) {
        // Verifica se a etapa mudou
        if ($current_step !== $item->nome_etapa) {
           
            
            $current_step = $item->nome_etapa; // Atualiza a etapa atual
            ?>
            <!-- Exibe o nome da etapa -->
            <tr>
                <td colspan="5" style="background-color: #e6f7ff; width: 100%; color: #005b8c; font-weight: bold; padding: 10px; text-transform: uppercase;">
                    <?php echo $current_step; ?>
                </td>
            </tr>
            <?php
        }
        ?>
        <!-- Exibe os itens da etapa -->
        <tr style="background-color: #f4f4f4;">
            <td style="display: none; width: 45%; border: 1px solid #fff; padding: 10px;"><?php echo $item->nome_etapa; ?></td>
            <td style="width: 45%; border: 1px solid #fff; padding: 10px;">
                <?php echo $item->title; ?>
                <br />
                
            </td>
            <td style="text-align: center; width: 15%; border: 1px solid #fff;">
                <?php echo $item->quantity; ?>
            </td>
            <td style="text-align: right; width: 20%; border: 1px solid #fff;">
               <?php echo to_currency(($item->p_total / $item->quantity), $item->currency_symbol); ?>
            </td>
            <td style="text-align: right; width: 20%; border: 1px solid #fff;">
                <?php echo to_currency($item->p_total, $item->currency_symbol); ?>
            </td>
        </tr>
        
    <?php 

    $total = $item->p_total + $total;
        } ?>
    <?php
    if ($estimate_total_summary->discount_total && $estimate_total_summary->discount_type == "after_tax") {
        echo $discount_row;
    }
    ?> 
    <tr>
        <td colspan="3" style="text-align: right;"><?php echo app_lang("total"); ?></td>
        <td style="text-align: right; width: 20%; background-color: <?php echo $color; ?>; color: #fff;">
            <?php echo to_currency($total); ?>
        </td>
    </tr>
</table>
<?php } else {?>

  <div class="section-title">Proposta Comercial</div> 
<table class="table-responsive" style="width: 100%; color: #444;">
    <tr style="font-weight: bold; background-color: <?php echo $color; ?>; color: #fff;">
        <th style="display: none; width: 45%; border-right: 1px solid #eee;">Etapa</th>
        <th style="width: 85%; border-right: 1px solid #eee;"> <?php echo app_lang("item"); ?> </th>
        <th style="text-align: center; width: 15%; border-right: 1px solid #eee;"> <?php echo app_lang("quantity"); ?></th>
       
    </tr>
    <?php
    $current_step = null; // Variável para rastrear a etapa atual
    $total = 0;
    foreach ($estimate_items as $item) {
        // Verifica se a etapa mudou
        if ($current_step !== $item->nome_etapa) {
            
            $current_step = $item->nome_etapa; // Atualiza a etapa atual
            ?>
            <!-- Exibe o nome da etapa -->
            <tr>
                <td colspan="5" style="background-color: #e6f7ff; width: 100%; color: #005b8c; font-weight: bold; padding: 10px; text-transform: uppercase;">
                    <?php echo $current_step; ?>
                </td>
            </tr>
            <?php
        }
        ?>
        <!-- Exibe os itens da etapa -->
        <tr style="background-color: #f4f4f4;">
            <td style="display: none; width: 45%; border: 1px solid #fff; padding: 10px;"><?php echo $item->nome_etapa; ?></td>
            <td style="width: 85%; border: 1px solid #fff; padding: 10px;">
                <?php echo $item->title; ?>
                <br />
                
            </td>
            <td style="text-align: center; width: 15%; border: 1px solid #fff;">
                <?php echo $item->quantity . " " . $item->unit_type; ?>
            </td>
            
        </tr>
    <?php 
        $total = $item->p_total + $total;
        } ?>
    <?php
    if ($estimate_total_summary->discount_total && $estimate_total_summary->discount_type == "after_tax") {
        echo $discount_row;
    }
    ?> 
    <tr>
        <td colspan="0" style="text-align: right;"><?php echo app_lang("total"); ?></td>
        <td style="text-align: right; width: 15%; background-color: <?php echo $color; ?>; color: #fff;">
            <?php echo to_currency($total); ?>
        </td>
    </tr>
</table>




<?php

}
if ($estimate_info->note) { ?>
    <br />
    <br />
    <div style="border-top: 2px solid #f2f2f2; color:#444; padding:0 0 20px 0;"><br /><?php echo nl2br($estimate_info->note); ?></div>
<?php } else { ?><!-- use table to avoid extra spaces -->
    <br /><br /><table class="invoice-pdf-hidden-table" style="border-top: 2px solid #f2f2f2; margin: 0; padding: 0; display: block; width: 100%; height: 10px;"></table>
<?php } ?>

<span style="color:#444; line-height: 14px;">
    <?php echo get_setting("estimate_footer"); ?>
</span>

<?php

if(!empty($estimate_info->descricao)){
    echo ($estimate_info->descricao);
}


if(!empty($estimate_info->observacao)){
    echo ($estimate_info->observacao);
}
    
if(!empty($estimate_info->pagamento)){
    echo ($estimate_info->pagamento);
}

?>
