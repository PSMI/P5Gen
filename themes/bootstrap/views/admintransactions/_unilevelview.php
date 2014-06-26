<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-11-2014
------------------------*/

$this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'unilvl-grid',
        'type'=>'striped bordered condensed',
        'dataProvider' => $dataProvider,
        'htmlOptions'=>array('style'=>'font-size:12px'),
        'enablePagination' => true,
        'columns' => array(
                        array(
                            'header' => '',
                            'value' => '$row + ($this->grid->dataProvider->pagination->currentPage
                            * $this->grid->dataProvider->pagination->pageSize + 1)',
                        ),
                        array('name'=>'member_name',
                              'header'=>'Member Name',
                              'htmlOptions' => array('style' => 'text-align:left'),
                              'headerHtmlOptions' => array('style' => 'text-align:left'),
                              'footer'=>'<strong>Total Payout</strong>',
                              'footerHtmlOptions'=>array('style'=>'font-size:14px'),
                        ), 
                        array('name'=>'ibo_count',
                            'header'=>'IBO Count',
                            'htmlOptions' => array('style' => 'text-align:center'),  
                            'headerHtmlOptions' => array('style' => 'text-align:center'),                            
                            'footer'=>'<strong>'.number_format($total['total_ibo'],0).'</strong>',
                            'footerHtmlOptions'=>array('style'=>'text-align:center; font-size:14px'),
                        ),
                        array('name'=>'amount',
                            'header'=>'Amount',
                            'value'=>'AdmintransactionsController::numberFormat($data["amount"])',
                            'htmlOptions' => array('style' => 'text-align:right'), 
                            'headerHtmlOptions' => array('style' => 'text-align:right'),
                            'footer'=>'<strong>'.number_format($total['total_amount'],2).'</strong>',
                            'footerHtmlOptions'=>array('style'=>'text-align:right; font-size:14px'),
                        ),
                        array('name'=>'date_approved',
                            'header'=>'Date Approved',
                            'htmlOptions' => array('style' => 'text-align:center'), 
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'approved_by',
                            'header'=>'Approved By',
                            'htmlOptions' => array('style' => 'text-align:center'),
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'date_claimed',
                            'header'=>'Date Claimed',
                            'htmlOptions' => array('style' => 'text-align:center'), 
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'claimed_by',
                            'header'=>'Processed By',
                            'htmlOptions' => array('style' => 'text-align:center'),  
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'status',
                            'header'=>'Status',
                            'value' => 'AdmintransactionsController::getStatus($data["status"], 2)',
                            'htmlOptions' => array('style' => 'text-align:center'),  
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('class'=>'bootstrap.widgets.TbButtonColumn',
                            'template'=>'{approve}{claim}{download}{update}',
                            'buttons'=>array
                            (
                                'approve'=>array
                                (
                                    'label'=>'Approve',
                                    'icon'=>'ok-sign',
                                    'url'=>'Yii::app()->createUrl("/admintransactions/processtransaction", array("id" =>$data["unilevel_id"], "status" => "1", "transtype" => "unilvl"))',
                                    'visible'=>'AdmintransactionsController::getStatusForButtonDisplayGoc($data["status"], 1)',
                                    'options' => array(
                                        'class'=>"btn btn-small",
                                        'confirm'=>'Are you sure you want to APPROVE?',
                                        'ajax' => array(
                                            'type' => 'GET',
                                            'dataType'=>'json',
                                            'url' => 'js:$(this).attr("href")',
                                            'success' => 'function(data){
                                                if(data.result_code == 0)
                                                {
                                                    alert(data.result_msg);
                                                    $.fn.yiiGridView.update("unilvl-grid");
                                                    location.reload();
                                                }
                                                else
                                                    alert(data.result_msg);
                                             }',
                                        ),

                                    ),
                                    array('id' => 'send-link-'.uniqid())
                                ),
                                'claim'=>array
                                (
                                    'label'=>'Claim',
                                    'icon'=>'ok-sign',
                                    'url'=>'Yii::app()->createUrl("/admintransactions/processtransaction", array("id" =>$data["unilevel_id"], "status" => "2", "transtype" => "unilvl"))',
                                    'visible'=>'AdmintransactionsController::getStatusForButtonDisplayGoc($data["status"], 2)',
                                    'options' => array(
                                        'class'=>"btn btn-small",
                                        'confirm'=>'Are you sure you want to CLAIM?',
                                        'ajax' => array(
                                            'type' => 'GET',
                                            'dataType'=>'json',
                                            'url' => 'js:$(this).attr("href")',
                                            'success' => 'function(data){
                                                if(data.result_code == 0)
                                                {
                                                    alert(data.result_msg);
                                                    $.fn.yiiGridView.update("unilvl-grid");
                                                }
                                                else
                                                    alert(data.result_msg);
                                             }',
                                        ),

                                    ),
                                    array('id' => 'send-link-'.uniqid())
                                ),
                                'download'=>array
                                (
                                    'label'=>'Download',
                                    'icon'=>'icon-download-alt',
                                    'url'=>'Yii::app()->createUrl("/admintransactions/pdfunilevel", array("id" =>$data["member_id"], "cutoff_id" =>$data["cutoff_id"]))',
                                    'options' => array(
                                        'class'=>"btn btn-small",
                                    ),
                                    array('id' => 'send-link-'.uniqid())
                                ),
                                'update' => array
                                    (
                                    'label' => 'Fix discrepancies',
                                    'icon' => 'icon-edit',
                                    'url' => 'Yii::app()->createUrl("/admintransactions/getvalues", array("id" =>$data["unilevel_id"], "name"=>$data["member_name"], "member_id" =>$data["member_id"],"cutoff_id"=>$data["cutoff_id"],"ibo_count"=>$data["ibo_count"],"amount"=>$data["amount"]))',
                                    'visible'=>'AdmintransactionsController::getStatusForButtonDisplayGoc($data["status"], 1)',
                                    'options' => array(
                                        'class' => "btn btn-small",
                                        'ajax' => array(
                                                'type' => 'GET',
                                                'dataType'=>'json',
                                                'url' => 'js:$(this).attr("href")',
                                                'success' => 'function(data){   
                                                     $.each(data, function(name,val){
                                                        $("#ibo_count").val(val.ibo_count);
                                                        $("#amount").val(val.amount);
                                                        $("#cutoff_id").val(val.cutoff_id);
                                                        $("#member_id").val(val.member_id);
                                                        $("#member_name").text(val.name);
                                                        $("#unilevel_id").val(val.unilevel_id);
                                                        $("#unilevel_rate").val(val.unilevel_rate);
                                                    });
                                                    $("#update-dialog").modal("show");
                                                 }',
                                            ),
                                    ),
                                array('id' => 'send-link-' . uniqid())
                                ),
                            ),
                            'header'=>'Action',
                            'htmlOptions'=>array('style'=>'width:120px;text-align:center'),
                        ),
        )
        ));
?>

<?php
Yii::app()->clientScript->registerScript('ui','
        
    function update_amount()
    {
        var total;
        total = $("#unilevel_rate").val() * $("#ibo_count").val();
        $("#amount").val(total.toFixed(2));    
    }
             
 ', CClientScript::POS_END);
?>

<form name="updateForm" id="optionForm" method="post">
<!-- MESSAGE DIALOG -->
<?php $this->beginWidget('bootstrap.widgets.TbModal', 
        array('id'=>'update-dialog',
              'autoOpen'=>false,
              'fade'=>true,
)); ?>
 
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Fix Discrepancy </h4> <strong>Member </strong><span id="member_name"></span>
</div>

<div class="modal-body">    
   <?php echo CHtml::hiddenField('unilevel_rate'); ?>
   <?php echo CHtml::hiddenField('unilevel_id'); ?>
   <?php echo CHtml::hiddenField('cutoff_id'); ?>
   <?php echo CHtml::hiddenField('member_id'); ?>    
   <?php echo CHtml::label('IBO Count', 'ibo_count'); ?>
   <?php echo CHtml::textField('ibo_count','',array(
        'onchange'=>'update_amount();',
   )); ?><br />
   <?php echo CHtml::label('Amount', 'amount'); ?>
   <?php echo CHtml::textField('amount','',array('readonly'=>'readonly')); ?>
</div>
 
<div class="modal-footer">
    
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'ajaxButton',
        'type'=>'primary',
        'icon'=>'icon-edit',
        'label'=>'Update',        
        'url'=>  Yii::app()->createUrl('admintransactions/modifyunilevel',array()),
        'ajaxOptions'=>array(
            'confirm'=>'Are you sure you want to continue updating the values?',
            'type' => 'GET',
            'dataType'=>'json',
            'url' => 'js:$(this).attr("href")',
            'success' => 'function(data){
                if(data["result_code"] == 0)
                {
                    $("#update-dialog").modal("hide");   
                    alert(data["result_msg"]);
                    $("#searchForm").submit();
                }
                else
                {
                    alert(data["result_msg"]);
                }
             }',
        ),
    )); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Close',
        'url'=> array('admintransactions/unilevel'),
        'htmlOptions'=> array('data-dismiss'=>'modal'),
    )); ?>
</div>
</form>
<?php $this->endWidget(); ?>
