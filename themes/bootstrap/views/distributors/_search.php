<?php

/**
 * @author Noel Antonio
 * @date 04-01-2014
 */

Yii::app()->clientScript->registerScript('ui','
         
     $(\'input[rel="tooltip"]\').tooltip();     
     var member_name = $("#MemberDetailsModel_autocomplete_name"),
         member_id = $("#member_id");
    
 ', CClientScript::POS_END);

echo CHtml::hiddenField('member_id');
$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
        'model'=>$model,
        'attribute'=>'autocomplete_name',
        'sourceUrl'=>  Yii::app()->createUrl('distributors/search'),
        'options'=>array(
            'minLength'=>'2',
            'showAnim'=>'fold',
            'focus' => 'js:function(event, ui){ member_name.val(ui.item["value"]) }',
            'select' => 'js:function(event, ui){ member_id.val(ui.item["id"]); }',
        ),
        'htmlOptions'=>array(
            'class'=>'span4',
            'rel'=>'tooltip',
            'title'=>'Please type the distributor\'s name.',
            'autocomplete'=>'off',
        ),        
    ));

$this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit', 
        'type'=>'primary',
        'icon'=>'icon-search',
        'label'=>'Search', 
        'htmlOptions'=>array('id'=>'btnSearch', 'name'=>'btnSearch','style'=>'margin-top:-10px;')
)); 
?>