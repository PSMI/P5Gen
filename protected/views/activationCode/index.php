<?php

/**
 * @author Noel Antonio
 * @date 01-27-2014
 */
$this->breadcrumbs = array(
    'Activation Code Generator'
);
?>
<h1>Activation Code Generator</h1>

<?php
$form = $this->beginWidget('CActiveForm', array(
        'id' => 'index-form',
        'enableClientValidation' => true,
        'enableAjaxValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),        
    ));
?>

<div id="form-details">
    <?php echo CHtml::label('Quantity', 'quantity'); ?>
    <?php echo $form->textField($model, 'quantity', array('autocomplete'=>'off', 'onkeypress'=>'return numberonly(event);')); ?>
    <?php echo CHtml::submitButton('GENERATE', array('id'=>'btnGenerate')); ?>
</div>

<!-- dialog box -->
<?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id'=>'dialog-box',
        'options'=>array(
            'title'=>$this->title,
            'modal'=>true,
            'width'=>'350',
            'height'=>'auto',
            'resizable'=>false,
            'autoOpen'=>$this->showDialog,
            'buttons'=>array(
                'OK'=>'js:function(){
                    $(this).dialog("close");
                }'
            )
        ),
)); ?>

<br />
<?php echo $this->msg; ?>
<br />

<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<!-- dialog box -->

<?php $this->endWidget(); ?>

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
            'buttons'=>array(
                'Yes'=>'js:function(){
                    $("#hiddenQty").val($("#ActivationCodeModel_quantity").val());
                    $("#generate-form").submit();
                    $(this).dialog("close");
                }',
                'No'=>'js:function(){
                    $(this).dialog("close");
                }'
            )
        ),
)); ?>

<br />
<?php echo $this->msg; ?>
<br />

<?php echo CHtml::beginForm(array('activationCode/index'), 'POST', array(
        'id'=>'generate-form',
        'name'=>'generate-form')); 
      echo CHtml::hiddenField('hiddenQty');
      echo CHtml::endForm(); 
      
$this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<!-- confirmation dialog box -->



<script type="text/javascript">
//    $(document).ready(function(){
//        $('#btnGenerate').live('click', function(){
//                var qty = $('#ActivationCodeModel_quantity').val();
//
//                if (qty == '') {
//                    alert("Quantity field is empty! Please enter a valid quantity.");
//                }
//                else if (qty == 0) {
//                    alert("Zero value not accepted! Please enter a valid quantity.")
//                }
//                else {
//                    var confirmation = confirm("Are you sure you want to generate " + qty + " new activation code(s)?");
//                    if (confirmation) {
//                        $('#generate-form').submit();
//                    }
//                }
//        });
//    });
</script>