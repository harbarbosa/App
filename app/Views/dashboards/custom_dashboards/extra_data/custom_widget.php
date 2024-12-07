<?php
$border_class = "";
if ($widget_info->show_border) {
    $border_class = "bg-white p15";
}
?>

<div class="custom-widget mb20">

    <?php if ($widget_info->show_title) { ?>
        <div class="custom-widget-title">
            <?php echo $widget_info->title; ?>
        </div>
    <?php } ?>

    <div class="<?php echo $border_class ?>"> 
        <?php 

            if($widget_info->html){

                $conteudo = html_entity_decode($widget_info->html);
                ob_start(); // Inicia o buffer de saída
                eval('?>' . $conteudo); // Executa o PHP
                echo ob_get_clean(); // Exibe o resultado e limpa o buffer

            }else{
                echo process_images_from_content($widget_info->content); 
                    }


        
        
        ?>
    </div>

</div>