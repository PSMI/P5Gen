<?php

/*
 * @author : owliber
 * @date : 2014-04-04
 */
?>
<h3>Purchase History</h3>
<?php
    Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');

    /** @var BootActiveForm $form */
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id'=>'searchForm',
        'type'=>'search',
        'htmlOptions'=>array('class'=>'well'),
    ));

    echo CHtml::label('From &nbsp;','lblFrom');
    $this->widget('CJuiDateTimePicker',array(
                    'model' => $model,
                    'attribute' => 'date_from',
                    'value'=> $model->date_from,
                    'mode'=>'date', //use "time","date" or "datetime" (default)
                    'options'=>array(
                        'dateFormat'=>'yy-mm-dd',
                        'timeFormat'=> 'hh:mm',
                        'showAnim'=>'fold', // 'show' (the default), 'slideDown', 'fadeIn', 'fold'
                        'showOn'=>'button', // 'focus', 'button', 'both'
                        'buttonText'=>Yii::t('ui','Date'), 
                        'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.png', 
                        'buttonImageOnly'=>true,
                    ),// jquery plugin options
                    'htmlOptions'=>array('readonly'=>'readonly', 'class'=>'input-medium'),
                    'language'=>'',
                ));

    echo CHtml::label('To  &nbsp;','lblTo', array('style' => 'margin-left: 20px;'));
    $this->widget('CJuiDateTimePicker',array(
                    'model' => $model,
                    'attribute' => 'date_to',
                    'value'=> $model->date_to,
                    'mode'=>'date', //use "time","date" or "datetime" (default)
                    'options'=>array(
                        'dateFormat'=>'yy-mm-dd',
                        'timeFormat'=> 'hh:mm',
                        'showAnim'=>'fold', // 'show' (the default), 'slideDown', 'fadeIn', 'fold'
                        'showOn'=>'button', // 'focus', 'button', 'both'
                        'buttonText'=>Yii::t('ui','Date'), 
                        'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.png', 
                        'buttonImageOnly'=>true,
                    ),// jquery plugin options
                    'htmlOptions'=>array('readonly'=>'readonly', 'class'=>'input-medium'),
                    'language'=>'',
                ));

    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit', 
        'label'=>'Search', 
        'icon'=>'icon-search',
        'type'=>'primary',
        'htmlOptions' => array(
            'style' => 'margin-left: 10px;'
            )
        ));

?>
<?php $this->endWidget(); ?>

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
