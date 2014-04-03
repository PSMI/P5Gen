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
        array('name'=>'quantity', 
                'header'=>'Quantity',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('name'=>'srp', 
                'header'=>'SRP',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
            ),
        array('name'=>'discount', 
                'header'=>'Discount (%)',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
            ),
        array('name'=>'net_price', 
                'header'=>'Net Price',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
            ),
        array('name'=>'savings', 
                'header'=>'Savings',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
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
                        'url'=>'Yii::app()->createUrl("/purchase/getvalues", array("id" =>$data["purchase_id"]))',
                        'options' => array(
                            'class'=>"btn btn-small",
                            'ajax' => array(
                                'type' => 'GET',
                                'dataType'=>'json',
                                'url' => 'js:$(this).attr("href")',
                                'success' => 'function(data){
                                    $.each(data, function(name,val){
                                       $("#Update_distributor_id").val(val.distributor_id);
                                       $("#Update_purchase_id").val(val.purchase_id);
                                       $("#Update_product_id").val(val.product_id);
                                       $("#Update_products").val(val.product_id);
                                       $("#Update_qty").val(val.quantity);
                                       $("#Update_payment_type_id").val(val.payment_type_id);
                                    });
                                    $("#purchase-update-modal").modal("show");
                                 }',
                            ),

                        ),
                        array('id' => 'send-link-'.uniqid())
                    ),
                    'delete'=>array
                    (
                        'label'=>'Delete Item',
                        'icon'=>'icon-remove-sign',
                        'url'=>'Yii::app()->createUrl("/purchase/removeitem", array("id" =>$data["purchase_id"]))',
                        'confirm'=>'Are you sure you want to remove this item?',
                        'options' => array(
                            'class'=>"btn btn-small",
                            'ajax' => array(
                                'type' => 'GET',
                                'dataType'=>'json',
                                'url' => 'js:$(this).attr("href")',
                                'success' => 'function(data){
                                    $("#search-form").submit();
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

