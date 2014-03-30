<?php

/*
 * @author : owliber
 * @date : 2014-03-30
 */
?>
    
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type'=>'horizontal',
        'id' => 'purchase-form',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ), 
        'htmlOptions'=>array('class'=>'well')
    ));
?>
<?php echo '<h4>'.$distributor['last_name'] . ', ' . $distributor['first_name'] . ' ' . $distributor['middle_name'] . '</h4>'; ?>
<?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
    'buttons'=>array(
        array(
            'label'=>'Add Items',
            'url'=>'#',
            'icon'=>'icon-plus-sign',
            'htmlOptions'=>array(
                'data-toggle'=>'modal',
                'data-target'=>'#purchase-modal',
            ),
        ),
        array(
            'label'=>'Purchase History', 
            'url'=>'#',
            'icon'=>'icon-shopping-cart'
        ),
    ),
)); ?>
<?php $this->renderPartial('_form',array('model'=>$model)); ?>
<?php $this->renderPartial('_lists',array('dataProvider'=>$dataProvider)); ?>
<?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
    'buttons'=>array(
        array(
            'label'=>'Checkout',
            'url'=>'#',
            'type'=>'primary',
            'icon'=>'icon-check',            
            'htmlOptions'=>array(
                'confirm'=>'Are you sure you want continue purchasing?',
                'data-toggle'=>'modal',
                'data-target'=>'#purchase-modal',
            ),
        ),
        array(
            'label'=>'Cancel Purchase', 
            'url'=>'#',
            'icon'=>'icon-trash',
            'htmlOptions'=>array(
                'confirm'=>'Are you sure you want cancel purchasing?',
                'data-toggle'=>'modal',
                'data-target'=>'#purchase-modal',
            ),
        ),
    ),
)); ?>
<?php $this->endWidget(); ?>





