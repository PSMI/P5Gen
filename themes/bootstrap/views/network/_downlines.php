<?php

/**
 * @author Noel Antonio
 * @date 02/11/2014
 */

$this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'placement-grid',
        'type'=>'striped bordered condensed',
        'dataProvider'=>$dataProvider,
        'enablePagination' => true,
        'selectionChanged'=>'function(id){
                var cellValue = $("#placement-grid .items tbody tr.selected td").find(".downline_id").attr("downline_attr");
                $("#hidden_member_id").val(cellValue);
                $("#index-form").submit();
        }',
        'columns' => array(
                array('name'=>'DownlineName',
                    'header'=>'<center>Downline Name</center>',
                    'type'=>'raw',
                    'value'=>'CHtml::link($data["Name"], "", array("class"=>"downline_id", "downline_attr"=>$data["ID"], "style"=>"cursor: pointer"))',                    
                    'htmlOptions' => array('style' => 'text-align:center'),  
                ), 
                array('name'=>'DateEnrolled',
                    'header'=>'<center>Date Enrolled</center>',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["DateEnrolled"])',                    
                    'htmlOptions' => array('style' => 'text-align:center'),  
                ), 
                array('name'=>'PlacedUnder',
                    'header'=>'<center>Placed Under</center>',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["Upline"])',                    
                    'htmlOptions' => array('style' => 'text-align:center'),  
                ),
                array('name'=>'EndorsedBy',
                    'header'=>'<center>Endorsed By</center>',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["Endorser"])',                    
                    'htmlOptions' => array('style' => 'text-align:center'),  
                ),
                array('name'=>'IBOCount',
                    'header'=>'<center>IBO Count</center>',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["Count"])',
                    'htmlOptions' => array('style' => 'text-align:center'),    
                ),
        )
));
?>