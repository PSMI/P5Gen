<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-12-2014
------------------------*/

class TransactionController extends Controller
{
    public $layout = 'column2';
    
    //For Loan
    public function actionLoans()
    {
        $model = new LoanMember();
 
        if (isset($_POST["calDateFrom"]) && $_POST["calDateTo"])
        {
            $dateFrom = $_POST["calDateFrom"];
            $dateTo = $_POST["calDateTo"];
            $member_id = Yii::app()->user->getId();
            
            $rawData = $model->getLoanTransactions($dateFrom, $dateTo, $member_id);
            
            $dataProvider = new CArrayDataProvider($rawData, array(
                                                    'keyField' => false,
                                                    'pagination' => array(
                                                    'pageSize' => 10,
                                                ),
                                    ));
            
            $this->render('loans', array('dataProvider' => $dataProvider));
        }
        else
        {
            $this->render('loans');
        }
    }
    
    //For GOC
    public function actionGoc()
    {
        $model = new GroupOverrideCommissionMember();
        
        if (isset($_POST["calDateFrom"]) && $_POST["calDateTo"])
        {
            $dateFrom = $_POST["calDateFrom"];
            $dateTo = $_POST["calDateTo"];
            $member_id = Yii::app()->user->getId();
            
            $rawData = $model->getComissions($dateFrom, $dateTo, $member_id);
            
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
    
    //For Bonus
    public function actionBonus()
    {
        $model = new BonusMember();
        
        if (isset($_POST["calDateFrom"]) && $_POST["calDateTo"])
        {
            $dateFrom = $_POST["calDateFrom"];
            $dateTo = $_POST["calDateTo"];
            $member_id = Yii::app()->user->getId();
            
            $rawData = $model->getBonus($dateFrom, $dateTo, $member_id);
            
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
        $model = new DirectEndorsementMember();
        
        if (isset($_POST["calDateFrom"]) && $_POST["calDateTo"])
        {
            $dateFrom = $_POST["calDateFrom"];
            $dateTo = $_POST["calDateTo"];
            $member_id = Yii::app()->user->getId();
            
            $rawData = $model->getDirectEndorsement($dateFrom, $dateTo, $member_id);
            
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
}
