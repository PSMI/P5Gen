<?php

/**
 * @author Noel Antonio
 * @date 01-30-2014
 */
?>
<h1>Update Member Profile</h1>

<?php
$form = $this->beginWidget('CActiveForm', array(
        'id' => 'update-form',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),        
    ));
?>

<p class="note">Fields with <span style="color: red">*</span> are required.</p>

<div style="color:red;"><?php echo $form->errorSummary($model); ?></div>

<?php echo $form->hiddenField($model, 'member_id', array('value'=>$data["member_id"])); ?>

<table style="width: auto;">
    <tr>
        <td><?php echo $form->labelEx($model,'last_name'); ?></td>
        <td><?php echo $form->textField($model, 'last_name', array('value'=>$data["last_name"])); ?></td>
        <!--<td><?php echo $form->error($model, 'last_name'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'first_name'); ?></td>
        <td><?php echo $form->textField($model, 'first_name', array('value'=>$data["first_name"])); ?></td>
        <!--<td><?php echo $form->error($model, 'first_name'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'middle_name'); ?></td>
        <td><?php echo $form->textField($model, 'middle_name', array('value'=>$data["middle_name"])); ?></td>
        <!--<td><?php echo $form->error($model, 'middle_name'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'address1'); ?></td>
        <td><?php echo $form->textArea($model, 'address1', array('value'=>$data["address1"])); ?></td>
        <!--<td><?php echo $form->error($model, 'address1'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'address2'); ?></td>
        <td><?php echo $form->textArea($model, 'address2', array('value'=>$data["address2"])); ?></td>
        <!--<td><?php echo $form->error($model, 'address2'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'address3'); ?></td>
        <td><?php echo $form->textArea($model, 'address3', array('value'=>$data["address3"])); ?></td>
        <!--<td><?php echo $form->error($model, 'address3'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'zip_code'); ?></td>
        <td><?php echo $form->textField($model, 'zip_code', array('value'=>$data["zip_code"])); ?></td>
        <!--<td><?php echo $form->error($model, 'zip_code'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'gender'); ?></td>
        <td><?php echo $form->dropDownList($model, 'gender', array('1'=>'Male', '2'=>'Female'), array('prompt'=>'Please Select')); ?></td>
        <!--<td><?php echo $form->error($model, 'gender'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'civil_status'); ?></td>
        <td><?php echo $form->dropDownList($model, 'civil_status', array('-- Please Select --', 'Single', 'Married', 'Widow', 'Separated')); ?></td>
        <!--<td><?php echo $form->error($model, 'civil_status'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model, "birth_date"); ?></td>
        <td><?php echo $form->textField($model,'birth_date', array('id'=>'birth_date','readonly'=>'true', 'style'=>'width: 120px; text-align: center;')).
                  CHtml::image(Yii::app()->request->baseUrl."/images/calendar.png","calendar", array("id"=>"calbutton","class"=>"pointer","style"=>"cursor: pointer;"));
                  $this->widget('application.extensions.calendar.SCalendar',
                  array(
                    'inputField'=>'birth_date',
                    'button'=>'calbutton',
                    'showsTime'=>false,
                    'ifFormat'=>'%Y-%m-%d',
                  )); ?>
        </td>
        <!--<td><?php echo $form->error($model, 'birth_date'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'mobile_no'); ?></td>
        <td><?php echo $form->textField($model, 'mobile_no', array('value'=>$data["mobile_no"])); ?></td>
        <!--<td><?php echo $form->error($model, 'mobile_no'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'telephone_fax_no'); ?></td>
        <td><?php echo $form->textField($model, 'telephone_fax_no', array('value'=>$data["telephone_fax_no"])); ?></td>
        <!--<td><?php echo $form->error($model, 'telephone_fax_no'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'email'); ?></td>
        <td><?php echo $form->textField($model, 'email', array('value'=>$data["email"])); ?></td>
        <!--<td><?php echo $form->error($model, 'email'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'tin_number'); ?></td>
        <td><?php echo $form->textField($model, 'tin_number', array('value'=>$data["tin_number"])); ?></td>
        <!--<td><?php echo $form->error($model, 'tin_number'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'company'); ?></td>
        <td><?php echo $form->textField($model, 'company', array('value'=>$data["company"])); ?></td>
        <!--<td><?php echo $form->error($model, 'company'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'occupation_id'); ?></td>
        <td><?php echo $form->textField($model, 'occupation_id', array('value'=>$data["occupation_id"])); ?></td>
        <!--<td><?php echo $form->error($model, 'occupation_id'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'spouse_name'); ?></td>
        <td><?php echo $form->textField($model, 'spouse_name', array('value'=>$data["spouse_name"])); ?></td>
        <!--<td><?php echo $form->error($model, 'spouse_name'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'spouse_contact_no'); ?></td>
        <td><?php echo $form->textField($model, 'spouse_contact_no', array('value'=>$data["spouse_contact_no"])); ?></td>
        <!--<td><?php echo $form->error($model, 'spouse_contact_no'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'beneficiary'); ?></td>
        <td><?php echo $form->textField($model, 'beneficiary', array('value'=>$data["beneficiary"])); ?></td>
        <!--<td><?php echo $form->error($model, 'beneficiary'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo $form->labelEx($model,'relationship'); ?></td>
        <td><?php echo $form->textField($model, 'relationship', array('value'=>$data["relationship"])); ?></td>
        <!--<td><?php echo $form->error($model, 'relationship'); ?></td>-->
    </tr>
    <tr>
        <td><?php echo CHtml::submitButton('UPDATE'); ?></td>
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