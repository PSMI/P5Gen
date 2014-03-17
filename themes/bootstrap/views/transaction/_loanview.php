<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-12-2014
------------------------*/

$this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'loans-grid',
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
                        array('name'=>'date_created',
                            'header'=>'Transaction Date',
                            //'value'=>'TransactionController::dateFormat($data["date_completed"])',
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
                        array('name'=>'ibo_count',
                            'header'=>'IBO Count',
                            'htmlOptions' => array('style' => 'text-align:center'),
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'loan_amount',
                            'header'=>'Loan Amount',
                            'value'=>'TransactionController::numberFormat($data["loan_amount"])',
                            'htmlOptions' => array('style' => 'text-align:center'),
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'date_completed',
                            'header'=>'Date Completed',
                            //'value'=>'TransactionController::dateFormat($data["date_completed"])',
                            'htmlOptions' => array('style' => 'text-align:center'), 
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'date_filed',
                            'header'=>'Date Filed',
                            //'value'=>'TransactionController::dateFormat($data["date_completed"])',
                            'htmlOptions' => array('style' => 'text-align:center'), 
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'date_approved',
                            'header'=>'Date Approved',
                            //'value'=>'TransactionController::dateFormat($data["date_approved"])',
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
                            //'value'=>'TransactionController::dateFormat($data["date_claimed"])',
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
                            'value' => 'TransactionController::getStatus($data["status"], 1)',
                            'htmlOptions' => array('style' => 'text-align:center'),  
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('class'=>'bootstrap.widgets.TbButtonColumn',
                            'template'=>'{file}{download}',
                            'buttons'=>array
                            (
                                'download'=>array
                                (
                                    'label'=>'Download',
                                    'icon'=>'icon-download-alt',
                                    'visible'=>'TransactionController::getStatusForButtonDisplayLoan($data["status"], 4)',
                                    'url'=>'Yii::app()->createUrl("/transaction/pdfloans", array("id" =>$data["loan_id"], "member_id" =>$data["member_id"], "loan_type_id" =>$data["loan_type_id"], "level_no" =>$data["level_no"], "member_name" =>$data["member_name"], "loan_amount" =>$data["loan_amount"]))',
                                    'options' => array(
                                        'class'=>"btn btn-small",
                                    ),
                                    array('id' => 'send-link-'.uniqid())
                                ),
                                'file'=>array
                                (
                                    'label'=>'File Loan',
                                    'icon'=>'ok-sign',
                                    'url'=>'Yii::app()->createUrl("/transaction/processtransaction", array("id" =>$data["loan_id"], "status" => "2"))',
                                    'visible'=>'TransactionController::getStatusForButtonDisplayLoan($data["status"], 3)',
                                    'options' => array(
                                        'class'=>"btn btn-small",
                                        'confirm'=>'Are you sure you want to FILE LOAN?',
                                        'ajax' => array(
                                            'type' => 'GET',
                                            'dataType'=>'json',
                                            'url' => 'js:$(this).attr("href")',
                                            'success' => 'function(data){
                                                if(data.result_code == 0)
                                                {
                                                    alert(data.result_msg);
                                                    $.fn.yiiGridView.update("loans-grid");
                                                    location.reload();
                                                }
                                                else
                                                    alert(data.result_msg);
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
