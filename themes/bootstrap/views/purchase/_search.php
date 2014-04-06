<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::app()->clientScript->registerScript('ui','
         
     $(\'input[rel="tooltip"]\').tooltip();     
     var distributor_name = $("#PurchasesModel_autocomplete_name"),
         distributor_id = $("#distributor_id");
    
 ', CClientScript::POS_END);
?>
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type'=>'search',
        'id' => 'search-form',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ), 
        'htmlOptions'=>array('class'=>'well')
    ));
?>
<?php echo CHtml::hiddenField('distributor_id',  Yii::app()->session['distributor_id']); ?>
<?php echo CHtml::label('Find Distributor &nbsp;', 'autocomplete_name'); ?>
<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
            'model'=>$model,
            'attribute'=>'autocomplete_name',
            'sourceUrl'=>  Yii::app()->createUrl('distributors/search'),
            'options'=>array(
                'minLength'=>'2',
                'showAnim'=>'fold',
                'focus' => 'js:function(event, ui){ distributor_name.val(ui.item["value"]) }',
                'select' => 'js:function(event, ui){ distributor_id.val(ui.item["id"]); }',
            ),
            'htmlOptions'=>array(
                'class'=>'span4',
                'rel'=>'tooltip',
                'title'=>'Please type the distributor\'s name.',
                'autocomplete'=>'off',
            ),        
        ));
?>
    
<?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'submit', 
    'type'=>'primary',
    'label'=>'Select', 
)); ?>

<?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'link', 
    'type'=>'info',
    'label'=>'Clear',
    'url'=>  Yii::app()->createUrl('purchase/index'),
)); ?>
<?php $this->endWidget(); ?>