<?php

/**
 * @author Noel Antonio
 * @date 01-28-2014
 */
?>
<?php $this->breadcrumbs = array('Administration'=>'#','Accounts'); ?>
<h3>Administration Accounts</h3>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'index-form',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),        
    ));

$this->widget('bootstrap.widgets.TbButton', array(
    'label'=>'Add New Account',
    'type'=>'primary',
    'size'=>'large',
    'htmlOptions'=>array('onclick'=>'location.href="' . Yii::app()->createUrl("accounts/create") . '";')
));
?>

<?php echo $this->renderPartial('_search'); ?>

<?php echo $this->renderPartial('_view', array('dataProvider'=>$dataProvider)); ?>

<?php $this->endWidget(); ?>