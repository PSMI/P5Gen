<?php

/*
 * @author : owliber
 * @date : 2014-03-30
 */
?>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type'=>'horizontal',
        'id' => 'purchase-form',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ), 
        'htmlOptions'=>array('class'=>'well')
    ));
?>

<div class="span9">
    <div class="pull-left">
    <?php echo '<h4>'.$member['last_name'] . ', ' . $member['first_name'] . ' ' . $member['middle_name'] . '</h4>'; ?>
    </div>
    <div class="pull-right">
        <table class="items table table-bordered table-condensed">
            <tr>
                <td style="text-align:right">Total Amount</td>
                <td style="text-align:right; font-size:20px;"><?php echo $totals['total_amount']; ?></td>
            </tr>
            <tr>
                <td style="text-align:right">Total Savings</td>
                <td style="text-align:right; font-size:20px;"><?php echo $totals['total_savings']; ?></td>
            </tr>
            <tr>
                <td style="text-align:right">Quantity</td>
                <td style="text-align:right; font-size:20px;"><?php echo $totals['total_quantity']; ?></td>
            </tr>
        </table>
    </div>
</div>
<?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
    'buttons'=>array(
        array(
            'label'=>'Add Items',
            'url'=>'#',
            'icon'=>'icon-plus-sign',
            'htmlOptions'=>array(
                'data-toggle'=>'modal',
                'data-target'=>'#purchase-modal',
            ),
        ),
        array(
            'label'=>'Purchase History', 
            'url'=>  Yii::app()->createUrl('purchase/history',array('id'=>Yii::app()->session['member_id'])),
            'icon'=>'icon-shopping-cart',
            'buttonType'=>'link',
        ),
    ),
)); ?>
<?php $this->renderPartial('_form',array('model'=>$model)); ?>
<?php $this->renderPartial('_lists',array('dataProvider'=>$dataProvider)); ?>
<?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
    'buttons'=>array(
        array(
            'label'=>'Checkout',
            'type'=>'primary',
            'icon'=>'icon-check',            
            'htmlOptions'=>array(
                'data-toggle'=>'modal',
                'data-target'=>'#checkout-modal',
            ),
        ),
        array(
            'buttonType'=>'ajaxButton',
            'label'=>'Cancel Cart',
            'icon'=>'icon-shopping-cart',
            'htmlOptions'=>array(
                'confirm'=>'Are you sure you want cancel?',
            ),
            'url'=>  Yii::app()->createUrl('purchase/cancelcart',array(
                'member_id'=>'js:function(){return member_id.val()}',
                'purchase_summary_id'=>'js:function(){return $("#purchase_summary_id").val()}'
            )),
            'ajaxOptions'=>array(
                'type' => 'GET',
                'dataType'=>'json',
                'url' => 'js:$(this).attr("href")',
                'success' => 'function(data){
                    if(data["result_code"] == 0)
                    {
                        $("#result_title").html("Cancel Cart");
                        $("#result_msg").html(data["result_msg"]);
                        $("#message-modal").modal("show"); 
                    }
                    else
                    {
                        alert(data["result_msg"]);
                    }
                 }',
            ),
        ),
    ),
)); ?>
<?php $this->endWidget(); ?>

<!-- Message Dialog -->
<?php $this->beginWidget('bootstrap.widgets.TbModal', array(
        'id'=>'message-modal',
        'autoOpen'=>false,
        'fade'=>true
     )); ?>
 
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4 id="result_title"></h4>
</div>
 
<div class="modal-body">
    <p id="result_msg"></p>
</div>
 
<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Close',
        'url'=>  Yii::app()->createUrl('purchase/clearsession'),
//        'htmlOptions'=>array(
//            'onclick'=>'$("#search-form").submit();'
//        ),
    )); ?>
</div> 
<?php $this->endWidget(); ?>

<?php
Yii::app()->clientScript->registerScript('ui','
         
     var distributor_id = $("#Update_distributor_id"),
         purchase_id = $("#Update_purchase_id"),
         product_id = $("#Update_product_id"),
         qty = $("#Update_qty"),
         payment_type_id = $("#Update_payment_type_id");
             
 ', CClientScript::POS_END);
?>

<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'purchase-update-modal')); ?>
 
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Update Cart</h4>
</div>
 
<div class="modal-body">
    <?php echo CHtml::hiddenField('Update_distributor_id'); ?>
    <?php echo CHtml::hiddenField('Update_purchase_id'); ?>
    <?php echo CHtml::hiddenField('Update_product_id'); ?>
    <?php echo CHtml::label('Product', 'Update_products'); ?>
    <?php echo CHtml::dropDownList('Update_products', '',ProductsForm::listProducts(), array('class'=>'span3','disabled'=>'disabled')); ?>
    <?php echo CHtml::label('Quantity', 'Update_qty'); ?>
    <?php echo CHtml::textField('Update_qty','',array('style'=>'text-align:right','class'=>'span1','tooltip'=>'Quantity')); ?>
    <?php echo CHtml::label('Payment Type', 'Update_payment_type_id'); ?>
    <?php echo CHtml::dropDownList('Update_payment_type_id', '',  ReferenceModel::list_payment_types(), array('class'=>'span2')); ?>
</div>
 
<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'ajaxButton',
        'type'=>'primary',
        'label'=>'Update Cart',
        'icon'=>'icon-shopping-cart',
        'url'=>  Yii::app()->createUrl('purchase/updatecart'),
        'ajaxOptions'=>array(
            'type' => 'GET',
            'data'=>array(
                'purchase_id'=>'js:function(){return purchase_id.val()}',
                'product_id'=>'js:function(){return product_id.val()}',
                'quantity'=>'js:function(){return qty.val()}',
                'distributor_id'=>'js:function(){return distributor_id.val()}',
                'payment_type_id'=>'js:function(){return payment_type_id.val()}'
            ),
            'dataType'=>'json',
            'url' => 'js:$(this).attr("href")',
            'success' => 'function(data){
                if(data["result_code"] == 0)
                {
                    $("#purchase-update-modal").modal("hide");     
                    $("#result_title").html("Update Item");
                    $("#result_msg").html(data["result_msg"]);
                    $("#message-modal").modal("show"); 
                }
                else
                {
                    alert(data["result_msg"]);
                }
             }',
        ),
    )); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Cancel',
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>
 
<?php $this->endWidget(); ?>

<?php
Yii::app()->clientScript->registerScript('ui','
         
     var member_id = $("#member_id"),
         purchase_summary_id = $("#purchase_summary_id"),
         receipt_no = $("#receipt_no"),
         payment_type_id = $("#payment_type_id");
      
     $( "#target" ).submit(function( event ) {
        alert( "Handler for .submit() called." );
        event.preventDefault();
    });

     $( "#other" ).click(function() {
        $( "#target" ).click();
    });
             
 ', CClientScript::POS_END);
?>

<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'checkout-modal')); ?>
 
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Checkout</h4>
</div>
 
<div class="modal-body">
    <?php echo CHtml::hiddenField('member_id'); ?>
    <?php echo CHtml::hiddenField('purchase_summary_id'); ?>
    <?php echo CHtml::label('Receipt No', 'receipt_no'); ?>
    <?php echo CHtml::textField('receipt_no','',array('style'=>'text-align:right','class'=>'span2','tooltip'=>'Enter receipt number')); ?>
    <?php echo CHtml::label('Payment Type', 'payment_type_id'); ?>
    <?php echo CHtml::dropDownList('payment_type_id', '',  ReferenceModel::list_payment_types(), array('class'=>'span2')); ?>
</div>
 
<div class="modal-footer">
<?php $this->widget('bootstrap.widgets.TbButton', array(
        'id'=>'btn-checkout',
        'label'=>'Checkout',
        'url'=>  Yii::app()->createUrl('purchase/checkout'),
        'type'=>'primary',
        'buttonType'=>'ajaxButton',
        'icon'=>'icon-check',            
        'htmlOptions'=>array(
            'confirm'=>'Are you sure you want continue purchasing?',           
        ),
        'ajaxOptions'=>array(
            'type' => 'GET',
            'dataType'=>'json',
            'data'=>array(
                'member_id'=>'js:function(){return member_id.val()}',
                'purchase_summary_id'=>'js:function(){return purchase_summary_id.val()}',
                'receipt_no'=>'js:function(){return receipt_no.val()}',
                'payment_type_id'=>'js:function(){return payment_type_id.val()}'
            ),
            'url' => 'js:$(this).attr("href")',
            'success' => 'function(data){
                if(data["result_code"] == 0)
                {
                    $("#result_title").html("Checkout");
                    $("#result_msg").html(data["result_msg"]);
                    $("#checkout-modal").modal("hide"); 
                    $("#message-modal").modal("show");   
                }
                else
                {
                    alert(data["result_msg"]);
                }
             }',
        ),
    )); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Cancel',
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>
 
<?php $this->endWidget(); ?>





