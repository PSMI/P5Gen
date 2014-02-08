<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-06-2014
------------------------*/

class AdminTransactionsController extends Controller
{
    public $layout = 'column2';
    
    public function actionLoan()
    {
        $model = new Loan();
        
        if (isset($_POST["calDateFrom"]) && $_POST["calDateTo"])
        {
            $dateFrom = $_POST["calDateFrom"];
            $dateTo = $_POST["calDateTo"];
            
            $rawData = $model->getLoanApplications($dateFrom, $dateTo);
            
            $dataProvider = new CArrayDataProvider($rawData, array(
                                                    'keyField' => false,
                                                    'pagination' => array(
                                                    'pageSize' => 10,
                                                ),
                                    ));
            
            $this->render('loan', array('dataProvider' => $dataProvider));
        }
        else
        {
            $this->render('loan');
        }        
    }
}
?>
