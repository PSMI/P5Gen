<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div align="right">
    <?php echo CHtml::textField("txtSearch"); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Search', 'htmlOptions'=>array('id'=>'btnSearch', 'name'=>'btnSearch'))); ?>
</div>