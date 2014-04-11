<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 03-29-2014
------------------------*/

$this->breadcrumbs = array('Payouts'=>'#','IBO RP Commission');

Yii::app()->user->setFlash('info', '<strong>Information </strong>| Select the cut-off date from the dropdown list that you want to generate.');

$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'info'//=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
));
?>

<h3>IBO Repeat Purchase Commission</h3>

<?php
/** @var BootActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'searchForm',
    'type'=>'search',
    'htmlOptions'=>array('class'=>'well'),
));

echo $form->dropDownListRow($model,'cutoff_id', ReferenceModel::list_cutoffs(TransactionTypes::REPEAT_PURCHASE_COMMISSION), array('class'=>'span3'));

$this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Search', 'htmlOptions' => array('style' => 'margin-left: 10px;')));

$this->widget("bootstrap.widgets.TbButton", array(
                                            "label"=>"Export to PDF",
                                            //"icon"=>"icon-chevron-left",
                                            "type"=>"info",
                                            'url'=>'ibopdfrpcommissionsummary?cutoff_id='.$model->cutoff_id,
                                            "htmlOptions"=>array("style"=>"float: right"),
                                        ));

$this->endWidget(); 


//display table
if (isset($dataProvider))
{
    $this->renderPartial('_iborpcommissionview', array(
                'dataProvider'=>$dataProvider,
                'total'=>$total,
        ));
}
else
{
    
}
?>

