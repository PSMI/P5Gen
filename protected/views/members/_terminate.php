<?php

/**
 * @author Noel Antonio
 * @date 01-30-2014
 */
?>
<h1>Terminate Member Account</h1>

<?php
$form = $this->beginWidget('CActiveForm', array(
        'id' => 'terminate-form',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),        
    ));
?>

<?php echo $form->hiddenField($model, 'member_id', array('value'=>$data["member_id"])); ?>

<div style="color:red;"><?php echo $form->errorSummary($model); ?></div>

<table style="width: auto;">
    <tr>
        <td><?php echo CHtml::label("Member Name"); ?></td>
        <td><?php echo CHtml::textField("txtName", $fullName, array('style'=>'font-weight: bold; text-align: center', 'readonly'=>true)); ?></td>
    </tr>
    <tr>
        <td><?php echo CHtml::label("Current Status"); ?></td>
        <td><?php echo CHtml::textField("txtCurrent", $status, array('style'=>'font-weight: bold; text-align: center', 'readonly'=>true)); ?></td>
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'status'); ?></td>
        <td><?php echo $form->dropDownList($model, 'status', $list, array('prompt'=>'Please Select')); ?></td>
        <?php echo $form->error($model, 'status'); ?>
    </tr>
    <tr>
        <td><?php echo CHtml::submitButton('SUBMIT'); ?></td>
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