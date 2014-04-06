<?php

/*
 * @author : owliber
 * @date : 2014-03-30
 */
?>
<h3>Purchase History</h3>
<h4>Distributor : <?php echo $info['last_name'] . ', ' . $info['first_name']; ?></h4>
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
                'footer'=>'<strong>Total Purchase</strong>',
                'footerHtmlOptions'=>array('style'=>'font-size:14px'),
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
                'footer'=>'<strong>'.number_format($total['total_quantity'],0).'</strong>',
                'footerHtmlOptions'=>array('style'=>'text-align:center; font-size:14px'),
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
                'footer'=>'<strong>'.number_format($total['total_savings'],2).'</strong>',
                'footerHtmlOptions'=>array('style'=>'text-align:right; font-size:14px'),
            ),
        array('name'=>'total', 
                'header'=>'Total',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
                'footer'=>'<strong>'.number_format($total['total_amount'],2).'</strong>',
                'footerHtmlOptions'=>array('style'=>'text-align:right; font-size:14px'),
            ),
    ),
)); ?>

<?php $this->endWidget(); ?>

