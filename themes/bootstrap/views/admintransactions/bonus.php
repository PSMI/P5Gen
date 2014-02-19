<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-11-2014
------------------------*/

$this->breadcrumbs = array('Member Transactions'=>'#','Bonus');

Yii::app()->user->setFlash('info', '<strong>Important!</strong> Please make sure that the date input is a valid cut-off.');

$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'info'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
));
?>

<h3>Member Transactions - Bonus</h3>

<?php    
//display table
if (isset($dataProvider))
{
    $this->renderPartial('_bonusview', array(
                'dataProvider'=>$dataProvider,
        ));
}
else
{
    
}
?>

