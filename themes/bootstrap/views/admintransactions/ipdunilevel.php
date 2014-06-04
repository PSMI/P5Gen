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

?>

<table style="width: 100%;" class="table-condensed">
    <tr>
        <td><?php echo $form->dropDownListRow($model,'cutoff_id', ReferenceModel::list_cutoffs(TransactionTypes::IPD_UNILEVEL), array('class'=>'span3')); ?></td>
        <td>
            <?php
                echo CHtml::hiddenField('member_id');
                $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
                    'model'=>$model,
                    'attribute'=>'autocomplete_name',
                    'sourceUrl'=>  Yii::app()->createUrl('distributors/search'),
                    'options'=>array(
                        'minLength'=>'2',
                        'showAnim'=>'fold',
                        'focus' => 'js:function(event, ui){ $("#Unilevel_autocomplete_name").val(ui.item["value"]) }',
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
                    "type"=>"info",
                    'url'=>'pdfipdunilevelsummary?cutoff_id='.$model->cutoff_id,
                    "htmlOptions"=>array("style"=>"float: right"),
                ));
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php
                echo CHtml::label('RP Status:&nbsp;', 'lblStatus', array('style'=>'margin-left: 20px;'));
                $options = array('0, 1, 2, 3'=>'All', '0'=>'Pending', '1'=>'Approved', '2'=>'Claimed', '3'=>'Flushed out', '4'=>'Completed' );
                echo $form->dropDownList($model, 'status', $options, array('style'=>'width: 120px;'));
            ?>
        </td>
    </tr>
</table>

<div style="margin-left: 9%">
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Search', 'htmlOptions' => array('style' => 'margin-left: 10px;'))); ?>
</div>

<?php











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

