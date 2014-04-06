<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 03-31-2014
------------------------*/

Yii::app()->clientScript->registerScript('ui','
         
     var direct_endorsement_id = $("#DirectEndorsement_direct_endorsement_id"),
        endorser_id = $("#DirectEndorsement_endorser_id"),
        cutoff_id = $("#DirectEndorsement_cutoff_id");
        
        function reloadPage(){window.location.href="directendorse";}

', CClientScript::POS_END);

$this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'directendorse-grid',
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
                        array('name'=>'endorser_name',
                              'header'=>'Endorser Name',
                              'htmlOptions' => array('style' => 'text-align:left'),
                              'headerHtmlOptions' => array('style' => 'text-align:center'),
                              'footer'=>'<strong>Total Payout</strong>',
                              'footerHtmlOptions'=>array('style'=>'font-size:14px'),
                        ),   
                        array('name'=>'ipd_count',
                            'header'=>'IPD Count',
                            'htmlOptions' => array('style' => 'text-align:center'), 
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                            'footer'=>'<strong>'.number_format($total['total_ipd'],0).'</strong>',
                            'footerHtmlOptions'=>array('style'=>'text-align:center; font-size:14px'),
                        ),
                        array('name'=>'total_payout',
                            'header'=>'Total Payout',
                            'htmlOptions' => array('style' => 'text-align:right'), 
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
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
                            'value' => 'AdmintransactionsController::getStatus($data["status"], 3)',
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
                                    'url'=>'Yii::app()->createUrl("/admintransactions/processtransaction", array("id" =>$data["direct_endorsement_id"], "status" => "1", "transtype" => "ipddirectendrse", "endorser_id" =>$data["endorser_id"], "cutoff_id" =>$data["cutoff_id"]))',
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
                                                    $.fn.yiiGridView.update("directendrse-grid");
                                                    reloadPage();
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
                                    'url'=>'Yii::app()->createUrl("/admintransactions/processtransaction", array("id" =>$data["direct_endorsement_id"], "status" => "2", "transtype" => "ipddirectendrse", "endorser_id" =>$data["endorser_id"], "cutoff_id" =>$data["cutoff_id"]))',
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
                                                    $.fn.yiiGridView.update("directendrse-grid");
                                                    reloadPage();
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
                                    'url'=>'Yii::app()->createUrl("/admintransactions/pdfdirect", array("id" =>$data["endorser_id"], "cutoff_id" =>$data["cutoff_id"]))',
                                    'options' => array(
                                        'class'=>"btn btn-small",
                                    ),
                                    array('id' => 'send-link-'.uniqid())
                                ),
                                'update'=>array
                                (
                                    'label'=>'Update Claim Date',
                                    'icon'=>'icon-edit',
                                    'url'=>'Yii::app()->createUrl("/admintransactions/gettransaction", array("id" =>$data["direct_endorsement_id"], "endorser_id" =>$data["endorser_id"], "cutoff_id" =>$data["cutoff_id"]))',
                                    'visible'=>'$data["status"] == 2',
                                    'options' => array(
                                        'class'=>"btn btn-small",
//                                        'confirm'=>'Are you sure you want to update?',
                                        'ajax' => array(
                                            'type' => 'GET',
                                            'dataType'=>'json',
                                            'url' => 'js:$(this).attr("href")',
                                            'success' => 'function(data){
                                                $.each(data, function(name,val){
                                                    direct_endorsement_id.val(val.direct_endorsement_id);
                                                    endorser_id.val(val.endorser_id);
                                                    cutoff_id.val(val.cutoff_id);
                                                });
                                                $("#claim-modal").modal("show");
                                             }',
                                        ),

                                    ),
                                    array('id' => 'send-link-'.uniqid())
                                ),
                            ),
                            'header'=>'Action',
                            'htmlOptions'=>array('style'=>'width:80px;text-align:center'),
                        ),
                )
        ));
?>

<?php $this->beginWidget('bootstrap.widgets.TbModal', 
        array('id'=>'claim-modal',
              'autoOpen'=>false,
            
)); ?>
 
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Update Claim Date</h4>
</div>
<div class="modal-body">
    <?php /** @var BootActiveForm $form  */
    $form = $this->widget('bootstrap.widgets.TbActiveForm', array
    (
        'id'=>'verticalForm',
        'type'=>'horizontal',
        'inlineErrors'=>true,
        'enableClientValidation'=>true,
        'clientOptions'=>array(
                'validateOnSubmit'=>true,
            ),
    )); ?>
    <div class="control-group">
        <?php echo CHtml::label('Date Claimed '. '<span class="required">*</span>', 'DirectEndorsement_date_claimed',array('class'=>'control-label required')) ?>
        <div class="controls">  
        <?php 
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'model' => $model,
                'attribute' => 'date_claimed',
                'htmlOptions' => array(
                    'size' => '10',
                    'maxlength' => '10',
                    'readonly' => true,
                    'value'=>$model->date_claimed,
                ),
                'options' => array(
                    'showOn'=>'button',
                    'buttonImageOnly' => true,
                    'changeMonth' => true,
                    'changeYear' => true,
                    'buttonText'=> 'Select Date Claimed',
                    'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.png',
                    'dateFormat'=>'yy-mm-dd',
                    'maxDate' =>'0',
                    'yearRange'=>'1900:' . date('Y'),
                )
            ));

            echo $form->error($model, 'date_claimed');
        ?>
        </div>    
    </div>
     
    <?php echo $form->hiddenField($model, 'direct_endorsement_id'); ?>
    <?php echo $form->hiddenField($model, 'endorser_id'); ?>
    <?php echo $form->hiddenField($model, 'cutoff_id'); ?>
</div>
 
<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'ajaxButton', 
        'label'=>'Update',
        'type'=>'primary',
        'url'=>array('admintransactions/processtransaction'),
        'ajaxOptions'=>array
         (
            'type' => 'GET',
            'dataType'=>'json',
            'url' => 'js:$(this).attr("href")',
            'data'=>array(
                'id'=>'js:function(){return direct_endorsement_id.val();}',
                'status'=>2,
                'transtype'=>'directendrse',
                'endorser_id'=>'js:function(){return endorser_id.val();}',
                'cutoff_id'=>'js:function(){return cutoff_id.val();}',
                'date_claimed'=>'js:function(){return $("#DirectEndorsement_date_claimed").val();}',
             ),
            'success'=>'js:function(data){
             if(data.result_code == 0)
             {
                alert(data.result_msg);
                $.fn.yiiGridView.update("directendrse-grid");
                window.location.href="directendorse";
             }
             else
                alert(data.result_msg);
             }'
         ),
        'htmlOptions'=>array
         (
            'data-dismiss'=>'modal',
         ),
    )); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Close',
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>
 
<?php $this->endWidget(); ?>