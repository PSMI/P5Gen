<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Yii::app()->clientScript->registerScript('ui','
         
     $(\'input[rel="tooltip"]\').tooltip();     
    // var member_name = $("#PurchasesModel_autocomplete_name"),
     //    purchaser_id = $("#purchaser_id");
    
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
<?php echo CHtml::hiddenField('purchase_summary_id', Yii::app()->session['purchase_summary_id']); ?>
<?php echo CHtml::hiddenField('purchaser_id',  Yii::app()->session['purchaser_id']); ?>
<?php echo CHtml::label('Find Member &nbsp;', 'autocomplete_name'); ?>
<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
            'model'=>$model,
            'attribute'=>'autocomplete_name',
            'sourceUrl'=>  Yii::app()->createUrl('distributors/searchAll'),
            'options'=>array(
                'minLength'=>'2',
                'showAnim'=>'fold',
                'focus' => 'js:function(event, ui){ $("#PurchasesModel_autocomplete_name").val(ui.item["value"]) }',
                'select' => 'js:function(event, ui){ $("#purchaser_id").val(ui.item["id"]); }',
            ),
            'htmlOptions'=>array(
                'class'=>'span4',
                'rel'=>'tooltip',
                'title'=>'Please type the member\'s name.',
                'autocomplete'=>'off',
//                'disabled'=>$this->input_disabled,
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
    'url'=>  Yii::app()->createUrl('purchase/clearsession'),
)); ?>

<?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'link', 
    'type'=>'warning',
    'label'=>'Purchase History',
    'url'=>  Yii::app()->createUrl('purchase/history'),
    'htmlOptions'=>array('class'=>'pull-right','target'=>'blank'),
)); ?>

<?php $this->endWidget(); ?>