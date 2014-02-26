<?php

/*
 * @author : owliber
 * @date : 2014-02-01
 * @var $this RegistrationController 
 * @var $this RegistrationForm 
 */

$this->breadcrumbs = array('Registration');

?>
<script type="text/javascript">
function validateUpline()
{
    var text_upline_name = $("#RegistrationForm_upline_name"),
        hidden_upline = $("#RegistrationForm_upline_id");
    
    if (text_upline_name.val() != '') {
        if (hidden_upline.val() == '') {
            $("#RegistrationForm_upline_name").val("");
        }
    }
    else {
        hidden_upline.val("");
    }
}
</script>

<?php Yii::app()->clientScript->registerScript('ui','
         
     $(\'input[rel="tooltip"]\').tooltip();     
     var upline_id = $("#RegistrationForm_upline_id"),
         upline_name = $("#RegistrationForm[upline_name]");
    
 ', CClientScript::POS_END);
 ?>

<?php 

//Yii::app()->user->setFlash('success', '<strong>Well done!</strong> You have successfully registered our new business partner.');
//Yii::app()->user->setFlash('error', '<strong>Ooops!</strong> A problem encountered during the registration. Please contact P5 support.');
Yii::app()->user->setFlash('info', '<strong>Important!</strong> Please make sure to fill-up all required information specially the email address as this is required for the activation of the new partner\'s account.');

$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'X', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'info'=>array('block'=>true, 'fade'=>true, 'closeText'=>'X'), // success, info, warning, error or danger
        ),
)); ?>

<h3>Independent Business Owners (IBO) Registration</h3>
<p class="note">Please take note all fields with <span class="required">*</span> are required.</p>
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'verticalForm',
    'type'=>'horizontal', //vertical, horizontal, inline, search
    'inlineErrors'=>false,
    'enableClientValidation'=>true,
    'clientOptions'=>array(
            'validateOnSubmit'=>true,
        ),
    'htmlOptions'=>array('class'=>'well'),
)); ?>

<?php echo $form->hiddenField($model, 'member_id'); ?>
<?php echo $form->hiddenField($model, 'upline_id'); ?>

<h5>Placement Information</h5>
<?php echo $form->textFieldRow($model,'activation_code', array(
        'class'=>'span3',
        'rel'=>'tooltip',
        'title'=>'Important: Please enter the 20 alphanumeric activation codes provided.',
    )); ?>

<?php //echo $form->dropDownListRow($model,'placement_id', array(''=>Yii::t('none','Select your downlines')) + $model->listDownlines($model->member_id), array('class'=>'span3')); ?>

<div class="control-group">
<?php echo CHtml::label('Place under '. '<span class="required">*</span>', 'RegistrationForm_upline_id',array('class'=>'control-label required')) ?>
    <div class="controls">   
        <?php
        
        $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
                'model'=>$model,
                'attribute'=>'upline_name',
                'sourceUrl'=>  Yii::app()->createUrl('registration/downlines'),
                'options'=>array(
                    'minLength'=>'2',
                    'showAnim'=>'fold',
                    'focus' => 'js:function(event, ui){upline_name.val(ui.item["value"])}',
                    'select' => 'js:function(event, ui){upline_id.val(ui.item["id"]); }',
                ),
                'htmlOptions'=>array(
                    'class'=>'span3',
                    'rel'=>'tooltip',
                    'title'=>'Please type your downline\'s name.',
                    'autocomplete'=>'off',
                    'onblur'=>'validateUpline()'
                ),        
            ));
        
        
        ?>
        <?php echo $form->error($model, 'upline_name'); ?>
    </div>    
</div>

<h5>Partner's Personal Information</h5>
<?php echo $form->textFieldRow($model,'last_name', array('class'=>'span3')); ?>
<?php echo $form->textFieldRow($model,'first_name', array('class'=>'span3')); ?>
<?php echo $form->textFieldRow($model,'middle_name', array('class'=>'span3')); ?>
<?php echo $form->textFieldRow($model,'address1', array('class'=>'span4')); ?>
<?php echo $form->textFieldRow($model,'address2', array('class'=>'span4')); ?>
<?php echo $form->textFieldRow($model,'address3', array('class'=>'span4')); ?>
<?php echo $form->dropDownListRow($model,'country_id', array(''=>Yii::t('none','Select country')) + $model->listCountries(), array('class'=>'span2')); ?>
<?php echo $form->textFieldRow($model,'zip_code', array('class'=>'span1')); ?><br />
<?php echo $form->dropDownListRow($model,'gender', array(''=>Yii::t('none','Select gender')) + array(1=>'Male',2=>'Female'), array('class'=>'span2')); ?>
<?php echo $form->dropDownListRow($model,'civil_status', array(''=>Yii::t('none','Select civil status')) + array(1=>'Single',2=>'Married',3=>'Divorced',4=>'Separated'), array('class'=>'span2')); ?>
<?php echo $form->textFieldRow($model,'birth_date', array('class'=>'span2','rel'=>'tooltip','title'=>'yyyy-mm-dd')); ?>
<?php echo $form->textFieldRow($model,'mobile_no', array('class'=>'span2')); ?>
<?php echo $form->textFieldRow($model,'telephone_no', array('class'=>'span2')); ?>
<?php echo $form->textFieldRow($model,'email', array('class'=>'span3')); ?>
<?php echo $form->textFieldRow($model,'tin_no', array('class'=>'span3')); ?>
<?php echo $form->textFieldRow($model,'company', array('class'=>'span3')); ?>
<?php echo $form->textFieldRow($model,'occupation', array('class'=>'span3')); ?>
<?php echo $form->textFieldRow($model,'spouse_name', array('class'=>'span4')); ?>
<?php echo $form->textFieldRow($model,'spouse_contact_no', array('class'=>'span2')); ?>
<?php echo $form->textFieldRow($model,'beneficiary_name', array('class'=>'span4')); ?>
<?php echo $form->textFieldRow($model,'relationship', array('class'=>'span4')); ?>

<h5>Purchased Production Information</h5>
<?php echo $form->textFieldRow($model,'product_code', array('class'=>'span2')); ?>
<?php echo $form->textFieldRow($model,'product_name', array('class'=>'span3')); ?>
<?php echo $form->textFieldRow($model,'date_purchased', array('class'=>'span2')); ?>
<?php echo $form->dropDownListRow($model,'payment_mode_id', array(''=>Yii::t('none','Select payment type')) + $model->listPaymentTypes(), array('class'=>'span2')); ?>

<?php //echo $form->passwordFieldRow($model, 'password', array('class'=>'span3')); ?>
<?php //echo $form->checkboxRow($model, 'checkbox'); ?>

<!-- Button Group -->
<?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'reset',
    'label'=>'Reset Information',
    'type'=>'danger', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
    'size'=>'large', // null, 'large', 'small' or 'mini'
)); ?>

<?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'submit', 
    'label'=>'Register Business Partner',
    'type'=>'primary',
    'size'=>'large',
)); ?>

<?php $this->endWidget(); ?>

<?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id'=>'ajaxloader',
        'options'=>array(
            'title'=>'Loading',
            'modal'=>true,
            'width'=>'200',
            'height'=>'45',
            'resizable'=>false,
            'autoOpen'=>false,
        ),
)); ?>

<div class="loading"></div><div class="loadingtext">Loading, please wait...</div>

<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>

<?php $this->beginWidget('bootstrap.widgets.TbModal', 
        array('id'=>'message-dialog',
              'autoOpen'=>$this->showDialog,
              'fade'=>true,
)); ?>
 
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4><?php echo $this->dialogTitle; ?></h4>
</div>
 
<div class="modal-body">
    <p><?php echo $this->dialogMessage; ?></p>
</div>
 
<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Close',
        'url'=>$this->errorCode > 0 ? '#' : array('registration/index'),
        'htmlOptions'=>$this->errorCode > 0 ? array('data-dismiss'=>'modal') : "",
    )); ?>
</div>
 
<?php $this->endWidget(); ?>