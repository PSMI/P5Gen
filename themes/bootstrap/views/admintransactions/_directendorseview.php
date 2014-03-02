<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-11-2014
------------------------*/

$this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'directendrse-grid',
        'type'=>'striped bordered condensed',
        //'filter' => $model->search(),
        'dataProvider' => $dataProvider,
        'htmlOptions'=>array('style'=>'font-size:12px'),
        'enablePagination' => false,
        'template'=>"{items}",
        'columns' => array(
                        array(
                            'header' => '',
                            'value' => '$row + ($this->grid->dataProvider->pagination->currentPage
                            * $this->grid->dataProvider->pagination->pageSize + 1)',
                        ),
//                        array('name'=>'member_name',
//                            'header'=>'Member Name',
//                            'htmlOptions' => array('style' => 'text-align:left'),  
//                            'headerHtmlOptions' => array('style' => 'text-align:left'),
//                        ),
                        array('name'=>'endorser_name',
                              'header'=>'Endorser Name',
                              'htmlOptions' => array('style' => 'text-align:left'),
                              'headerHtmlOptions' => array('style' => 'text-align:left'),
                        ),   
                        array('name'=>'date_created',
                            'header'=>'Date Endorsed',
                            //'value'=>'AdmintransactionsController::dateFormat($data["date_created"])',
                            'htmlOptions' => array('style' => 'text-align:center'), 
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'date_approved',
                            'header'=>'Date Approved',
                            //'value'=>'AdmintransactionsController::dateFormat($data["date_approved"])',
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
                            //'value'=>'AdmintransactionsController::dateFormat($data["date_claimed"])',
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
                            'template'=>'{approve}{claim}{print}',
                            'buttons'=>array
                            (
                                'approve'=>array
                                (
                                    'label'=>'Approve',
                                    'icon'=>'ok-sign',
                                    'url'=>'Yii::app()->createUrl("/admintransactions/processtransaction", array("id" =>$data["direct_endorsement_id"], "status" => "1", "transtype" => "directendrse"))',
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
                                    'url'=>'Yii::app()->createUrl("/admintransactions/processtransaction", array("id" =>$data["direct_endorsement_id"], "status" => "2", "transtype" => "directendrse"))',
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
                                                }
                                                else
                                                    alert(data.result_msg);
                                             }',
                                        ),

                                    ),
                                    array('id' => 'send-link-'.uniqid())
                                ),
                                'print'=>array
                                (
                                    'label'=>'Print',
                                    'icon'=>'icon-print',
                                    'url'=>'Yii::app()->createUrl("/admintransactions/pdfunilevel", array("id" =>$data["direct_endorsement_id"], "cutoff_id" =>$data["cutoff_id"], "endorser_name" =>$data["endorser_name"]))',
                                    'options' => array(
                                        'class'=>"btn btn-small",
                                    ),
                                    array('id' => 'send-link-'.uniqid())
                                ),
                            ),
                            'header'=>'Action',
                            'htmlOptions'=>array('style'=>'width:80px;text-align:center'),
                        ),
//                        array('class'=>'bootstrap.widgets.TbButtonColumn',
//                            'template'=>'{print}',
//                            'buttons'=>array
//                            (
//                                'print'=>array
//                                (
//                                    'label'=>'Print',
//                                    'icon'=>'icon-print',
//                                    //'url'=>'Yii::app()->createUrl("/admintransactions/pdf", array())',
//                                    'options' => array(
//                                        'class'=>"btn btn-small",
//                                    ),
//                                    array('id' => 'send-link-'.uniqid())
//                                ),
//                            ),
//                            'header'=>'Print',
//                            'htmlOptions'=>array('style'=>'width:80px;text-align:center'),
//                        ),
        )
        ));
?>
