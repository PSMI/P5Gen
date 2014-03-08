<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div align="right">
    <?php echo CHtml::textField("txtSearch",'',array('class'=>'span4')); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'submit',
            'type'=>'primary',
            'icon'=>'icon-search',
            'label'=>'Search', 
            'htmlOptions'=>array(
                'id'=>'btnSearch', 
                'name'=>'btnSearch',
                'style'=>'margin-top:-10px;'
            )
        )); ?>
</div>