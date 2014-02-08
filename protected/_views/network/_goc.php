<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h1>Group Override Commission</h1>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'index-form',
    'enableClientValidation' => true,
    'clientOptions' => array(
            'validateOnSubmit' => true,
    ),
    'htmlOptions'=>array('class'=>'well'),
));

$this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $dataProvider,
        'enablePagination' => true,
        'columns' => array(
                array('name'=>'LoanLevel',
                    'header'=>'Loan Level',
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
                array('name'=>'CurrentIBO',
                    'header'=>'Current IBO',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["last_name"])',
                    'htmlOptions' => array('style' => 'text-align:center'),    
                ),
                array('name'=>'RemainingIBO',
                    'header'=>'Remaining IBO',
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
        )
));

$this->endWidget(); 
?>