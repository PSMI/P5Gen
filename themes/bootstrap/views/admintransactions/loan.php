<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-06-2014
------------------------*/

$this->breadcrumbs = array('Member Transactions'=>'#','Loan');

Yii::app()->user->setFlash('info', '<strong>Note </strong> | All loans automatically becomes available once they are completed.');

$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'info'//=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
));
?>

<h3>Loan Payout</h3>


<?php
    
    
    
    Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');

    /** @var BootActiveForm $form */
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id'=>'searchForm',
        'type'=>'search',
        'htmlOptions'=>array('class'=>'well'),
    ));

    echo CHtml::label('From &nbsp;','lblFrom');

    //date from
    $this->widget('CJuiDateTimePicker',array(
                    'model' => $model,
                    'attribute' => 'date_from2',
                    //'name'=>'calDateFrom',
                    //'id'=>'calDateFrom',
                    //'value'=>date('Y-m-d'),
                    'value'=> $model->date_from2,
                    'mode'=>'date', //use "time","date" or "datetime" (default)
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

    echo CHtml::label('To  &nbsp;','lblTo', array('style' => 'margin-left: 20px;'));

    //date to
    $this->widget('CJuiDateTimePicker',array(
                    'model' => $model,
                    'attribute' => 'date_to',
                    //'name'=>'calDateTo',
                    //'id'=>'calDateTo',
                    //'value'=>date('Y-m-d'),
                    'value'=> $model->date_to,
                    'mode'=>'date', //use "time","date" or "datetime" (default)
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

    echo CHtml::label('Status  &nbsp;', 'lblStatus', array('style'=>'margin-left: 20px;'));
    $options = array('1, 2, 3, 4'=>'All', '1'=>'Completed', '2'=>'Filed', '3'=>'Approved', '4'=>'Claimed');
    echo $form->dropDownList($model, 'status', $options, array('style'=>'width: 120px;'));
    
    $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Search', 'htmlOptions' => array('style' => 'margin-left: 10px;')));

    $this->widget("bootstrap.widgets.TbButton", array(
                                        "label"=>"Export to PDF",
                                        //"icon"=>"icon-chevron-left",
                                        "type"=>"info",
                                        'url'=>'pdfloansummary?status='.$model->status.'&date_from2='.$model->date_from2.'&date_to='.$model->date_to,
                                        "htmlOptions"=>array("style"=>"float: right;"),
                                    ));
    $this->endWidget();
    
?>


<?php
//display table
if (isset($dataProvider))
{
    $this->renderPartial('_loanview', array(
                'dataProvider'=>$dataProvider,
                'total'=>$total
        ));
}
else
{
    
}
?>