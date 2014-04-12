<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-11-2014
------------------------*/

$this->breadcrumbs = array('Payouts'=>'#','IPD Unilevel');

Yii::app()->user->setFlash('info', '<strong>Info</strong> | Select the cut-off date from the dropdown list that you want to generate.');

$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'info'//=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
));
?>

<h3>Distributor Unilevel Payout</h3>

<?php
Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');

/** @var BootActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'searchForm',
    'type'=>'search',
    'htmlOptions'=>array('class'=>'well'),
));

echo $form->dropDownListRow($model,'cutoff_id', ReferenceModel::list_cutoffs(TransactionTypes::IPD_UNILEVEL), array('class'=>'span3'));

echo CHtml::label('RP Status:  &nbsp;', 'lblStatus', array('style'=>'margin-left: 20px;'));

$options = array('1, 2'=>'All', '1'=>'Completed', '2'=>'Pending');
echo $form->dropDownList($model, 'status', $options, array('style'=>'width: 120px;'));

$this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Search', 'htmlOptions' => array('style' => 'margin-left: 10px;')));

if ($model->status == 2)
{
    $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Flush Out', 'htmlOptions' => array('style' => 'margin-left: 10px;', 'onclick' => 'if(!confirm("Are you sure you want to FLUSH OUT?")){return false;};')));
    echo CHtml::hiddenField('fout', '', array());
}

$this->widget("bootstrap.widgets.TbButton", array(
                                            "label"=>"Export to PDF",
                                            //"icon"=>"icon-chevron-left",
                                            "type"=>"info",
                                            'url'=>'pdfipdunilevelsummary?cutoff_id='.$model->cutoff_id,
                                            "htmlOptions"=>array("style"=>"float: right"),
                                        ));

$this->endWidget(); 

//display table
if (isset($dataProvider))
{
    $this->renderPartial('_ipdunilevelview', array(
                'dataProvider'=>$dataProvider,
                'total'=>$total,
        ));
}
?>

