<?php

/*
 * @author : owliber
 * @date : 2014-02-16
 */

class Transactions extends Controller
{
        
    /**
     * Process GOC transactions
     * @param type $member_id
     * @param type $endorser_id
     * @param type $upline_id
     * @return boolean
     */
    public function process_goc($member_id, $endorser_id, $upline_id)
    {
        $model = new GroupOverrideCommission();
        $audit = new AuditLog();
        $reference = new ReferenceModel();
        
        $conn = $model->_connection;
        $trx = $conn->beginTransaction();
               
        
        //If upline is the same as logged user
        if($upline_id == $endorser_id)                        
            $uplines = Networks::getUplines($upline_id);
        else
            $uplines = Networks::getUplines($member_id);
                
        if(count($uplines == 1))
            $upline_list = array($upline_id);
        else
            $upline_list = array_diff($uplines, array($upline_id));
   
        $cutoff_id = $reference->get_cutoff();
        
        try
        {
            //Check if all uplines has existing records, add new otherwise
            $retval = $model->check_transactions($upline_list,$cutoff_id);
            
            //Check if uplines has current transactions
            if(is_array($retval) && count($retval)> 0 )
            {

                //Uplines with valid and existing transactions
                $uplines_wt = implode(',',$retval);

                $new_list = array_diff($upline_list,array($uplines_wt));

                //Update current transaction, +1 to current ibo_count. NOTE: MUST BE LOGGED TO AUDIT TRAIL FOR BACK TRACKING
                $update = $model->update_transactions($uplines_wt, $cutoff_id);

                if(count($update) > 0 && count($new_list)>0)
                {

                    $audit->log_message = "Members ".$uplines_wt." has been updated.";
                    $audit->log_cron();

                    //Add new commission to uplines without transactions
                    $result = $model->add_transactions($new_list,$cutoff_id);

                    if(count($result)>0)
                    {

                        $audit->log_message = "New transaction for members ".implode(',',$new_list)." is added.";
                        $audit->log_cron();

                        $trx->commit();
                        return true;
                    }
                    else
                    {
                        $trx->rollback();

                        $audit->log_message = "Failed adding new transactions for members ".implode(',',$new_list);
                        $audit->log_cron();

                        return false;
                    }
                }
                else
                {
                    $trx->rollback();

                    $audit->log_message = "Failed updating transactions of members ".implode(',',$new_list);
                    $audit->log_cron();

                    return false;
                }
            }
            else
            {                

                $result = $model->add_transactions($upline_list,$cutoff_id);                

                if(count($result)>0)
                {
                    $audit->log_message = "Failed adding new transactions for members ".implode(',',$new_list).".";
                    $audit->log_cron();

                    $trx->commit();
                    return true;
                }
                else
                {
                    $trx->rollback();

                     $audit->log_message = "Failed adding new transactions for members ".implode(',',$new_list).".";
                     $audit->log_cron();

                    return false;
                }
            }
        }
        catch (PDOException $e) 
        {
            $trx->rollback();

            $audit->log_message = "Failed processing cutoffs.";
            $audit->log_cron();

            return false;
        }
        
    }
    
    public function process_loan_completion($member_id, $endorser_id, $upline_id)
    {
        $model = new CronLoanCompletion();
                
        //get member uplines
        //If upline is the same as logged user
        if($upline_id == $endorser_id)                        
            $uplines = Networks::getUplines($upline_id);
        else
            $uplines = Networks::getUplines($member_id);
                    
        if(count($uplines == 1))
            $upline_list = array($upline_id);
       
        foreach($upline_list as $upline)
        {
            $model->member_id = $upline;
            
            //get total members
            $result = $model->getTotalMembers();

            if (count($result) > 0)
            {
                $total_members = $result[0]['total_member'];
                $model->total_members = $total_members;

                if ($total_members > 0)
                {
                    //get current level
                    $level = Transactions::getLevel($total_members);
                    $model->level_no = $level;

                    //check if member_id exist in loans table
                    $doexist = $model->checkIfLoanExistWithLevel();

                    if (count($doexist) > 0)
                    {
                        $loan_id = $doexist[0]['loan_id'];

                        if ($total_members > 5 && $level == 1)
                        {
                            $target_level = $level + 1;
                        }
                        else if ($total_members > 5)
                        {
                            $target_level = $level + 1;
                        }
                        else
                        {
                            $target_level = $level;
                        }

                        $model->target_level = $target_level;

                        $result = $model->getTotalEntries();
                        $complete_count_entries = $result[0]['total_entries'];
                        $loan_amount = $result[0]['loan'];

                        $model->loan_id = $loan_id;
                        $model->loan_amount = $loan_amount;

                        if ($total_members == $complete_count_entries)
                        {
                            //update loans table, set ibo_count to $total_members and status to 1(Completed)
                            $model->status = 1;                        

                            $result = $model->updateLoanCompleted();

                            echo $result;
                            echo "</br>";
                            echo "Successfully updated loans table (Level Completed)"; 
                        }
                        else
                        {
                            //update loans table, set ibo_count + 1
                            $model->status = 0;

                            $result = $model->updateLoanIbo();

                            echo $result; 
                            echo "</br>";
                            echo "Successfully updated loans table (Update IBO Count)";
                        }
                    }
                    else
                    {
                        //insert new record to loans table
                        $result = $model->getTotalEntries();
                        $model->loan_amount = $result[0]['loan'];

                        $insertresult = $model->insertLoan();

                        echo $insertresult; 
                        echo "</br>";
                        echo "Successfully inserted new record to loans table"; 
                    }
                }
                else
                {
                    echo "No downline"; 
                }
            }
            else
            {
                echo "Does not exist in running_accounts table. Not Registered!"; 
            }
        }
        
    }
    
    public function process_loan_direct($member_id, $endorser_id, $upline_id)
    {
        $model = new CronLoanDirect();
        
        //get member uplines
        //If upline is the same as logged user
        if($upline_id == $endorser_id)                        
            $uplines = Networks::getUplines($upline_id);
        else
            $uplines = Networks::getUplines($member_id);
                    
        if(count($uplines == 1))
            $upline_list = array($upline_id);
        
        foreach($upline_list as $upline)
        {
            $model->member_id = $upline;
            
            //get total members
            $result = $model->getDirectEndorse();

            if (count($result) > 0)
            {
                $direct_endorse = $result[0]['direct_endorse'];

                if ($direct_endorse > 0)
                {
                    //check if member_id exist in loans table
                    $doexist = $model->checkIfLoanExist();

                    if (count($doexist) > 0)
                    {
                        //update loans table, add 
                        $ibo_count = $doexist[0]['ibo_count'];
                        $loan_id = $doexist[0]['loan_id'];
                        $model->loan_id = $loan_id;

                        //get overall total ibo_count
                        $result = $model->getOverallIboCount();
                        $total_ibo = $result[0]['total_ibo'];

                        //get difference
                        $difference = $direct_endorse - $total_ibo;

                        $ibo_count = $ibo_count + $difference;      

                        $model->ibo_count = $ibo_count;
                        
                        if ($ibo_count == 5)
                        {
                            //update loans table, set ibo_count to $ibo_count and status to 1(Completed)
                            $model->status = 1;

                            $result = $model->updateLoanDirectCompleted();

                            echo $result;
                            echo "</br>";
                            echo "Successfully updated loans table (Direct 5 Completed)"; 
                        }
                        else
                        {
                            //update loans table, set ibo_count to $ibo_count
                            $model->status = 0;

                            $result = $model->updateLoanDirectIbo();

                            echo $result; 
                            echo "</br>";
                            echo "Successfully updated loans table (Update IBO Count)";
                        }
                    }
                    else
                    {
                        //insert new record to loans table
                        $result = $model->insertLoan();
                        echo "insert new record to loans table"; exit;
                    }
                }
                else
                {
                    echo "No direct endorse";
                }
            }
            else
            {
                echo "Does not exist in running_accounts table. Not Registered!"; 
            }
        }
        
    }
    
    public function getLevel($total_members)
    {   
        if (($total_members > 0) && ($total_members < 26))
        {
            $level = 1;
        }
        else if (($total_members > 25) && ($total_members < 126))
        {
            $level = 2;
        }
        else if (($total_members > 125) && ($total_members < 626))
        {
            $level = 3;
        }
        else if (($total_members > 625) && ($total_members < 3126))
        {
            $level = 4;
        }
        else if (($total_members > 3125) && ($total_members < 15626))
        {
            $level = 5;
        }
        else if (($total_members > 15625) && ($total_members < 78126))
        {
            $level = 6;
        }
        else if (($total_members > 78125) && ($total_members < 390626))
        {
            $level = 7;
        }
        else if (($total_members > 390625) && ($total_members < 1953126))
        {
            $level = 8;
        }
        else if (($total_members > 1953125) && ($total_members < 9765626))
        {
            $level = 9;
        }
        else if ($total_members > 9765625)
        {
            $level = 10;
        }
        else
        {
            $level = 0;
        }
        
        return $level;
    }
    
}
?>
