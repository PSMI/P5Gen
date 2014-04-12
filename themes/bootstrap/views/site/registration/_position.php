<?php

$this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'confirm-grid',
        'type'=>'striped bordered condensed',
        'dataProvider' => $dataProvider,
        'htmlOptions'=>array('style'=>'font-size:12px'),
        'enablePagination' => false,
        'columns' => array(
                        array('name'=>'member_name',
                            'header'=>'Member Name',
                            'value'=>'CHtml::encode($data["member_name"])',
                            'htmlOptions' => array('style' => 'text-align:center'), 
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'placed_under',
                              'header'=>'Placed Under',
                                'value'=>'CHtml::encode($data["upline_name"])',
                              'htmlOptions' => array('style' => 'text-align:center'),
                              'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
                        array('name'=>'endorser_name',
                            'header'=>'Endorser Name',
                            'value'=>'CHtml::encode($data["endorser_name"])',
                            'htmlOptions' => array('style' => 'text-align:center'),
                            'headerHtmlOptions' => array('style' => 'text-align:center'),
                        ),
        )
));
?>
