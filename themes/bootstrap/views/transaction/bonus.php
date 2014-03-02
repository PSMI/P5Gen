<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-12-2014
------------------------*/

$this->breadcrumbs = array('Member Transactions'=>'#','Bonus');

?>

<h3>Bonus</h3>

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

