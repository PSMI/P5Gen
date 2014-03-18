<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-06-2014
------------------------*/

$this->breadcrumbs = array('Member Transactions'=>'#','Loan');

Yii::app()->user->setFlash('info', '<strong>Note </strong> | All loans automatically becomes available once they are completed.');

$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'info'//=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
));
?>

<h3>Loan Payout</h3>


<?php
    $this->widget("bootstrap.widgets.TbButton", array(
                                        "label"=>"Export to PDF",
                                        //"icon"=>"icon-chevron-left",
                                        "type"=>"info",
                                        'url'=>'pdfloansummary',
                                        "htmlOptions"=>array("style"=>"float: right;"),
                                    ));
?>

<br><br>

<?php
//display table
if (isset($dataProvider))
{
    $this->renderPartial('_loanview', array(
                'dataProvider'=>$dataProvider,
                'total'=>$total
        ));
}
else
{
    
}
?>