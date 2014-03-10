<?php

/*
 * @author : owliber
 * @date : 2014-03-10
 */
?>
<?php
Yii::app()->clientScript->registerScript('ui','
         
     var variable_id = $("#variable_id"),
         variable_value = $("#variable_value"),
         variable_text = $("#variable_text"),
         variable_sched = $("#schedule"),
         new_value = $("#new_value"),
         payout_rate_id = $("#payout_rate_id"),
         transaction_text = $("#transaction_text"),
         payout_amount = $("#payout_amount"),
         trans_type_id = $("#transaction_type_id"),
         new_payout_amount = $("#new_payout_amount");
         
 ', CClientScript::POS_END);
?>
<h3>System Options</h3>

<?php Yii::app()->user->setFlash('danger', '<strong>Warning!</strong> It is recommened that changing values below should be done only every after cutoff. If it is needed you can stop first the job scheduler in the Administration->Job Scheduler menu and make sure there are no more queued transactions before changing the current options values.');

$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'danger'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times'), // success, info, warning, error or danger
        ),
)); ?>

<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'schedule-option-grid',
    'type' => 'striped bordered condensed',
    'dataProvider' => $dataProvider,
    'enablePagination' => true,
    'columns' => array(
        array('name' => 'variable_text',
            'header' => 'Schedules',
            'htmlOptions' => array('style' => 'text-align:left'),
            'headerHtmlOptions' => array('style' => 'text-align:left'),
        ),
        array('name' => 'default_value',
            'header' => 'Default Values',
            'htmlOptions' => array('style' => 'text-align:center'),
            'headerHtmlOptions' => array('style' => 'text-align:center'),
        ),
        array('name' => 'variable_value',
            'header' => 'Values',
            'htmlOptions' => array('style' => 'text-align:center'),
            'headerHtmlOptions' => array('style' => 'text-align:center'),
        ),        
        array('class' => 'bootstrap.widgets.TbButtonColumn',
            'template' => '{update}',
            'buttons' => array
                (
                'update' => array
                    (
                    'label' => 'Modify values',
                    'icon' => 'icon-edit',
                    'url' => 'Yii::app()->createUrl("/admin/getvariableoptions", array("id" =>$data["variable_id"]))',
                    'options' => array(
                        'class' => "btn btn-small",
                        'ajax' => array(
                                'type' => 'GET',
                                'dataType'=>'json',
                                'url' => 'js:$(this).attr("href")',
                                'success' => 'function(data){                                   
                                     $.each(data, function(name,val){
                                        variable_text.text(val.text);
                                        variable_value.val(val.value);
                                        variable_id.val(val.id);
                                    });
                                    $("#update-dialog").modal("show");
                                 }',
                            ),
                    ),
                    array('id' => 'send-link-' . uniqid())
                ),
            ),
            'header' => 'Action',
            'htmlOptions' => array('style' => 'width:80px;text-align:center'),
        ),
    ),
));

$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'rate-option-grid',
    'type' => 'striped bordered condensed',
    'dataProvider' => $dataProvider2,
    'enablePagination' => true,
    'columns' => array(
        array('name' => 'variable_text',
            'header' => 'Rates and Charges',
            'htmlOptions' => array('style' => 'text-align:left'),
            'headerHtmlOptions' => array('style' => 'text-align:left'),
        ),
        array('name' => 'default_value',
            'header' => 'Default Values',
            'htmlOptions' => array('style' => 'text-align:center'),
            'headerHtmlOptions' => array('style' => 'text-align:center'),
        ),
        array('name' => 'variable_value',
            'header' => 'Values',
            'htmlOptions' => array('style' => 'text-align:center'),
            'headerHtmlOptions' => array('style' => 'text-align:center'),
        ),        
        array('class' => 'bootstrap.widgets.TbButtonColumn',
            'template' => '{update}',
            'buttons' => array
                (
                'update' => array
                    (
                    'label' => 'Modify values',
                    'icon' => 'icon-edit',
                    'url' => 'Yii::app()->createUrl("/admin/getvariableoptions", array("id" =>$data["variable_id"]))',
                    'options' => array(
                        'class' => "btn btn-small",
                        'ajax' => array(
                                'type' => 'GET',
                                'dataType'=>'json',
                                'url' => 'js:$(this).attr("href")',
                                'success' => 'function(data){                                   
                                     $.each(data, function(name,val){
                                        variable_text.text(val.text);
                                        variable_value.val(val.value);
                                        variable_id.val(val.id);
                                        new_value.val(val.value);
                                        variable_sched.remove();
                                    });
                                    $("#update-dialog").modal("show");
                                 }',
                            ),
                    ),
                    array('id' => 'send-link-' . uniqid())
                ),
            ),
            'header' => 'Action',
            'htmlOptions' => array('style' => 'width:80px;text-align:center'),
        ),
    ),
));


$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'payout-rate-grid',
    'type' => 'striped bordered condensed',
    'dataProvider' => $dataProvider3,
    'enablePagination' => true,
    'columns' => array(
        array('name' => 'transaction_type_name',
            'header' => 'Transaction Type',
            'htmlOptions' => array('style' => 'text-align:left'),
            'headerHtmlOptions' => array('style' => 'text-align:left'),
        ),
        array('name' => 'amount',
            'header' => 'Payout Rate (Php)',
            'htmlOptions' => array('style' => 'text-align:center'),
            'headerHtmlOptions' => array('style' => 'text-align:center'),
        ),  
        array('class' => 'bootstrap.widgets.TbButtonColumn',
            'template' => '{update}',
            'buttons' => array
                (
                'update' => array
                    (
                    'label' => 'Modify values',
                    'icon' => 'icon-edit',
                    'url' => 'Yii::app()->createUrl("/admin/getpayoutrates", array("id" =>$data["payout_rate_id"]))',
                    'options' => array(
                        'class' => "btn btn-small",
                        'ajax' => array(
                                'type' => 'GET',
                                'dataType'=>'json',
                                'url' => 'js:$(this).attr("href")',
                                'success' => 'function(data){                                   
                                     $.each(data, function(name,val){
                                       payout_rate_id.val(val.id);
                                       trans_type_id.val(val.trans_type_id);
                                       transaction_text.text(val.text);
                                       payout_amount.val(val.value);
                                       new_payout_amount.val(val.value);
                                    });
                                    $("#payout-rate-dialog").modal("show");
                                 }',
                            ),
                    ),
                    array('id' => 'send-link-' . uniqid())
                ),
            ),
            'header' => 'Action',
            'htmlOptions' => array('style' => 'width:80px;text-align:center'),
        ),
    ),
));
?>

<form name="optionForm" id="optionForm" method="post">
<!-- MESSAGE DIALOG -->
<?php $this->beginWidget('bootstrap.widgets.TbModal', 
        array('id'=>'update-dialog',
              'autoOpen'=>false,
              'fade'=>true,
)); ?>
 
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Modify Option Values</h4>
</div>

<div class="modal-body">
    
    <?php echo CHtml::hiddenField('variable_id'); ?>
    <table>
        <tr>
            <th align="right">Option Name</th>
            <td style="padding:2px 10px"><span id="variable_text"></span></td>
        </tr>
        <tr>
            <th align="right">Current Value</th>
            <td style="padding:2px 10px"><?php echo CHtml::textField('variable_value','',array('readonly'=>'readonly')); ?></td>
        </tr>
        <tr>
            <th align="right">New Value</th>
            <td style="padding:2px 10px"><?php echo CHtml::textField('new_value', 1,array('style'=>'width:80px')); ?> <?php echo CHtml::dropDownList('schedule', '', array('m'=>'MONTH','w'=>'WEEK','d'=>'DAY'),array('style'=>'width:120px')); ?></td>
        </tr>
    </table>
</div>
 
<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'label'=>'Update',
        'htmlOptions'=>array(
            'onclick'=>'$("#optionForm").submit()',
            'confirm'=>'Are you sure you want to continue updating the options values?'
        ),
    )); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Close',
        'url'=>$this->errorCode > 0 ? '#' : array('admin/options'),
        'htmlOptions'=>$this->errorCode > 0 ? array('data-dismiss'=>'modal') : "",
    )); ?>
</div>
</form>
<?php $this->endWidget(); ?>

<form name="payoutForm" id="optionForm" method="post">
<!-- PAYOUT RATE DIALOG -->
<?php $this->beginWidget('bootstrap.widgets.TbModal', 
        array('id'=>'payout-rate-dialog',
              'autoOpen'=>false,
              'fade'=>true,
)); ?>
 
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Payout Rate Values</h4>
</div>

<div class="modal-body">
    
    <?php echo CHtml::hiddenField('payout_rate_id'); ?>
    <?php echo CHtml::hiddenField('transaction_type_id'); ?>
    <table>
        <tr>
            <th align="right">Transaction</th>
            <td style="padding:2px 10px"><span id="transaction_text"></span></td>
        </tr>
        <tr>
            <th align="right">Current Payout Rate</th>
            <td style="padding:2px 10px"><?php echo CHtml::textField('payout_amount','',array('readonly'=>'readonly')); ?></td>
        </tr>
        <tr>
            <th align="right">New Payout Rate</th>
            <td style="padding:2px 10px"><?php echo CHtml::textField('new_payout_amount', 1,array('style'=>'width:80px')); ?></td>
        </tr>
    </table>
</div>
 
<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'label'=>'Update',
        'htmlOptions'=>array(
            'onclick'=>'$("#payoutForm").submit()',
            'confirm'=>'Are you sure you want to continue updating the payout rates?'
        ),
    )); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Close',
        'url'=>$this->errorCode > 0 ? '#' : array('admin/options'),
        'htmlOptions'=>$this->errorCode > 0 ? array('data-dismiss'=>'modal') : "",
    )); ?>
</div>

</form>
<?php $this->endWidget(); ?>

<!-- ALERT DIALOG -->
<?php $this->beginWidget('bootstrap.widgets.TbModal', 
        array('id'=>'alert-dialog',
              'autoOpen'=>$this->alertDialog,
              'fade'=>true,
)); ?>
 
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4><?php echo $this->alertTitle; ?></h4>
</div>

<div class="modal-body">
    <p><?php echo $this->alertMessage; ?></p>
</div>
 
<div class="modal-footer">
   
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Close',
        'url'=>$this->errorCode > 0 ? '#' : array('admin/options'),
        'htmlOptions'=>$this->errorCode > 0 ? array('data-dismiss'=>'modal') : "",
    )); ?>
</div>
</form>
<?php $this->endWidget(); ?>