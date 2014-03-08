<?php

/**
 * @author Noel Antonio
 * @date 01-30-2014
 */
?>
<?php $this->breadcrumbs = array('Members'=>'#','Change Member Status'); ?>
<h3>Change Member Status</h3>

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
        <td>
        <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=>'submit', 
                'type'=>'primary', 
                'label'=>'Submit'
             )); 
        ?>
        <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=>'button', 
                'type'=>'primary',
                'label'=>'Back', 
                'htmlOptions'=>array(
                    'onclick'=>'location.href = "' . Yii::app()->createUrl("members/index") . '";'
                    )
            ));
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

<!-- confirmation dialog box -->
<?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id'=>'confirm-box',
        'options'=>array(
            'title'=>$this->title,
            'modal'=>true,
            'width'=>'350',
            'height'=>'auto',
            'resizable'=>false,
            'autoOpen'=>$this->showConfirm,
        ),
)); 
?>

<br />
<?php echo $this->msg; ?>
<br />

<div align="right">
<?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'ajaxSubmit', 
    'url'=>Yii::app()->createUrl("members/terminateSuccess"), 
    'label'=>'YES',
        'ajaxOptions'=>array(
            'type'=>'POST',
            'data'=>'js:$("#terminate-form").serialize()',
            'success'=>'function(data){
                $("#msg").html(data);
                $("#confirm-box").dialog("close");
                $("#success-box").dialog("open");
            }'
        )
    ));

$this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'button', 
    'label'=>'NO', 
    'htmlOptions'=>array(
        'onclick'=>'$("#confirm-box").dialog("close")'
        )
    ));
?>
</div>
      
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<!-- confirmation dialog box -->

<!-- success dialog box -->
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id'=>'success-box',
        'options'=>array(
            'title'=>'UPDATE MEMBER STATUS',
            'modal'=>true,
            'width'=>'350',
            'height'=>'auto',
            'resizable'=>false,
            'autoOpen'=>false,
            'buttons'=>array(
                'OK'=>'js:function(){
                    location.href = "'. Yii::app()->createUrl('members/index') . '";
                }'
            )
        ),
)); ?>

<br />
<div id="msg"></div>
<br />

<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<!-- success dialog box -->