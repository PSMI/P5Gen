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
            
        $rawData = $model->getLoanApplications();

        $dataProvider = new CArrayDataProvider($rawData, array(
                                                'keyField' => false,
                                                'pagination' => array(
                                                'pageSize' => 10,
                                            ),
                                ));

        $this->render('loan', array('dataProvider' => $dataProvider));  
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
            else if($transtype == 'directendrse')
            {
                $direct_endorsement_id = $_GET["id"];
                
                $model = new DirectEndorsement();
                $result = $model->updateDirectEndorsementStatus($direct_endorsement_id, $status, $userid);
                
                if (count($result) > 0)
                {
                    $result_code = 0;
                    $result_msg = "Direct Endorsement Claimed.";
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
    
    //For Bonus
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
    
    //For Direct Endorsement
    public function actionDirectendorse()
    {
        $model = new DirectEndorsement();
        
        if (isset($_POST["calDateFrom"]) && $_POST["calDateTo"])
        {
            $dateFrom = $_POST["calDateFrom"];
            $dateTo = $_POST["calDateTo"];
            
            $rawData = $model->getDirectEndorsement($dateFrom, $dateTo);
            
            $dataProvider = new CArrayDataProvider($rawData, array(
                                                    'keyField' => false,
                                                    'pagination' => array(
                                                    'pageSize' => 10,
                                                ),
                                    ));
            
            $this->render('directendorse', array('dataProvider' => $dataProvider));
        }
        else
        {
            $this->render('directendorse');
        }
    }
    
    public function getStatus($status_id)
    {
        if ($status_id == 0)
        {
            return "Pending";
        }
        else if($status_id == 1)
        {
            return "Completed";
        }
        else if($status_id == 2)
        {
            return "Approved";
        }
        else
        {
            return "Claimed";
        }
    }
    
    public function getStatusLoan($status_id, $status_type)
    {
        if ($status_type == 1)
        {
            if ($status_id == 1)
            {
                return true;
            }
            else if($status_id == 3)
            {
                return false;
            }
            else
            {
                return false;
            }
        }
        else if ($status_type == 2)
        {
            if ($status_id == 1)
            {
                return false;
            }
            else if($status_id == 2)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    
    public function dateFormat($date)
    {
        if ($date == '')
        {
            return false;
        }
        else
        {
            $new_date = new DateTime($date);

            return date_format($new_date, 'F j, Y, g:i a');
        } 
    }
    
    public function numberFormat($amount)
    {
        return number_format($amount, 2);
    }
}
?>
