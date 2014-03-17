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
        $reference = new ReferenceModel();
        
        $cutoff = $reference->get_cutoff_dates(TransactionTypes::GOC);
        $next_cutoff = date('M d Y',strtotime($cutoff['next_cutoff_date']));

        $member_id = Yii::app()->user->getId();

        $rawData = $model->getComissions($member_id);

        $dataProvider = new CArrayDataProvider($rawData, array(
                                                'keyField' => false,
                                                'pagination' => array(
                                                'pageSize' => 10,
                                            ),
                                ));

        $this->render('goc', array('dataProvider' => $dataProvider,'next_cutoff'=>$next_cutoff));
    }
    
    //For Bonus
    public function actionBonus()
    {
        $model = new BonusMember();

        $member_id = Yii::app()->user->getId();

        $rawData = $model->getBonus($member_id);
        $promo = $model->getActivePromo();

        $dataProvider = new CArrayDataProvider($rawData, array(
                                                'keyField' => false,
                                                'pagination' => array(
                                                'pageSize' => 10,
                                            ),
                                ));

        $this->render('bonus', array('dataProvider' => $dataProvider,'promo'=>$promo));
    }
    
    //For Direct Endorsement
    public function actionDirectendorse()
    {
        $model = new DirectEndorsementMember();
        $reference = new ReferenceModel();
        
        $cutoff = $reference->get_cutoff_dates(TransactionTypes::DIRECT_ENDORSE);
        $next_cutoff = date('M d Y',strtotime($cutoff['next_cutoff_date']));

        $member_id = Yii::app()->user->getId();

        $rawData = $model->getDirectEndorsement($member_id);

        $dataProvider = new CArrayDataProvider($rawData, array(
                                                'keyField' => false,
                                                'pagination' => array(
                                                'pageSize' => 10,
                                            ),
                                ));

        $this->render('directendorse', array('dataProvider' => $dataProvider,'next_cutoff'=>$next_cutoff));
    }
    
    //For Unilevel
    public function actionUnilevel()
    {
        $model = new UnilevelMember();
        $reference = new ReferenceModel();
        
        $cutoff = $reference->get_cutoff_dates(TransactionTypes::UNILEVEL);
        $next_cutoff = date('M d Y',strtotime($cutoff['next_cutoff_date']));
        
        $member_id = Yii::app()->user->getId();

        $rawData = $model->getUnilevel($member_id);

        $dataProvider = new CArrayDataProvider($rawData, array(
                                                'keyField' => false,
                                                'pagination' => array(
                                                'pageSize' => 10,
                                            ),
                                ));

        $this->render('unilevel', array('dataProvider' => $dataProvider,'next_cutoff'=>$next_cutoff));
    }
    
    public function getStatusForButtonDisplayLoan($status_id, $status_type)
    {
        if ($status_type == 3)
        {
            //file loan button (member)
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
            else if($status_id == 3)
            {
                return false;
            }
            else if($status_id == 4)
            {
                return false;
            }
        }
        else if ($status_type == 4)
        {
            //download button (member)
            if ($status_id == 0)
            {
                return false;
            }
            else if($status_id == 1)
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
            else if($status_id == 4)
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
    
    public function actionPdfLoans()
    {
        $model = new LoanMember();
        
        $html2pdf = Yii::app()->ePdf->HTML2PDF();
        
        if(isset($_GET["id"]))
        {
            $loan_id = $_GET["id"];
            $member_id = $_GET["member_id"];
            $loan_type_id = $_GET["loan_type_id"];
            $level_no = $_GET["level_no"];
            $member_name = $_GET["member_name"];
            $loan_amount = $_GET["loan_amount"];
            
            //Convert amount in words
            //$amount_in_words = $this->convert_number_to_words($num);
            $loan_amount_nodecimal = floor($loan_amount);
            $convert_amounttoword = $this->widget('ext.NumtoWord.NumtoWord', array('num'=>$loan_amount_nodecimal)); 
            $amount_in_words = ucfirst($convert_amounttoword->result);
            
            //Get Payee Details
            $payee = $model->getPayeeDetails($member_id);

            //Check if member has previous loan/s.
            $prev_loan = $model->getPreviousLoans($member_id, $loan_id);
            $limit = 5 * count($prev_loan);

            //Get direct endorse details
            $direct_downlines = $model->getLoanDirectEndorsementDownlines($member_id, $limit);

            //Total Amount table
//            $amount['cash'] = (80 / 100) * $net_loan_amount;
//            $amount['check'] = (20 / 100) * $net_loan_amount;

            $html2pdf->WriteHTML($this->renderPartial('_loandirectreport', array(
                    'member_name'=>$member_name,
                    'payee'=>$payee,
                    'amount_in_words'=>$amount_in_words,
                    'loan_amount'=>$loan_amount,
                    'direct_downlines'=>$direct_downlines,
                ), true
             ));
            $html2pdf->Output('LoanDirect_' . $member_name . '_' . date('Y-m-d') . '.pdf', 'D'); 
            Yii::app()->end();
        }
        else
        {
            echo "id not set";
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
            $loan_id = $_GET["id"];
                
            $model = new LoanMember();
            $result = $model->updateLoanStatus($loan_id, $status);

            if (count($result) > 0)
            {
                $result_code = 0;
                $result_msg = "Loan Filed.";
            }
            else
            {
                $result_code = 1;
                $result_msg = "An error occured. Please try again.";
            }
        }
        else
        {
            $result_code = 2;
            $result_msg = "An error occured. Please try again.";
        }
        
        echo CJSON::encode(array('result_code'=>$result_code, 'result_msg'=>$result_msg));
    }
}
