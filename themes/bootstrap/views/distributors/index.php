<?php

/**
 * @author Noel Antonio
 * @date 04-01-2014
 */
?>
<?php $this->breadcrumbs = array('Distributors'=>'#','Distributor Management'); ?>
<h3>Distributor Management</h3>
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'index-form',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),        
    ));
?>

<?php echo $this->renderPartial('_search', array('model'=>$model)); ?>

<?php echo $this->renderPartial('_view', array('dataProvider'=>$dataProvider)); ?>

<?php $this->endWidget(); ?>