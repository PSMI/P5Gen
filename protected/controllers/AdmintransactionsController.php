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
            
            $model = new Loan();
            
            if ($loan_type_id == 1)
            {
                //direct 5
                $content = "Direct Endorsement Loan Completion";
                $content .= "<br>";
                $content .= "Member Name: ".$member_name;
            }
            else
            {
                $rawData = Networks::getDownlines($member_id);
                
                if (count($rawData) > 0)
                {
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

                    //Get downline names
                    $content = "Loan Completion for Level #: ".$level_no;
                    $content .= "<br>";
                    $content .= "Member Name: ".$member_name;
                    $content .= '<table cellspacing="20">';
                    $content .= '<tr>';
                    $content .= '<td>Downline Name</td>';
                    $content .= '<td>Date Joined</td>';
                    $content .= '</tr>';
                    foreach ($downline_details as $dd)
                    {
                        $content .= '<tr>';
                        $content .= '<td>' . $dd["member_name"] . '</td>';
                        $content .= '<td>' . $dd["date_created"] . '</td>';
                        $content .= '</tr>';
                    }
                    $content .= '</table>';
                }
            }
        }
        else
        {
            echo "id not set";
        }
        
        $html2pdf = Yii::app()->ePdf->HTML2PDF();
        $html2pdf->WriteHTML($content);
        $html2pdf->Output('Loan_' . date('Y-m-d') . '.pdf', 'D'); 
    }
}
?>
