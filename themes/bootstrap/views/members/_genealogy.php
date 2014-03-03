<?php

/**
 * @author Noel Antonio
 * @date 02/11/2014
 */

?>
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

echo '<table>';
echo '<tr>';
echo '<td>';
$this->widget('bootstrap.widgets.TbLabel', array(
    'type'=>'default',
    'label'=>'Genealogy of',
));
echo '</td>';
echo '<td>';
$this->widget('bootstrap.widgets.TbLabel', array(
    'type'=>'info',
    'label'=>$member_name,
));
echo '</td>';
echo '</tr>';
echo '<tr>';
echo '<td>';
$this->widget('bootstrap.widgets.TbLabel', array(
    'type'=>'default',
    'label'=>'Total Network:',
));
echo '</td>';
echo '<td>';
$this->widget('bootstrap.widgets.TbLabel', array(
    'type'=>'info',
    'label'=>$counter,
));
echo '</td>';
echo '</tr>';
echo '</table>';

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
                array('name'=>'IBOCount',
                    'header'=>'<center>IBO Count</center>',
                    'type'=>'raw',
                    'value'=>'CHtml::ajaxLink($data["Total"],
                              Yii::app()->createUrl("members/genealogyDownlines"), 
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
<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'button', 'label'=>'Back to Parent Network', 'htmlOptions'=>array('onclick'=>'location.href = "";'))); ?>
</div>    

<?php
$this->endWidget(); 
?>
<div id="data" style="display: none"><?php echo $this->renderPartial('_downlines', array('dataProvider'=>$dataProvider)); ?></div>