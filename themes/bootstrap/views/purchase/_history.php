<?php

/*
 * @author : owliber
 * @date : 2014-04-04
 */
?>
<h3>Purchase History</h3>
<?php
    Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');

    /** @var BootActiveForm $form */
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id'=>'searchForm',
        'type'=>'search',
        'htmlOptions'=>array('class'=>'well'),
    ));

    echo CHtml::label('From &nbsp;','lblFrom');
    $this->widget('CJuiDateTimePicker',array(
                    'model' => $model,
                    'attribute' => 'date_from',
                    'value'=> $model->date_from,
                    'mode'=>'date', //use "time","date" or "datetime" (default)
                    'options'=>array(
                        'dateFormat'=>'yy-mm-dd',
                        'timeFormat'=> 'hh:mm',
                        'showAnim'=>'fold', // 'show' (the default), 'slideDown', 'fadeIn', 'fold'
                        'showOn'=>'button', // 'focus', 'button', 'both'
                        'buttonText'=>Yii::t('ui','Date'), 
                        'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.png', 
                        'buttonImageOnly'=>true,
                    ),// jquery plugin options
                    'htmlOptions'=>array('readonly'=>'readonly', 'class'=>'input-medium'),
                    'language'=>'',
                ));

    echo CHtml::label('To  &nbsp;','lblTo', array('style' => 'margin-left: 20px;'));
    $this->widget('CJuiDateTimePicker',array(
                    'model' => $model,
                    'attribute' => 'date_to',
                    'value'=> $model->date_to,
                    'mode'=>'date', //use "time","date" or "datetime" (default)
                    'options'=>array(
                        'dateFormat'=>'yy-mm-dd',
                        'timeFormat'=> 'hh:mm',
                        'showAnim'=>'fold', // 'show' (the default), 'slideDown', 'fadeIn', 'fold'
                        'showOn'=>'button', // 'focus', 'button', 'both'
                        'buttonText'=>Yii::t('ui','Date'), 
                        'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.png', 
                        'buttonImageOnly'=>true,
                    ),// jquery plugin options
                    'htmlOptions'=>array('readonly'=>'readonly', 'class'=>'input-medium'),
                    'language'=>'',
                ));

    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit', 
        'label'=>'Search', 
        'icon'=>'icon-search',
        'type'=>'primary',
        'htmlOptions' => array(
            'style' => 'margin-left: 10px;'
            )
        ));

?>
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
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('name'=>'member', 
                'header'=>'Member Name',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
                'footer'=>'<strong>Total Purchase</strong>',
                'footerHtmlOptions'=>array('style'=>'font-size:14px'),
            ),
        array('name'=>'account_type', 
                'header'=>'Membership',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('name'=>'date_purchased', 
                'header'=>'Date Purchased',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('name'=>'receipt_no', 
                'header'=>'Receipt #',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
//        array('name'=>'product_code', 
//                'header'=>'Product Code',
//                'htmlOptions'=>array('style'=>'text-align:center'),
//                'headerHtmlOptions' => array('style' => 'text-align:center'),
//            ),
        array('name'=>'product_name', 
                'header'=>'Product Name',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),        
        array('name'=>'quantity', 
                'header'=>'Quantity',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
                'footer'=>'<strong>'.number_format($total['total_quantity'],0).'</strong>',
                'footerHtmlOptions'=>array('style'=>'text-align:center; font-size:14px'),
            ),
        array('name'=>'SRP', 
                'header'=>'SRP',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
            ),
        array('name'=>'discount', 
                'header'=>'Discount',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
            ),
        array('name'=>'net_price', 
                'header'=>'Net Price',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
            ),
        array('name'=>'total', 
                'header'=>'Total',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
                'footer'=>'<strong>'.number_format($total['total_amount'],2).'</strong>',
                'footerHtmlOptions'=>array('style'=>'text-align:right; font-size:14px'),
            ),
        array('class'=>'bootstrap.widgets.TbButtonColumn',
                'template'=>'{cancel}',
                'buttons'=>array
                (
                    'cancel'=>array
                    (
                        'label'=>'Cancel Purchase',
                        'icon'=>'icon-remove-sign',
                        'url'=>'Yii::app()->createUrl("/purchase/cancel", array(
                                "id" =>$data["purchase_summary_id"],
                                "receipt_no"=>$data["receipt_no"],
                                "mid"=>$data["member_id"],
                                "name"=>$data["member"],
                                "date_purchased"=>$data["date_purchased"]                                
                             ))',
                        'confirm'=>'Are you sure you want to cancel this purchase?',
                        'options' => array(
                            'class'=>"btn btn-small",
                            'ajax' => array(
                                'type' => 'GET',
                                'dataType'=>'json',
                                'url' => 'js:$(this).attr("href")',
                                'success' => 'function(data){
                                    $("#receipt_text").text(data.receipt_no);
                                    $("#receipt_no").val(data.receipt_no);
                                    $("#purchase_summary_id").val(data.purchase_summary_id);
                                    $("#purchaser_id").val(data.member_id);
                                    $("#member_name_text").text(data.name);
                                    $("#date_purchased_text").text(data.date_purchased);
                                    $("#cancel-purchase-modal").modal("show");
                                 }',
                            ),

                        ),
                        array('id' => 'send-link-'.uniqid())
                    ),
                ),
                'header'=>'&nbsp;',
                'htmlOptions'=>array('style'=>'width:50px;text-align:center'),
            ),
    ),
)); ?>

<?php $this->endWidget(); ?>

<?php /** @var BootActiveForm $form */
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id'=>'cancelForm',
        'type'=>'search',
    ));
?>
<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'cancel-purchase-modal')); ?>
 
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Cancel Purchase for Receipt # <span id="receipt_text"></span></h4>
</div>
 
<div class="modal-body">
    <?php echo CHtml::hiddenField('purchase_summary_id'); ?>
    <?php echo CHtml::hiddenField('purchaser_id'); ?>
    <?php echo CHtml::hiddenField('receipt_no'); ?>
    Member : <strong><span id="member_name_text"></span></strong><br />
    Date Purchased : <strong><span id="date_purchased_text"></span></strong><br />
    <?php echo CHtml::label('Cancellation Reason', 'cancellation_reason'); ?>
    
    <?php echo CHtml::textArea('cancellation_reason','',array('class'=>'span5','tooltip'=>'Please provide reason to cancel')); ?>
</div>
 
<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'ajaxButton',
        'type'=>'primary',
        'icon'=>'icon-remove',
        'label'=>'Cancel Purchase',
        'url'=>  Yii::app()->createUrl('purchase/cancelpurchase',array(
            'product_summary_id'=>'js:function(){return $("#product_summary_id").val()}',
            'purchaser_id'=>'js:function(){return $("#purchaser_id").val()}',
            'receipt_no'=>'js:function(){return $("#receipt_no").val()}',
            'reason'=>'js:function(){return $("#cancellation_reason").val()}'
        )),
        'ajaxOptions'=>array(
            'type' => 'POST',
            'dataType'=>'json',
            'url' => 'js:$(this).attr("href")',
            'success' => 'function(data){
                if(data["result_code"] == 0)
                {
                    $("#cancel-purchase-modal").modal("hide");   
                    alert(data.result_msg);
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
<?php $this->endWidget(); ?>

