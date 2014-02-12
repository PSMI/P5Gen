<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h1>Loan</h1>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'index-form',
    'enableClientValidation' => true,
    'clientOptions' => array(
            'validateOnSubmit' => true,
    ),
    'htmlOptions'=>array('class'=>'well'),
));

// infinite 5-member loan
$this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $dataProvider,
        'enablePagination' => true,
        'columns' => array(
                array('name'=>'DateCompleted',
                    'header'=>'Date Completed',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["member_id"])',
                    'htmlOptions' => array('style' => 'text-align:center'),    
                ), 
                array('name'=>'IBOCount',
                    'header'=>'IBO Count',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["last_name"])',
                    'htmlOptions' => array('style' => 'text-align:center'),    
                ),
                array('name'=>'Amount',
                    'header'=>'Amount',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["last_name"])',
                    'htmlOptions' => array('style' => 'text-align:center'),    
                ),
                array('name'=>'Status',
                    'header'=>'Status',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["last_name"])',
                    'htmlOptions' => array('style' => 'text-align:center'),    
                ),
                array('name'=>'Action',
                    'header'=>'Action',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["last_name"])',
                    'htmlOptions' => array('style' => 'text-align:center'),    
                ),
        )
));

// level completion
$this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $dataProvider,
        'enablePagination' => true,
        'columns' => array(
                array('name'=>'Level',
                    'header'=>'Level',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["member_id"])',
                    'htmlOptions' => array('style' => 'text-align:center'),    
                ), 
                array('name'=>'DateCompleted',
                    'header'=>'Date Completed',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["member_id"])',
                    'htmlOptions' => array('style' => 'text-align:center'),    
                ), 
                array('name'=>'IBOCount',
                    'header'=>'IBO Count',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["last_name"])',
                    'htmlOptions' => array('style' => 'text-align:center'),    
                ),
                array('name'=>'Amount',
                    'header'=>'Amount',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["last_name"])',
                    'htmlOptions' => array('style' => 'text-align:center'),    
                ),
                array('name'=>'Status',
                    'header'=>'Status',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["last_name"])',
                    'htmlOptions' => array('style' => 'text-align:center'),    
                ),
                array('name'=>'Action',
                    'header'=>'Action',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["last_name"])',
                    'htmlOptions' => array('style' => 'text-align:center'),    
                ),
        )
));

$this->endWidget(); 
?>