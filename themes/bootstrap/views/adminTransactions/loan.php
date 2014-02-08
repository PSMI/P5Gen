<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-06-2014
------------------------*/
?>

<h1>Transactions - Loan</h1>

<?php
Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');

/** @var BootActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'searchForm',
    'type'=>'search',
    'htmlOptions'=>array('class'=>'well'),
));

echo CHtml::label('From: ','lblFrom');

//date from
$this->widget('CJuiDateTimePicker',array(
                'name'=>'calDateFrom',
                'id'=>'calDateFrom',
                'value'=>date('Y-m-d H:i'),
                'mode'=>'datetime', //use "time","date" or "datetime" (default)
                'options'=>array(
                    'dateFormat'=>'yy-mm-dd',
                    'timeFormat'=> 'hh:mm',
                    'showAnim'=>'fold', // 'show' (the default), 'slideDown', 'fadeIn', 'fold'
                    'showOn'=>'button', // 'focus', 'button', 'both'
                    'buttonText'=>Yii::t('ui','Date'), 
                    'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.png', 
                    'buttonImageOnly'=>true,
                ),// jquery plugin options
                'htmlOptions'=>array('readonly'=>'readonly', 'class'=>'input-medium'),
                'language'=>'',
            ));

echo CHtml::label('To: ','lblTo', array('style' => 'margin-left: 20px;'));

//date to
$this->widget('CJuiDateTimePicker',array(
                'name'=>'calDateTo',
                'id'=>'calDateTo',
                'value'=>date('Y-m-d H:i'),
                'mode'=>'datetime', //use "time","date" or "datetime" (default)
                'options'=>array(
                    'dateFormat'=>'yy-mm-dd',
                    'timeFormat'=> 'hh:mm',
                    'showAnim'=>'fold', // 'show' (the default), 'slideDown', 'fadeIn', 'fold'
                    'showOn'=>'button', // 'focus', 'button', 'both'
                    'buttonText'=>Yii::t('ui','Date'), 
                    'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.png', 
                    'buttonImageOnly'=>true,
                ),// jquery plugin options
                'htmlOptions'=>array('readonly'=>'readonly', 'class'=>'input-medium'),
                'language'=>'',
            ));


$this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Search', 'htmlOptions' => array('style' => 'margin-left: 10px;')));

$this->endWidget(); ?>

<?php
//display table
if (isset($dataProvider))
{
    $this->renderPartial('_view', array(
                'dataProvider'=>$dataProvider,
        ));
}
else
{
    
}
?>