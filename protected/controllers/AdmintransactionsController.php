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
        $total = $model->getTotalLoans();

        $dataProvider = new CArrayDataProvider($rawData, array(
                                                'keyField' => false,
                                                'pagination' => array(
                                                'pageSize' => 25,
                                            ),
                                ));

        $this->render('loan', array(
            'dataProvider' => $dataProvider,
            'total'=>$total
        ));  
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
                    
                    if ($status == 2)
                    {
                        $result_msg = "Loan Approved.";
                    }
                    else
                    {
                        $result_msg = "Loan Claimed.";
                    }
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
                    
                    if ($status == 1)
                    {
                        $result_msg = "GOC Approved.";
                    }
                    else
                    {
                        $result_msg = "GOC Claimed.";
                    }
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
                    
                    if ($status == 1)
                    {
                        $result_msg = "Unilevel Approved.";
                    }
                    else
                    {
                        $result_msg = "Unilevel Claimed.";
                    }
                    
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
                    
                    if ($status == 2)
                    {
                        $result_msg = "Bonus Approved.";
                    }
                    else
                    {
                        $result_msg = "Bonus Claimed.";
                    }
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
                $endorser_id = $_GET["endorser_id"];
                $cutoff_id = $_GET["cutoff_id"];
                
                
                $model = new DirectEndorsement();
                $result = $model->updateDirectEndorsementStatus($endorser_id, $cutoff_id, $status, $userid);
                
                if (count($result) > 0)
                {
                    $result_code = 0;
                    
                    if ($status == 1)
                    {
                        $result_msg = "Direct Endorsement Approved.";
                    }
                    else
                    {
                        $result_msg = "Direct Endorsement Claimed.";
                    }
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

        if (isset($_POST["GroupOverrideCommission"]))
        {
            unset(Yii::app()->session['groupoc']);
            $model->attributes = $_POST['GroupOverrideCommission'];
            Yii::app()->session['groupoc'] = $model->attributes;
        }
        else
        {
            $model->attributes = Yii::app()->session['groupoc'];
        }
        
        $rawData = $model->getComissions();
        $total = $model->getCommissionsTotal();
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                    'keyField' => false, //'direct_endorsement_id',
                    'pagination' => array(
                        'pageSize' => 25,
                    ),
                ));
        
        $this->render('goc', array('model'=>$model, 'dataProvider' => $dataProvider, 'total'=>$total));
    }
    
    //For Unilevel
    public function actionUnilevel()
    {
        $model = new Unilevel();
        $reference = new ReferenceModel();
                
        if (isset($_POST['Unilevel']))
        {            
            if(isset(Yii::app()->session['unilevel']))
                unset(Yii::app()->session['unilevel']);
            
            $cutoff = $reference->get_cutoff_by_id($model->cutoff_id);
            
            $model->last_cutoff_date = $cutoff['last_cutoff_date'];
            $model->next_cutoff_date = $cutoff['next_cutoff_date'];
        
            $model->attributes = $_POST['Unilevel'];
            Yii::app()->session['unilevel'] = $model->attributes;
        }
        else
        {
            $model->attributes = Yii::app()->session['unilevel'];
            
        }
        
        $rawData = $model->getUnilevel();
        $total = $model->getPayoutTotal();
        $dataProvider = new CArrayDataProvider($rawData, array(
                                                'keyField' => false,
                                                'pagination' => array(
                                                    'pageSize' => 25,
                                                ),
                                ));

        $this->render('unilevel', array(
                'dataProvider' => $dataProvider,
                'model'=>$model,
                'total'=>$total,
            ));
    }
    
    //For Bonus
    public function actionBonus()
    {
        $model = new Bonus();

        $rawData = $model->getBonus();

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
        $model = new DirectEndorsement();       
        
        if (isset($_POST["DirectEndorsement"]))
        {
            unset(Yii::app()->session['endorsements']);
            $model->attributes = $_POST['DirectEndorsement'];
            Yii::app()->session['endorsements'] = $model->attributes;
                
        }
        else
        {
            $model->attributes = Yii::app()->session['endorsements'];
        }
        
        $rawData = $model->getDirectEndorsement();
        $total = $model->getPayoutTotal();
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                    'keyField' => false, //'direct_endorsement_id',
                    'pagination' => array(
                        'pageSize' => 25,
                    ),
                ));

        $this->render('directendorse', array(
            'model'=>$model,
            'dataProvider' => $dataProvider,
            'total'=>$total,
         ));
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
        $html2pdf = Yii::app()->ePdf->HTML2PDF();
        
        if(isset($_GET["id"]))
        {
            $reference = new ReferenceModel();
            $model = new Loan();
            
            $loan_id = $_GET["id"];
            $member_id = $_GET["member_id"];
            $loan_type_id = $_GET["loan_type_id"];
            $level_no = $_GET["level_no"];
            $member_name = $_GET["member_name"];
            $loan_amount = $_GET["loan_amount"];
            $tax_withheld = $reference->get_variable_value('TAX_WITHHELD');
            $total_tax = $loan_amount * ($tax_withheld/100);
            $net_loan_amount = $loan_amount - $total_tax;
            
            $amount['total_loan'] = $loan_amount;
            $amount['tax_amount'] = $total_tax;
            $amount['net_loan'] = $net_loan_amount;
                        
            if ($loan_type_id == 1)
            {
                //direct 5
                //Get Payee Details
                $payee = $model->getPayeeDetails($member_id);

                //Check if member has previous loan/s.
                $prev_loan = $model->getPreviousLoans($member_id, $loan_id);
                $limit = 5 * count($prev_loan);

                //Get direct endorse details
                $direct_downlines = $model->getLoanDirectEndorsementDownlines($member_id, $limit);

                //Total Amount table
                $amount['cash'] = (80 / 100) * $net_loan_amount;
                $amount['check'] = (20 / 100) * $net_loan_amount;
                
                $html2pdf->WriteHTML($this->renderPartial('_loandirectreport', array(
                        'member_name'=>$member_name,
                        'payee'=>$payee,
                        'amount'=>$amount,
                        //'loan_amount'=>$loan_amount,
                        'direct_downlines'=>$direct_downlines,
                    ), true
                 ));
                $html2pdf->Output('LoanDirect_' . $member_name . '_' . date('Y-m-d') . '.pdf', 'D'); 
                Yii::app()->end();
                    
                    
            }
            else
            {
                //Get names of endorsed IBO
                $rawData = Networks::getDownlines($member_id);
                
                if (count($rawData) > 0)
                {   
                    //Get Payee Details
                    $payee = $model->getPayeeDetails($member_id);
                    
                    $final = Networks::arrangeLevel($rawData);
                    
                    //Get level 1 downline ids
                    foreach ($final['network'] as $val)
                    {
                        if ($val['Level'] == $level_no)
                        {
                            $downline_ids = $val['Members'];

                            $downlines = $model->getLoanCompletionDownlines($downline_ids);
                        }
                    }
                    
                    //Total Amount table
                    $amount['cash'] = (80 / 100) * $net_loan_amount;
                    $amount['check'] = (20 / 100) * $net_loan_amount;
                    
                    $html2pdf->WriteHTML($this->renderPartial('_loancompletionreport', array(
                            'member_name'=>$member_name,
                            'payee'=>$payee,
                            'amount'=>$amount,
                          //  'loan_amount'=>$loan_amount,
                            'downlines'=>$downlines,
                            'level_no'=>$level_no,
                        ), true
                     ));
                    $html2pdf->Output('LoanCompletion_' . $member_name . '_' . date('Y-m-d') . '.pdf', 'D'); 
                    Yii::app()->end();
                }
            }
        }
    }
    
    public function actionPdfGoc()
    {
        $model = new GroupOverrideCommission();
        $reference = new ReferenceModel();
        
        $html2pdf = Yii::app()->ePdf->HTML2PDF();
        
        if(isset($_GET["id"]))
        {
            $member_id = $_GET["member_id"];
            $member_name = $_GET["member_name"];
            $commission_amount = $_GET["amount"];
            $ibo_count = $_GET["ibo_count"];
            
            //Get Payee Details
            $payee = $model->getPayeeDetails($member_id);
            
            //Get names of endorsed IBO
            $rawData = Networks::getDownlines($member_id);
            $final = Networks::arrangeLevel($rawData,'ASC');
            
            //get cutoff dates
            $cutoff = ReferenceModel::get_cutoff_by_id($_GET["cutoff_id"]);
            $from_cutoff = $cutoff['last_cutoff_date'];
            $to_cutoff = $cutoff['next_cutoff_date'];
            
            
            //Get previous loans
            $prev_loans_total = $model->getPrevousLoans($member_id, $from_cutoff, $to_cutoff);
            $total_previous_loan = $prev_loans_total[0]['total_loan'];
            
            
            //Get downlines excluding level 1
            $downlines = array();
            
            foreach ($final['network'] as $val)
            {   
                if ($val['Level'] != 1)
                {
                    $exploded_members = explode(",", $val['Members']);
                    
                    $current_level = $val["Level"];
                    $i = 0;
                    foreach ($exploded_members as $ibo_id)
                    {
                        $exist = $model->checkIfExistInCutoff($ibo_id, $from_cutoff, $to_cutoff);
                        
                        if (count($exist) > 0)
                        {
                            $downlines_new = $model->getPayeeDownlineDetails($ibo_id);
                            
                            $downlines["level"] = $current_level;
                            $downlines["member_name"] = $downlines_new[0]["member_name"];
                            $downlines["upline_name"] = $downlines_new[0]["upline_name"];
                            $downlines["date_joined"] = $downlines_new[0]["date_joined"];
                            $dt[] = $downlines;
                        }
                        
                        $i++;
                        
                    }
                }
            }
            $tax_withheld = $reference->get_variable_value('TAX_WITHHELD');
            $amount['total_commission'] = $commission_amount;
            $previous_loan = $total_previous_loan;
            $commission_amount = $commission_amount - $previous_loan;
            $total_tax = $commission_amount * ($tax_withheld/100);
            $net_commission = $commission_amount - $total_tax;
            $amount['tax'] = $total_tax;
            $amount['net_commission'] = $net_commission;
            $amount['previous_loan'] = $previous_loan;
            
            //Total Amount table
            $amount['cash'] = (80 / 100) * $net_commission;
            $amount['check'] = (20 / 100) * $net_commission;
            
            $html2pdf->WriteHTML($this->renderPartial('_gocreport', array(
                            'member_name'=>$member_name,
                            'payee'=>$payee,
                            'amount'=>$amount,
                            'downlines'=>$dt,
                            'ibo_count'=>$ibo_count,
                            'previous_loan'=>$previous_loan,
                        ), true
                     ));
            
            $html2pdf->Output('GOC_' . $member_name . '_'  . date('Y-m-d') . '.pdf', 'D'); 
            Yii::app()->end();
        }
        else
        {
            echo "id not set";
        }
    }
    
    public function actionPdfUnilevel()
    {
        if(isset($_GET["id"]) && isset($_GET['cutoff_id']))
        {
            $member_id = $_GET["id"];
            $cutoff_id = $_GET["cutoff_id"];
            
            $model = new Unilevel();
            $member = new MembersModel();            
            $reference = new ReferenceModel();
            
            $model->cutoff_id = $cutoff_id;
            $model->member_id = $member_id;
            
            $result = $model->getUnilevelDetails();
            $total_amount = $result['amount'];
            $tax_withheld = $reference->get_variable_value('TAX_WITHHELD');
            $total_tax = $total_amount * ($tax_withheld/100);
            
            $payout['total_amount'] = $total_amount;
            $payout['ibo_count'] = $result['ibo_count'];
            
            $payout['tax_amount'] = $total_tax;
            $payout['net_amount'] = $total_amount - $total_tax;
            
            //Payee Information
            $payee = $member->selectMemberDetails($member_id);
            $payee_endorser_id = $payee['endorser_id'];
            $payee_name = $payee['last_name'] . '_' . $payee['first_name'];
            
            //Endorser Information
            $endorser = $member->selectMemberDetails($payee_endorser_id);
            
            //Cut-Off Dates
            $cutoff = $reference->get_cutoff_by_id($cutoff_id);
            $date_from = $cutoff['last_cutoff_date'];
            $date_to = $cutoff['next_cutoff_date'];
              
            $downline = Networks::getUnilevel($member_id);
            //$downline = Networks::getUnilevelByCutOff($member_id,$date_from, $date_to);            
            $unilevels = Networks::arrangeLevel($downline, 'ASC');
                
            $first_cutoff = $reference->is_first_cutoff(TransactionTypes::UNILEVEL);
            
            //Check if current cutoff is the first cutoff
            if($cutoff_id == $first_cutoff || $model->is_first_transaction())
            {
                $first_trx = true;

                foreach($unilevels['network'] as $level)
                {
                    $unilevel['level'] = $level['Level'];
                    $unilevel['downlines'] = Networks::getUnilevelDownlines($level['Members']);
                    $unilevel_downlines[] = $unilevel;
                }
            }
            else
            {
                $first_trx = false;

                //Next transactions
                foreach($unilevels['network'] as $level)
                {
                    $unilevel['level'] = $level['Level'];
                    $unilevel['downlines'] = Networks::getUnilevelDownlines($level['Members']);
                    $unilevel_downlines[] = $unilevel;
                }

                foreach($unilevel_downlines as $rows)
                {
                    $new['level'] = $rows['level'];
                    foreach($rows['downlines'] as $row)
                    {
                        $placement_date = date('Y-m-d',strtotime($row['Placement_Date']));
                        if($placement_date > $date_from && $placement_date <= $date_to)
                            $new_row[] = $row;
                    }
                    $new['downlines'] = $new_row;

                } 

                $new_rows[] = $new;
            }
                
            //var_dump($unilevel_downlines); exit;
            $html2pdf = Yii::app()->ePdf->HTML2PDF();
            $html2pdf->WriteHTML($this->renderPartial('_unilevelreport', array(
                    'payee'=>$payee,
                    'endorser'=>$endorser,
                    'downlines'=>($first_trx === false) ? $new_rows : $unilevel_downlines,
                    'cutoff'=>$cutoff,
                    'payout'=>$payout,
                ), true
             ));
            $html2pdf->Output('Unilevel_' . $payee_name . '_' . date('Y-m-d') . '.pdf', 'D'); 
             
        }
    }
    
    public function actionPdfBonus()
    {
        if(isset($_GET["id"]))
        {
            $promo_redemption_id = $_GET["id"];
            $member_id = $_GET["member_id"];
            $member_name = $_GET["member_name"];

            $content = "Bonus Payout for ".$promo_redemption_id." cut off";
            $content .= "<br>";
            $content .= "Member Name: ".$member_name;
            
            $html2pdf = Yii::app()->ePdf->HTML2PDF();
            $html2pdf->WriteHTML($content);
            $html2pdf->Output('Bonus_' . date('Y-m-d') . '.pdf', 'D'); 
        }
        else
        {
            echo "id not set";
        }
    }
    
    public function actionPdfDirect()
    {
        if(isset($_GET['id']) && isset($_GET['cutoff_id']))
        {
            
            $endorser_id = $_GET["id"];
            $cutoff_id = $_GET["cutoff_id"];
            
            $member = new MembersModel();            
            $model = new DirectEndorsement();
            $reference = new ReferenceModel();
            
            $model->cutoff_id = $cutoff_id;
            $model->endorser_id = $endorser_id;
            
            //Payee Information
            $payee = $member->selectMemberDetails($endorser_id);
            $payee_endorser_id = $payee['endorser_id'];
            $payee_name = $payee['last_name'] . '_' . $payee['first_name'];
            
            //Endorser Information
            $endorser = $member->selectMemberDetails($payee_endorser_id);
            
            //Cut-Off Dates
            $cutoff = $reference->get_cutoff_by_id($cutoff_id);
            
            $endorsee = $model->getEndorseeByCutoff();
            $total = $model->getEndorsementTotalAmount();
            
            $total_amount = $total['total_amount'];
            
            $tax_withheld = $reference->get_variable_value('TAX_WITHHELD');
            $total_tax = $total_amount * ($tax_withheld/100);
            
            $total['tax_amount'] = $total_tax;
            $total['net_amount'] = $total_amount - $total_tax;
           
            $html2pdf = Yii::app()->ePdf->HTML2PDF();            
            $html2pdf->WriteHTML($this->renderPartial('_directendorsereport', array(
                    'payee'=>$payee,
                    'endorser'=>$endorser,
                    'endorsee'=>$endorsee,
                    'cutoff'=>$cutoff,
                    'total'=>$total,
                ), true
             ));
            $html2pdf->Output('DirectEndorsement_' . $payee_name . '_' . date('Y-m-d') . '.pdf', 'D'); 
            Yii::app()->end();
        }
    }
}
?>
