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
         variable_text = $("#variable_text");
         
 ', CClientScript::POS_END);
?>
<h3>System Options</h3>
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
                    'url' => 'Yii::app()->createUrl("/admintransactions/processtransaction", array("id" =>$data["direct_endorsement_id"], "status" => "1", "transtype" => "directendrse", "endorser_id" =>$data["endorser_id"], "cutoff_id" =>$data["cutoff_id"]))',
                    'options' => array(
                        'class' => "btn btn-small",
                        'confirm' => 'Are you sure you want to APPROVE?',
                        'ajax' => array(
                            'type' => 'GET',
                            'dataType' => 'json',
                            'url' => 'js:$(this).attr("href")',
                            'success' => 'function(data){
                                    if(data.result_code == 0)
                                    {
                                        alert(data.result_msg);
                                        $.fn.yiiGridView.update("directendrse-grid");
                                        location.reload();
                                    }
                                    else
                                        alert(data.result_msg);
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
                    'url' => 'Yii::app()->createUrl("/admintransactions/processtransaction", array("id" =>$data["direct_endorsement_id"], "status" => "1", "transtype" => "directendrse", "endorser_id" =>$data["endorser_id"], "cutoff_id" =>$data["cutoff_id"]))',
                    'options' => array(
                        'class' => "btn btn-small",
                        'confirm' => 'Are you sure you want to APPROVE?',
                        'ajax' => array(
                            'type' => 'GET',
                            'dataType' => 'json',
                            'url' => 'js:$(this).attr("href")',
                            'success' => 'function(data){
                                    if(data.result_code == 0)
                                    {
                                        alert(data.result_msg);
                                        $.fn.yiiGridView.update("directendrse-grid");
                                        location.reload();
                                    }
                                    else
                                        alert(data.result_msg);
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

<?php /** @var BootActiveForm $form */
$form = $this->widget('bootstrap.widgets.TbActiveForm', array
(
    'id'=>'optionForm',
    'inlineErrors'=>true,
    'enableClientValidation'=>true,
    'clientOptions'=>array(
            'validateOnSubmit'=>true,
        ),
)); ?>

<div class="modal-body">
    <?php echo CHtml::hiddenField('variable_id'); ?>
    <table>
        <tr>
            <th>Option Name</th>
            <td><span id="variable_text"></span></td>
        </tr>
        <tr>
            <th>Current Value</th>
            <td><?php echo CHtml::textField('variable_value','',array('readonly'=>'readonly')); ?></td>
        </tr>
        <tr>
            <th>New Value</th>
            <td><?php echo CHtml::textField('new_value', 1,array('style'=>'width:20px')); ?> <?php echo CHtml::dropDownList('schedule', '', array('m'=>'MONTH','w'=>'WEEK','d'=>'DAY')) ?></td>
        </tr>
    </table>
</div>
 
<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'label'=>'Update',
        'url'=>array('admin/options'),
        'htmlOptions'=>array('onclick'=>'$("#optionForm").submit()'),
    )); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Close',
        'url'=>$this->errorCode > 0 ? '#' : array('admin/options'),
        'htmlOptions'=>$this->errorCode > 0 ? array('data-dismiss'=>'modal') : "",
    )); ?>
</div>
 
<?php $this->endWidget(); ?>