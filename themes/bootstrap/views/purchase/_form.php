<?php

/*
 * @author : owliber
 * @date : 2014-03-30
 */
?>
<?php
Yii::app()->clientScript->registerScript('ui','
         
     $(\'input[rel="tooltip"]\').tooltip();     
     var quantity = $("#quantity"),
         product_id = $("#product_id"),
         distributor_id = $("#distributor_id"),
         payment_type_id = $("#payment_type_id");
             
 ', CClientScript::POS_END);
?>

<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'purchase-modal')); ?>
 
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Add Items</h4>
</div>
 
<div class="modal-body">
    <?php echo CHtml::hiddenField('distributor_id',  Yii::app()->session['distributor_id']); ?>
    <?php echo CHtml::label('Product', 'product_id'); ?>
    <?php echo CHtml::dropDownList('product_id', '',ProductsForm::listProducts(), array('class'=>'span3')); ?>
    <?php echo CHtml::label('Quantity', 'quantity'); ?>
    <?php echo CHtml::textField('quantity',1,array('style'=>'text-align:right','class'=>'span1','tooltip'=>'Quantity')); ?>
    <?php echo CHtml::label('Payment Type', 'payment_type'); ?>
    <?php echo CHtml::dropDownList('payment_type_id', '',  ReferenceModel::list_payment_types(), array('class'=>'span2')); ?>
</div>
 
<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'ajaxButton',
        'type'=>'primary',
        'label'=>'Purchase Item',
        'url'=>  Yii::app()->createUrl('purchase/additem',array(
            'product_id'=>'js:function(){return product_id.val()}',
            'quantity'=>'js:function(){return quantity.val()}',
            'distributor_id'=>'js:function(){return distributor_id.val()}',
            'payment_type_id'=>'js:function(){return payment_type_id.val()}'
        )),
        'ajaxOptions'=>array(
            'type' => 'GET',
            'dataType'=>'json',
            'url' => 'js:$(this).attr("href")',
            'success' => 'function(data){
                if(data["result_code"] == 0)
                {
                    $("#purchase-modal").modal("hide");     
                    $("#search-form").submit();
                }
                else
                {
                    alert(data["result_msg"]);
                }
             }',
            'update'=>'#product-grid',
        ),
    )); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Cancel',
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>
 
<?php $this->endWidget(); ?>