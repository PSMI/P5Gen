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
            
        if (isset($_POST["Loan"]))
        {   
            unset(Yii::app()->session['statusid']);
            $model->attributes = $_POST['Loan'];
            Yii::app()->session['statusid'] = $model->attributes;
        }
        else
        {
            $model->date_from2 = date('Y-m-d');
            $model->date_to = date('Y-m-d');
            $model->status = "1, 2, 3, 4";
            $model->attributes = Yii::app()->session['statusid'];
        }
        
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
            'total'=>$total,
            'model'=>$model
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
                    
                    if ($status == 3)
                    {
                        $result_msg = "Loan Approved.";
                    }
                    else if ($status == 4)
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
            else if($transtype == 'ipdunilvl')
            {
                $unilevel_id = $_GET["id"];
                
                $model = new IpdUnilevel();
                $result = $model->updateUnilevelStatus($unilevel_id, $status, $userid);
                
                if (count($result) > 0)
                {
                    $result_code = 0;
                    if ($status == 1)
                    {
                        $result_msg = "IpdUnilevel Approved.";
                    }
                    else
                    {
                        $result_msg = "IpdUnilevel Claimed.";
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
                $date_claimed = $_GET['date_claimed'];
                
                $model = new DirectEndorsement();
                $result = $model->updateDirectEndorsementStatus($endorser_id, $cutoff_id, $status, $userid, $date_claimed);
                
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
            else if($transtype == 'ipddirectendrse')
            {
                $direct_endorsement_id = $_GET["id"];
                $endorser_id = $_GET["endorser_id"];
                $cutoff_id = $_GET["cutoff_id"];
                $date_claimed = $_GET['date_claimed'];
                
                $model = new IpdDirectEndorsement();
                $result = $model->updateDirectEndorsementStatus($endorser_id, $cutoff_id, $status, $userid, $date_claimed);
                
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
            else if($transtype == 'iborpcommission')
            {
                $distributor_commission_id = $_GET["id"];
                
                $model = new IboRpCommission();
                $result = $model->updateIboRpCommissionStatus($distributor_commission_id, $status, $userid);
                
                if (count($result) > 0)
                {
                    $result_code = 0;
                    
                    if ($status == 1)
                    {
                        $result_msg = "IBO RP Commission Approved.";
                    }
                    else
                    {
                        $result_msg = "IBO RP Commission Claimed.";
                    }
                }
                else
                {
                    $result_code = 1;
                    $result_msg = "An error occured. Please try again.";
                }
            }
            else if($transtype == 'ipdrpcommission')
            {
                $distributor_commission_id = $_GET["id"];
                
                $model = new IpdRpCommission();
                $result = $model->updateIpdRpCommissionStatus($distributor_commission_id, $status, $userid);
                
                if (count($result) > 0)
                {
                    $result_code = 0;
                    
                    if ($status == 1)
                    {
                        $result_msg = "IPD RP Commission Approved.";
                    }
                    else
                    {
                        $result_msg = "IPD RP Commission Claimed.";
                    }
                }
                else
                {
                    $result_code = 1;
                    $result_msg = "An error occured. Please try again.";
                }
            }
            else if($transtype == 'ipdretention')
            {
                $distributor_retention_id = $_GET["id"];
                
                $model = new IpdRetention();
                $result = $model->updateIpdRetentionStatus($distributor_retention_id, $status, $userid);
                
                if (count($result) > 0)
                {
                    $result_code = 0;
                    $result_msg = "IPD Retention Money Withdrawn.";
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
    
    //For IpdUnilevel
    public function actionIpdUnilevel()
    {
        $model = new IpdUnilevel();
        $reference = new ReferenceModel();
        if (isset($_POST['IpdUnilevel']))
        {            
            if(isset(Yii::app()->session['ipdunilevel']))
                unset(Yii::app()->session['ipdunilevel']);
            $cutoff = $reference->get_cutoff_by_id($model->cutoff_id);
            $model->last_cutoff_date = $cutoff['last_cutoff_date'];
            $model->next_cutoff_date = $cutoff['next_cutoff_date'];
            $model->attributes = $_POST['IpdUnilevel'];
            Yii::app()->session['ipdunilevel'] = $model->attributes;
            
//            if ($model->status == "0")
//            {
//                if (isset($_POST['fout']))
//                {
//                    //$result = $model->flashOut();
//                    unset($_POST['fout']);
//                }
//            }
        }
        else
        {
            $model->attributes = Yii::app()->session['ipdunilevel'];
            $model->status = "0, 1, 2, 3";
        }
        $rawData = $model->getUnilevel();
        $total = $model->getPayoutTotal();
        $dataProvider = new CArrayDataProvider($rawData, array(
                                                'keyField' => false,
                                                'pagination' => array(
                                                    'pageSize' => 25,
                                                ),
                                ));
        $this->render('ipdunilevel', array(
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
                                                'pageSize' => 25,
                                            ),
                                ));

        $this->render('bonus', array('dataProvider' => $dataProvider));
    }
    
    //For Direct Endorsement
    public function actionDirectendorse()
    {
        $model = new DirectEndorsement();      
        $model->date_claimed = date('Y-m-d');
        
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
    //For IPD Direct Endorsement
    public function actionIpdDirectendorse()
    {
        $model = new IpdDirectEndorsement();      
        $model->date_claimed = date('Y-m-d');
        if (isset($_POST["IpdDirectEndorsement"]))
        {
            unset(Yii::app()->session['ipdendorsements']);
            $model->attributes = $_POST['IpdDirectEndorsement'];
            Yii::app()->session['ipdendorsements'] = $model->attributes;  
        }
        else
        {
            $model->attributes = Yii::app()->session['ipdendorsements'];
        }
        $rawData = $model->getDirectEndorsement();
        $total = $model->getPayoutTotal();
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                    'keyField' => false, //'direct_endorsement_id',
                    'pagination' => array(
                        'pageSize' => 25,
                    ),
                ));
        $this->render('ipddirectendorse', array(
            'model'=>$model,
            'dataProvider' => $dataProvider,
            'total'=>$total,
         ));
    }
    
    //For Ipd Retention
    public function actionIpdRetention()
    {
        $model = new IpdRetention();
                
        if (isset($_POST['IpdRetention']))
        {            
            if(isset(Yii::app()->session['ipdretention']))
                unset(Yii::app()->session['ipdretention']);
        
            $model->attributes = $_POST['IpdRetention'];
            Yii::app()->session['ipdretention'] = $model->attributes;
        }
        else
        {
            $model->attributes = Yii::app()->session['ipdretention'];
        }
        
        $rawData = $model->getIpdRetentionMoney();
        $total = $model->getPayoutTotal();
        $dataProvider = new CArrayDataProvider($rawData, array(
                                                'keyField' => false,
                                                'pagination' => array(
                                                    'pageSize' => 25,
                                                ),
                                ));

        $this->render('ipdretention', array(
                'dataProvider' => $dataProvider,
                'model'=>$model,
                'total'=>$total,
            ));
    }
    
    //For IDP RP Commission
    public function actionIpdRpCommission()
    {
        $model = new IpdRpCommission();
                
        if (isset($_POST['IpdRpCommission']))
        {            
            if(isset(Yii::app()->session['ipdrpcommission']))
                unset(Yii::app()->session['ipdrpcommission']);
        
            $model->attributes = $_POST['IpdRpCommission'];
            Yii::app()->session['ipdrpcommission'] = $model->attributes;
        }
        else
        {
            $model->attributes = Yii::app()->session['ipdrpcommission'];
        }
        
        $rawData = $model->getIpdRpCommission();
        $total = $model->getPayoutTotal();
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                                                'keyField' => false,
                                                'pagination' => array(
                                                    'pageSize' => 25,
                                                ),
                                ));

        $this->render('ipdrpcommission', array(
                'dataProvider' => $dataProvider,
                'model'=>$model,
                'total'=>$total,
            ));
    }
    
    //For IBO RP Commission
    public function actionIboRpCommission()
    {
        $model = new IboRpCommission();
                
        if (isset($_POST['IboRpCommission']))
        {            
            if(isset(Yii::app()->session['iborpcommission']))
                unset(Yii::app()->session['iborpcommission']);
        
            $model->attributes = $_POST['IboRpCommission'];
            Yii::app()->session['iborpcommission'] = $model->attributes;
        }
        else
        {
            $model->attributes = Yii::app()->session['iborpcommission'];
        }
        
        $rawData = $model->getIboRpCommission();
        $total = $model->getPayoutTotal();
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                                                'keyField' => false,
                                                'pagination' => array(
                                                    'pageSize' => 25,
                                                ),
                                ));

        $this->render('iborpcommission', array(
                'dataProvider' => $dataProvider,
                'model'=>$model,
                'total'=>$total,
            ));
    }
    
    public function getStatusForButtonDisplayLoan($status_id, $status_type)
    {
        if ($status_type == 1)
        {
            //approve button (admin)
            if ($status_id == 2)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else if ($status_type == 2)
        {
            //claim button (admin)
            if ($status_id == 3)
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
        else if ($status_type == 3)
        {
            //claim button
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
            $model = new Loan();
            
            $loan_id = $_GET["id"];
            $member_id = $_GET["member_id"];
            $loan_type_id = $_GET["loan_type_id"];
            $level_no = $_GET["level_no"];
            $member_name = $_GET["member_name"];
            $loan_amount = $_GET["loan_amount"];
            
            $model->loan_id = $loan_id;
            $loan = $model->getLoanDetails();
            
            $interest_rate = $loan['interest_rate'];
            $interest = ($interest_rate / 100) * $loan_amount;            
            $other_charges_rate = $loan['other_charges'];
            $other_charges = ($other_charges_rate / 100) * $loan_amount;            
            $profit_share_entry = $loan['profit_share'];
            
            if($loan_type_id == 1)
                $net_loan_amount = $loan_amount - ($interest + $other_charges + $profit_share_entry); 
            else
                $net_loan_amount = $loan_amount - ($interest + $other_charges); 

            $amount['total_loan'] = $loan_amount;
            $amount['interest'] = $interest;
            $amount['interest_rate'] = $interest_rate;
            $amount['other_charges'] = $other_charges;
            $amount['other_charges_rate'] = $other_charges_rate;
            $amount['profit_share_entry'] = $profit_share_entry;
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

                $html2pdf->WriteHTML($this->renderPartial('_loandirectreport', array(
                        'member_name'=>$member_name,
                        'payee'=>$payee,
                        'amount'=>$amount,
                        'direct_downlines'=>$direct_downlines,
                    ), true
                 ));
                $html2pdf->Output('LoanDirect_' . $member_name . '_' . date('Y-m-d') . '.pdf', 'D'); 
                Yii::app()->end();
                    
                    
            }
            else
            {
                $amount['cash'] = $net_loan_amount * (80/100);
                $amount['check'] = $net_loan_amount * (20/100);
                
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
                    
                    $html2pdf->WriteHTML($this->renderPartial('_loancompletionreport', array(
                            'member_name'=>$member_name,
                            'payee'=>$payee,
                            'amount'=>$amount,
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
            
            //Get Payee loan balance
            $loan_balance_arr = $model->getLoanBalance($member_id);
            $loan_balance = $loan_balance_arr['loan_balance'];
            
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
            $total_tax = $commission_amount * ($tax_withheld/100);
            $commission_amount = $commission_amount - $previous_loan;
            $net_commission = $commission_amount - $total_tax;
            $amount['tax'] = $total_tax;
            $amount['net_commission'] = $net_commission;
            $amount['previous_loan'] = $previous_loan;
            
            //Total Amount table
            if($net_commission < '100000')
            {
                $amount['cash'] = (90 / 100) * $net_commission;
                $amount['check'] = (10 / 100) * $net_commission;
                $amount['cash_pct'] = 90;
                $amount['check_pct'] = 10;
            }
            else
            {
                $amount['cash'] = (95 / 100) * $net_commission;
                $amount['check'] = (5 / 100) * $net_commission;
                $amount['cash_pct'] = 95;
                $amount['check_pct'] = 5;
            }
            
            $html2pdf->WriteHTML($this->renderPartial('_gocreport', array(
                            'member_name'=>$member_name,
                            'payee'=>$payee,
                            'amount'=>$amount,
                            'downlines'=>$dt,
                            'ibo_count'=>$ibo_count,
                            'loan_balance'=>$loan_balance,
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
            $date_from = date('Y-m-d',strtotime($cutoff['last_cutoff_date']));
            $date_to = date('Y-m-d',strtotime($cutoff['next_cutoff_date']));
              
            $downline = Networks::getUnilevel($member_id);
            $unilevels = Networks::arrangeLevel($downline, 'ASC');
                
            foreach($unilevels['network'] as $level)
            {                    
                $levels = $level['Level'];
                 if($levels < 11)
                 {
                    $model->cutoff_id = $cutoff_id;
                    
                    if($model->is_first_transaction())
                    {
                        $flush_out = $reference->get_variable_value('UNILEVEL_FLUSHOUT_INTERVAL');
                        $month = explode(" ", $flush_out);
                        $interval = $month[1];
                        $model->upline_id = $member_id;
                        $account = $model->get_running_account($interval);                        
                        
                        if($account['num_of_months'] > $month[0])
                            $downlines = Networks::getUnilevelDownlinesByFlushOut($level['Members'],$account['date_first_five_completed']);
                        else
                            $downlines = Networks::getUnilevelDownlinesByDate($level['Members'], $date_from);
                    }
                    else
                    {
                        $downlines = Networks::getUnilevelDownlinesByCutOff($level['Members'],$date_from,$date_to);
                    }
                    
                    if(!is_null($downlines))
                    {
                        $unilevel['member_id'] = $member_id;
                        $total =+ count($downlines);
                        $unilevel['total'] = $total;
                        $unilevel['level'] = $levels;                     
                        $unilevel['downlines'] = $downlines;

                        $unilevel_downlines[] = $unilevel;
                    }
                 }
            }
            
            $html2pdf = Yii::app()->ePdf->HTML2PDF();
            $html2pdf->WriteHTML($this->renderPartial('_unilevelreport', array(
                    'payee'=>$payee,
                    'endorser'=>$endorser,
                    'downlines'=>$unilevel_downlines,
                    'cutoff'=>$cutoff,
                    'payout'=>$payout,
                ), true
             ));
            $html2pdf->Output('Unilevel_' . $payee_name . '_' . date('Y-m-d') . '.pdf', 'D'); 
             
        }
    }
    
    public function actionPdfIpdUnilevel()
    {
        if(isset($_GET["id"]) && isset($_GET['cutoff_id']))
        {
            $member_id = $_GET["id"];
            $cutoff_id = $_GET["cutoff_id"];
            $model = new IpdUnilevel();
            $member = new MembersModel();            
            $reference = new ReferenceModel();
            $distributor = new DistributorForm();
            
            $model->cutoff_id = $cutoff_id;
            $model->member_id = $member_id;
            $result = $model->getUnilevelDetails();
            $total_amount = $result['amount'];
            $tax_withheld = $reference->get_variable_value('TAX_WITHHELD');
            $total_tax = $total_amount * ($tax_withheld/100);
            $payout['total_amount'] = $total_amount;
            $payout['ipd_count'] = $result['ipd_count'];
            $payout['tax_amount'] = $total_tax;
            $payout['net_amount'] = $total_amount - $total_tax;
            //Payee Information
            $payee = $member->selectMemberDetails($member_id);
            $payee_endorser_id = $payee['ipd_endorser_id'];
            $payee_name = $payee['last_name'] . '_' . $payee['first_name'];
            //Endorser Information
            $endorser = $member->selectMemberDetails($payee_endorser_id);
            //Cut-Off Dates
            $cutoff = $reference->get_cutoff_by_id($cutoff_id);
            $date_from = date('Y-m-d',strtotime($cutoff['last_cutoff_date']));
            $date_to = date('Y-m-d',strtotime($cutoff['next_cutoff_date']));
                        
            $distributors = $distributor->getDistributorsByCutOff($date_from, $date_to);
            
            foreach($distributors as $member)
            {
                $array[] = $member['member_id'];
            }
            
            $rawData = Networks::getRPCMembersForPDF($member_id, $array);
            $reference = new ReferenceModel();
            
            foreach($rawData as $key => $level)
            {
                $levels = $level['level'];

                $downlines = Networks::getIPDUnilevelDownlines($level['member_id']);
                $unilevel['member_id'] = $member_id;
                $total =+ count($downlines);
                $unilevel['total'] = $total;
                $unilevel['level'] = $levels;                     
                $unilevel['downlines'] = $downlines;
                
                // additional: for display of payout rate
                $ipd_direct_count = Networks::getIPDDirectCount($member_id);
                $payout_rate = Transactions::getIpdUnilevelBonusByDirectEndorseCount($ipd_direct_count, $reference, $levels);
                $unilevel['payout_rate'] = $payout_rate;
                
                $unilevel_downlines[] = $unilevel;

            }

            $html2pdf = Yii::app()->ePdf->HTML2PDF();
            $html2pdf->WriteHTML($this->renderPartial('_ipdunilevelreport', array(
                    'payee'=>$payee,
                    'endorser'=>$endorser,
                    'downlines'=>$unilevel_downlines,
                    'cutoff'=>$cutoff,
                    'payout'=>$payout,
                ), true
             ));
            $html2pdf->Output('IPD_Unilevel_' . $payee_name . '_' . date('Y-m-d') . '.pdf', 'D'); 
        }
    }
    public function actionPdfBonus()
    {
        $model = new Bonus();
        $reference = new ReferenceModel();
        
        $html2pdf = Yii::app()->ePdf->HTML2PDF();
        
        if(isset($_GET["id"]))
        {
            $member_id = $_GET["member_id"];
            $member_name = $_GET["member_name"];
            $date_joined = $_GET["date_joined"];
            $total_amount = 25000;
            
            $total['total_amount'] = $total_amount;
            
            //Get Payee Details
            $payee = $model->getPayeeDetails($member_id);
                
            $tax_withheld = $reference->get_variable_value('TAX_WITHHELD');
            $total_tax = $total_amount * ($tax_withheld/100);
            
            $total['tax_amount'] = $total_tax;
            $total['net_amount'] = $total_amount - $total_tax;
            
            //Get downlines
            $direct_downlines = $model->getLoanDirectEndorsementDownlines($member_id, $date_joined);
        }
     
        $html2pdf->WriteHTML($this->renderPartial('_bonusreport', array(
                'member_name'=>$member_name,
                'payee'=>$payee,
                'total'=>$total,
                'direct_downlines'=>$direct_downlines,
            ), true
         ));
        
        $html2pdf->Output('Bonus' . $member_name . '_' . date('Y-m-d') . '.pdf', 'D'); 
        Yii::app()->end();
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
    
    public function actionIpdPdfDirect()
    {
        if(isset($_GET['id']) && isset($_GET['cutoff_id']))
        {
            $endorser_id = $_GET["id"];
            $cutoff_id = $_GET["cutoff_id"];
            
            $member = new MembersModel();            
            $model = new IpdDirectEndorsement();
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
            $html2pdf->WriteHTML($this->renderPartial('_ipddirectendorsereport', array(
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
    
    public function actionPdfIpdRetention()
    {
        if(isset($_GET['id']))
        {        
            $member_id = $_GET["member_id"];
            $account_type_id = $_GET["account_type_id"];
            $total_retention = $_GET["total_retention"];
            
            $member = new MembersModel();            
            $model = new IpdRetention();
            $reference = new ReferenceModel();
            
            //tax info
            $tax_withheld = $reference->get_variable_value('TAX_WITHHELD');
            $total_tax = $total_retention * ($tax_withheld/100);
            $payout['tax_amount'] = $total_tax;
            $payout['net_amount'] = $total_retention - $total_tax;
            
            //Payee Information
            $payee = $member->selectMemberDetails($member_id);
            if ($account_type_id == 5)
            {
                $payee_endorser_id = $payee['ipd_endorser_id'];
            }
            else if ($account_type_id == 3)
            {
                $payee_endorser_id = $payee['endorser_id'];
            }
            $payee_name = $payee['last_name'] . '_' . $payee['first_name'];
            
            //Endorser Information
            $endorser = $member->selectMemberDetails($payee_endorser_id);
            
            //Get all member purchases per cutoff
            $member_id_rp = $model->getMemberRepeatPurchaseByCutoff();
            
            //Get downlines
            $rawdata = Networks::getRPCMembersForPDF($member_id, $member_id_rp);
            
            if (count($rawdata) > 0)
            {
                foreach($rawdata as $mids)
                {
                    $member_ids2[] = $mids['member_id'];
                }

                $member_ids = implode(",", $member_ids2);
                
                //RP Own RP Commission Details
                $comm_details_own_arr = $model->getCommissionDetails($member_id, $member_id);
                
                foreach ($comm_details_own_arr as $cdo)
                {
                    $own_purchase_comm_details_table_arr['member_name'] = $cdo['member_name'];
                    $own_purchase_comm_details_table_arr['account_type'] = AdmintransactionsController::getMemberType($cdo['account_type_id']);
                    $own_purchase_comm_details_table_arr['date_purchased'] = $cdo['date_purchased'];
                    $own_purchase_comm_details_table_arr['product_name'] = $cdo['product_name'];
                    $own_purchase_comm_details_table_arr['quantity'] = $cdo['quantity'];
                    $own_purchase_comm_details_table_arr['total'] = $cdo['total'];
                    $own_purchase_comm_details_table_arr['savings'] = ($cdo['ipd_retention_rate'] / 100) * $cdo['total'];
                    $own_purchase_comm_details_table_arr['ipd_retention_rate'] = $cdo['ipd_retention_rate'];
                    $own_purchase_comm_details_table_arr['grand_total'] += $cdo['total'];
                    $own_purchase_comm_details_table_arr['total_savings'] += $own_purchase_comm_details_table_arr['savings'];

                    $own_purchase_comm_details_table[] = $own_purchase_comm_details_table_arr;
                }
                
                //Get Downlines purchase details
                $comm_details_downlines_arr = $model->getCommissionDetails($member_ids, $member_id);
                
                //Loop to get direct IPD
                foreach ($comm_details_downlines_arr as $cdd)
                {
                    //check if payee is IPD
                    if ($account_type_id == 5)
                    {
                        //check if downline is IPD and direct endorse
                        if ($cdd['account_type_id'] == 5 && $cdd['ipd_endorser_id'] == $member_id)
                        {
                            $ipd_direct_comm_table_arr['member_name'] = $cdd['member_name'];
                            $ipd_direct_comm_table_arr['account_type'] = AdmintransactionsController::getMemberType($cdd['account_type_id']);
                            $ipd_direct_comm_table_arr['date_purchased'] = $cdd['date_purchased'];
                            $ipd_direct_comm_table_arr['product_name'] = $cdd['product_name'];
                            $ipd_direct_comm_table_arr['quantity'] = $cdd['quantity'];
                            $ipd_direct_comm_table_arr['total'] = $cdd['total'];
                            $ipd_direct_comm_table_arr['savings'] = ($cdd['comm_ipd_rate'] / 100) * $cdd['total'];
                            $ipd_direct_comm_table_arr['comm_ipd_rate'] = $cdd['comm_ipd_rate'];
                            $ipd_direct_comm_table_arr['grand_total'] += $cdd['total'];
                            $ipd_direct_comm_table_arr['total_savings'] += $ipd_direct_comm_table_arr['savings'];

                            $ipd_direct_comm_table[] = $ipd_direct_comm_table_arr;
                        }
                        else
                        {
                            $indirect_endorse_table_arr['member_name'] = $cdd['member_name'];
                            $indirect_endorse_table_arr['account_type'] = AdmintransactionsController::getMemberType($cdd['account_type_id']);
                            $indirect_endorse_table_arr['date_purchased'] = $cdd['date_purchased'];
                            $indirect_endorse_table_arr['product_name'] = $cdd['product_name'];
                            $indirect_endorse_table_arr['quantity'] = $cdd['quantity'];
                            $indirect_endorse_table_arr['total'] = $cdd['total'];
                            $indirect_endorse_table_arr['savings'] = ($cdd['rpc_rate'] / 100) * $cdd['total'];
                            $indirect_endorse_table_arr['rpc_rate'] = $cdd['rpc_rate'];
                            $indirect_endorse_table_arr['grand_total'] += $cdd['total'];
                            $indirect_endorse_table_arr['total_savings'] += $indirect_endorse_table_arr['savings'];

                            $indirect_endorse_table[] = $indirect_endorse_table_arr;
                        }
                    }
                    else if ($account_type_id == 3)
                    {
                        //check if downline is IPD  and direct endorse
                        if ($cdd['account_type_id'] == 5 && $cdd['ipd_endorser_id'] == $member_id)
                        {
                            $ipd_direct_comm_table_arr['member_name'] = $cdd['member_name'];
                            $ipd_direct_comm_table_arr['account_type'] = AdmintransactionsController::getMemberType($cdd['account_type_id']);
                            $ipd_direct_comm_table_arr['date_purchased'] = $cdd['date_purchased'];
                            $ipd_direct_comm_table_arr['product_name'] = $cdd['product_name'];
                            $ipd_direct_comm_table_arr['quantity'] = $cdd['quantity'];
                            $ipd_direct_comm_table_arr['total'] = $cdd['total'];
                            $ipd_direct_comm_table_arr['savings'] = ($cdd['comm_ipd_rate'] / 100) * $cdd['total'];
                            $ipd_direct_comm_table_arr['comm_ipd_rate'] = $cdd['comm_ipd_rate'];
                            $ipd_direct_comm_table_arr['grand_total'] += $cdd['total'];
                            $ipd_direct_comm_table_arr['total_savings'] += $ipd_direct_comm_table_arr['savings'];

                            $ipd_direct_comm_table[] = $ipd_direct_comm_table_arr;
                        }
                        else if ($cdd['account_type_id'] == 3 && $cdd['endorser_id'] == $member_id)
                        {
                            $ibo_direct_comm_table_arr['member_name'] = $cdd['member_name'];
                            $ibo_direct_comm_table_arr['account_type'] = AdmintransactionsController::getMemberType($cdd['account_type_id']);
                            $ibo_direct_comm_table_arr['date_purchased'] = $cdd['date_purchased'];
                            $ibo_direct_comm_table_arr['product_name'] = $cdd['product_name'];
                            $ibo_direct_comm_table_arr['quantity'] = $cdd['quantity'];
                            $ibo_direct_comm_table_arr['total'] = $cdd['total'];
                            $ibo_direct_comm_table_arr['savings'] = ($cdd['comm_ibo_rate'] / 100) * $cdd['total'];
                            $ibo_direct_comm_table_arr['comm_ibo_rate'] = $cdd['comm_ibo_rate'];
                            $ibo_direct_comm_table_arr['grand_total'] += $cdd['total'];
                            $ibo_direct_comm_table_arr['total_savings'] += $ibo_direct_comm_table_arr['savings'];

                            $ibo_direct_comm_table_table[] = $ibo_direct_comm_table_arr;
                        }
                        else
                        {
                            $indirect_endorse_table_arr['member_name'] = $cdd['member_name'];
                            $indirect_endorse_table_arr['account_type'] = AdmintransactionsController::getMemberType($cdd['account_type_id']);
                            $indirect_endorse_table_arr['date_purchased'] = $cdd['date_purchased'];
                            $indirect_endorse_table_arr['product_name'] = $cdd['product_name'];
                            $indirect_endorse_table_arr['quantity'] = $cdd['quantity'];
                            $indirect_endorse_table_arr['total'] = $cdd['total'];
                            $indirect_endorse_table_arr['savings'] = ($cdd['rpc_rate'] / 100) * $cdd['total'];
                            $indirect_endorse_table_arr['rpc_rate'] = $cdd['rpc_rate'];
                            $indirect_endorse_table_arr['grand_total'] += $cdd['total'];
                            $indirect_endorse_table_arr['total_savings'] += $indirect_endorse_table_arr['savings'];

                            $indirect_endorse_table[] = $indirect_endorse_table_arr;
                        }
                    }
                }
            }
            else
            {
                //RP Own RP Commission Details
                $comm_details_own_arr = $model->getCommissionDetails($member_id, $member_id);
                
                foreach ($comm_details_own_arr as $cdo)
                {
                    $own_purchase_comm_details_table_arr['member_name'] = $cdo['member_name'];
                    $own_purchase_comm_details_table_arr['account_type'] = AdmintransactionsController::getMemberType($cdo['account_type_id']);
                    $own_purchase_comm_details_table_arr['date_purchased'] = $cdo['date_purchased'];
                    $own_purchase_comm_details_table_arr['quantity'] = $cdo['quantity'];
                    $own_purchase_comm_details_table_arr['total'] = $cdo['total'];
                    $own_purchase_comm_details_table_arr['savings'] = ($cdo['ipd_retention_rate'] / 100) * $cdo['total'];
                    $own_purchase_comm_details_table_arr['ipd_retention_rate'] = $cdo['ipd_retention_rate'];
                    $own_purchase_comm_details_table_arr['grand_total'] += $cdo['total'];
                    $own_purchase_comm_details_table_arr['total_savings'] += $own_purchase_comm_details_table_arr['savings'];

                    $own_purchase_comm_details_table[] = $own_purchase_comm_details_table_arr;
                }
            }
            
            
            $html2pdf = Yii::app()->ePdf->HTML2PDF();     
            $html2pdf->WriteHTML($this->renderPartial('_ipdretentionreport', array(
                    'payee'=>$payee,
                    'endorser'=>$endorser,
                    'total_retention'=>$total_retention,
                    'payout'=>$payout,
                    'own_purchase_comm_details_table'=>$own_purchase_comm_details_table,
                    'ipd_direct_comm_table'=>$ipd_direct_comm_table,
                    'ibo_direct_comm_table_table'=>$ibo_direct_comm_table_table,
                    'indirect_endorse_table'=>$indirect_endorse_table,
                ), true
             ));
            $html2pdf->Output('Distributor_Retention_Money' . $payee_name . '_' . date('Y-m-d') . '.pdf', 'D'); 
            Yii::app()->end();
        }
    }
    
    public function actionPdfIboRpCommission()
    {
        if(isset($_GET['id']))
        {
            $member_id = $_GET["member_id"];
            $account_type_id = $_GET["account_type_id"];
            $cutoff_id = $_GET["cutoff_id"];
            $commission_amount = $_GET["commission_amount"];
            
            $member = new MembersModel();            
            $model = new IboRpCommission();
            $reference = new ReferenceModel();
            
            $cutoff_ibo_rp_comm_arr = ReferenceModel::get_cutoff_by_id($cutoff_id);
            
            $last_cutoff_date = $cutoff_ibo_rp_comm_arr['last_cutoff_date'];
            $next_cutoff_date = $cutoff_ibo_rp_comm_arr['next_cutoff_date'];
            
            //tax info
            $tax_withheld = $reference->get_variable_value('TAX_WITHHELD');
            $total_tax = $commission_amount * ($tax_withheld/100);
            $payout['tax_amount'] = $total_tax;
            $payout['net_amount'] = $commission_amount - $total_tax;
            
            //Payee Information
            $payee = $member->selectMemberDetails($member_id);
            if ($account_type_id == 5)
            {
                $payee_endorser_id = $payee['ipd_endorser_id'];
            }
            else if ($account_type_id == 3)
            {
                $payee_endorser_id = $payee['endorser_id'];
            }
            $payee_name = $payee['last_name'] . '_' . $payee['first_name'];
            
            //Endorser Information
            $endorser = $member->selectMemberDetails($payee_endorser_id);
            
            //Get all member purchases per cutoff
            $member_id_rp = $model->getMemberRepeatPurchaseByCutoff($last_cutoff_date, $next_cutoff_date);
            
            //Get downlines
            $rawdata = Networks::getRPCMembersForPDF($member_id, $member_id_rp);
            
            foreach($rawdata as $mids)
            {
                $member_ids2[] = $mids['member_id'];
            }
            
            $member_ids = implode(",", $member_ids2);
            
            //Get Downlines purchase details
            $comm_details_downlines_arr = $model->getCommissionDetails($member_ids, $member_id, $last_cutoff_date, $next_cutoff_date);
            
            //Loop to get direct IPD
            foreach ($comm_details_downlines_arr as $cdd)
            {
                //check if payee is IPD
                if ($account_type_id == 5)
                {
                    //check if downline is IPD and direct endorse
                    if ($cdd['account_type_id'] == 5 && $cdd['ipd_endorser_id'] == $member_id)
                    {
                        $ipd_direct_comm_table_arr['member_name'] = $cdd['member_name'];
                        $ipd_direct_comm_table_arr['account_type'] = AdmintransactionsController::getMemberType($cdd['account_type_id']);
                        $ipd_direct_comm_table_arr['date_purchased'] = $cdd['date_purchased'];
                        $ipd_direct_comm_table_arr['product_name'] = $cdd['product_name'];
                        $ipd_direct_comm_table_arr['quantity'] = $cdd['quantity'];
                        $ipd_direct_comm_table_arr['total'] = $cdd['total'];
                        $ipd_direct_comm_table_arr['savings'] = ($cdd['comm_ipd_rate'] / 100) * $cdd['total'];
                        $ipd_direct_comm_table_arr['comm_ipd_rate'] = $cdd['comm_ipd_rate'];
                        $ipd_direct_comm_table_arr['grand_total'] += $cdd['total'];
                        $ipd_direct_comm_table_arr['total_savings'] += $ipd_direct_comm_table_arr['savings'];
                        
                        $ipd_direct_comm_table[] = $ipd_direct_comm_table_arr;
                    }
                    else
                    {
                        $indirect_endorse_table_arr['member_name'] = $cdd['member_name'];
                        $indirect_endorse_table_arr['account_type'] = AdmintransactionsController::getMemberType($cdd['account_type_id']);
                        $indirect_endorse_table_arr['date_purchased'] = $cdd['date_purchased'];
                        $indirect_endorse_table_arr['product_name'] = $cdd['product_name'];
                        $indirect_endorse_table_arr['quantity'] = $cdd['quantity'];
                        $indirect_endorse_table_arr['total'] = $cdd['total'];
                        $indirect_endorse_table_arr['savings'] = ($cdd['rpc_rate'] / 100) * $cdd['total'];
                        $indirect_endorse_table_arr['rpc_rate'] = $cdd['rpc_rate'];
                        $indirect_endorse_table_arr['grand_total'] += $cdd['total'];
                        $indirect_endorse_table_arr['total_savings'] += $indirect_endorse_table_arr['savings'];
                        
                        $indirect_endorse_table[] = $indirect_endorse_table_arr;
                    }
                }
                else if ($account_type_id == 3)
                {
                    //check if downline is IPD  and direct endorse
                    if ($cdd['account_type_id'] == 5 && $cdd['ipd_endorser_id'] == $member_id)
                    {
                        $ipd_direct_comm_table_arr['member_name'] = $cdd['member_name'];
                        $ipd_direct_comm_table_arr['account_type'] = AdmintransactionsController::getMemberType($cdd['account_type_id']);
                        $ipd_direct_comm_table_arr['date_purchased'] = $cdd['date_purchased'];
                        $ipd_direct_comm_table_arr['product_name'] = $cdd['product_name'];
                        $ipd_direct_comm_table_arr['quantity'] = $cdd['quantity'];
                        $ipd_direct_comm_table_arr['total'] = $cdd['total'];
                        $ipd_direct_comm_table_arr['savings'] = ($cdd['comm_ipd_rate'] / 100) * $cdd['total'];
                        $ipd_direct_comm_table_arr['comm_ipd_rate'] = $cdd['comm_ipd_rate'];
                        $ipd_direct_comm_table_arr['grand_total'] += $cdd['total'];
                        $ipd_direct_comm_table_arr['total_savings'] += $ipd_direct_comm_table_arr['savings'];
                        
                        $ipd_direct_comm_table[] = $ipd_direct_comm_table_arr;
                    }
                    else if ($cdd['account_type_id'] == 3 && $cdd['endorser_id'] == $member_id)
                    {
                        $ibo_direct_comm_table_arr['member_name'] = $cdd['member_name'];
                        $ibo_direct_comm_table_arr['account_type'] = AdmintransactionsController::getMemberType($cdd['account_type_id']);
                        $ibo_direct_comm_table_arr['date_purchased'] = $cdd['date_purchased'];
                        $ibo_direct_comm_table_arr['product_name'] = $cdd['product_name'];
                        $ibo_direct_comm_table_arr['quantity'] = $cdd['quantity'];
                        $ibo_direct_comm_table_arr['total'] = $cdd['total'];
                        $ibo_direct_comm_table_arr['savings'] = ($cdd['comm_ibo_rate'] / 100) * $cdd['total'];
                        $ibo_direct_comm_table_arr['comm_ibo_rate'] = $cdd['comm_ibo_rate'];
                        $ibo_direct_comm_table_arr['grand_total'] += $cdd['total'];
                        $ibo_direct_comm_table_arr['total_savings'] += $ibo_direct_comm_table_arr['savings'];
                        
                        $ibo_direct_comm_table_table[] = $ibo_direct_comm_table_arr;
                    }
                    else
                    {
                        $indirect_endorse_table_arr['member_name'] = $cdd['member_name'];
                        $indirect_endorse_table_arr['account_type'] = AdmintransactionsController::getMemberType($cdd['account_type_id']);
                        $indirect_endorse_table_arr['date_purchased'] = $cdd['date_purchased'];
                        $indirect_endorse_table_arr['product_name'] = $cdd['product_name'];
                        $indirect_endorse_table_arr['quantity'] = $cdd['quantity'];
                        $indirect_endorse_table_arr['total'] = $cdd['total'];
                        $indirect_endorse_table_arr['savings'] = ($cdd['rpc_rate'] / 100) * $cdd['total'];
                        $indirect_endorse_table_arr['rpc_rate'] = $cdd['rpc_rate'];
                        $indirect_endorse_table_arr['grand_total'] += $cdd['total'];
                        $indirect_endorse_table_arr['total_savings'] += $indirect_endorse_table_arr['savings'];
                        
                        $indirect_endorse_table[] = $indirect_endorse_table_arr;
                    }
                }
            }
            
            $html2pdf = Yii::app()->ePdf->HTML2PDF();           
            $html2pdf->WriteHTML($this->renderPartial('_iborpcommissionreport', array(
                    'payee'=>$payee,
                    'commission_amount'=>$commission_amount,
                    'payout'=>$payout,
                    'endorser'=>$endorser,
                    'ipd_direct_comm_table'=>$ipd_direct_comm_table,
                    'ibo_direct_comm_table_table'=>$ibo_direct_comm_table_table,
                    'indirect_endorse_table'=>$indirect_endorse_table,
                ), true
             ));
            $html2pdf->Output('Member_Repeat_Purchase_commission_' . $payee_name . '_' . date('Y-m-d') . '.pdf', 'D'); 
            Yii::app()->end();
        }
    }
    
    public function actionPdfIpdRpCommission()
    {
        if(isset($_GET['id']))
        {        
            $member_id = $_GET["member_id"];
            $account_type_id = $_GET["account_type_id"];
            $cutoff_id = $_GET["cutoff_id"];
            $commission_amount = $_GET["commission_amount"];
            
            $member = new MembersModel();            
            $model = new IpdRpCommission();
            $reference = new ReferenceModel();
            
            $cutoff_ipd_rp_comm_arr = ReferenceModel::get_cutoff_by_id($cutoff_id);
            
            $last_cutoff_date = $cutoff_ipd_rp_comm_arr['last_cutoff_date'];
            $next_cutoff_date = $cutoff_ipd_rp_comm_arr['next_cutoff_date'];
            
            //tax info
            $tax_withheld = $reference->get_variable_value('TAX_WITHHELD');
            $total_tax = $commission_amount * ($tax_withheld/100);
            $payout['tax_amount'] = $total_tax;
            $payout['net_amount'] = $commission_amount - $total_tax;
            
            //Payee Information
            $payee = $member->selectMemberDetails($member_id);
            if ($account_type_id == 5)
            {
                $payee_endorser_id = $payee['ipd_endorser_id'];
            }
            else if ($account_type_id == 3)
            {
                $payee_endorser_id = $payee['endorser_id'];
            }
            $payee_name = $payee['last_name'] . '_' . $payee['first_name'];
            
            //Endorser Information
            $endorser = $member->selectMemberDetails($payee_endorser_id);
            
            //Get all member purchases per cutoff
            $member_id_rp = $model->getMemberRepeatPurchaseByCutoff($last_cutoff_date, $next_cutoff_date);
            
            //Get downlines
            $rawdata = Networks::getRPCMembersForPDF($member_id, $member_id_rp);
            
            foreach($rawdata as $mids)
            {
                $member_ids2[] = $mids['member_id'];
            }
            
            $member_ids = implode(",", $member_ids2);
            
            //Get Downlines purchase details
            $comm_details_downlines_arr = $model->getCommissionDetails($member_ids, $member_id, $last_cutoff_date, $next_cutoff_date);
            
            //Loop to get direct IPD
            foreach ($comm_details_downlines_arr as $cdd)
            {
                //check if payee is IPD
                if ($account_type_id == 5)
                {
                    //check if downline is IPD and direct endorse
                    if ($cdd['account_type_id'] == 5 && $cdd['ipd_endorser_id'] == $member_id)
                    {
                        $ipd_direct_comm_table_arr['member_name'] = $cdd['member_name'];
                        $ipd_direct_comm_table_arr['account_type'] = AdmintransactionsController::getMemberType($cdd['account_type_id']);
                        $ipd_direct_comm_table_arr['date_purchased'] = $cdd['date_purchased'];
                        $ipd_direct_comm_table_arr['product_name'] = $cdd['product_name'];
                        $ipd_direct_comm_table_arr['quantity'] = $cdd['quantity'];
                        $ipd_direct_comm_table_arr['total'] = $cdd['total'];
                        $ipd_direct_comm_table_arr['savings'] = ($cdd['comm_ipd_rate'] / 100) * $cdd['total'];
                        $ipd_direct_comm_table_arr['comm_ipd_rate'] = $cdd['comm_ipd_rate'];
                        $ipd_direct_comm_table_arr['grand_total'] += $cdd['total'];
                        $ipd_direct_comm_table_arr['total_savings'] += $ipd_direct_comm_table_arr['savings'];
                        
                        $ipd_direct_comm_table[] = $ipd_direct_comm_table_arr;
                    }
                    else
                    {
                        $indirect_endorse_table_arr['member_name'] = $cdd['member_name'];
                        $indirect_endorse_table_arr['account_type'] = AdmintransactionsController::getMemberType($cdd['account_type_id']);
                        $indirect_endorse_table_arr['date_purchased'] = $cdd['date_purchased'];
                        $indirect_endorse_table_arr['product_name'] = $cdd['product_name'];
                        $indirect_endorse_table_arr['quantity'] = $cdd['quantity'];
                        $indirect_endorse_table_arr['total'] = $cdd['total'];
                        $indirect_endorse_table_arr['savings'] = ($cdd['rpc_rate'] / 100) * $cdd['total'];
                        $indirect_endorse_table_arr['rpc_rate'] = $cdd['rpc_rate'];
                        $indirect_endorse_table_arr['grand_total'] += $cdd['total'];
                        $indirect_endorse_table_arr['total_savings'] += $indirect_endorse_table_arr['savings'];
                        
                        $indirect_endorse_table[] = $indirect_endorse_table_arr;
                    }
                }
                else if ($account_type_id == 3)
                {
                    //check if downline is IPD  and direct endorse
                    if ($cdd['account_type_id'] == 5 && $cdd['ipd_endorser_id'] == $member_id)
                    {
                        $ipd_direct_comm_table_arr['member_name'] = $cdd['member_name'];
                        $ipd_direct_comm_table_arr['account_type'] = AdmintransactionsController::getMemberType($cdd['account_type_id']);
                        $ipd_direct_comm_table_arr['date_purchased'] = $cdd['date_purchased'];
                        $ipd_direct_comm_table_arr['product_name'] = $cdd['product_name'];
                        $ipd_direct_comm_table_arr['quantity'] = $cdd['quantity'];
                        $ipd_direct_comm_table_arr['total'] = $cdd['total'];
                        $ipd_direct_comm_table_arr['savings'] = ($cdd['comm_ipd_rate'] / 100) * $cdd['total'];
                        $ipd_direct_comm_table_arr['comm_ipd_rate'] = $cdd['comm_ipd_rate'];
                        $ipd_direct_comm_table_arr['grand_total'] += $cdd['total'];
                        $ipd_direct_comm_table_arr['total_savings'] += $ipd_direct_comm_table_arr['savings'];
                        
                        $ipd_direct_comm_table[] = $ipd_direct_comm_table_arr;
                    }
                    else if ($cdd['account_type_id'] == 3 && $cdd['endorser_id'] == $member_id)
                    {
                        $ibo_direct_comm_table_arr['member_name'] = $cdd['member_name'];
                        $ibo_direct_comm_table_arr['account_type'] = AdmintransactionsController::getMemberType($cdd['account_type_id']);
                        $ibo_direct_comm_table_arr['date_purchased'] = $cdd['date_purchased'];
                        $ibo_direct_comm_table_arr['product_name'] = $cdd['product_name'];
                        $ibo_direct_comm_table_arr['quantity'] = $cdd['quantity'];
                        $ibo_direct_comm_table_arr['total'] = $cdd['total'];
                        $ibo_direct_comm_table_arr['savings'] = ($cdd['comm_ibo_rate'] / 100) * $cdd['total'];
                        $ibo_direct_comm_table_arr['comm_ibo_rate'] = $cdd['comm_ibo_rate'];
                        $ibo_direct_comm_table_arr['grand_total'] += $cdd['total'];
                        $ibo_direct_comm_table_arr['total_savings'] += $ibo_direct_comm_table_arr['savings'];
                        
                        $ibo_direct_comm_table_table[] = $ibo_direct_comm_table_arr;
                    }
                    else
                    {
                        $indirect_endorse_table_arr['member_name'] = $cdd['member_name'];
                        $indirect_endorse_table_arr['account_type'] = AdmintransactionsController::getMemberType($cdd['account_type_id']);
                        $indirect_endorse_table_arr['date_purchased'] = $cdd['date_purchased'];
                        $indirect_endorse_table_arr['product_name'] = $cdd['product_name'];
                        $indirect_endorse_table_arr['quantity'] = $cdd['quantity'];
                        $indirect_endorse_table_arr['total'] = $cdd['total'];
                        $indirect_endorse_table_arr['savings'] = ($cdd['rpc_rate'] / 100) * $cdd['total'];
                        $indirect_endorse_table_arr['rpc_rate'] = $cdd['rpc_rate'];
                        $indirect_endorse_table_arr['grand_total'] += $cdd['total'];
                        $indirect_endorse_table_arr['total_savings'] += $indirect_endorse_table_arr['savings'];
                        
                        $indirect_endorse_table[] = $indirect_endorse_table_arr;
                    }
                }
            }
            
            $html2pdf = Yii::app()->ePdf->HTML2PDF();          
            $html2pdf->WriteHTML($this->renderPartial('_ipdrpcommissionreport', array(
                    'payee'=>$payee,
                    'commission_amount'=>$commission_amount,
                    'payout'=>$payout,
                    'endorser'=>$endorser,
                    'ipd_direct_comm_table'=>$ipd_direct_comm_table,
                    'ibo_direct_comm_table_table'=>$ibo_direct_comm_table_table,
                    'indirect_endorse_table'=>$indirect_endorse_table,
                ), true
             ));
            $html2pdf->Output('Distributor_Repeat_Purchase_commission_' . $payee_name . '_' . date('Y-m-d') . '.pdf', 'D'); 
            Yii::app()->end();
        }
    }
    
    public function actionPdfLoanSummary()
    {
        $model = new Loan();
        $model->status = $_GET['status'];
        $model->date_from2 = $_GET['date_from2'];
        $model->date_to = $_GET['date_to'];
        $loan_details = $model->getLoanApplications();
        $total = $model->getTotalLoans();
        
        $html2pdf = Yii::app()->ePdf->HTML2PDF();            
        $html2pdf->WriteHTML($this->renderPartial('_loansummaryreport', array(
                'loan_details'=>$loan_details,
                'total'=>$total,
            ), true
         ));
        $html2pdf->Output('Loan_Summary_' . date('Y-m-d') . '.pdf', 'D'); 
        Yii::app()->end();
    }
    
    public function actionPdfGocSummary()
    {
        $model = new GroupOverrideCommission();
        
        $model->cutoff_id = $_GET['cutoff_id'];
        $goc_details = $model->getComissions();
        $total_goc_arr = $model->getCommissionsTotal();
        $total_goc_ibo = $total_goc_arr['total_ibo'];
        $total_goc = $total_goc_arr['total_amount'];
        $cutoff_arr = ReferenceModel::get_cutoff_by_id($_GET['cutoff_id']);
        $cutoff = $cutoff_arr['cutoff_date'];
        
        $html2pdf = Yii::app()->ePdf->HTML2PDF();            
        $html2pdf->WriteHTML($this->renderPartial('_gocsummaryreport', array(
                'goc_details'=>$goc_details,
                'total_goc'=>$total_goc,
                'total_goc_ibo'=>$total_goc_ibo,
                'cutoff'=>$cutoff,
            ), true
         ));
        $html2pdf->Output('GOC_Summary_' . date('Y-m-d') . '.pdf', 'D'); 
        Yii::app()->end();
    }
    
    public function actionPdfUnilevelSummary()
    {
        $model = new Unilevel();
        
        $model->cutoff_id = $_GET['cutoff_id'];
        $unilvl_details = $model->getUnilevel();
        $total_unilvl_arr = $model->getPayoutTotal();
        $total_unilvl_ibo = $total_unilvl_arr['total_ibo'];
        $total_unilvl = $total_unilvl_arr['total_amount'];
        $cutoff_unilvl_arr = ReferenceModel::get_cutoff_by_id($_GET['cutoff_id']);
        $cutoff_unilvl = $cutoff_unilvl_arr['cutoff_date'];
        
        $html2pdf = Yii::app()->ePdf->HTML2PDF();            
        $html2pdf->WriteHTML($this->renderPartial('_unilevelsummaryreport', array(
                'unilvl_details'=>$unilvl_details,
                'total_unilvl'=>$total_unilvl,
                'total_unilvl_ibo'=>$total_unilvl_ibo,
                'cutoff_unilvl'=>$cutoff_unilvl,
            ), true
         ));
        $html2pdf->Output('Unilevel_Summary_' . date('Y-m-d') . '.pdf', 'D'); 
        Yii::app()->end();
    }
    
    public function actionPdfIpdUnilevelSummary()
    {
        $model = new IpdUnilevel();
        $model->cutoff_id = $_GET['cutoff_id'];
        $unilvl_details = $model->getUnilevel();
        $total_unilvl_arr = $model->getPayoutTotal();
        $total_unilvl_ibo = $total_unilvl_arr['total_ipd'];
        $total_unilvl = $total_unilvl_arr['total_amount'];
        $cutoff_unilvl_arr = ReferenceModel::get_cutoff_by_id($_GET['cutoff_id']);
        $cutoff_unilvl = $cutoff_unilvl_arr['cutoff_date'];
        $html2pdf = Yii::app()->ePdf->HTML2PDF();            
        $html2pdf->WriteHTML($this->renderPartial('_ipdunilevelsummaryreport', array(
                'unilvl_details'=>$unilvl_details,
                'total_unilvl'=>$total_unilvl,
                'total_unilvl_ibo'=>$total_unilvl_ibo,
                'cutoff_unilvl'=>$cutoff_unilvl,
            ), true
         ));
        $html2pdf->Output('IPD_Unilevel_Summary_' . date('Y-m-d') . '.pdf', 'D'); 
        Yii::app()->end();
    }
    public function actionPdfDirectSummary()
    {
        $model = new DirectEndorsement();
        
        $model->cutoff_id = $_GET['cutoff_id'];
        $direct_details = $model->getDirectEndorsement();
        $total_direct_arr = $model->getPayoutTotal();
        $total_direct_ibo = $total_direct_arr['total_ibo'];
        $total_direct = $total_direct_arr['total_amount'];
        $cutoff_direct_arr = ReferenceModel::get_cutoff_by_id($_GET['cutoff_id']);
        $cutoff_direct = $cutoff_direct_arr['cutoff_date'];
        
        $html2pdf = Yii::app()->ePdf->HTML2PDF();            
        $html2pdf->WriteHTML($this->renderPartial('_directsummaryreport', array(
                'direct_details'=>$direct_details,
                'total_direct'=>$total_direct,
                'total_direct_ibo'=>$total_direct_ibo,
                'cutoff_direct'=>$cutoff_direct,
            ), true
         ));
        $html2pdf->Output('Direct_Endorsement_Summary_' . date('Y-m-d') . '.pdf', 'D'); 
        Yii::app()->end();
    }
    public function actionPdfIpdDirectSummary()
    {
        $model = new IpdDirectEndorsement();
        $model->cutoff_id = $_GET['cutoff_id'];
        $direct_details = $model->getDirectEndorsement();
        $total_direct_arr = $model->getPayoutTotal();
        $total_direct_ibo = $total_direct_arr['total_ipd'];
        $total_direct = $total_direct_arr['total_amount'];
        $cutoff_direct_arr = ReferenceModel::get_cutoff_by_id($_GET['cutoff_id']);
        $cutoff_direct = $cutoff_direct_arr['cutoff_date'];
        $html2pdf = Yii::app()->ePdf->HTML2PDF();            
        $html2pdf->WriteHTML($this->renderPartial('_ipddirectsummaryreport', array(
                'direct_details'=>$direct_details,
                'total_direct'=>$total_direct,
                'total_direct_ibo'=>$total_direct_ibo,
                'cutoff_direct'=>$cutoff_direct,
            ), true
         ));
        $html2pdf->Output('Distributor_Direct_Endorsement_Summary_' . date('Y-m-d') . '.pdf', 'D'); 
        Yii::app()->end();
    }
    public function actionGetTransaction()
    {
        if(Yii::app()->request->isAjaxRequest)
        {            
            $details[] = array('direct_endorsement_id'=>$_GET['id'],'endorser_id'=>$_GET['endorser_id'],'cutoff_id'=>$_GET['cutoff_id']);
            echo CJSON::encode($details);
        }
    }
    
    public function actionIboPdfRpCommissionSummary()
    {
        $model = new IboRpCommission();
        $model->cutoff_id = $_GET['cutoff_id'];
        
        $direct_details = $model->getIboRpCommission();
        $total = $model->getPayoutTotal();
        
        $cutoff_rpcomm_arr = ReferenceModel::get_cutoff_by_id($_GET['cutoff_id']);
        $cutoff_rpcomm = $cutoff_rpcomm_arr['cutoff_date'];
        
        $html2pdf = Yii::app()->ePdf->HTML2PDF();            
        $html2pdf->WriteHTML($this->renderPartial('_iborpcommissionsummaryreport', array(
                'direct_details'=>$direct_details,
                'total'=>$total,
                'cutoff_rpcomm'=>$cutoff_rpcomm,
            ), true
         ));
        $html2pdf->Output('Member_Repeat_Purchase_Commission' . date('Y-m-d') . '.pdf', 'D'); 
        Yii::app()->end();
    }
    
    public function actionIpdPdfRetentionSummary()
    {
        $model = new IpdRetention();
        $member_id = Yii::app()->user->getId();
        
        $member_name_arr = $model->getMemberName($member_id);
        $member_name = $member_name_arr[0]['member_name'];
        
        $direct_details = $model->getIpdRetentionMoney($member_id);
        $total = $model->getPayoutTotal($member_id);
        
        $html2pdf = Yii::app()->ePdf->HTML2PDF();            
        $html2pdf->WriteHTML($this->renderPartial('_ipdretentionsummaryreport', array(
                'direct_details'=>$direct_details,
                'member_name'=>$member_name,
                'total'=>$total,
            ), true
         ));
        $html2pdf->Output('Distributor_Retention_Summary_' . date('Y-m-d') . '.pdf', 'D'); 
        Yii::app()->end();
    }
    
    public function actionIpdPdfRpCommissionSummary()
    {
        $model = new IpdRpCommission();
        $model->cutoff_id = $_GET['cutoff_id'];
        
        $direct_details = $model->getIpdRpCommission();
        $total = $model->getPayoutTotal();
        
        $cutoff_ipdrpcomm_arr = ReferenceModel::get_cutoff_by_id($_GET['cutoff_id']);
        $cutoff_ipdrpcomm = $cutoff_ipdrpcomm_arr['cutoff_date'];
        
        $html2pdf = Yii::app()->ePdf->HTML2PDF();            
        $html2pdf->WriteHTML($this->renderPartial('_ipdrpcommissionsummaryreport', array(
                'direct_details'=>$direct_details,
                'cutoff_ipdrpcomm'=>$cutoff_ipdrpcomm,
                'total'=>$total,
            ), true
         ));
        $html2pdf->Output('Distributor_Repeat_Purchase_Commission' . date('Y-m-d') . '.pdf', 'D'); 
        Yii::app()->end();
    }
    
    public function actionGetValues()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $model = new ReferenceModel();
            $rate = $model->get_payout_rate(TransactionTypes::UNILEVEL);
            
            $values[] = array(
                'unilevel_id'=>$_GET['id'],
                'member_id'=>$_GET['member_id'],
                'name'=>$_GET['name'],
                'amount'=>$_GET['amount'],
                'ibo_count'=>$_GET['ibo_count'],
                'cutoff_id'=>$_GET['cutoff_id'],
                'unilevel_rate'=>$rate);
            
            echo CJSON::encode($values);
        }
    }
    
    public function actionModifyUnilevel()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $model = new Unilevel();
            
            $model->unilevel_id = $_GET['unilevel_id'];
            $model->member_id = $_GET['member_id'];
            $model->amount = $_GET['amount'];
            $model->ibo_count = $_GET['ibo_count'];
            $model->cutoff_id = $_GET['cutoff_id'];
            
            $model->update_unilevel_discrepancies();
            
            if(!$model->hasErrors())
            {
                $result_code=0;
                $result_msg='Unilevel discrepanicy is successfully corrected';
            }
            else
            {
                $result_code=1;
                $result_msg=$model->getErrors();
            }
            echo CJSON::encode(array('result_code'=>$result_code,'result_msg'=>$result_msg));
        }
    }
    
    
}
?>
