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
    public function process_goc($member_id, $upline_id)
    {
        $model = new GroupOverrideCommission();
        $audit = new AuditLog();
        $reference = new ReferenceModel();
        $member = new MembersModel();
      
        $member->member_id = $member_id;                
        $uplines = Networks::getUplines($upline_id);             
        
        $conn = $model->_connection;
        $trx = $conn->beginTransaction();

        try
        {
            if(count($uplines)>0)
            {
                
                $cutoff_id = $reference->get_cutoff(TransactionTypes::GOC);
                $model->cutoff_id = $cutoff_id;                
                $model->uplines = $uplines;
                $member->status = 1;
                
                //Check if all uplines has existing records, add new otherwise
                $retval = $model->check_transactions();

                //Check if uplines has current transactions
                if(is_array($retval) && count($retval)> 0 )
                {
                    //Uplines with valid and existing transactions
                    $uplines_wt = implode(',',$retval);

                    $new_list = array_diff($uplines,$retval);
                    $model->uplines = $uplines_wt;
                    
                    //Update current transaction, +1 to current ibo_count. NOTE: MUST BE LOGGED TO AUDIT TRAIL FOR BACK TRACKING
                    $update = $model->update_transactions();
                              
                    if(count($update) > 0)
                    {
                        $audit->log_message = "Members ".$uplines_wt." has been updated.";
                        $audit->log_cron();
                    
                        if(count($new_list)>0)
                        {
                            //Add new commission to uplines without transactions
                            $model->uplines = array();
                            $model->uplines = $new_list;
                            $model->add_transactions();
                            
                        }
                        
                    }
                }
                else
                {                

                    $model->uplines = $uplines;
                    $model->add_transactions();                
                    
                }

            }
            
            $member->status = 1;
            $member->updateUnprocessedMembers();
            
            if(!$model->hasErrors() && !$member->hasErrors())
            {
                $trx->commit();
                $audit->log_message = "GOC job completed";
                $audit->log_cron();
                return array('result_code'=>0, 'result_msg'=>$audit->log_message); 
            }
            else
            {
                $trx->rollback();
                $audit->log_message = "GOC job has failed";
                $audit->log_cron();
                return array('result_code'=>1, 'result_msg'=>$audit->log_message); 
            }
            
        }
        catch (PDOException $e) 
        {
            $trx->rollback();
            $audit->log_message = $e->getMessage();
            $audit->log_cron();
            return array('result_code'=>2, 'result_msg'=>$e->getMessage());
        } 
   
        
        
    }
    
    /**
     * 
     * @param type $member_id
     * @param type $endorser_id
     * @return boolean
     */
    public function process_direct_endorsement($member_id, $endorser_id)
    {
        $model = new DirectEndorsement();
        $reference = new ReferenceModel();
        $member = new MembersModel();
        
        $member->member_id = $member_id;        
        $cutoff_id = $reference->get_cutoff(TransactionTypes::DIRECT_ENDORSE);
        
        $model->member_id = $member_id;
        $model->endorser_id = $endorser_id;
        $model->cutoff_id = $cutoff_id;
        
        $retval = $model->add_transactions();
        
        if($retval)
        {
            $member->status = 2; //Processed by direct endorsement
            $result = $member->updateUnprocessedMembers();
            
            if(count($result)>0)
                return true;
            else
                return false;
        }
        else
        {
            return false;
        }
     
    }
    
    /**
     * 
     * @param type $member_id
     * @return type
     */
    public function process_unilevel($member_id)
    {
        $model = new Unilevel();
        $reference = new ReferenceModel();
        $member = new MembersModel();
                                     
        $member->member_id = $member_id;
        
        $uplines = Networks::getUplines($member_id);
        
        if(is_null($uplines))//root record
            $uplines = array($member_id);
        
        $cutoff_id = $reference->get_cutoff(TransactionTypes::UNILEVEL); 
        $model->cutoff_id = $cutoff_id;      
        
        $conn = $model->_connection;
        $trx = $conn->beginTransaction();
        
        try
        {
            foreach($uplines as $upline)
            {
                //Check each upline running account
                $model->upline_id = $upline;
                $account = $model->get_running_account();
                
                // If member has already unilevel transaction
                if($account['with_unilevel_trx'] == 1)
                {
                    //Check existing active transaction for current cutoff
                    $trans = $model->check_transaction();
                    
                    if(count($trans) > 0)
                        $model->update_transaction();
                    else
                        $model->new_transaction();
                }
                else //First transaction
                {
                    
                    if($account['direct_endorse'] >= 5)
                    {
                        $model->total_direct_endorse = $account['direct_endorse'];
                        
                        //Check direct endorse count if >= 5 date and if no. of month < 3 months
                        if($account['num_of_months'] < 3)
                        {                            
                            //Insert first payout
                            $retval = $model->insert_first_transaction();
                            
                        }
                        else
                        {
                            //If first 5 direct endorse completed in > 3 months, 
                            //get only all new members joined after 3 months?

                            $member->member_id = $upline;
                            $row = $member->get_count_with_flush_out();
                            $model->total_direct_endorse = $row['total_direct_endorse'];
                            $retval = $model->insert_first_transaction_with_flushout();

                        }
                    }
                }//with_unilevel_trx     
            }//foreach
            
            $member->status = 3; //Processed by unilevel endorsement
            $member->updateUnprocessedMembers();
                        
            if(!$model->hasErrors() && !$member->hasErrors())
            {
                
                $trx->commit();
                return array('result_code'=>0, 'result_msg'=>'Successfully process unilevel transactions');
            }
            else
            {
                $trx->rollback();
                return array('result_code'=>1, 'result_msg'=>$model->getErrors() . ' /' . $member->getErrors());
            }
        }
        catch(PDOException $e)
        {
            $trx->rollback();
            return array('result_code'=>3, 'result_msg'=>$e->getMessage());
        }
        
        
    }
    
    public function process_loan_direct($member_id)//, $endorser_id, $upline_id)
    {
        $model = new CronLoanDirect();
        $member = new MembersModel();
        $uplines = Networks::getUplines($member_id);
        
        $member->member_id = $member_id;
        
        if(is_null($uplines))//root record
            $uplines = array($member_id);        
        
        $conn = $model->_connection;
        $trx = $conn->beginTransaction();
        
        try
        {
            foreach($uplines as $upline)
            {
                $model->member_id = $upline;

                //get total members
                $result = $model->getDirectEndorse();

                if ($result['direct_endorse'] > 0)
                {
                    $doexist = $model->checkIfLoanExist();

                    if (count($doexist) > 0)
                    {
                        //update loans table, add 
                        $ibo_count = $doexist[0]['ibo_count'];
                        $loan_id = $doexist[0]['loan_id'];
                        $model->loan_id = $loan_id;

                        if ($ibo_count == 5)
                        {
                            //update loans table, set ibo_count to $ibo_count and status to 1(Completed)
                            $model->status = 1;
                            $model->updateLoanDirectCompleted();

                        }
                        else
                        {
                            //update loans table, set ibo_count to $ibo_count
                            $model->status = 0;
                            $model->updateLoanDirectIbo();

                        }
                    }
                    else
                    {
                        //insert new record to loans table
                        $model->insertLoan();
                    }
                }
            }
            
            $member->status = 4; //Processed by unilevel endorsement
            $member->updateUnprocessedMembers();

            if(!$model->hasErrors() && !$member->hasErrors())
            {
                $trx->commit();
                return true;
            }
            else
            {
                $trx->rollback();
                return false;
            }
        }
        catch(PDOException $e)
        {
            $trx->rollback();
            return false;
        }
        
    }
    
    public function process_loan_completion($member_id) //, $endorser_id, $upline_id)
    {
        $model = new CronLoanCompletion();   
        $member = new MembersModel();
                            
        $uplines = Networks::getUplines($member_id);
        if(is_null($uplines))
            $uplines = array($member_id);
       
        $member->member_id = $member_id;
        
        $conn = $model->_connection;
        $trx = $conn->beginTransaction();
        
        try
        {
            foreach($uplines as $upline)
            {
                $model->member_id = $upline;

                $rawData = Networks::getDownlines($model->member_id);
                
                if (count($rawData) > 0)
                {
                    $final = Networks::arrangeLevel($rawData);
//                    echo CJSON::encode($final); exit;
                    foreach ($final as $val)
                    {
                        $model->level_no = $val['Level'];
                        $model->total_members = $val['Total'];                    
                        $model->target_level = $val['Level'];
                        
                        //check if member_id exist in loans table
                        $doexist = $model->checkIfLoanExistWithLevel();

                        if (count($doexist) > 0)
                        {
                            $loan_id = $doexist[0]['loan_id'];

                            //update loans table
                            $result = $model->getTotalEntries($val['Level']);
                            $complete_count_entries = $result[0]['total_entries'];
                            $amount = $result[0]['loan'];

                            $model->loan_id = $loan_id;
                            $model->loan_amount = $amount;

                            if ($val['Total'] == $complete_count_entries)
                            {
                                //update loans table, set ibo_count to $total_members and status to 1(Completed)                
                                $model->status = 1;
                                $model->updateLoanCompleted();
                            }
                            else
                            {
                                //update loans table, set ibo_count + 1
                                $model->status = 0;
                                $model->updateLoanIbo();
                            }
                        }
                        else
                        {
                            //insert new record to loans table
                            
                            $result = $model->getTotalEntries();
                            
                            $amount = $result[0]['loan'];                        
                            $model->loan_amount = $amount;
                            $model->insertLoan();
                            
                        }
                    }//foreach final
                }//ifcount($rawData)
            }//foreach uplines
            
            $member->status = 5; //Processed by unilevel endorsement
            $member->updateUnprocessedMembers();

            if(!$model->hasErrors() && !$member->hasErrors())
            {
                $trx->commit();
                return true;
            }
            else
            {
                $trx->rollback();
                return false;
            }
            
        }
        catch(PDOException $e)
        {
            $trx->rollback();
            return false;
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
