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
    $("#DistributorForm_hidden_flag").val(1);
    $("#verticalForm").submit();
}

function confirmFinalNetwork()
{
    $.ajax({
        url: 'confirm2',
        type: 'post',
        data: { distributor_name: $("#DistributorForm_distributor_name").val(),
                upline_id: $("#DistributorForm_upline_id").val(),
                ibo_endorser_id: $("#DistributorForm_ibo_endorser_id").val()
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
}
</script>
<?php Yii::app()->clientScript->registerScript('ui','
         
     $(\'input[rel="tooltip"]\').tooltip();     
     var distributor_id = $("#DistributorForm_distributor_id"),
         distributor_name = $("#DistributorForm[distributor_name]"),
         upline_id = $("#DistributorForm_upline_id"),
         upline_name = $("#DistributorForm[upline_name]");', 
        
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

<?php
$form1 = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'defaultForm',
    'type'=>'horizontal',
    'inlineErrors'=>false,
    'enableClientValidation'=>true,
    'clientOptions'=>array(
            'validateOnSubmit'=>true,
        ),
    'htmlOptions'=>array('class'=>'well'),
)); ?>

<h5>Distributor List</h5>
<div class="control-group">
<?php echo CHtml::label('Qualified Distributor', 'DistributorForm_distributor_id',array('class'=>'control-label required')) ?>
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
        
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'ajaxButton',
            'label'=>'View Profile',
            'type'=>'primary',
            'url'=>'viewprofile',
            'ajaxOptions'=>array(
                'type'=>'POST',
                'data'=>'js:{ "member_id": $("#DistributorForm_distributor_id").val()}',
                'success'=>'function(data){
                    $("#data").html(data);
                    $("#displayDiv").show();
                }',
                'error'=>'function(e){
                    alert(e);
                }'
            )
        )); ?>
    </div>
</div>
<?php $this->endWidget(); // first form end ?>

<div id="displayDiv" style="display: none">
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'verticalForm',
    'type'=>'horizontal',
    'inlineErrors'=>false,
    'enableClientValidation'=>true,
    'clientOptions'=>array(
            'validateOnSubmit'=>true,
        ),
    'htmlOptions'=>array('class'=>'well'),
)); ?>


<div id="data"></div>


<?php echo $form->hiddenField($model, 'distributor_id'); ?>
<?php echo $form->hiddenField($model, 'upline_id'); ?>
<?php echo $form->hiddenField($model, 'hidden_flag'); ?>

<h5>Activation Information</h5>
<?php echo $form->textFieldRow($model,'activation_code', array(
        'class'=>'span3',
        'rel'=>'tooltip',
        'title'=>'Important: Please enter the 20 alphanumeric activation codes provided.',
)); ?>

<h5>Placement Information</h5>
<div class="control-group">
<?php echo CHtml::label('Place Under'. '<span class="required">*</span>', 'DistributorForm_upline_id',array('class'=>'control-label required')) ?>
    <div class="controls">   
        <?php

        $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
                'model'=>$model,
                'attribute'=>'upline_name',
                'source'=>'js: function(request, response) {
                    $.ajax({
                        url: "'.Yii::app()->createUrl('registration/downlinesOfImmediateIBO').'",
                        dataType: "json",
                        data: {
                            term: request.term,
                            ibo: $("#DistributorForm_ibo_endorser_id").val()
                        },
                        success: function (data) {
                                response(data);
                        },
                        error: function (e) {
                                alert(e);
                        }
                    })
                 }',
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
                ),        
            ));


        ?>
        <?php echo $form->error($model, 'upline_name'); ?>
    </div>    
</div>

<!-- Button Group -->
<?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'button',
    'label'=>'Reset Information',
    'type'=>'danger',
    'size'=>'large',
    'htmlOptions'=>array('onclick'=>'location.href = "'.Yii::app()->createUrl('registration/new').'";')
)); ?>

<?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'ajaxButton',
    'label'=>'Register Business Partner',
    'type'=>'primary',
    'size'=>'large',
    'url'=>'ajaxRegister',
    'ajaxOptions'=>array(
        'type'=>'POST',
        'dataType'=>'json',
        'data'=>'js:$("#verticalForm").serialize()',
        'success'=>'function(data){
            if (data.code == 0)
            {
                confirmFinalNetwork();
                $("#confirm-dialog").dialog("open");
            }
            else
            {
                $("#msg").html(data.message);
                $("#message-dialog").dialog("open");
            }
        }',
        'error'=>'function(e){
            alert(e);
        }'
    )
)); ?>
    

<!-- MESSAGE DIALOG -->
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id'=>'message-dialog',
        'options'=>array(
            'title'=>'NOTIFICATION',
            'modal'=>true,
            'width'=>'500',
            'height'=>'auto',
            'resizable'=>false,
            'autoOpen'=>false,
            'buttons'=>array(
                'OK'=>'js:function(){
                    $(this).dialog("close");
                }'
            )
        ),
)); ?>
<br />
<div id="msg"></div>
<br />
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<!-- MESSAGE DIALOG -->

<!-- NETWORK CONFIRMATION DIALOG -->
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id'=>'confirm-dialog',
        'options'=>array(
            'title'=>'CONFIRMATION',
            'modal'=>true,
            'width'=>'700',
            'height'=>'auto',
            'resizable'=>false,
            'autoOpen'=>false,
        ),
)); ?>
<br />
<div id="cont-msg"></div>
<div id="confirm-msg"></div>
<br/>
<div style="text-align: right">
<?php
    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'button',
        'label' => 'YES',
        'type' => 'primary',
        'htmlOptions'=>array('onclick'=>'submitForm()')
    ));
    echo '&nbsp;&nbsp;';
    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'button',
        'label' => 'NO',
        'type' => 'primary',
        'htmlOptions'=>array('onclick'=>'$("#confirm-dialog").dialog("close")')
    ));
?>
</div>
<br />
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<!-- NETWORK CONFIRMATION DIALOG -->


<!-- SUCCESS MESSAGE DIALOG -->
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id'=>'success-dialog',
        'options'=>array(
            'title'=>$this->dialogTitle,
            'modal'=>true,
            'width'=>'500',
            'height'=>'auto',
            'resizable'=>false,
            'autoOpen'=>$this->showDialog,
            'buttons'=>array(
                'OK'=>'js:function(){
                    location.href = "'.Yii::app()->createUrl('registration/new').'";
                }'
            )
        ),
)); ?>
<br />
<?php echo $this->dialogMessage; ?>
<br />
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<!-- SUCCESS MESSAGE DIALOG -->

<?php $this->endWidget(); // form end widget ?>
</div>