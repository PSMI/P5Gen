<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-12-2014
------------------------*/

$this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'bonus-grid',
        'type'=>'striped bordered condensed',
        //'filter' => $model->search(),
        'dataProvider' => $dataProvider,
        'enablePagination' => true,
        //'template'=>"{items}",
        'columns' => array(
                        array(
                            'header' => '',
                            'value' => '$row + ($this->grid->dataProvider->pagination->currentPage
                            * $this->grid->dataProvider->pagination->pageSize + 1)',
                        ),
                        array('name'=>'promo_name',
                              'header'=>'Promo Name',
                              'htmlOptions' => array('style' => 'text-align:center'),
                              'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'ibo_count',
                            'header'=>'IBO Count',
                            'htmlOptions' => array('style' => 'text-align:center'), 
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'date_completed',
                            'header'=>'Date Completed',
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
        )
        ));
?>
