<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-09-2014
------------------------*/

$this->breadcrumbs = array('Member Transactions'=>'#','Group Override Commission');

Yii::app()->user->setFlash('warning', '<strong>Important!</strong> Please make sure that the date input is a valid cut-off.');

$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'warning'//=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
));
?>

<h3>GOC Payout</h3>

<?php

/** @var BootActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'searchForm',
    'type'=>'search',
    'htmlOptions'=>array('class'=>'well'),
));

echo $form->dropDownListRow($model,'cutoff_id', ReferenceModel::list_cutoffs(TransactionTypes::GOC), array('class'=>'span3'));

$this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Generate', 'htmlOptions' => array('style' => 'margin-left: 10px;')));

$this->endWidget(); 

    
//display table
if (isset($dataProvider))
{
    $this->renderPartial('_gocview', array(
                'dataProvider'=>$dataProvider,
                'total'=>$total
        ));
}
else
{
    
}
?>

