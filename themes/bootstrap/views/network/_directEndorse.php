<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h3>Direct Endorsement</h3>

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
    'label'=>'Total Direct Endorsements:',
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
        'id'=>'direct-grid',
        'type'=>'striped bordered condensed',
        'dataProvider'=>$dataProvider,
        'enablePagination' => true,
        'columns' => array(
                array('name'=>'Name',
                    'header'=>'Name',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["first_name"] . " " . $data["middle_name"] . " " . $data["last_name"])',
                    'htmlOptions' => array('style' => 'text-align:center'),    
                ), 
                array('name'=>'DateEnrolled',
                    'header'=>'Date Enrolled',
                    'type'=>'raw',
                    'value'=>'CHtml::encode(date("Y M d h:i:s A", strtotime($data["date_created"])))',
                    'htmlOptions' => array('style' => 'text-align:center'),    
                ),
        ),
));

$this->endWidget(); 
?>