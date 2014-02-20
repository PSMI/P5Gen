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

        $member_id = Yii::app()->user->getId();

        $rawData = $model->getLoanTransactions($member_id);

        $dataProvider = new CArrayDataProvider($rawData, array(
                                                'keyField' => false,
                                                'pagination' => array(
                                                'pageSize' => 10,
                                            ),
                                ));

        $this->render('loans', array('dataProvider' => $dataProvider));
    }
    
    //For GOC
    public function actionGoc()
    {
        $model = new GroupOverrideCommissionMember();

        $member_id = Yii::app()->user->getId();

        $rawData = $model->getComissions($member_id);

        $dataProvider = new CArrayDataProvider($rawData, array(
                                                'keyField' => false,
                                                'pagination' => array(
                                                'pageSize' => 10,
                                            ),
                                ));

        $this->render('goc', array('dataProvider' => $dataProvider));
    }
    
    //For Bonus
    public function actionBonus()
    {
        $model = new BonusMember();

        $member_id = Yii::app()->user->getId();

        $rawData = $model->getBonus($member_id);

        $dataProvider = new CArrayDataProvider($rawData, array(
                                                'keyField' => false,
                                                'pagination' => array(
                                                'pageSize' => 10,
                                            ),
                                ));

        $this->render('bonus', array('dataProvider' => $dataProvider));
    }
    
    //For Direct Endorsement
    public function actionDirectendorse()
    {
        $model = new DirectEndorsementMember();

        $member_id = Yii::app()->user->getId();

        $rawData = $model->getDirectEndorsement($member_id);

        $dataProvider = new CArrayDataProvider($rawData, array(
                                                'keyField' => false,
                                                'pagination' => array(
                                                'pageSize' => 10,
                                            ),
                                ));

        $this->render('directendorse', array('dataProvider' => $dataProvider));
    }
    
    //For Unilevel
    public function actionUnilevel()
    {
        $model = new UnilevelMember();

        $member_id = Yii::app()->user->getId();

        $rawData = $model->getUnilevel($member_id);

        $dataProvider = new CArrayDataProvider($rawData, array(
                                                'keyField' => false,
                                                'pagination' => array(
                                                'pageSize' => 10,
                                            ),
                                ));

        $this->render('unilevel', array('dataProvider' => $dataProvider));
    }
    
    
    public function getStatusForButtonDisplayLoan($status_id, $status_type)
    {
        if ($status_type == 1)
        {
            //approve button
            if ($status_id == 1)
            {
                return true;
            }
            else if($status_id == 3)
            {
                return false;
            }
            else if($status_id == 2)
            {
                return false;
            }
        }
        else if ($status_type == 2)
        {
            //claim button
            if ($status_id == 1)
            {
                return false;
            }
            else if($status_id == 2)
            {
                return true;
            }
            else if($status_id == 3)
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    
    public function getStatusForButtonDisplayGoc($status_id, $status_type)
    {
        if ($status_type == 1)
        {
            //approve button
            if ($status_id == 0)
            {
                return true;
            }
            else if($status_id == 1)
            {
                return false;
            }
            else if($status_id == 2)
            {
                return false;
            }
        }
        else if ($status_type == 2)
        {
            //claim button
            if ($status_id == 0)
            {
                return false;
            }
            else if($status_id == 1)
            {
                return true;
            }
            else if($status_id == 2)
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
}
