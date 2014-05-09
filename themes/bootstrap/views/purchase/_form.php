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
         product_name = $("#PurchasesModel_product_name"),
         purchaser_id = $("#purchaser_id"),
         payment_type_id = $("#payment_type_id");
             
 ', CClientScript::POS_END);
?>

<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'purchase-modal')); ?>
 
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Add to Cart</h4>
</div>
 
<div class="modal-body">
    <?php echo CHtml::hiddenField('purchase_summary_id',  $model->purchase_summary_id); ?>
    <?php echo CHtml::hiddenField('purchaser_id', $model->member_id); ?>
    <?php echo CHtml::hiddenField('product_id'); ?>
    <?php echo CHtml::label('Product', 'product_name'); ?>
    <?php //echo CHtml::dropDownList('product_id', '',ProductsForm::listProducts(), array('class'=>'span3')); 
        $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
                'model'=>$model,
                'attribute'=>'product_name',
                //'id'=>'product_name',
                'sourceUrl'=>  Yii::app()->createUrl('purchase/products'),
                'options'=>array(
                    'minLength'=>'2',
                    'showAnim'=>'fold',
                    'focus' => 'js:function(event, ui){$("#PurchasesModel_product_name").val(ui.item["value"])}',
                    'select' => 'js:function(event, ui){$("#product_id").val(ui.item["id"]); }',
                ),
                'htmlOptions'=>array(
                    'class'=>'span3',
                    'rel'=>'tooltip',
                    'title'=>'Please type the product code or name.',
                    'autocomplete'=>'off',
                    //'onblur'=>'validateUpline()'
                ),        
            ));
    ?>
    <?php echo CHtml::label('Quantity', 'quantity'); ?>
    <?php echo CHtml::textField('quantity',1,array('style'=>'text-align:right','class'=>'span1','tooltip'=>'Quantity')); ?>
    <?php echo CHtml::label('Payment Type', 'payment_type'); ?>
    <?php echo CHtml::dropDownList('payment_type_id', '',  ReferenceModel::list_payment_types(), array('class'=>'span2')); ?>
</div>
 
<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'ajaxButton',
        'type'=>'primary',
        'icon'=>'icon-shopping-cart',
        'label'=>'Add to Cart',
        'url'=>  Yii::app()->createUrl('purchase/addtocart',array(
            'product_id'=>'js:function(){return product_id.val()}',
            'quantity'=>'js:function(){return $("#quantity").val()}',
            'purchaser_id'=>'js:function(){return purchaser_id.val()}',
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
            //'update'=>'#product-grid',
        ),
    )); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Cancel',
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>

<?php $this->endWidget(); ?>

