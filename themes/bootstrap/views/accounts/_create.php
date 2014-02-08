<?php

/**
 * @author Noel Antonio
 * @date 01-30-2014
 */
?>
<h1>Create Account Profile</h1>

<script type="text/javascript">
function generateUsername()
{
    $.ajax({
        url: 'ajaxUser',
        type: 'post',
        dataType: 'json',
        data: {
            id: $("#MembersModel_member_id").val(),
            first: $("#MemberDetailsModel_first_name").val(),
            last: $("#MemberDetailsModel_last_name").val()
        },
        success: function(data){
            $("#MembersModel_username").val(data);
        },
        error: function(e){
            alert(e);
        }
    });
}
</script>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'update-form',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
        'htmlOptions'=>array('class'=>'well'),
    ));
?>

<p class="note">Fields with <span style="color: red">*</span> are required.</p>

<table style="width: 50%;">
    <tr>
        <td><?php echo $form->hiddenField($membersModel, 'member_id', array('readonly'=>true, 'value'=>$maxId)); ?></td>
    </tr>
    <tr>
        <td><?php echo $form->textFieldRow($membersModel, 'username', array('readonly'=>true)); ?></td>
    </tr>
    <tr>
        <td><?php echo $form->passwordFieldRow($membersModel, 'password'); ?></td>
    </tr>
    <tr>
        <td><?php echo $form->dropDownListRow($membersModel, 'account_type_id', CHtml::listData($accountList, 'account_type_id', 'account_type_name'), array('prompt'=>'Please Select')); ?></td>
    </tr>
</table>

<div style="border: solid 1px gainsboro; width: auto; margin-top: 2%; margin-bottom: 2%;"></div>

<table style="width: 40%;">
    <tr>
        <td><?php echo $form->textFieldRow($model, 'last_name', array('onblur'=>'generateUsername()')); ?></td>
        <td><?php echo $form->textFieldRow($model, 'first_name', array('onblur'=>'generateUsername()')); ?></td>
        <td><?php echo $form->textFieldRow($model, 'middle_name'); ?></td>
    </tr>
    <tr>
        <td><?php echo $form->textAreaRow($model, 'address1'); ?></td>
        <td><?php echo $form->textAreaRow($model, 'address2'); ?></td>
        <td><?php echo $form->textAreaRow($model, 'address3'); ?></td>
    </tr>
    <tr>
        <td><?php echo $form->textFieldRow($model, 'zip_code'); ?></td>
        <td><?php echo $form->dropDownListRow($model, 'gender', array('1'=>'Male', '2'=>'Female'), array('prompt'=>'Please Select')); ?></td>
        <td><?php echo $form->dropDownListRow($model, 'civil_status', array('Please Select', 'Single', 'Married', 'Divorced', 'Separated')); ?></td>
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
        <td><?php echo $form->textFieldRow($model, 'mobile_no'); ?></td>
        <td><?php echo $form->textFieldRow($model, 'telephone_no'); ?></td>
    </tr>
    <tr>
        <td><?php echo $form->textFieldRow($model, 'email'); ?></td>
        <td><?php echo $form->textFieldRow($model, 'tin_no'); ?></td>
        <td><?php echo $form->textFieldRow($model, 'company'); ?></td>
    </tr>
    <tr>
        <td><?php echo $form->textFieldRow($model, 'occupation'); ?></td>
        <td><?php echo $form->textFieldRow($model, 'spouse_name'); ?></td>
        <td><?php echo $form->textFieldRow($model, 'spouse_contact_no'); ?></td>
    </tr>
    <tr>
        <td><?php echo $form->textFieldRow($model, 'beneficiary_name'); ?></td>
        <td><?php echo $form->textFieldRow($model, 'relationship'); ?></td>
    </tr>
    <tr>
        <td><?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Add Account')); $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'button', 'label'=>'Back', 'htmlOptions'=>array('onclick'=>'location.href = "' . Yii::app()->createUrl("accounts/index") . '";'))); ?></td>
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
<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'ajaxSubmit', 'url'=>Yii::app()->createUrl("accounts/createSuccess"), 'label'=>'YES',
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
            'title'=>'CREATE ADMIN ACCOUNT',
            'modal'=>true,
            'width'=>'350',
            'height'=>'auto',
            'resizable'=>false,
            'autoOpen'=>false,
            'buttons'=>array(
                'OK'=>'js:function(){
                    location.href = "'. Yii::app()->createUrl('accounts/index') . '";
                }'
            )
        ),
)); ?>

<br />
<div id="msg"></div>
<br />

<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<!-- success dialog box -->