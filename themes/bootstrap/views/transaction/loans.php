<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-12-2014
------------------------*/

$this->breadcrumbs = array('Member Transactions'=>'#','Loan');

?>

<h3>Loans</h3>

<?php
//display table
if (isset($dataProvider))
{
    $this->renderPartial('_loanview', array(
                'dataProvider'=>$dataProvider,
        ));
}
else
{
    
}
?>