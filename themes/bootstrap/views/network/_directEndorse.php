<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<style type="text/css">
    table#summary{font-size:14px; width:100%;}
    table#summary, table#summary th, table#summary td{border:1px solid #e1e1e1; border-collapse: collapse; padding: 2px 10px 2px 10px}
    table#summary td.data{color:#0088cc}
</style>
<?php $this->breadcrumbs = array('Networks'=>'#',
    'Direct Endorsements'
);
?>
<?php $this->beginWidget('bootstrap.widgets.TbHeroUnit', array(
    'heading'=>'My Direct Endorsements',
    'headingOptions'=>array('style'=>'font-size:200%')
)); ?>
<p style="font-size:14px"> Earn up to <strong>P<?php echo $payout; ?></strong> for every direct endorsement per entry.</p>
  <table with="100%" id="summary">
      <tr>
          <td align="right" width="25%">Total direct endorsements</td>
          <td class="data" width="75%"><?php echo $counter; ?></td>
      </tr>
  </table>
<?php $this->endWidget(); ?>
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'index-form',
    'enableClientValidation' => true,
    'clientOptions' => array(
            'validateOnSubmit' => true,
    ),
    'htmlOptions'=>array('class'=>'well'),
));
/*
echo '<h3>Direct Endorsements</h3>';
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
$this->endWidget(); 
*/

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
                ), 
                array('name'=>'DateEnrolled',
                    'header'=>'Date Enrolled',
                    'type'=>'raw',
                    'value'=>'CHtml::encode(date("m-d-Y", strtotime($data["date_created"])))', 
                ),
        ),
));

?>
<?php $this->widget('bootstrap.widgets.TbButton', array(
    'label'=>'Back',
    'icon'=>'icon-chevron-left',
    'type'=>'info', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
    'htmlOptions'=>array('onclick'=>'history.back()'),
)); ?>
   
<?php $this->endWidget(); ?>