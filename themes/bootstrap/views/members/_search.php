<?php

/**
 * @author Noel Antonio
 * @date 01-30-2014
 */

/*Yii::app()->clientScript->registerScript('ui','
         
     $(\'input[rel="tooltip"]\').tooltip();     
     var member_name = $("#MemberDetailsModel_autocomplete_name"),
         member_id = $("#member_id");
    
 ', CClientScript::POS_END);*/
?>

<?php

    /*echo CHtml::hiddenField('member_id');
    $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
            'model'=>$model,
            'attribute'=>'autocomplete_name',
            'sourceUrl'=>  Yii::app()->createUrl('members/search'),
            'options'=>array(
                'minLength'=>'2',
                'showAnim'=>'fold',
                'focus' => 'js:function(event, ui){ member_name.val(ui.item["value"]) }',
                'select' => 'js:function(event, ui){ member_id.val(ui.item["id"]); }',
            ),
            'htmlOptions'=>array(
                'class'=>'span4',
                'rel'=>'tooltip',
                'title'=>'Please type your member\'s name.',
                'autocomplete'=>'off',
            ),        
        ));*/

    echo CHtml::textField("txtSearchCode", '', array('style'=>'width: 355px', 'autocomplete'=>'off', 'placeholder'=>'Search By Name Or Activation Code'));

    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit', 
        'type'=>'primary',
        'icon'=>'icon-search',
        'label'=>'Search', 
        'htmlOptions'=>array('id'=>'btnSearch', 'name'=>'btnSearch','style'=>'margin-top:-10px;')
    )); 
?>

<br/>

<?php
    /*echo CHtml::textField("txtSearchCode", '', array('style'=>'width: 355px', 'autocomplete'=>'off'));
    
    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit', 
        'type'=>'primary',
        'icon'=>'icon-search',
        'label'=>'Search Activation Code', 
        'htmlOptions'=>array('id'=>'btnSearchCode', 'name'=>'btnSearchCode','style'=>'margin-top:-10px;')
    )); */
?>