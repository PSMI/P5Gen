<?php

/**
 * @author Noel Antonio
 * @date 02/11/2014
 */

?>
<style type="text/css">
    table#summary{font-size:14px; width:100%;}
    table#summary, table#summary th, table#summary td{border:1px solid #e1e1e1; border-collapse: collapse; padding: 2px 10px 2px 10px}
    table#summary td.data{color:#0088cc}
</style>
<?php $this->breadcrumbs = array('Distributors'=>'#','IPD Unilevel'); ?>
<?php Yii::app()->clientScript->registerScript('ui','
            function goto_data(id){
                    $("html,body").animate({scrollTop: $("#"+id).offset().top},"slow");
            }', CClientScript::POS_END);
?>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'index-form',
    'enableClientValidation' => true,
    'clientOptions' => array(
            'validateOnSubmit' => true,
    ),
    'htmlOptions'=>array('class'=>'well'),
));
?>
<table with="100%" id="summary">
    <tr>
        <td width="15%" align="right">IPD Unilevel of</td>
        <td width="75%" class="data"><?php echo $member_name; ?></td>
    </tr>
    <tr>
        <td align="right">Total Network</td>
        <td class="data"><?php echo $counter; ?></td>
    </tr>
</table>
<?php 
$this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'genealogy-grid',
        'type'=>'striped bordered condensed',
        'dataProvider'=>$dataProvider,
        'enablePagination' => false,
        'columns' => array(
                array('name'=>'Level',
                    'header'=>'<center>Level</center>',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["Level"])',
                    'htmlOptions' => array('style' => 'text-align:center'),    
                ), 
                array('name'=>'IPDCount',
                    'header'=>'<center>IPD Count</center>',
                    'type'=>'raw',
                    'value'=>'CHtml::ajaxLink($data["Total"],
                              Yii::app()->createUrl("distributors/unilevelDownlines"), 
                              array(
                                    "type"=>"post",
                                    "data" => array("postData"=>$data["Members"]),
                                    "success" => "function(data){
                                        $(\"#data\").html(data);
                                        $(\"#data\").show();   
                                        goto_data(\"data\");
                                    }"
                              )
                    )',
                    'htmlOptions' => array('style' => 'text-align:center'),     
                ),
            )
));

echo CHtml::hiddenField('hidden_member_id', '', array('id'=>'hidden_member_id'));
?>

<div align="right">
<?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'button', 
    'type'=>'info',
    'label'=>'Go to Parent Network', 
    'htmlOptions'=>array(
        'onclick'=>'location.href = "'.Yii::app()->createUrl('distributors/index').'";'))); ?>
</div>    

<?php
$this->endWidget(); 
?>
<div id="data" style="display: none"><?php echo $this->renderPartial('_downlines', array('dataProvider'=>$dataProvider)); ?></div>