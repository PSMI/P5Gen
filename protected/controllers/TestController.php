<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-18-2014
------------------------*/

class TestController extends Controller
{
    public function actionIndex()
    {
        $member_id = 6;
        
        $model = new CronLoanDirect();
        $model->member_id = $member_id;
        
        $result = $model->getDirectEndorse();
        
        if ($result['direct_endorse'] > 0)
        {
            $result_overallibo = $model->getOverallIboCount();
            $overall_ibo_count = $result_overallibo[0]['total_ibo'];

            $difference = $result['direct_endorse'] - $overall_ibo_count;

            for ($i = 0; $i < $difference; $i++)
            {
                $doexist = $model->checkIfLoanExist();
            
                if (count($doexist) > 0)
                {
                    $ibo_count = $doexist[0]['ibo_count'];
                    $loan_id = $doexist[0]['loan_id'];
                    $model->loan_id = $loan_id;
                    //$model->ibo_count = $ibo_count;
                    
                    if ($ibo_count == 4)
                    {
                        $model->status = 1;
                        $result_update_direct_completed =  $model->updateLoanDirectCompleted();
                        
                        echo $result_update_direct_completed;
                        echo "</br>";
                        echo "update loans table (Complete Direct 5)";
                        echo "</br>";
                    }
                    else
                    {
                        $model->status = 0;
                        $result_update_direct_ibo = $model->updateLoanDirectIbo();
                        
                        echo $result_update_direct_ibo;
                        echo "</br>";
                        echo "update loans table (Plus 1 to ibo_count)";
                        echo "</br>";
                    }
                }
                else
                {
                    $result_insert = $model->insertLoan();
                
                    echo $result_insert;
                    echo "</br>";
                    echo "Insert successful in loans table (New Record)";
                }
            }
        }
        else
        {
            echo "No Downline(s)";
        }
        exit;
    }
}

?>