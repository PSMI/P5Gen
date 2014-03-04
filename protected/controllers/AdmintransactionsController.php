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
                
                $model = new DirectEndorsement();
                $result = $model->updateDirectEndorsementStatus($direct_endorsement_id, $status, $userid);
                
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
        
        if (isset($_POST["calDateFrom"]) && $_POST["calDateTo"])
        {
            $dateFrom = $_POST["calDateFrom"];
            $dateTo = $_POST["calDateTo"];
//            Yii::app()->session['dateFromGoc'] = $dateFrom;
//            Yii::app()->session['dateToGoc'] = $dateTo;
            
            $rawData = $model->getComissions($dateFrom, $dateTo);
            
            $dataProvider = new CArrayDataProvider($rawData, array(
                                                    'keyField' => false,
                                                    'pagination' => false,
                                                    //'pageSize' => 10,
                                                //),
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
                                                    'pagination' => false,
//                                                    'pageSize' => 10,
//                                                ),
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
        
        if (isset($_POST["calDateFrom"]) && $_POST["calDateTo"])
        {
            $dateFrom = $_POST["calDateFrom"];
            $dateTo = $_POST["calDateTo"];
            
            $rawData = $model->getDirectEndorsement($dateFrom, $dateTo);
            
            $dataProvider = new CArrayDataProvider($rawData, array(
                                                    'keyField' => false,
                                                    'pagination' => false,
//                                                    'pageSize' => 10,
//                                                ),
                                    ));
            
            $this->render('directendorse', array('dataProvider' => $dataProvider));
        }
        else
        {
            $this->render('directendorse');
        }
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
    
    public function actionPdf()
    {
        /*$pdf = CTCPDF::c_getInstance();
        $pdf->c_commonReportFormat();
        $pdf->c_setHeader('Activation Codes');
        $pdf->SetFontSize(10);
        $pdf->c_generatePDF('Activation_Codes_' . date('Y-m-d') . '.pdf'); */
        
        if(isset($_GET["id"]))
        {
            $loan_id = $_GET["id"];
            $member_id = $_GET["member_id"];
            $loan_type_id = $_GET["loan_type_id"];
            $level_no = $_GET["level_no"];
            $member_name = $_GET["member_name"];
            $loan_amount = $_GET["loan_amount"];
            
            $model = new Loan();
            
            if ($loan_type_id == 1)
            {
                //direct 5
                //Get Payee Details
                $payee_details = $model->getPayeeDetails($member_id);

                $username = $payee_details[0]['username'];
                $date_joined = $payee_details[0]['date_created'];
                $email = $payee_details[0]['email'];
                $mobile_no = $payee_details[0]['mobile_no'];
                $telephone_no = $payee_details[0]['telephone_no'];
                $endorser_name = $payee_details[0]['endorser_name'];


                $content = "<table  align='center'><tr><td><h3>LOAN - DIRECT ENDORSEMENT</h3></td></tr></table>";

                $content .= "<br>";

                $content .= "<table style='width: 100%;'>";
                $content .= "<tr>";
                $content .= "<td align='left' style='font-weight:bold;'>Name of Payee: </td>";
                $content .= "<td>$member_name</td>";
                $content .= "</tr>";

                $content .= "<tr>";
                $content .= "<td align='left' style='font-weight:bold;'>Username: </td>";
                $content .= "<td>$username</td>";
                $content .= "</tr>";

                $content .= "<tr>";
                $content .= "<td align='left' style='font-weight:bold;'>Endorser Name: </td>";
                $content .= "<td>$endorser_name</td>";
                $content .= "</tr>";

                $content .= "<tr>";
                $content .= "<td align='left' style='font-weight:bold;'>Email Address: </td>";
                $content .= "<td>$email</td>";
                $content .= "</tr>";

                $content .= "<tr>";
                $content .= "<td align='left' style='font-weight:bold;'>Mobile Number: </td>";
                $content .= "<td>$mobile_no</td>";
                $content .= "</tr>";

                $content .= "<tr>";
                $content .= "<td align='left' style='font-weight:bold;'>Telephone Number: </td>";
                $content .= "<td>$telephone_no</td>";
                $content .= "</tr>";

                $content .= "<tr>";
                $content .= "<td align='left' style='font-weight:bold;'>Date Joined: </td>";
                $content .= "<td>$date_joined</td>";
                $content .= "</tr>";

                $content .= "</table>";

                $content .= "<br><br><br><br>";

                //Downlines table
                $content .= "<table>";
                $content .= "<tr>";
                $content .= "<td></td>";
                $content .= "<td align='center' style='font-weight:bold;'>Name of Endorsed IBO</td>";
                $content .= "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                $content .= "<td align='center' style='font-weight:bold;'>Placed Under</td>";
                $content .= "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                $content .= "<td align='center' style='font-weight:bold;'>Date Joined</td>";
                $content .= "</tr>";

                //Check if member has previous loan/s.
                $prev_loan = $model->getPreviousLoans($member_id, $loan_id);
                $limit = 5 * count($prev_loan);

                //Get direct endorse details
                $direct_downline_details = $model->getLoanDirectEndorsementDownlines($member_id, $limit);

                $count = 1;
                foreach ($direct_downline_details as $ddd)
                {
                    $content .= "<tr>";
                    $content .= "<td>" . $count . ". </td>";
                    $content .= "<td>" . $ddd['member_name'] . "</td>";
                    $content .= "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                    $content .= "<td>" . $ddd['endorser_name'] . "</td>";
                    $content .= "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                    $content .= "<td>" . $ddd['date_created'] . "</td>";
                    $content .= "</tr>";
                    $count++;
                }
                $content .= "</table>";

                $content .= "<br><br><br><br>";

                //Total Amount table
                $cash_percentage = (80 / 100) * $loan_amount;
                $check_percentage = (20 / 100) * $loan_amount;

                $content .= "<table style='font-weight:bold;'>";
                $content .= "<tr>";
                $content .= "<td>LOAN AMOUNT:</td>";
                $content .= "<td>" . $this->numberFormat($loan_amount) . " </td>";
                $content .= "</tr>";

                $content .= "<tr>";
                $content .= "<td>80% - Cash:</td>";
                $content .= "<td>" . $this->numberFormat($cash_percentage) . "</td>";
                $content .= "</tr>";

                $content .= "<tr>";
                $content .= "<td>20% - G.C:</td>";
                $content .= "<td>" . $this->numberFormat($check_percentage) . "</td>";
                $content .= "</tr>";
                $content .= "</table>";
            }
            else
            {
                //Get names of endorsed IBO
                $rawData = Networks::getDownlines($member_id);
                
                if (count($rawData) > 0)
                {   
                    //Get Payee Details
                    $payee_details = $model->getPayeeDetails($member_id);
                    
                    $username = $payee_details[0]['username'];
                    $date_joined = $payee_details[0]['date_created'];
                    $email = $payee_details[0]['email'];
                    $mobile_no = $payee_details[0]['mobile_no'];
                    $telephone_no = $payee_details[0]['telephone_no'];
                    $endorser_name = $payee_details[0]['endorser_name'];

                    
                    $content = "<table  align='center'><tr><td><h3>LOAN - LEVEL COMPLETION</h3></td></tr></table>";
                    
                    $content .= "<br>";
                    
                    $content .= "<table style='width: 100%;'>";
                    $content .= "<tr>";
                    $content .= "<td align='left' style='font-weight:bold;'>Name of Payee: </td>";
                    $content .= "<td>$member_name</td>";
                    $content .= "</tr>";
                    
                    $content .= "<tr>";
                    $content .= "<td align='left' style='font-weight:bold;'>Username: </td>";
                    $content .= "<td>$username</td>";
                    $content .= "</tr>";
                    
                    $content .= "<tr>";
                    $content .= "<td align='left' style='font-weight:bold;'>Endorser Name: </td>";
                    $content .= "<td>$endorser_name</td>";
                    $content .= "</tr>";
                    
                    $content .= "<tr>";
                    $content .= "<td align='left' style='font-weight:bold;'>Email Address: </td>";
                    $content .= "<td>$email</td>";
                    $content .= "</tr>";
                    
                    $content .= "<tr>";
                    $content .= "<td align='left' style='font-weight:bold;'>Mobile Number: </td>";
                    $content .= "<td>$mobile_no</td>";
                    $content .= "</tr>";
                    
                    $content .= "<tr>";
                    $content .= "<td align='left' style='font-weight:bold;'>Telephone Number: </td>";
                    $content .= "<td>$telephone_no</td>";
                    $content .= "</tr>";
                    
                    $content .= "<tr>";
                    $content .= "<td align='left' style='font-weight:bold;'>Date Joined: </td>";
                    $content .= "<td>$date_joined</td>";
                    $content .= "</tr>";
                    
                    $content .= "</table>";
                    
                    $content .= "<br><br><br><br>";
                    
                    $final = Networks::arrangeLevel($rawData);
                    
                    //Get level 1 downline ids
                    foreach ($final['network'] as $val)
                    {
                        if ($val['Level'] == $level_no)
                        {
                            $downline_ids = $val['Members'];

                            $downline_details = $model->getLoanCompletionDownlines($downline_ids);
                        }
                    }

                    //Downlines table
                    $content .= "<table>";
                    $content .= "<tr>";
                    $content .= "<td></td>";
                    $content .= "<td align='center' style='font-weight:bold;'>Name of IBO</td>";
                    $content .= "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                    $content .= "<td align='center' style='font-weight:bold;'>Level No.</td>";
                    $content .= "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                    $content .= "<td align='center' style='font-weight:bold;'>Placed Under</td>";
                    $content .= "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                    $content .= "<td align='center' style='font-weight:bold;'>Date Joined</td>";
                    $content .= "</tr>";
                    
                    $count = 1;
                    foreach ($downline_details as $dd)
                    {
                        $content .= "<tr>";
                        $content .= "<td>" . $count . ". </td>";
                        $content .= "<td>" . $dd['member_name'] . "</td>";
                        $content .= "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        $content .= "<td align='center'>" . $level_no . "</td>";
                        $content .= "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        $content .= "<td>" . $dd['endorser_name'] . "</td>";
                        $content .= "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        $content .= "<td>" . $dd['date_created'] . "</td>";
                        $content .= "</tr>";
                        $count++;
                    }
                    $content .= "</table>";
                    
                    $content .= "<br><br><br><br>";
                    
                    //Total Amount table
                    $cash_percentage = (80 / 100) * $loan_amount;
                    $check_percentage = (20 / 100) * $loan_amount;
                    
                    $content .= "<table style='font-weight:bold;'>";
                    $content .= "<tr>";
                    $content .= "<td>LOAN AMOUNT:</td>";
                    $content .= "<td>" . $this->numberFormat($loan_amount) . " </td>";
                    $content .= "</tr>";
                    
                    $content .= "<tr>";
                    $content .= "<td>80% - Cash:</td>";
                    $content .= "<td>" . $this->numberFormat($cash_percentage) . "</td>";
                    $content .= "</tr>";
                    
                    $content .= "<tr>";
                    $content .= "<td>20% - G.C:</td>";
                    $content .= "<td>" . $this->numberFormat($check_percentage) . "</td>";
                    $content .= "</tr>";
                    $content .= "</table>";
                }
            }
        }
        else
        {
            echo "id not set";
        }
        //echo $content;
        $html2pdf = Yii::app()->ePdf->HTML2PDF();
        $html2pdf->WriteHTML($content);
        $html2pdf->Output('Loan_' . date('Y-m-d') . '.pdf', 'D'); 
    }
    
    public function actionPdfGoc()
    {
        if(isset($_GET["id"]))
        {
            $commission_id = $_GET["id"];
            $member_id = $_GET["member_id"];
            $member_name = $_GET["member_name"];

            $content = "Group Override Commission for ".$commission_id." cut off";
            $content .= "<br>";
            $content .= "Member Name: ".$member_name;
            
            $html2pdf = Yii::app()->ePdf->HTML2PDF();
            $html2pdf->WriteHTML($content);
            $html2pdf->Output('GOC_' . date('Y-m-d') . '.pdf', 'D'); 
        }
        else
        {
            echo "id not set";
        }
    }
    
    public function actionPdfUnilevel()
    {
        if(isset($_GET["id"]))
        {
            $unilevel_id = $_GET["id"];
            $member_id = $_GET["member_id"];
            $member_name = $_GET["member_name"];

            $content = "Unilevel Payout for ".$unilevel_id." cut off";
            $content .= "<br>";
            $content .= "Member Name: ".$member_name;
            
            $html2pdf = Yii::app()->ePdf->HTML2PDF();
            $html2pdf->WriteHTML($content);
            $html2pdf->Output('Unilevel_' . date('Y-m-d') . '.pdf', 'D'); 
        }
        else
        {
            echo "id not set";
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
        if(isset($_GET["id"]))
        {
            $direct_endorsement_id = $_GET["id"];
            $cutoff_id = $_GET["cutoff_id"];
            $endorser_name = $_GET["endorser_name"];

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
}
?>
