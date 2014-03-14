<?php

/**
 * @author Noel Antonio
 * @date 01-28-2014
 */
$this->breadcrumbs = array('Members'=>'#','Pending Placements');
?>
<h3>Pending Placements</h3>
<?php
$this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'placement-grid',
        'type'=>'striped bordered condensed',
        'dataProvider'=>$dataProvider,
        'enablePagination' => true,
        'columns' => array(
                        array(
                            'header' => '',
                            'value' => '$row + ($this->grid->dataProvider->pagination->currentPage
                            * $this->grid->dataProvider->pagination->pageSize + 1)',
                        ),
                        array('name'=>'FullName',
                            'header'=>'Full Name',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["member_name"])',
                            'htmlOptions' => array('style' => 'text-align:left'),  
                            'headerHtmlOptions' => array('style' => 'text-align:left'),  
                        ),
                        array('name'=>'DateJoined',
                            'header'=>'Date Joined',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["date_joined"])',
                            'htmlOptions' => array('style' => 'text-align:left'),  
                            'headerHtmlOptions' => array('style' => 'text-align:left'),  
                        ),
                        array('name'=>'Endorser',
                            'header'=>'Endorser Name',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["endorser"])',
                            'htmlOptions' => array('style' => 'text-align:left'),   
                            'headerHtmlOptions' => array('style' => 'text-align:left'),  
                        ),
                        array('name'=>'Upline',
                            'header'=>'Upline Name',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["upline"])',
                            'htmlOptions' => array('style' => 'text-align:left'),    
                            'headerHtmlOptions' => array('style' => 'text-align:left'),  
                        ),
                )
        ));
?>

