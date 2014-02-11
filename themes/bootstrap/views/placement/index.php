<?php

/*
 * @author : owliber
 * @date : 2014-02-06
 * @var PlacementController
 * @var PlacementModel
 */

$this->breadcrumbs = array('Profile'=>array('member/index'),'Placement Approval');

Yii::app()->user->setFlash('info', '<strong>Important!</strong> Please check here regularly to for new downline\'s approval. Unapproved downlines within (3) days will be considered approved.');

$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'info'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
)); ?>

<h3>Placement Approval</h3>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id'=>'placement-grid',
    'type'=>'striped bordered condensed',
    //'filter' => $model->search(),
    'dataProvider'=>$gridDataProvider,
    'enablePagination' => true,
    //'template'=>"{items}",
    'columns'=>array(
        array('name'=>'member_id', 
                'header'=>'ID',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('name'=>'member_name', 
                'header'=>'Member Name',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'placed_by', 
                'header'=>'Placed By',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'date_joined', 
                'header'=>'Date Placed',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),        
        array('class'=>'bootstrap.widgets.TbButtonColumn',
                'template'=>'{approve}{disapprove}',
                'buttons'=>array
                (
                    'approve'=>array
                    (
                        'label'=>'Approve ',
                        'icon'=>'ok-sign',
                        'url'=>'Yii::app()->createUrl("/placement/approve", array("id" =>$data["member_id"]))',
                        'options' => array(
                            'class'=>"btn btn-small",
                            'confirm'=>'Are you sure you want to APPROVE?',
                            'ajax' => array(
                                'type' => 'GET',
                                'dataType'=>'json',
                                'url' => 'js:$(this).attr("href")',
                                'success' => 'function(data){
                                    if(data.result_code == 0)
                                    {
                                        alert(data.result_msg);
                                        $.fn.yiiGridView.update("placement-grid");
                                    }
                                    else
                                        alert(data.result_msg);
                                 }',
                            ),

                        ),
                        array('id' => 'send-link-'.uniqid())
                    ),
                    'disapprove'=>array
                    (
                        'label'=>'Disapprove ',
                        'icon'=>'remove-sign',
                        'url'=>'Yii::app()->createUrl("/placement/disapprove", array("id"=>$data["member_id"]))',
                        'options' => array(
                            'class'=>"btn btn-small",
                            'confirm'=>'Are you sure you want to DISAPPROVE?',
                            'ajax' => array(
                                'type' => 'GET',
                                'dataType'=>'json',
                                'url' => 'js:$(this).attr("href")',
                                'success' => 'function(data){
                                    if(data.result_code == 0)
                                    {
                                        alert(data.result_msg);
                                        $.fn.yiiGridView.update("placement-grid");
                                    }
                                    else
                                        alert(data.result_msg);
                                 }',
                            ),

                        ),
                        array('id' => 'send-link-'.uniqid())
                    ),
                ),
                'header'=>'Options',
                'htmlOptions'=>array('style'=>'width:120px;text-align:center'),
            ),
        
    ),
)); ?>
