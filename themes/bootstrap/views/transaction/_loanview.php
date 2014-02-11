<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-12-2014
------------------------*/

$this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'loans-grid',
        'type'=>'striped bordered condensed',
        //'filter' => $model->search(),
        'dataProvider' => $dataProvider,
        'enablePagination' => true,
        'template'=>"{items}",
        'columns' => array(
                        array('name'=>'member_name',
                              'header'=>'Member Name',
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
                            'htmlOptions' => array('style' => 'text-align:center'),  
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'date_created',
                            'header'=>'Date Created',
                            'htmlOptions' => array('style' => 'text-align:center'), 
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'date_approved',
                            'header'=>'Date Processed',
                            'htmlOptions' => array('style' => 'text-align:center'), 
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'approved_by',
                            'header'=>'Approved By',
                            'htmlOptions' => array('style' => 'text-align:center'),  
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'status',
                            'header'=>'Status',
                            'value' => '$data["status"] == 0 ? "Pending" : "Approved"',
                            'htmlOptions' => array('style' => 'text-align:center'),  
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
        )
        ));
?>
