<?php

/**
 * @author Noel Antonio
 * @date 01-30-2014
 */
?>
<h1>Terminate Member Account</h1>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'terminate-form',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
        'htmlOptions'=>array('class'=>'well'),
    ));
?>

<?php echo $form->hiddenField($model, 'member_id', array('value'=>$data["member_id"])); ?>

<table style="width: auto;">
    <tr>
        <td><?php echo CHtml::label("Member Name", "txtName"); ?></td>
        <td><?php echo CHtml::textField("txtName", $fullName, array('style'=>'font-weight: bold; text-align: center', 'readonly'=>true)); ?></td>
    </tr>
    <tr>
        <td><?php echo CHtml::label("Current Status", "txtCurrent"); ?></td>
        <td><?php echo CHtml::textField("txtCurrent", $status, array('style'=>'font-weight: bold; text-align: center', 'readonly'=>true)); ?></td>
    </tr>
    <tr>
        <td colspan="2"><?php echo $form->dropDownListRow($model, 'status', $list, array('prompt'=>'Please Select')); ?></td>
    </tr>
    <tr>
        <td><?php   
                    $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Submit'));
                    $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'button', 'label'=>'Back', 'htmlOptions'=>array('onclick'=>'location.href = "' . Yii::app()->createUrl("members/index") . '";')));
        ?></td>
    </tr>
</table>

<?php $this->endWidget(); ?>

<!-- dialog box -->
<?php 
$trigger = false;
if ($this->showDialog) 
{
    $buttons = array(
                'OK'=>'js:function(){
                    $(this).dialog("close");
                }'
            );
    $trigger = $this->showDialog;
}
else if ($this->showRedirect)
{
    $buttons = array(
                'OK'=>'js:function(){
                    location.href = "'. Yii::app()->createUrl('members/index') . '";
                }'
            );
    $trigger = $this->showRedirect;
}

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id'=>'dialog-box',
        'options'=>array(
            'title'=>$this->title,
            'modal'=>true,
            'width'=>'350',
            'height'=>'auto',
            'resizable'=>false,
            'autoOpen'=>$trigger,
            'buttons'=>$buttons
        ),
)); ?>

<br />
<?php echo $this->msg; ?>
<br />

<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<!-- dialog box -->