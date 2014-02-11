<?php

/*
 * @author : owliber
 * @date : 2014-02-07
 * @var DownlineController
 * @var Downlines
 */

$this->breadcrumbs = array('Profile'=>array('member/index'),'Downline Assignment');

Yii::app()->clientScript->registerScript('ui','
         
     $(\'input[rel="tooltip"]\').tooltip();     
     var upline_id = $("#PlacementModel_upline_id"),
         upline_name = $("#PlacementModel_upline_name"),
         downline_id = $("#PlacementModel_downline_id"),
         downline_name = $("#PlacementModel_downline_name");
    
 ', CClientScript::POS_END);

Yii::app()->user->setFlash('warning', '<strong>Warning!</strong> Please assign below new members to your downlines.');

$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'warning'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
)); ?>

<h3>Downline Assignment</h3>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id'=>'placement-grid',
    'type'=>'striped bordered condensed',
    'dataProvider'=>$gridDataProvider,
    'enablePagination' => true,
    'template'=>"{items}",
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
        array('name'=>'date_joined', 
                'header'=>'Date Joined',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ), 
        array('name'=>'upline_name', 
                'header'=>'Place under',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('class'=>'bootstrap.widgets.TbButtonColumn',
                'template'=>'{assign}',
                'buttons'=>array
                (
                    'assign'=>array
                    (
                        'label'=>'Assign ',
                        'icon'=>'share-alt', //share-alt
                        'url'=>'Yii::app()->createUrl("/placement/assignform", array("id" =>$data["member_id"],"name"=>CHtml::encode($data["member_name"])))',
                        'visible'=>'(!empty($data["upline_name"])) ? false : true',
                        'options' => array(
                            'class'=>"btn btn-small",
                            'ajax' => array(
                                'type' => 'GET',
                                'dataType'=>'json',
                                'url' => 'js:$(this).attr("href")',
                                'success' => 'function(data){                                   
                                     $.each(data, function(name,val){
                                        downline_name.val(val.downline_name);
                                        downline_id.val(val.downline);
                                    });
                                    $("#assign-modal").modal("show");
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

<?php $this->beginWidget('bootstrap.widgets.TbModal', 
        array('id'=>'assign-modal',
              'autoOpen'=>false,
            
)); ?>
 
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Downline Assignment</h4>
</div>

<div class="modal-body">
    <?php /** @var BootActiveForm $form */
    $form = $this->widget('bootstrap.widgets.TbActiveForm', array
    (
        'id'=>'verticalForm',
        'inlineErrors'=>true,
        'enableClientValidation'=>true,
        'clientOptions'=>array(
                'validateOnSubmit'=>true,
            ),
    )); ?>

    <?php echo $form->textFieldRow($model, 'downline_name',array('readonly'=>'readonly','class'=>'span4')); ?>
    <div class="control-group">
    <?php echo CHtml::label('Place under '. '<span class="required">*</span>', 'PlacementModel_upline_id',array('class'=>'control-label required')) ?>
        <div class="controls">   
            <?php
            $this->widget('zii.widgets.jui.CJuiAutoComplete',array
                (
                    'model'=>$model,
                    'attribute'=>'upline_name',
                    //'sourceUrl'=>  Yii::app()->createUrl('placement/downlines',array('id'=>$model->downline_id)),
                    'source'=>'js: function(request, response) {
                       $.ajax({
                           url: "'.Yii::app()->createUrl('placement/downlines').'",
                           dataType: "json",
                           data: {
                               term: request.term,
                               id: downline_id.val()
                           },
                           success: function (data) {
                                   response(data);
                           }
                       })
                    }',
                    'options'=>array
                    (
                        'minLength'=>'2',
                        'showAnim'=>'fold',
                        'focus' => 'js:function(event, ui){upline_name.val(ui.item["value"])}',
                        'select' => 'js:function(event, ui){upline_id.val(ui.item["id"]); }',
                        //'select' => 'js:function(event, ui){alert(ui.item["id"]); }',
                    ),
                    'htmlOptions'=>array(
                        'class'=>'span4',
                        'rel'=>'tooltip',
                        'title'=>'Please type your downline\'s name.',
                    ),        
                ));


            ?>
            <?php echo $form->error($model, 'upline_name'); ?>
        </div>    
    </div>
    <?php echo $form->hiddenField($model, 'upline_id'); ?>
    <?php echo $form->hiddenField($model, 'downline_id'); ?>
</div>
 
<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'ajaxButton', 
        'label'=>'Assign',
        'type'=>'primary',
        'url'=>array('placement/assign'),
        'ajaxOptions'=>array
         (
            'data'=>array(
                'upline_id'=>'js:function(){return upline_id.val();}',
                'downline_id'=>'js:function(){return downline_id.val();}'
             ),
            'success'=>'js:function(data){
                 var obj = jQuery.parseJSON(data);
                    alert(obj.result_msg);
                    $.fn.yiiGridView.update("placement-grid");                
            }'
         ),
        'htmlOptions'=>array
         (
            'data-dismiss'=>'modal',
         ),
    )); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Close',
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>
 
<?php $this->endWidget(); ?>
