<?php

/*
 * @author : owliber
 * @date : 2014-03-30
 */
?>
<?php Yii::app()->clientScript->registerScript('ui','
         
     $(\'input[rel="tooltip"]\').tooltip();                 
    
 ', CClientScript::POS_END);
 ?>
<?php $this->breadcrumbs = array('Administration'=>'#',
    'Inventory'=>'#','Add new product'
);?>
<h3>Add new product</h3>
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type'=>'vertical',
        'id' => 'product-form',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),        
        'htmlOptions'=>array('class'=>'well'),
    ));
?>
    
<?php echo $form->textFieldRow($model,'product_code',array('class'=>'span2')); ?>
<?php echo $form->textFieldRow($model,'product_name',array('class'=>'span3')); ?>
<?php echo $form->textFieldRow($model,'amount',array('class'=>'span2','style'=>'text-align:right')); ?>
<?php echo $form->textFieldRow($model,'ibo_discount',array('class'=>'span2','style'=>'text-align:right')); ?>
<?php echo $form->textFieldRow($model,'ipd_discount',array('class'=>'span2','style'=>'text-align:right;')); ?>
<?php //echo $form->dropDownListRow($model,'status', array('1'=>'Active','2'=>'Inactive'), array('class'=>'span2')); ?>
 <br />
 
 <?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
    'buttons'=>array(
        array(
            'buttonType'=>'submit',
            'label'=>'Add Product',
            'type'=>'primary',
        ),
        array(
            'label'=>'Product Lists',
            'icon'=>'icon-list-alt',
            'url'=>  Yii::app()->createUrl('inventory/index'),
        )
    ),
)); ?>

<?php $this->endWidget(); ?>

 <?php $this->beginWidget('bootstrap.widgets.TbModal', array(
        'id'=>'message-modal',
        'autoOpen'=>$this->show_dialog,
        'fade'=>true
     )); ?>
 
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Add new product</h4>
</div>
 
<div class="modal-body">
    <p><?php echo $this->dialog_message; ?></p>
</div>
 
<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Close',
        'url'=>Yii::app()->createUrl('inventory/index'),
    )); ?>
</div>
 
<?php $this->endWidget(); ?>
