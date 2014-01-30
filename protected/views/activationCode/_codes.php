<?php

/**
 * @author Noel Antonio
 * @date 01-28-2014
 */
$this->breadcrumbs = array(
    'Activation Code Generation History'
);
?>
<style>
    .grid-view { width: 50%; }
</style>

<h1>Activation Code Generation History</h1>

<?php
$form = $this->beginWidget('CActiveForm', array(
        'id' => 'history-form',
        'enableClientValidation' => true,
        'enableAjaxValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),        
    ));

$this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $dataProvider,
        'enablePagination' => true,
        'columns' => array(
                        array('name'=>'ActivationCode',
                            'header'=>'Activation Code',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["activation_code"])',
                            'htmlOptions' => array('style' =>'text-align:center', 'width'=>'1%'),    
                        ), 
                        array('name'=>'Status',
                            'header'=>'Status',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["status"])',
                            'htmlOptions' => array('style' => 'text-align:center', 'width'=>'1%'),    
                        ),                        
        )
        ));

?>

<?php $this->endWidget(); ?>