<?php

/* @var $this SiteController */
$this->layout = '//layouts/column2';
$this->pageTitle = Yii::app()->name;
?>
<?php
//Add alerts to homepage
$unassigned_count = $alert['unassigned-downline'];
$new_placement_count = $alert['new-placement'];

($unassigned_count > 1 || $new_placement_count > 1) ? $s = ' members' : $s = ' member';

if($unassigned_count > 0)
    Yii::app()->user->setFlash('error', "<strong>Urgent </strong> | You have (".$unassigned_count.") unassigned ".$s.". Kindly place them under your downlines as soon as possible! ".CHtml::link('Click here to assign.', array('placement/assign')));

if($new_placement_count > 0)
    Yii::app()->user->setFlash('warning', "<strong>Notice </strong> | You have (".$new_placement_count.") new ".$s." that needs your approval! ".CHtml::link('Click here to approve.', array('placement/index')));

// Render them all with single `TbAlert`
$this->widget('bootstrap.widgets.TbAlert', array(
    'block' => true,
    'fade' => true,
    'closeText' => '&times;', // false equals no close link
    'events' => array(),
    'htmlOptions' => array(),
    'alerts' => array(// configurations per alert type
        'success',
        'info', // you don't need to specify full config
        'warning',
        'error',
    ),
));
?>

<?php
$this->beginWidget('bootstrap.widgets.TbHeroUnit', array(
    'heading' => 'Welcome to ' . CHtml::encode(Yii::app()->name),
    'headingOptions' => array(
        'style' => 'font-size:50px',
    ),
));
?>

<?php $this->endWidget(); ?>
