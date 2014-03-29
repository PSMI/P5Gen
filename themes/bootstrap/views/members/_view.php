<?php

/**
 * @author Noel Antonio
 * @date 01-28-2014
 */

$this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'placement-grid',
        'type'=>'striped bordered condensed',
        'dataProvider'=>$dataProvider,
        'enablePagination' => true,
        'columns' => array(
//                        array(
//                            'header' => '',
//                            'value' => '$row + ($this->grid->dataProvider->pagination->currentPage
//                            * $this->grid->dataProvider->pagination->pageSize + 1)',
//                        ),
                        array('name'=>'member_id',
                            'header'=>'MID',
                            'type'=>'raw',
//                            'value'=>'CHtml::encode($data["username"])',
                            'htmlOptions' => array('style' => 'text-align:left'),  
                            'headerHtmlOptions' => array('style' => 'text-align:left'),  
                        ),
                        array('name'=>'Username',
                            'header'=>'User Name',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["username"])',
                            'htmlOptions' => array('style' => 'text-align:left'),  
                            'headerHtmlOptions' => array('style' => 'text-align:left'),  
                        ),
                        array('name'=>'FullName',
                            'header'=>'Full Name',
                            'type'=>'raw',
                            'value'=>'CHtml::link(CHtml::encode($data["last_name"] . ", " . $data["first_name"] . " " . $data["middle_name"]), 
                                array("members/genealogy", "id"=>$data["member_id"])
                            )',
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
                       
                        array('name'=>'Status',
                            'header'=>'Status',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["status"])',
                            'htmlOptions' => array('style' => 'text-align:left; width: 5%;'),  
                            'headerHtmlOptions' => array('style' => 'text-align:left'),  
                        ),
                        array('class'=>'bootstrap.widgets.TbButtonColumn',
                            'template'=>'{terminate}{update}',
                            'buttons'=>array
                            (
                                'terminate'=>array
                                (
                                    'label'=>'Terminate Member',
                                    'icon'=>'icon-remove-circle',
                                    'url'=>'Yii::app()->createUrl("/members/terminate", array("id" =>$data["member_id"]))',
                                    'options' => array(
                                        'class'=>"btn btn-small",
                                    ),
                                    array('id' => 'send-link-'.uniqid())
                                ),
                                'update'=>array
                                (
                                    'label'=>'Update Member Profile',
                                    'icon'=>'icon-edit',
                                    'url'=>'Yii::app()->createUrl("/members/update", array("id" =>$data["member_id"]))',
                                    'options' => array(
                                        'class'=>"btn btn-small",
                                    ),
                                    array('id' => 'send-link-'.uniqid())
                                ),
                            ),
                            'header'=>'Action',
                            'htmlOptions'=>array('style'=>'width:80px;text-align:center'),
                        ),
        )
        ));
?>

