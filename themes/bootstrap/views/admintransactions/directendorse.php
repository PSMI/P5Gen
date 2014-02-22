<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-11-2014
------------------------*/

$this->breadcrumbs = array('Member Transactions'=>'#','Direct Endorsement');

Yii::app()->user->setFlash('info', '<strong>Important!</strong> Please make sure that the date input is a valid cut-off.');

$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'info'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
));
?>

<h3>Direct Endorsement Payout</h3>

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

$this->endWidget(); 


    
//display table
if (isset($dataProvider))
{
    $this->renderPartial('_directendorseview', array(
                'dataProvider'=>$dataProvider,
        ));
}
else
{
    
}
?>

