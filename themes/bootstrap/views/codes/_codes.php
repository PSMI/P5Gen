<?php

/**
 * @author Noel Antonio
 * @date 01-28-2014
 */
$this->breadcrumbs = array(
    'Activation Code Generation History'
);
?>
<style type="text/css">
    /*.grid-view { width: 50%; }*/
</style>

<h1>Activation Code Generation History</h1>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'history-form',
        'enableClientValidation' => true,
        'enableAjaxValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),        
    'htmlOptions'=>array('class'=>'well'),
));

$this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'placement-grid',
        'type'=>'striped bordered condensed',
        'dataProvider'=>$dataProvider,
        'enablePagination' => true,
        'columns' => array(
                        array('name'=>'ActivationCode',
                            'header'=>'Activation Code',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["activation_code"])',
                            'htmlOptions' => array('style' =>'text-align:center'),    
                        ), 
                        array('name'=>'Status',
                            'header'=>'Status',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["status"])',
                            'htmlOptions' => array('style' => 'text-align:center'),    
                        ),                        
        )
        ));

$this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'button', 'label'=>'Back', 'htmlOptions'=>array('onclick'=>'location.href = "' . Yii::app()->createUrl("codes/index") . '";')));

?>

<?php $this->endWidget(); ?>