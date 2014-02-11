<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-06-2014
------------------------*/

class AdmintransactionsController extends Controller
{
    public $layout = 'column2';
    
    //For Loan
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
    
    public function actionProcessTransaction()
    {
        if(!Yii::app()->request->isAjaxRequest)
        {
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
        }
        
        if(isset($_GET["id"]))
        {
            $status = $_GET["status"];
            $transtype = $_GET["transtype"];
            $userid = Yii::app()->user->getId();
 
            //update status
            if ($transtype == 'loan')
            {
                $loan_id = $_GET["id"];
                
                $model = new Loan();
                $result = $model->updateLoanStatus($loan_id, $status, $userid);
                
                if (count($result) > 0)
                {
                    $result_code = 0;
                    $result_msg = "Loan Approved.";
                }
                else
                {
                    $result_code = 1;
                    $result_msg = "An error occured. Please try again.";
                }
            }
            else if($transtype == 'goc')
            {
                $comm_id = $_GET["id"];
                
                $model = new GroupOverrideCommission();
                $result = $model->updateCommisionStatus($comm_id, $status, $userid);
                
                if (count($result) > 0)
                {
                    $result_code = 0;
                    $result_msg = "GOC Claimed.";
                }
                else
                {
                    $result_code = 1;
                    $result_msg = "An error occured. Please try again.";
                }
            }
            else if($transtype == 'unilvl')
            {
                $unilevel_id = $_GET["id"];
                
                $model = new Unilevel();
                $result = $model->updateUnilevelStatus($unilevel_id, $status, $userid);
                
                if (count($result) > 0)
                {
                    $result_code = 0;
                    $result_msg = "Unilevel Claimed.";
                }
                else
                {
                    $result_code = 1;
                    $result_msg = "An error occured. Please try again.";
                }
            }
            else if($transtype == 'bonus')
            {
                $promo_redemption_id = $_GET["id"];
                
                $model = new Bonus();
                $result = $model->updateBonusStatus($promo_redemption_id, $status, $userid);
                
                if (count($result) > 0)
                {
                    $result_code = 0;
                    $result_msg = "Bonus Claimed.";
                }
                else
                {
                    $result_code = 1;
                    $result_msg = "An error occured. Please try again.";
                }
            }
        }
        else
        {
            $result_code = 2;
            $result_msg = "An error occured. Please try again.";
        }

        echo CJSON::encode(array('result_code'=>$result_code, 'result_msg'=>$result_msg));
    }
    
    //For GOC
    public function actionGoc()
    {
        $model = new GroupOverrideCommission();
        
        if (isset($_POST["calDateFrom"]) && $_POST["calDateTo"])
        {
            $dateFrom = $_POST["calDateFrom"];
            $dateTo = $_POST["calDateTo"];
            
            $rawData = $model->getComissions($dateFrom, $dateTo);
            
            $dataProvider = new CArrayDataProvider($rawData, array(
                                                    'keyField' => false,
                                                    'pagination' => array(
                                                    'pageSize' => 10,
                                                ),
                                    ));
            
            $this->render('goc', array('dataProvider' => $dataProvider));
        }
        else
        {
            $this->render('goc');
        }
    }
    
    //For Unilevel
    public function actionUnilevel()
    {
        $model = new Unilevel();
        
        if (isset($_POST["calDateFrom"]) && $_POST["calDateTo"])
        {
            $dateFrom = $_POST["calDateFrom"];
            $dateTo = $_POST["calDateTo"];
            
            $rawData = $model->getUnilevel($dateFrom, $dateTo);
            
            $dataProvider = new CArrayDataProvider($rawData, array(
                                                    'keyField' => false,
                                                    'pagination' => array(
                                                    'pageSize' => 10,
                                                ),
                                    ));
            
            $this->render('unilevel', array('dataProvider' => $dataProvider));
        }
        else
        {
            $this->render('unilevel');
        }
    }
    
    //For Unilevel
    public function actionBonus()
    {
        $model = new Bonus();
        
        if (isset($_POST["calDateFrom"]) && $_POST["calDateTo"])
        {
            $dateFrom = $_POST["calDateFrom"];
            $dateTo = $_POST["calDateTo"];
            
            $rawData = $model->getBonus($dateFrom, $dateTo);
            
            $dataProvider = new CArrayDataProvider($rawData, array(
                                                    'keyField' => false,
                                                    'pagination' => array(
                                                    'pageSize' => 10,
                                                ),
                                    ));
            
            $this->render('bonus', array('dataProvider' => $dataProvider));
        }
        else
        {
            $this->render('bonus');
        }
    }
}
?>
