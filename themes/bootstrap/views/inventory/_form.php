<?php

/*
 * @author : owliber
 * @date : 2014-03-30
 */
?>
<?php Yii::app()->clientScript->registerScript('ui','
         
     //$(\'input[rel="tooltip"]\').tooltip();     
     var discount_type = $("#Inventory_discount_type"),
         discount_amount = $("#Inventory_discount_amount"),
         discount_percent = $("#Inventory_discount_percent");
     
    $("#Inventory_discount_type").change(function() {
        if(discount_type.val() == 1)
        {
            discount_amount.attr("readonly", true);
            discount_percent.attr("readonly", false);
        }
        else
        {
            discount_percent.attr("readonly", true);
            discount_amount.attr("readonly", false);
        }
            
    });
     
    
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
<?php echo $form->dropDownListRow($model,'discount_type', array('1'=>'By percentage','2'=>'By fixed amount'), array('class'=>'span2')); ?>
<?php echo $form->textFieldRow($model,'discount_percent',array('class'=>'span2','style'=>'text-align:right')); ?>
<?php echo $form->textFieldRow($model,'discount_amount',array('class'=>'span2','style'=>'text-align:right;','readonly'=>'readonly')); ?>
<?php echo $form->dropDownListRow($model,'status', array('1'=>'Active','2'=>'Inactive'), array('class'=>'span2')); ?>
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
