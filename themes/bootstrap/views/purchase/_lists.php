<?php

/*
 * @author : owliber
 * @date : 2014-03-30
 */
?>
<?php $this->beginWidget('bootstrap.widgets.TbGridView', array(
    'id'=>'product-grid',
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'enablePagination' => true,
    'columns'=>array(
        array(
                'header' => '',
                'value' => '$row + ($this->grid->dataProvider->pagination->currentPage
                * $this->grid->dataProvider->pagination->pageSize + 1)',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('name'=>'date_purchased', 
                'header'=>'Date Purchased',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('name'=>'product_code', 
                'header'=>'Product Code',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('name'=>'product_name', 
                'header'=>'Product Name',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'amount', 
                'header'=>'Amount',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
            ),
        array('name'=>'quantity', 
                'header'=>'Quantity',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('name'=>'total', 
                'header'=>'Total',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
            ),
        array('class'=>'bootstrap.widgets.TbButtonColumn',
                'template'=>'{update}{delete}',
                'buttons'=>array
                (
                    'update'=>array
                    (
                        'label'=>'Update Item',
                        'icon'=>'icon-edit',
                        'url'=>'Yii::app()->createUrl("/inventory/getvalues", array("id" =>$data["product_id"]))',
                        'options' => array(
                            'class'=>"btn btn-small",
                            'ajax' => array(
                                'type' => 'GET',
                                'dataType'=>'json',
                                'url' => 'js:$(this).attr("href")',
                                'success' => 'function(data){
                                    alert(data);
                                 }',
                            ),

                        ),
                        array('id' => 'send-link-'.uniqid())
                    ),
                    'delete'=>array
                    (
                        'label'=>'Delete Item',
                        'icon'=>'icon-remove-sign',
                        'url'=>'Yii::app()->createUrl("/inventory/deleteitem", array("id" =>$data["product_id"]))',
                        'options' => array(
                            'class'=>"btn btn-small",
                            'ajax' => array(
                                'type' => 'GET',
                                'dataType'=>'json',
                                'url' => 'js:$(this).attr("href")',
                                'success' => 'function(data){
                                    alert(data);
                                 }',
                            ),

                        ),
                        array('id' => 'send-link-'.uniqid())
                    ),
                ),
                'header'=>'Action',
                'htmlOptions'=>array('style'=>'width:80px;text-align:center'),
            ),
    ),
)); ?>

<?php $this->endWidget(); ?>
