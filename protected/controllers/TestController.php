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
                
                $result = $model->getTotalEntries($val['Level']);
                $complete_count_entries = $result[0]['total_entries'];
                $amount = $result[0]['loan'];
                
                if (count($doexist) > 0)
                {
                    //update loans table
                    $loan_id = $doexist[0]['loan_id'];
                    
                    if ($complete_count_entries == $val['Total'])
                    {
                        if ($val['Level'] == 1 && $val['Total'] == 5)
                        {
                            $downlines_array = explode(',', $val['Members']);

                            foreach ($downlines_array as $downline_id)
                            {
                                $result = $model->checkIfDirectEndorse($downline_id);
                                $endorser_id = $result[0]['endorser_id'];

                                if ($member_id != $endorser_id)
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
                                    echo "Did not update Level 1 completion because level 1 is direct 5";
                                    echo "</br>";
                                }
                            }
                        }
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
                        $result = $model->getTotalEntries($val['Level']);
                        $amount = $result[0]['loan'];
                            
                        if ($complete_count_entries == $val['Total'])
                        {
                            if ($val['Level'] == 1 && $val['Total'] == 5)
                            {
                                $downlines_array = explode(',', $val['Members']);

                                foreach ($downlines_array as $downline_id)
                                {
                                    $result = $model->checkIfDirectEndorse($downline_id);
                                    $endorser_id = $result[0]['endorser_id'];

                                    if ($member_id != $endorser_id)
                                    {
                                        //insert new record to loans table with level completion
                                        $insertresult = $model->insertLoanWithCompletion($member_id, $val['Level'], $amount, $val['Total']);
                                        
                                        echo $insertresult;
                                        echo "</br>";
                                        echo "Successfully inserted new record to loans table";
                                    }
                                    else
                                    {
                                        echo "Did not insert Level 1 completion because level 1 is direct 5";
                                        echo "</br>";
                                    }
                                }
                            }
                        }
                        else
                        {
                            //insert new record to loans table
                            $insertresult = $model->insertLoan($member_id, $val['Level'], $amount, $val['Total']);
                            
                            echo $insertresult;
                            echo "</br>";
                            echo "Successfully inserted new record to loans table";
                        }
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