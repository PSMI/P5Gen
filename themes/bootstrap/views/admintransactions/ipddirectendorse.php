<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 03-31-2014
------------------------*/

$this->breadcrumbs = array('Payouts'=>'#','IPD Direct Endorsement');

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

<h3>Distributor Endorsement Payout</h3>

<?php

/** @var BootActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'searchForm',
    'type'=>'search',
    'htmlOptions'=>array('class'=>'well'),
));

echo $form->dropDownListRow($model,'cutoff_id', ReferenceModel::list_cutoffs(TransactionTypes::IPD_DIRECT_ENDORSE), array('class'=>'span3'));

$this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Generate', 'htmlOptions' => array('style' => 'margin-left: 10px;')));

echo CHtml::hiddenField('member_id');
$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
    'model'=>$model,
    'attribute'=>'autocomplete_name',
    'sourceUrl'=>  Yii::app()->createUrl('distributors/search'),
    'options'=>array(
        'minLength'=>'2',
        'showAnim'=>'fold',
        'focus' => 'js:function(event, ui){ $("#IpdDirectEndorsement_autocomplete_name").val(ui.item["value"]) }',
        'select' => 'js:function(event, ui){ $("#member_id").val(ui.item["id"]); }',
    ),
    'htmlOptions'=>array(
        'style'=>'margin-left: 10px',
        'rel'=>'tooltip',
        'title'=>'Please type the member\'s name.',
        'autocomplete'=>'off',
        'placeholder'=>'Search By Name'
    ),        
));

$this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'submit', 
    'icon'=>'icon-search',
    'label'=>'Search', 
    'htmlOptions'=>array('id'=>'btnSearch', 'name'=>'btnSearch','style'=>'margin-left:10px;')
));

$this->widget("bootstrap.widgets.TbButton", array(
                                            "label"=>"Export to PDF",
                                            //"icon"=>"icon-chevron-left",
                                            "type"=>"info",
                                            'url'=>'pdfipddirectsummary?cutoff_id='.$model->cutoff_id,
                                            "htmlOptions"=>array("style"=>"float: right"),
                                        ));

$this->endWidget(); 

if (isset($dataProvider))
{
    $this->renderPartial('_ipddirectendorseview', array(
                'dataProvider'=>$dataProvider,
                'total'=>$total,
                'model'=>$model
        ));
    
}
?>

