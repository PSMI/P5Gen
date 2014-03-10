<?php

/*
 * @author : owliber
 * @date : 2014-03-10
 */

?>

<?php $this->breadcrumbs = array('Administration'=>'#',
    'Job Scheduler'
);?>

<?php Yii::app()->user->setFlash('danger', '<strong>Important!</strong> Dragons Ahead! Stopping the job will cause all transactions not to be processed. Please be careful.');

$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'danger'//=>array('block'=>true, 'fade'=>true, 'closeText'=>'X'), // success, info, warning, error or danger
        ),
)); ?>

<h3>Job Scheduler</h3>

<?php
if($job_status == 1)
{
    $label = 'Stop Job';
    $type = 'primary';
    $status = 'Job scheduler is running. All transactions are being processed. Make sure there are no pending transaction before stopping.';
    $textcolor = '#4ba403';
    $icon = 'icon-stop';
}
else
{
    $label = 'Start Job';
    $type = 'danger';
    $status = 'Job scheduler is not running. All transactions are queued and stopped processing. Start the job to resume.';
    $textcolor = '#ff0000';
    $icon = 'icon-play';
}
?>
<?php 
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array
(
    'id'=>'JobSchedulerForm',
    'inlineErrors'=>false,
    'enableClientValidation'=>false,
    'clientOptions'=>array(
            'validateOnSubmit'=>true,
        ),
)); ?>
<?php echo CHtml::hiddenField('status', $job_status); ?>
<table>
    <tr>
        <td>
            <?php
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'submit',
                'icon'=>$icon,
                'label' => $label,
                'type' => $type,
                'htmlOptions'=>array(
                    'confirm'=>'Are you sure you want to continue?'
                ),
            ));
            ?>
        </td>
        <td style="color:<?php echo $textcolor; ?>;padding-left:10px;"><em><?php echo $status; ?></em></td>
    </tr>
</table>
<?php $this->endWidget(); ?>

<br />
<h4>Unprocessed Transactions</h4>
<?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'button',
        'icon'=>'icon-refresh',
        'label' => 'Refresh Queue',
        'type' => 'info',
        'htmlOptions'=>array(
            'onclick'=>'$.fn.yiiGridView.update("queue-grid");',
        )
    ));
?>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id'=>'queue-grid',
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'enablePagination' => true,
    'columns'=>array(
        array('name'=>'unprocessed_log_id', 
                'header'=>'Trx #',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('name'=>'member_name', 
                'header'=>'Member',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'endorser_name', 
                'header'=>'Endorser',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'upline_name', 
                'header'=>'Upline',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'status', 
                'header'=>'Status',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),       
    ),
)); ?>
