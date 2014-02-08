<?php

/**
 * @author Noel Antonio
 * @date 01-30-2014
 */
?>
<h1>Update Member Profile</h1>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'update-form',
    'enableClientValidation' => true,
    'clientOptions' => array(
            'validateOnSubmit' => true,
    ),
    'htmlOptions'=>array('class'=>'well'),
));
?>

<p class="note">Fields with <span style="color: red">*</span> are required.</p>

<?php echo $form->hiddenField($model, 'member_id', array('value'=>$data["member_id"])); ?>

<table style="width: 100%;">
    <tr>
        <td><?php echo $form->textFieldRow($model, 'last_name', array('value'=>$data["last_name"])); ?></td>
        <td><?php echo $form->textFieldRow($model, 'first_name', array('value'=>$data["first_name"])); ?></td>
        <td><?php echo $form->textFieldRow($model, 'middle_name', array('value'=>$data["middle_name"])); ?></td>
    </tr>
    <tr colspan="3">
        <td><?php echo $form->textAreaRow($model, 'address1', array('value'=>$data["address1"], 'class'=>'span3', 'rows'=>'5')); ?></td>
        <td><?php echo $form->textAreaRow($model, 'address2', array('value'=>$data["address2"], 'class'=>'span3', 'rows'=>'5')); ?></td>
        <td><?php echo $form->textAreaRow($model, 'address3', array('value'=>$data["address3"], 'class'=>'span3', 'rows'=>'5')); ?></td>
    </tr>
    <tr>
        <td><?php echo $form->textFieldRow($model, 'zip_code', array('value'=>$data["zip_code"])); ?></td>
        <td><?php echo $form->dropDownListRow($model, 'gender', array('1'=>'Male', '2'=>'Female'), array('prompt'=>'Please Select')); ?></td>
        <td><?php echo $form->dropDownListRow($model, 'civil_status', array('-- Please Select --', 'Single', 'Married', 'Divorced', 'Separated')); ?></td>
    </tr>
    <tr>
        <td><?php echo $form->textFieldRow($model,'birth_date', array('id'=>'birth_date','readonly'=>'true', 'style'=>'width: 120px; text-align: center;')).
                  CHtml::image(Yii::app()->request->baseUrl."/images/calendar.png","calendar", array("id"=>"calbutton","class"=>"pointer","style"=>"cursor: pointer;"));
                  $this->widget('application.extensions.calendar.SCalendar',
                  array(
                    'inputField'=>'birth_date',
                    'button'=>'calbutton',
                    'showsTime'=>false,
                    'ifFormat'=>'%Y-%m-%d',
                  )); ?>
        </td>
        <td><?php echo $form->textFieldRow($model, 'mobile_no', array('value'=>$data["mobile_no"])); ?></td>
        <td><?php echo $form->textFieldRow($model, 'telephone_no', array('value'=>$data["telephone_no"])); ?></td>
    </tr>
    <tr>
        <td><?php echo $form->textFieldRow($model, 'email', array('value'=>$data["email"])); ?></td>
        <td><?php echo $form->textFieldRow($model, 'tin_no', array('value'=>$data["tin_no"])); ?></td>
        <td><?php echo $form->textFieldRow($model, 'company', array('value'=>$data["company"])); ?></td>
    </tr>
    <tr>
        <td><?php echo $form->textFieldRow($model, 'occupation', array('value'=>$data["occupation"])); ?></td>
        <td><?php echo $form->textFieldRow($model, 'spouse_name', array('value'=>$data["spouse_name"])); ?></td>
        <td><?php echo $form->textFieldRow($model, 'spouse_contact_no', array('value'=>$data["spouse_contact_no"])); ?></td>
    </tr>
    <tr>
        <td><?php echo $form->textFieldRow($model, 'beneficiary_name', array('value'=>$data["beneficiary_name"])); ?></td>
        <td><?php echo $form->textFieldRow($model, 'relationship', array('value'=>$data["relationship"])); ?></td>
    </tr>
    <tr>
        <td><?php   
                    $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Update'));
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
<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'ajaxSubmit', 'url'=>Yii::app()->createUrl("members/updateSuccess"), 'label'=>'YES',
                                                                'ajaxOptions'=>array(
                                                                    'type'=>'POST',
                                                                    'data'=>'js:$("#update-form").serialize()',
                                                                    'success'=>'function(data){
                                                                        $("#msg").html(data);
                                                                        $("#confirm-box").dialog("close");
                                                                        $("#success-box").dialog("open");
                                                                    }'
                                                                )));

$this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'button', 'label'=>'NO', 'htmlOptions'=>array('onclick'=>'$("#confirm-box").dialog("close")')));
?>
</div>
      
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<!-- confirmation dialog box -->

<!-- success dialog box -->
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id'=>'success-box',
        'options'=>array(
            'title'=>'UPDATE MEMBER',
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