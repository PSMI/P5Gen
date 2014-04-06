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
                        ),
                        array('name'=>'member_name',
                              'header'=>'Distributor Name',
                              'htmlOptions' => array('style' => 'text-align:left'),
                              'headerHtmlOptions' => array('style' => 'text-align:center'),
                              'footer'=>'<strong>Total Payout</strong>',
                              'footerHtmlOptions'=>array('style'=>'font-size:14px'),
                        ), 
                        array('name'=>'product_name',
                            'header'=>'Product Name',
                            'htmlOptions' => array('style' => 'text-align:center'),  
                            'headerHtmlOptions' => array('style' => 'text-align:center'),                            
                        ),
                        array('name'=>'date_purchased',
                            'header'=>'Date Purchased',
                            'htmlOptions' => array('style' => 'text-align:center'), 
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'quantity',
                            'header'=>'Quantity',
                            'htmlOptions' => array('style' => 'text-align:right'), 
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'srp',
                            'header'=>'Srp',
                            'value'=>'AdmintransactionsController::numberFormat($data["srp"])',
                            'htmlOptions' => array('style' => 'text-align:right'),
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'discount',
                            'header'=>'Discount',
                            'value'=>'AdmintransactionsController::numberFormat($data["discount"])',
                            'htmlOptions' => array('style' => 'text-align:right'),
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'net_price',
                            'header'=>'Net Price',
                            'value'=>'AdmintransactionsController::numberFormat($data["net_price"])',
                            'htmlOptions' => array('style' => 'text-align:right'),
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'total',
                            'header'=>'Total',
                            'value'=>'AdmintransactionsController::numberFormat($data["total"])',
                            'htmlOptions' => array('style' => 'text-align:right'),
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                            'footer'=>'<strong>'.number_format($total['total_amount'],2).'</strong>',
                            'footerHtmlOptions'=>array('style'=>'text-align:right; font-size:14px'),
                        ),
                        array('name'=>'savings',
                            'header'=>'Savings',
                            'value'=>'AdmintransactionsController::numberFormat($data["savings"])',
                            'htmlOptions' => array('style' => 'text-align:right'),
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'payment_type_name',
                            'header'=>'Payment Type',
                            'htmlOptions' => array('style' => 'text-align:center'), 
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
//                        array('class'=>'bootstrap.widgets.TbButtonColumn',
//                            'template'=>'{approve}{claim}{download}',
//                            'buttons'=>array
//                            (
//                                'approve'=>array
//                                (
//                                    'label'=>'Approve',
//                                    'icon'=>'ok-sign',
//                                    'url'=>'Yii::app()->createUrl("/admintransactions/processtransaction", array("id" =>$data["unilevel_id"], "status" => "1", "transtype" => "ipdunilvl"))',
//                                    'visible'=>'AdmintransactionsController::getStatusForButtonDisplayGoc($data["status"], 1)',
//                                    'options' => array(
//                                        'class'=>"btn btn-small",
//                                        'confirm'=>'Are you sure you want to APPROVE?',
//                                        'ajax' => array(
//                                            'type' => 'GET',
//                                            'dataType'=>'json',
//                                            'url' => 'js:$(this).attr("href")',
//                                            'success' => 'function(data){
//                                                if(data.result_code == 0)
//                                                {
//                                                    alert(data.result_msg);
//                                                    $.fn.yiiGridView.update("ipdunilvl-grid");
//                                                }
//                                                else
//                                                    alert(data.result_msg);
//                                             }',
//                                        ),
//
//                                    ),
//                                    array('id' => 'send-link-'.uniqid())
//                                ),
//                                'claim'=>array
//                                (
//                                    'label'=>'Claim',
//                                    'icon'=>'ok-sign',
//                                    'url'=>'Yii::app()->createUrl("/admintransactions/processtransaction", array("id" =>$data["unilevel_id"], "status" => "2", "transtype" => "ipdunilvl"))',
//                                    'visible'=>'AdmintransactionsController::getStatusForButtonDisplayGoc($data["status"], 2)',
//                                    'options' => array(
//                                        'class'=>"btn btn-small",
//                                        'confirm'=>'Are you sure you want to CLAIM?',
//                                        'ajax' => array(
//                                            'type' => 'GET',
//                                            'dataType'=>'json',
//                                            'url' => 'js:$(this).attr("href")',
//                                            'success' => 'function(data){
//                                                if(data.result_code == 0)
//                                                {
//                                                    alert(data.result_msg);
//                                                    $.fn.yiiGridView.update("ipdunilvl-grid");
//                                                }
//                                                else
//                                                    alert(data.result_msg);
//                                             }',
//                                        ),
//                                    ),
//                                    array('id' => 'send-link-'.uniqid())
//                                ),
//                                'download'=>array
//                                (
//                                    'label'=>'Download',
//                                    'icon'=>'icon-download-alt',
//                                    'url'=>'Yii::app()->createUrl("/admintransactions/pdfipdunilevel", array("id" =>$data["distributor_id"], "cutoff_id" =>$data["cutoff_id"]))',
//                                    'options' => array(
//                                        'class'=>"btn btn-small",
//                                    ),
//                                    array('id' => 'send-link-'.uniqid())
//                                ),
//                            ),
//                            'header'=>'Action',
//                            'htmlOptions'=>array('style'=>'width:80px;text-align:center'),
//                        ),
        )
        ));
?>
