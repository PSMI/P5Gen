<?php

/**
 * @author Noel Antonio
 * @datecreated 03-25-2014
 */
?>
<style type="text/css">
    table#summary{font-size:14px; width:100%;}
    table#summary, table#summary th, table#summary td{border:1px solid #e1e1e1; border-collapse: collapse; padding: 2px 10px 2px 10px}
    table#summary td.data{color:#0088cc}
</style>
<?php $this->breadcrumbs = array('Networks'=>'#',
    'IPD Direct Endorsements'
);
?>
<?php $this->beginWidget('bootstrap.widgets.TbHeroUnit', array(
    'heading'=>'My IPD Direct Endorsements',
    'headingOptions'=>array('style'=>'font-size:200%')
)); ?>
<br/>
  <table with="100%" id="summary">
      <tr>
          <td align="right" width="25%">Total direct endorsements</td>
          <td class="data" width="75%"><?php echo $counter; ?></td>
      </tr>
  </table>
<?php $this->endWidget();

$this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'direct-grid',
        'type'=>'striped bordered condensed',
        'dataProvider'=>$dataProvider,
        'enablePagination' => true,
        'columns' => array(
                array(
                    'header' => 'No',
                    'value' => '$row + ($this->grid->dataProvider->pagination->currentPage
                    * $this->grid->dataProvider->pagination->pageSize + 1)',
                    'htmlOptions' => array('style' => 'text-align:center'),
                    'headerHtmlOptions' => array('style' => 'text-align:center'),
                ),
                array('name'=>'Name',
                    'header'=>'Member Name',
                    'type'=>'raw',
                    'value'=>'CHtml::encode($data["last_name"] . ", " . $data["first_name"] . " " . $data["middle_name"])', 
                ), 
                array('name'=>'DateEnrolled',
                    'header'=>'Date Enrolled',
                    'type'=>'raw',
                    'value'=>'CHtml::encode(date("F d, Y", strtotime($data["date_created"])))', 
                ),
        ),
));

?>
<?php $this->widget('bootstrap.widgets.TbButton', array(
    'label'=>'Back',
    'icon'=>'icon-chevron-left',
    'type'=>'info',
    'htmlOptions'=>array('onclick'=>'history.back()'),
)); ?>