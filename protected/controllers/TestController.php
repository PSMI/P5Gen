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
        $model = new TestModel();
        
        $rawData = Networks::getDownlines($member_id);
        
        if (count($rawData) > 0)
        {
            $final = Networks::arrangeLevel($rawData);
            
            foreach ($final as $val)
            {
                //check if member_id exist in loans table
                $doexist = $model->checkIfLoanExistWithLevel($member_id, $val['Level']);

                if (count($doexist) > 0)
                {
                    $loan_id = $doexist[0]['loan_id'];
                    
                    //update loans table
                    $result = $model->getTotalEntries($val['Level']);
                    $complete_count_entries = $result[0]['total_entries'];
                    $amount = $result[0]['loan'];

                    if ($complete_count_entries == $val['Total'])
                    {
                        //update loans table, set ibo_count to $total_members and status to 1(Completed)
                        $status = 1;                        

                        $result = $model->updateLoanCompleted($val['Total'], $status, $loan_id, $val['Level'], $amount);

                        echo $result;
                        echo "</br>";
                        echo "Successfully updated loans table (Level Completed)"; 
                    }
                    else
                    {
                        //update loans table, set ibo_count + 1
                        $status = 0;

                        $result = $model->updateLoanIbo($status, $loan_id, $val['Total']);

                        echo $result;
                        echo "</br>";
                        echo "Successfully updated loans table (Update IBO Count)";
                    }
                }
                else
                {
                    $doexistcompletion = $model->checkIfLoanExistWithLevelCompletion($member_id, $val['Level']);
                    
                    if (count($doexistcompletion) > 0)
                    {
                        echo "Did not insert due to level completion.";
                    }
                    else
                    {
                        //insert new record to loans table
                        $result = $model->getTotalEntries($val['Level']);
                        $amount = $result[0]['loan'];

                        $insertresult = $model->insertLoan($member_id, $val['Level'], $amount, $val['Total']);

                        echo $insertresult;
                        echo "</br>";
                        echo "Successfully inserted new record to loans table";
                    }
                }
            }
        }
        else
        {
            echo "No Downline(s)";
        }
    }
}

?>