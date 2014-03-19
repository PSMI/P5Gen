<?php

/**
 * @author Noel Antonio
 * @date 01-30-2014
 */
?>
<?php $this->breadcrumbs = array('Members'=>'#','Update Member Profile'); ?>

<?php
Yii::app()->user->setFlash('danger', '<strong>Important!</strong> Please make sure to fill-up all required information specially the email address.');

$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'danger'//=>array('block'=>true, 'fade'=>true, 'closeText'=>'X'), // success, info, warning, error or danger
        ),
));
?>
<h3>Update Member Profile</h3>

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

<br/>
<p>Activation Code: <?php $this->widget('bootstrap.widgets.TbLabel', array(
    'type'=>'important', // 'success', 'warning', 'important', 'info' or 'inverse'
    'label'=>$activationCode,
    'htmlOptions'=>array('style'=>'font-size:16px'),
)); ?>
</p>
<br/>

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
        <td> 
        <div class="control-group">
            <?php echo CHtml::label('Birth Date '. '<span class="required">*</span>', 'MemberDetailsModel_birth_date',array('class'=>'control-label required')) ?>
                <div class="controls">  
                <?php 
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $model,
                        'attribute' => 'birth_date',
                        'htmlOptions' => array(
                            'size' => '10',
                            'maxlength' => '10',
                            'readonly' => true,
                            'value'=>$model->birth_date,
                        ),
                        'options' => array(
                            'showOn'=>'button',
                            'buttonImageOnly' => true,
                            'changeMonth' => true,
                            'changeYear' => true,
                            'buttonText'=> 'Select Birth Date',
                            'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.png',
                            'dateFormat'=>'yy-mm-dd',
                            'maxDate' =>'0',
                            'yearRange'=>'1900:' . date('Y'),
                        )
                    ));
                    echo $form->error($model, 'birth_date');
                ?>
                </div>    
            </div>
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
        <td>
        <div class="control-group">
            <?php echo CHtml::label('Date Joined '. '<span class="required">*</span>', 'MemberDetailsModel_date_joined',array('class'=>'control-label required')) ?>
                <div class="controls">  
                <?php 
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $model,
                        'attribute' => 'date_joined',
                        'htmlOptions' => array(
                            'size' => '10',
                            'maxlength' => '10',
                            'readonly' => true,
                            'value'=>$model->date_joined,
                        ),
                        'options' => array(
                            'showOn'=>'button',
                            'buttonImageOnly' => true,
                            'changeMonth' => true,
                            'changeYear' => true,
                            'buttonText'=> 'Select Birth Date',
                            'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.png',
                            'dateFormat'=>'yy-mm-dd',
                            'maxDate' =>'0',
                            'yearRange'=>'1900:' . date('Y'),
                        )
                    ));
                    echo $form->error($model, 'date_joined');
                ?>
                </div>    
            </div>    
        </td>
    </tr>
    <tr>
        <td>
            <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Update')); ?>
            <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'button',  'type'=>'primary', 'label'=>'Back', 'htmlOptions'=>array('onclick'=>'location.href = "' . Yii::app()->createUrl("members/index") . '";'))); ?>
        </td>
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