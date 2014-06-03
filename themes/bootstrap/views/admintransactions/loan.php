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

?>

<table style="width: 100%;" class="table-condensed">
    <tr>
        <td><?php echo CHtml::label('From &nbsp;','lblFrom'); ?></td>
        <td>
            <?php
                $this->widget('CJuiDateTimePicker',array(
                                'model' => $model,
                                'attribute' => 'date_from2',
                                'value'=> $model->date_from2,
                                'mode'=>'date',
                                'options'=>array(
                                    'dateFormat'=>'yy-mm-dd',
                                    'timeFormat'=> 'hh:mm',
                                    'showAnim'=>'fold',
                                    'showOn'=>'button',
                                    'buttonText'=>Yii::t('ui','Date'), 
                                    'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.png', 
                                    'buttonImageOnly'=>true,
                                ),
                                'htmlOptions'=>array('readonly'=>'readonly', 'class'=>'input-medium'),
                                'language'=>'',
                            ));
            ?>
        </td>
        <td>
            <?php
                // Search Field
                echo CHtml::hiddenField('member_id');
                $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
                    'model'=>$model,
                    'attribute'=>'autocomplete_name',
                    'sourceUrl'=>  Yii::app()->createUrl('members/search'),
                    'options'=>array(
                        'minLength'=>'2',
                        'showAnim'=>'fold',
                        'focus' => 'js:function(event, ui){ $("#Loan_autocomplete_name").val(ui.item["value"]) }',
                        'select' => 'js:function(event, ui){ $("#member_id").val(ui.item["id"]); }',
                    ),
                    'htmlOptions'=>array(
                        'style'=>'margin-left: 10px; width: 300px',
                        'rel'=>'tooltip',
                        'title'=>'Please type the member\'s name.',
                        'autocomplete'=>'off',
                        'placeholder'=>'Search By Name',
                    ),        
                ));

                $this->widget('bootstrap.widgets.TbButton', array(
                    'buttonType'=>'submit', 
                    'icon'=>'icon-search',
                    'label'=>'Search', 
                    'htmlOptions'=>array('id'=>'btnSearch', 'name'=>'btnSearch','style'=>'margin-left:10px;')
                ));
            ?>
        </td>
        <td>
            <?php
                $this->widget("bootstrap.widgets.TbButton", array(
                    "label"=>"Export to PDF",
                    "type"=>"info",
                    'url'=>'pdfloansummary?status='.$model->status.'&date_from2='.$model->date_from2.'&date_to='.$model->date_to,
                    "htmlOptions"=>array("style"=>"float: right;"),
                ));
            ?>
        </td>
    </tr>
    <tr>
        <td><?php echo CHtml::label('To  &nbsp;','lblTo', array('style' => 'margin-left: 20px;')); ?></td>
        <td>
            <?php
                $this->widget('CJuiDateTimePicker',array(
                                'model' => $model,
                                'attribute' => 'date_to',
                                'value'=> $model->date_to,
                                'mode'=>'date',
                                'options'=>array(
                                    'dateFormat'=>'yy-mm-dd',
                                    'timeFormat'=> 'hh:mm',
                                    'showAnim'=>'fold',
                                    'showOn'=>'button',
                                    'buttonText'=>Yii::t('ui','Date'), 
                                    'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.png', 
                                    'buttonImageOnly'=>true,
                                ),
                                'htmlOptions'=>array('readonly'=>'readonly', 'class'=>'input-medium'),
                                'language'=>'',
                            ));
            ?>
        </td>
    </tr>
    <tr>
        <td><?php echo CHtml::label('Status', 'lblStatus'); ?></td>
        <td>
            <?php
                $options = array('1, 2, 3, 4'=>'All', '1'=>'Completed', '2'=>'Filed', '3'=>'Approved', '4'=>'Claimed');
                echo $form->dropDownList($model, 'status', $options, array('style'=>'width: 120px;'));
            ?>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Search')); ?></td>
    </tr>
</table>

<?php $this->endWidget(); ?>


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