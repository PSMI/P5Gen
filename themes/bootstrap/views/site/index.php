<?php
/* @var $this SiteController */
$this->layout = '//layouts/column2';
$this->pageTitle=Yii::app()->name;
?>

<?php $this->beginWidget('bootstrap.widgets.TbHeroUnit',array(
    'heading'=>'Welcome to '.CHtml::encode(Yii::app()->name),
    'headingOptions'=>array(
        'style'=>'font-size:50px',
    ),
)); ?>

<?php $this->endWidget(); ?>
