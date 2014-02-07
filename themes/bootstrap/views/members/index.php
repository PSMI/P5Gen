<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h1>Member Management</h1>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'index-form',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),        
    'htmlOptions'=>array('class'=>'well'),
    ));
?>

<?php echo $this->renderPartial('_search'); ?>

<?php echo $this->renderPartial('_view', array('dataProvider'=>$dataProvider)); ?>

<?php $this->endWidget(); ?>