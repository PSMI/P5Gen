<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 04-05-2014
------------------------*/

$this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'ipdretention-grid',
        'type'=>'striped bordered condensed',
        'dataProvider' => $dataProvider,
        'htmlOptions'=>array('style'=>'font-size:12px'),
        'enablePagination' => true,
        'columns' => array(
                        array(
                            'header' => '',
                            'value' => '$row + ($this->grid->dataProvider->pagination->currentPage
                            * $this->grid->dataProvider->pagination->pageSize + 1)',
                            'htmlOptions' => array('style' => 'text-align:center'),
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'member_name',
                              'header'=>'Distributor Name',
                              'htmlOptions' => array('style' => 'text-align:left'),
                              'headerHtmlOptions' => array('style' => 'text-align:left'),
                              'footer'=>'<strong>Total Payout</strong>',
                              'footerHtmlOptions'=>array('style'=>'font-size:14px'),
                        ), 
                        array('name'=>'purchase_retention',
                            'header'=>'Purchase Retention',
                            'htmlOptions' => array('style' => 'text-align:right'),
                            'headerHtmlOptions' => array('style' => 'text-align:right'),
                            'footer'=>'<strong>'.number_format($total['total_purchase_retention'],2).'</strong>',
                            'footerHtmlOptions'=>array('style'=>'font-size:14px; text-align:right'),
                        ),
                        array('name'=>'other_retention',
                            'header'=>'Other Retention',
                            'htmlOptions' => array('style' => 'text-align:right'),
                            'headerHtmlOptions' => array('style' => 'text-align:right'),
                            'footer'=>'<strong>'.number_format($total['total_other_retention'],2).'</strong>',
                            'footerHtmlOptions'=>array('style'=>'font-size:14px; text-align:right'),
                        ),
                        array('name'=>'total_retention',
                            'header'=>'Total Retention',
                            'htmlOptions' => array('style' => 'text-align:right'),
                            'headerHtmlOptions' => array('style' => 'text-align:right'),
                            'footer'=>'<strong>'.number_format($total['total_retentions'],2).'</strong>',
                            'footerHtmlOptions'=>array('style'=>'font-size:14px; text-align:right'),
                        ),

                        array('class'=>'bootstrap.widgets.TbButtonColumn',
                            'template'=>'{withdraw}{download}',
                            'buttons'=>array
                            (
                                'withdraw'=>array
                                (
                                    'label'=>'Withdraw',
                                    'icon'=>'ok-sign',
                                    'url'=>'Yii::app()->createUrl("/admintransactions/processtransaction", array("id" =>$data["distributor_retention_id"], "status" => "1", "transtype" => "ipdretention"))',
                                    'visible'=>'AdmintransactionsController::getWithdrawButtonDisplay($data["total_retention"])',
                                    'options' => array(
                                        'class'=>"btn btn-small",
                                        'confirm'=>'Are you sure you want to WITHDRAW?',
                                        'ajax' => array(
                                            'type' => 'GET',
                                            'dataType'=>'json',
                                            'url' => 'js:$(this).attr("href")',
                                            'success' => 'function(data){
                                                if(data.result_code == 0)
                                                {
                                                    alert(data.result_msg);
                                                    $.fn.yiiGridView.update("ipdretention-grid");
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
                                    'url'=>'Yii::app()->createUrl("/admintransactions/pdfipdretention", array("id" =>$data["distributor_retention_id"], "member_id" =>$data["member_id"], "total_retention" =>$data["total_retention"], "account_type_id" => $data["account_type_id"]))',
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
