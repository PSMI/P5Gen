<?php $this->breadcrumbs = array('Administration'=>'#','Accounts'=>  Yii::app()->createUrl('accounts/index'),'Change Password'); ?>
<h3>Change Password</h3>
<?php
$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'change-form',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),        
    'htmlOptions'=>array('class'=>'well'),
    ));
?>

<table style="width: 100%;">
    <tr>
        <td><?php echo CHtml::label('Current Password: ', 'txtCurrentPass'); ?></td>
        <td><?php echo CHtml::passwordField('txtCurrentPass'); ?></td>
    </tr>
    <tr>
        <td><?php echo CHtml::label('New Password: ', 'txtNewPass'); ?></td>
        <td><?php echo CHtml::passwordField('txtNewPass'); ?></td>
    </tr>
    <tr>
        <td><?php echo CHtml::label('Confirm Password: ', 'txtConfirmPass'); ?></td>
        <td><?php echo CHtml::passwordField('txtConfirmPass'); ?></td>
    </tr>
    <tr>
        <td>
            <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Change', 'htmlOptions'=>array('id'=>'btnChange', 'name'=>'btnChange', 'value'=>'Change'))); ?>
            <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'button', 'type'=>'primary', 'label'=>'Back', 'htmlOptions'=>array('onclick'=>'location.href = "' . Yii::app()->createUrl("accounts/index") . '";'))); ?>
        </td>
    </tr>
</table>

<?php $this->endWidget(); ?>

<!-- dialog box -->
<?php 
$trigger = false;
$buttons = array();
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
                    location.href = "'. Yii::app()->createUrl('accounts/index') . '";
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