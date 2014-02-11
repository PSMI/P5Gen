<?php

/**
 * @author Noel Antonio
 * @date 02/11/2014
 */

?>
<h1><a href="" style="text-decoration: none; color: black">My Genealogy</a></h1>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'index-form',
    'enableClientValidation' => true,
    'clientOptions' => array(
            'validateOnSubmit' => true,
    ),
    'htmlOptions'=>array('class'=>'well'),
));

$this->widget('bootstrap.widgets.TbLabel', array(
    'type'=>'default',
    'label'=>'Genealogy of',
));

$this->widget('bootstrap.widgets.TbLabel', array(
    'type'=>'info',
    'label'=>$member_name,
));

/*$this->widget('bootstrap.widgets.TbButton', array(
    'label'=>'Genealogy of',
    'type'=>'default',
    'size'=>'normal',
));

$this->widget('bootstrap.widgets.TbButton', array(
    'label'=>'JO PORMENTO',
    'type'=>'info',
    'size'=>'normal',
));*/

$this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'genealogy-grid',
        'type'=>'striped bordered condensed',
        'dataProvider'=>$dataProvider,
        'enablePagination' => true,
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
                              "downlines", 
                              array(
                                    "type"=>"post",
                                    "data" => array("postData"=>$data["Members"]),
                                    "success" => "function(data){
                                        $(\"#data\").html(data);
                                        $(\"#data\").show();
                                    }"
                              )
                    )',
//                    'value'=>'CHtml::linkButton($data["Total"], array("submit"=>array("downlines"), "params"=>array("postData"=>$data["Members"])))',
                    'htmlOptions' => array('style' => 'text-align:center'),    
                ),
            )
));

echo CHtml::hiddenField('hidden_member_id', '', array('id'=>'hidden_member_id'));

$this->endWidget(); 
?>
<div id="data" style="display: none"><?php echo $this->renderPartial('_downlines', array('dataProvider'=>$dataProvider)); ?></div>