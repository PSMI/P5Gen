<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-07-2014
------------------------*/

$this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'loans-grid',
        'type'=>'striped bordered condensed',
        //'filter' => $model->search(),
        'dataProvider' => $dataProvider,
        'htmlOptions'=>array('style'=>'font-size:12px'),
        'enablePagination' => true,
        //'template'=>"{items}",
        'columns' => array(
                        array('name'=>'date_created',
                            'header'=>'Transaction Date',
                            //'value'=>'AdmintransactionsController::dateFormat($data["date_completed"])',
                            'htmlOptions' => array('style' => 'text-align:center'), 
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'member_name',
                              'header'=>'Member Name',
                              'htmlOptions' => array('style' => 'text-align:center'),
                              'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'loan_type_id',
                              'header'=>'Loan Type',
                              'value' => '$data["loan_type_id"] == 1 ? "Direct" : "Completion"',
                              'htmlOptions' => array('style' => 'text-align:center'),
                              'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'level_no',
                            'header'=>'Level',
                            'htmlOptions' => array('style' => 'text-align:center'),
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'loan_amount',
                            'header'=>'Loan Amount',
                            'value'=>'AdmintransactionsController::numberFormat($data["loan_amount"])',
                            'htmlOptions' => array('style' => 'text-align:center'),
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'date_completed',
                            'header'=>'Date Completed',
                            //'value'=>'AdmintransactionsController::dateFormat($data["date_completed"])',
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
                            'header'=>'Claimed By',
                            'htmlOptions' => array('style' => 'text-align:center'),  
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'status',
                            'header'=>'Status',
                            'value' => 'AdmintransactionsController::getStatus($data["status"], 1)',
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
                                    'url'=>'Yii::app()->createUrl("/admintransactions/processtransaction", array("id" =>$data["loan_id"], "status" => "2", "transtype" => "loan"))',
                                    'visible'=>'AdmintransactionsController::getStatusForButtonDisplayLoan($data["status"], 1)',
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
                                                    $.fn.yiiGridView.update("loans-grid");
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
                                    'url'=>'Yii::app()->createUrl("/admintransactions/processtransaction", array("id" =>$data["loan_id"], "status" => "3", "transtype" => "loan"))',
                                    'visible'=>'AdmintransactionsController::getStatusForButtonDisplayLoan($data["status"], 2)',
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
                                                    $.fn.yiiGridView.update("loans-grid");
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
                                    'url'=>'Yii::app()->createUrl("/admintransactions/pdf", array("id" =>$data["loan_id"], "member_id" =>$data["member_id"], "loan_type_id" =>$data["loan_type_id"], "level_no" =>$data["level_no"], "member_name" =>$data["member_name"]))',
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
//                                    'url'=>'Yii::app()->createUrl("/admintransactions/pdf", array("id" =>$data["loan_id"]))',
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
