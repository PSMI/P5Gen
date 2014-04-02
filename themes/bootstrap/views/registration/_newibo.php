<?php

/**
 * @author Noel Antonio
 * @date 04-01-2014
 */

$this->breadcrumbs = array('IPD to IBO Registration');

?>
<script type="text/javascript">
function submitForm()
{
    $("#hidden_flag").val(1);
    $("#verticalForm").submit();
}
</script>
<?php Yii::app()->clientScript->registerScript('ui','
         
     $(\'input[rel="tooltip"]\').tooltip();     
     var distributor_id = $("#DistributorForm_distributor_id"),
         distributor_name = $("#DistributorForm[distributor_name]");', 
        
    CClientScript::POS_END);

Yii::app()->user->setFlash('danger', '<strong>Important!</strong> Please make sure to fill-up all required information specially the email address as this is required for the activation of the new partners\' account.');

$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true,
        'fade'=>true,
        'closeText'=>'&times;',
        'alerts'=>array(
            'danger'
        ),
)); ?>

<h3>IPD to IBO Registration</h3>
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

<?php echo $form->hiddenField($model, 'distributor_id'); ?>

<h5>Distributor List</h5>
<div class="control-group">
<?php echo CHtml::label('Type a Distributor', 'DistributorForm_distributor_id',array('class'=>'control-label required')) ?>
    <div class="controls">   
        <?php
        
        $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
                'model'=>$model,
                'attribute'=>'distributor_name',
                'sourceUrl'=>  Yii::app()->createUrl('registration/distributor'),
                'options'=>array(
                    'minLength'=>'2',
                    'showAnim'=>'fold',
                    'focus' => 'js:function(event, ui){ distributor_name.val(ui.item["value"]); }',
                    'select' => 'js:function(event, ui){ distributor_id.val(ui.item["id"]); }',
                ),
                'htmlOptions'=>array(
                    'class'=>'span3',
                    'rel'=>'tooltip',
                    'title'=>'Please type the distributor\'s name.',
                    'autocomplete'=>'off',
                ),        
            ));
        
        
        ?>
        <?php echo $form->error($model, 'distributor_name'); ?>
        
        <?php $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'ajaxSubmit',
            'label'=>'View Profile',
            'type'=>'primary',
            'url'=>'viewprofile',
            'ajaxOptions'=>array(
                'type'=>'POST',
                'data'=>'js:{ "member_id": $("#DistributorForm_distributor_id").val()}',
                'success'=>'function(data){
                    $("#data").html(data);
                    $("#data").show();
                    $("#reg_input").show();
                }',
                'error'=>'function(e){
                    alert(e);
                }'
            )
        )); ?>
    </div>
</div>

<div id="data" style="display: none"></div>

<div id="reg_input" style="display: none">
    <h5>Activation Information</h5>
    <?php echo $form->textFieldRow($model,'activation_code', array(
            'class'=>'span3',
            'rel'=>'tooltip',
            'title'=>'Important: Please enter the 20 alphanumeric activation codes provided.',
    )); ?>

    <?php echo CHtml::hiddenField('hidden_flag'); ?>

    <!-- Button Group -->
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'reset',
        'label'=>'Reset Information',
        'type'=>'danger',
        'size'=>'large'
    )); ?>

    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'label'=>'Register Business Partner',
        'type'=>'primary',
        'size'=>'large'
    )); ?>
</div>


<?php $this->beginWidget('bootstrap.widgets.TbModal', 
        array('id'=>'confirm-dialog',
              'autoOpen'=>$this->showConfirm,
              'fade'=>true,
)); ?>
 
<!-- NETWORK CONFIRMATION DIALOG -->
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>CONFIRMATION</h4>
</div>
 
<div id="cont-msg" class="modal-body"><p></p></div>
<div id="confirm-msg" class="modal-body">
</div>
 
<div class="modal-footer">
    <?php
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'button',
            'label' => 'YES',
            'type' => 'default',
            'htmlOptions'=>array('onclick'=>'submitForm()')
        ));
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'button',
            'label' => 'NO',
            'type' => 'default',
            'htmlOptions'=>array('data-dismiss'=>'modal')
        ));
    ?>
</div>
<?php $this->endWidget(); // end of confirm dialog ?>

<?php $this->endWidget(); // form end widget ?>

<!-- AJAX LOADER -->
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

<!-- MESSAGE DIALOG -->
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

<?php if ($this->showConfirm): ?>
<script type="text/javascript">
    $.ajax({
        url: 'confirm',
        type: 'post',
        data: { last_name: $("#RegistrationForm_last_name").val(),
                first_name: $("#RegistrationForm_first_name").val(),
                middle_name: $("#RegistrationForm_middle_name").val(),
                upline_id: $("#RegistrationForm_upline_id").val()
        },
        success: function(data){
            $("#cont-msg").html("Kindly check the table below to verify the \n\
                    possible location of the new member. Once you proceed, it will\n\
                    be FINAL. Do you wish to continue?");
            $("#confirm-msg").html(data);
        },
        error: function(e){
            alert("Error:" + e);
        }
    });
</script>
<?php endif; ?>