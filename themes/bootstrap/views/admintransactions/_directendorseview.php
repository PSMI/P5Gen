<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-11-2014
------------------------*/

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
                        array('name'=>'total_payout',
                            'header'=>'Total Payout',
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
                            'value' => 'AdmintransactionsController::getStatus($data["status"], 3)',
                            'htmlOptions' => array('style' => 'text-align:center'),  
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('class'=>'bootstrap.widgets.TbButtonColumn',
                            'template'=>'{approve}{claim}{download}',
                            'buttons'=>array
                            (
                                'approve'=>array
                                (
                                    'label'=>'Approve',
                                    'icon'=>'ok-sign',
                                    'url'=>'Yii::app()->createUrl("/admintransactions/processtransaction", array("id" =>$data["direct_endorsement_id"], "status" => "1", "transtype" => "directendrse", "endorser_id" =>$data["endorser_id"], "cutoff_id" =>$data["cutoff_id"]))',
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
                                    'url'=>'Yii::app()->createUrl("/admintransactions/processtransaction", array("id" =>$data["direct_endorsement_id"], "status" => "2", "transtype" => "directendrse", "endorser_id" =>$data["endorser_id"], "cutoff_id" =>$data["cutoff_id"]))',
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
                            ),
                            'header'=>'Action',
                            'htmlOptions'=>array('style'=>'width:80px;text-align:center'),
                        ),
                )
        ));
?>
