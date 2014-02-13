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

<h3>Activation Code Generation History</h3>

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

echo CHtml::hiddenField('batch_id', $batchId);

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

$this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 
//                                                    'url'=>Yii::app()->createUrl('codes/pdf'),
                                                    'htmlOptions'=>array('submit'=>Yii::app()->createUrl('codes/pdf')), 
                                                    'label'=>'Export to PDF', 'type'=>'primary', 'size'=>'large'));

$this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'button', 'label'=>'Back', 'type'=>'primary', 'size'=>'large', 'htmlOptions'=>array('onclick'=>'location.href = "' . Yii::app()->createUrl("codes/index") . '";')));

?>

<?php $this->endWidget(); ?>