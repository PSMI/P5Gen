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
    
    public function actionPdfLoans()
    {
        if(isset($_GET["id"]))
        {
            $loan_id = $_GET["id"];
            $member_id = $_GET["member_id"];
            $loan_type_id = $_GET["loan_type_id"];
            $level_no = $_GET["level_no"];
            $member_name = $_GET["member_name"];
            $loan_amount = $_GET["loan_amount"];
            
            $model = new LoanMember();
            
            $content = "<table  align='center'><tr><td><h3>LOAN ENDORSEMENT APPLICATION FORM</h3></td></tr></table>";
            
            $content .= "<br>";
                    
            $content .= "<table style='width: 100%;'>";
            $content .= "<tr>";
            $content .= "<td align='left'>Loan Amount</td>";
            $content .= "<td>_________________________________________________</td>";
            $content .= "<td>P " . $this->numberFormat($loan_amount) . "</td>";
            $content .= "</tr>";
            $content .= "</table>";
            
            $content .= "<table style='width: 100%;'>";
            $content .= "<tr>";
            $content .= "<td align='left'>Purchased Product</td>";
            $content .= "<td>&nbsp;&nbsp;</td>";
            $content .= "<td>" . CHtml::checkBox('chkboxProduct', false) . "</td>";
            $content .= "<td style='font-weight:bold;'>Water Filtration System - P2S</td>";
            $content .= "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
            $content .= "<td>" . CHtml::checkBox('chkboxOtherProduct', false) . "</td>";
            $content .= "<td style='font-weight:bold;'>Other Product/s</td>";
            $content .= "<td>___________________________</td>";
            $content .= "</tr>";
            $content .= "</table>";
            
            $content .= "<table align='center'><tr><td><h4>IBO's PERSONAL DETAILS</h4></td></tr></table>";
            
            
            $html2pdf = Yii::app()->ePdf->HTML2PDF();
            $html2pdf->WriteHTML($content);
            $html2pdf->Output('Loan_' . date('Y-m-d') . '.pdf', 'D'); 
        }
        else
        {
            echo "id not set";
        }
    }
}
