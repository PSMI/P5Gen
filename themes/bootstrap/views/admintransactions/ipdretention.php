<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-11-2014
------------------------*/

$this->breadcrumbs = array('Payouts'=>'#','IPD Retention Money');

Yii::app()->user->setFlash('info', '<strong>Info</strong> | Purchased items.');

$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'info'//=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
));
?>

<h3>Distributor Retention Money</h3>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'searchForm',
    'type'=>'search',
    'htmlOptions'=>array('class'=>'well'),
));
$this->widget("bootstrap.widgets.TbButton", array(
                                            "label"=>"Export to PDF",
                                            //"icon"=>"icon-chevron-left",
                                            "type"=>"info",
                                            'url'=>'ipdpdfretentionsummary',
                                            //"htmlOptions"=>array("style"=>"float: right"),
                                        ));
$this->endWidget(); 

//display table
if (isset($dataProvider))
{
    $this->renderPartial('_ipdretentionview', array(
                'dataProvider'=>$dataProvider,
                'total'=>$total,
        ));
}
?>

