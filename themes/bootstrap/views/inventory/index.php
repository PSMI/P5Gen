<?php

/*
 * @author : owliber
 * @date : 2014-03-30
 */
?>

<?php
Yii::app()->clientScript->registerScript('ui','
         
     //var product_id = $("#product_id");
         
 ', CClientScript::POS_END);
?>

<?php $this->breadcrumbs = array('Administration'=>'#',
    'Inventory'=>'#','Product Lists'
);?>

<h3>Product Lists</h3>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type'=>'search',
        'id' => 'index-form',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),        
    ));
?>

<?php echo $form->textFieldRow($model, 'search',array('class'=>'input-medium span3', 'prepend'=>'<i class="icon-search"></i>')); ?>

<?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
    'buttons'=>array(
        array(
            'buttonType'=>'submit',
            'type'=>'primary',
            'label'=>'Search', 
        ),
        array(
            'label'=>'Add new product',
            'icon'=>'icon-plus-sign',
            'url'=>  Yii::app()->createUrl('inventory/addproduct'),
        )
    ),
)); ?>

<?php $this->endWidget(); ?>

<?php $this->beginWidget('bootstrap.widgets.TbGridView', array(
    'id'=>'product-grid',
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'enablePagination' => true,
    'columns'=>array(
        array(
                'header' => '',
                'value' => '$row + ($this->grid->dataProvider->pagination->currentPage
                * $this->grid->dataProvider->pagination->pageSize + 1)',
            ),
        array('name'=>'product_code', 
                'header'=>'Product Code',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('name'=>'product_name', 
                'header'=>'Product Name',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'amount', 
                'header'=>'Amount',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
            ),
        array('name'=>'ibo_discount', 
                'header'=>'IBO Discount (%)',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
            ),
        array('name'=>'ipd_discount', 
                'header'=>'IPD Discount (%)',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('name'=>'status', 
                'header'=>'Status',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('class'=>'bootstrap.widgets.TbButtonColumn',
                'template'=>'{update}',
                'buttons'=>array
                (
                    'update'=>array
                    (
                        'label'=>'Update Item',
                        'icon'=>'icon-edit',
                        'url'=>'Yii::app()->createUrl("/inventory/getvalues", array("id" =>$data["product_id"]))',
                        'options' => array(
                            'class'=>"btn btn-small",
                            'ajax' => array(
                                'type' => 'GET',
                                'dataType'=>'json',
                                'url' => 'js:$(this).attr("href")',
                                'success' => 'function(data){
                                    $.each(data, function(name,val){
                                       $("#product_id").val(val.product_id);
                                       $("#product_code").val(val.product_code);
                                       $("#product_name").val(val.product_name);
                                       $("#amount").val(val.amount);
                                       $("#ibo_discount").val(val.ibo_discount);
                                       $("#ipd_discount").val(val.ipd_discount);
                                       $("#status").val(val.status);
                                    });
                                    $("#product-update-dialog").modal("show");
                                 }',
                            ),

                        ),
                        array('id' => 'send-link-'.uniqid())
                    ),
                ),
                'header'=>'Action',
                'htmlOptions'=>array('style'=>'width:80px;text-align:center'),
            ),
    ),
)); ?>

<?php $this->endWidget(); ?>

<form name="product-form" id="product-form" method="post" class="form-horizontal">
<?php $this->beginWidget('bootstrap.widgets.TbModal', 
        array('id'=>'product-update-dialog',
              'autoOpen'=>false,
              'fade'=>true,
)); ?>
 
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Update Product</h4>
</div>

<div class="modal-body">
    <?php echo CHtml::hiddenField('product_id'); ?>
    <?php echo CHtml::label('Product Code', 'product_code') ?>
    <?php echo CHtml::textField('product_code','',array('class'=>'span2')); ?>
    <?php echo CHtml::label('Product Name', 'product_name') ?>
    <?php echo CHtml::textField('product_name'); ?>
    <?php echo CHtml::label('Amount', 'amount') ?>
    <?php echo CHtml::textField('amount','',array('class'=>'span2','style'=>'text-align:right')); ?>
    <?php echo CHtml::label('IBO Discount (%)', 'ibo_discount') ?>
    <?php echo CHtml::textField('ibo_discount','',array('class'=>'span2','style'=>'text-align:right')); ?>
    <?php echo CHtml::label('IPD Discount (%)', 'ipd_discount') ?>
    <?php echo CHtml::textField('ipd_discount','',array('class'=>'span2','style'=>'text-align:right')); ?>
    <?php echo CHtml::label('Status', 'status') ?>
    <?php echo CHtml::dropDownList('status','',array('1'=>'Active','2'=>'Inactive')); ?>
</div>
 
<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'label'=>'Update Product',
        'type'=>'primary',
        'htmlOptions'=>array(
            'id'=>'update-product',
            'confirm'=>'Are you sure you want to continue?'
        ),
    )); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Close',
        'url'=>$this->error_code > 0 ? '#' : array('inventory/index'),
        'htmlOptions'=>$this->error_code > 0 ? array('data-dismiss'=>'modal') : "",
    )); ?>
</div>
</form>
<?php $this->endWidget(); ?>

<!-- Message Dialog -->
<?php $this->beginWidget('bootstrap.widgets.TbModal', array(
        'id'=>'message-modal',
        'autoOpen'=>$this->show_dialog,
        'fade'=>true
     )); ?>
 
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Update Product</h4>
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