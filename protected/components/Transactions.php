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
     * @author owliber
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
                $model->payout_rate = $reference->get_variable_value('GOC_LEVEL_10_BELOW_AMOUNT');

                //Check if all uplines has existing records, add new otherwise
                $retval = $model->check_transactions();

                //Check if uplines has current transactions
                if(is_array($retval) && count($retval)> 0 )
                {
                    //Uplines with valid and existing transactions
                    $uplines_wt = implode(',',$retval);

                    $new_list = array_diff($uplines,$retval);
                    $model->uplines = $uplines_wt;
                    
                    foreach($retval as $val)
                    {
                        $model->upline_id = $val;
                        $level = Networks::getLevel($val, $member_id);
                        
                        if($level > 10)
                            $model->payout_rate = $reference->get_variable_value('GOC_LEVEL_11_UP_AMOUNT');
                        else
                            $model->payout_rate = $reference->get_variable_value('GOC_LEVEL_10_BELOW_AMOUNT');

                        //Update current transaction, +1 to current ibo_count. NOTE: MUST BE LOGGED TO AUDIT TRAIL FOR BACK TRACKING
                        $model->update_transactions();
                    }
                              
                    if(!$model->hasErrors())
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
     * Process Direct Endorsement transactions
     * @param type $member_id
     * @param type $endorser_id
     * @return boolean
     * @author owliber
     */
    public function process_direct_endorsement($member_id, $endorser_id)
    {
        $model = new DirectEndorsement();
        $reference = new ReferenceModel();
        $member = new MembersModel();
        
        $member->member_id = $member_id;        
        $cutoff_id = $reference->get_cutoff(TransactionTypes::DIRECT_ENDORSE);
        $payout_rate = $reference->get_payout_rate(TransactionTypes::DIRECT_ENDORSE);
        
        $model->member_id = $member_id;
        $model->endorser_id = $endorser_id;
        $model->cutoff_id = $cutoff_id;
        $model->payout_rate = $payout_rate;
        
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
     * Process IPD Direct Endorsement transactions
     * @param type $member_id
     * @param type $endorser_id
     * @return boolean
     * @author jopormento
     */
    public function process_ipd_direct_endorsement($member_id, $endorser_id)
    {
        $model = new IpdDirectEndorsement();
        $reference = new ReferenceModel();
        $member = new MembersModel();
         
        $cutoff_id = $reference->get_cutoff(TransactionTypes::IPD_DIRECT_ENDORSE);
        $model->endorser_id = $endorser_id;
        
        if(Members::getMembershipType($endorser_id) == 'distributor')
        {
            $direct_count = $model->count_transactions();
            $payout = Transactions::getIpdDirectEndorseRateByDirectEndorseCount($direct_count, $reference);
        }
        else
        {
            $payout = $reference->get_variable_value ('IBO_DIRECT_COMMISSION_AMOUNT');
        }
        
        $model->member_id = $member_id;        
        $model->cutoff_id = $cutoff_id;
        $model->payout_rate = $payout;
        $retval = $model->add_transactions();
        if($retval)
        {
            $member->member_id = $member_id;
            $member->status = 1; //Processed by direct endorsement
            $result = $member->updateUnprocessedDistributors();
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
    
    public function getIpdDirectEndorseRateByDirectEndorseCount($direct_endorse_count, $reference)
    {
        if($direct_endorse_count < 5)
        {
            $payout = $reference->get_variable_value('IPD_DIRECT_5_COMMISSION_AMOUNT');
        }
        else if (($direct_endorse_count >= 5) && ($direct_endorse_count < 10))
        {
            $payout = $reference->get_variable_value('IPD_DIRECT_10_COMMISSION_AMOUNT');
        }
        else if (($direct_endorse_count >= 10) && ($direct_endorse_count < 15))
        {
            $payout = $reference->get_variable_value('IPD_DIRECT_15_COMMISSION_AMOUNT');
        }
        else
        {
            $payout = $reference->get_variable_value('IPD_DIRECT_COMMISSION_AMOUNT');
        }
        return $payout;
    }
    /**
     * Process unilevel transactions
     * @param type $member_id
     * @return type
     * @author owliber
     */
    public function process_unilevel($member_id)
    {
        $model = new Unilevel();
        $reference = new ReferenceModel();
        $member = new MembersModel();
                                     
        $member->member_id = $member_id;
        
        $uplines = Networks::getEndorser($member_id);
        
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
                //Check direct endorse count if >= 5 date and if no. of month <= N months
                $flush_out = $reference->get_variable_value('UNILEVEL_FLUSHOUT_INTERVAL');
                $month = explode(" ", $flush_out);
                
                $interval = $month[1];
                        
                //Check each upline running account
                $model->upline_id = $upline;
                $account = $model->get_running_account($interval);
                
                // If member has already unilevel transaction
                if($account['with_unilevel_trx'] == 1)
                {
                    //Check existing active transaction for current cutoff
                    $trans = $model->check_transaction();
                    $level = Networks::getLevel($upline, $member_id);
                    
                    if(count($trans) > 0)
                    {
                        if($level < 11) $model->update_transaction();
                    }
                    else
                    {
                        $model->new_transaction();
                    }
                }
                else //First transaction
                {
                    
                    if($account['direct_endorse'] >= 5)
                    {
                        //$model->total_direct_endorse = $account['total_member'];
                        $model->total_direct_endorse = $account['direct_endorse'];
                        
//                        //Check direct endorse count if >= 5 date and if no. of month <= N months
//                        $flush_out = $reference->get_variable_value('UNILEVEL_FLUSHOUT_INTERVAL');
//                        $month = explode(" ", $flush_out);
                        
                        if($account['num_of_months'] <= $month[0])
                        {                            
                            //Insert first payout
                            $retval = $model->insert_first_transaction();
                            
                        }
                        else
                        {
                            //If first 5 direct endorse completed in > 3 months, 
                            //get only all new members joined after 3 months?

                            $member->member_id = $upline;
                            $date_completed = $account['date_first_five_completed'];
                            $row = $member->get_count_with_flush_out($date_completed);
                            $flush_count = $account['direct_endorse'] - $row['total'];
                            $valid_ibo_count = $account['total_member'] - $flush_count;
                            $model->total_members = $valid_ibo_count;                            
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
    /**
     * Process ipdunilevel transactions
     * @param type $member_id
     * @return type
     * @author jopormento
     */
    public function process_ipdunilevel($member_id)
    {
        $model = new IpdUnilevel();
        $reference = new ReferenceModel();
        $member = new MembersModel();
        $member->member_id = $member_id;
        
        //Get all endorsers regardless if IPD or IBO
        $endorsers = Networks::getIPD10thUnilevelNetworkForPayout($member_id);
        
        $cutoff_id = $reference->get_cutoff(TransactionTypes::IPD_UNILEVEL); 
        $model->cutoff_id = $cutoff_id;
        $conn = $model->_connection;
        $trx = $conn->beginTransaction();
        try
        {
            foreach($endorsers as $endorser)
            {
                //Check each upline running account
                $model->endorser_id = $endorser['member_id'];

                if (Members::getMembershipType($endorser['member_id']) == 'distributor')
                {
                    $ipd_direct_count = Networks::getIPDDirectCount($endorser['member_id']);
                    $payout = Transactions::getIpdUnilevelBonusByDirectEndorseCount($ipd_direct_count, $reference, $endorser['level']);
                }
                else
                {
                    $payout = $reference->get_variable_value('IBO_UNILEVEL_BONUS');
                }
                
                // Check existing active transaction for current cutoff
                $trans = $model->check_transaction();
                if(count($trans) > 0)
                {
                    $model->update_transaction($payout);
                }
                else
                {
                    $model->new_transaction($payout);
                }
            }
            
            $member->status = 2; //Processed by ipd unilevel
            $member->updateUnprocessedDistributors();
            if(!$model->hasErrors() && !$member->hasErrors())
            {
                $trx->commit();
                return array('result_code'=>0, 'result_msg'=>'Successfully process IPD unilevel transactions');
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
    
    public function getIpdUnilevelBonusByDirectEndorseCount($direct_endorse_count, $reference, $level)
    {
        if($direct_endorse_count < 5)
        {
            $payout = $reference->get_variable_value('IPD_UNILEVEL_BONUS');
        }
        else if (($direct_endorse_count > 4) && ($direct_endorse_count < 10))
        {
            if ($level < 6)
            {
                $payout = $reference->get_variable_value('IPD_UNILEVEL_5_BONUS_LEVEL_5');
            }
        }
        else if (($direct_endorse_count > 9) && ($direct_endorse_count < 15))
        {
            if ($level < 8)
            {
                $payout = $reference->get_variable_value('IPD_UNILEVEL_10_BONUS_LEVEL_7');
            }
        }
        else
        {
            if ($level < 11)
            {
                $payout = $reference->get_variable_value('IPD_UNILEVEL_15_BONUS_LEVEL_10');
            }
        }
        return $payout;
    }
    
    /**
     * Process loan from direct endorsements
     * @param type $member_id
     * @return boolean
     * @author jopormento
     */
    public function process_loan_direct($member_id)
    {
        $model = new CronLoanDirect();
        $member = new MembersModel();
        $uplines = Networks::getUplines($member_id);
        $audit = new AuditLog();
        
        $member->member_id = $member_id;
        
        if(is_null($uplines))//root record
            $uplines = array($member_id);        
        
        $conn = $model->_connection;
        $trx = $conn->beginTransaction();
        
        try
        {
            foreach($uplines as $upline)
            {
        
                $model = new CronLoanDirect();
                $model->member_id = $upline;

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

                            if ($ibo_count == 4)
                            {
                                $model->status = 1;
                                $model->updateLoanDirectCompleted();
                                $audit->log_message = "update loans table (Complete Direct 5)";
                            }
                            else
                            {
                                $model->status = 0;
                                $model->updateLoanDirectIbo();
                                $audit->log_message = "update loans table (Plus 1 to ibo_count)";
                                
                            }
                        }
                        else
                        {
                            $model->insertLoan();
                            $audit->log_message = "Insert successful in loans table (New Record)";
                        }
                    }
                }
                else
                {
                    $audit->log_message = "No Downline(s)";
                }
            }
            
            $member->status = 4; //Processed by unilevel endorsement
            $member->updateUnprocessedMembers();

            if(!$model->hasErrors() && !$member->hasErrors())
            {
                $audit->log_cron();
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
    
    /**
     * Process loans from level completions
     * @param type $member_id
     * @return boolean
     * @author jopormento
     */
    public function process_loan_completion($member_id)
    {
        $model = new CronLoanCompletion();   
        $member = new MembersModel();
        $audit = new AuditLog();
                         
        $audit->job_id = 5;
        $member->member_id = $member_id;
        
        $uplines = Networks::getUplines($member_id);
        if(is_null($uplines))
            $uplines = array($member_id);
        
        $conn = $model->_connection;
        $trx = $conn->beginTransaction();
        
        try
        {
            foreach($uplines as $upline)
            {
                $member_id = $upline;

                $rawData = Networks::getDownlines($member_id);

                if (count($rawData) > 0)
                {
                    $final = Networks::arrangeLevel($rawData);

                    foreach ($final['network'] as $val)
                    {
                        if ($val['Level'] != 1)
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
                                                $audit->log_message = "Successfully updated loans table (Level Completed)"; 
                                            }
                                            else
                                            {
                                                $audit->log_message = "Did not update Level 1 completion because level 1 is direct 5";
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    //update loans table, set ibo_count + 1
                                    $status = 0;
                                    $result = $model->updateLoanIbo($status, $loan_id, $val['Total']);
                                    $audit->log_message = "Successfully updated loans table (Update IBO Count)";
                                }
                            }
                            else
                            {
                                $doexistcompletion = $model->checkIfLoanExistWithLevelCompletion($member_id, $val['Level']);

                                if (count($doexistcompletion) > 0)
                                {
                                    $audit->log_message = "Did not insert due to level completion.";
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
                                                    $audit->log_message = "Successfully inserted new record to loans table";
                                                }
                                                else
                                                {
                                                    $audit->log_message = "Did not insert Level 1 completion because level 1 is direct 5";
                                                }
                                            }
                                        }
                                    }
                                    else
                                    {
                                        //insert new record to loans table
                                        $insertresult = $model->insertLoan($member_id, $val['Level'], $amount, $val['Total']);
                                        $audit->log_message = "Successfully inserted new record to loans table";
                                    }
                                }
                            }
                        }
                        else
                        {
                            $audit->log_message = "Level 1 completion - Did nothing.";
                        }
                    }
                }
                else
                {
                    $audit->log_message = "No Downline(s)";
                }
            }//foreach uplines
            
            $member->status = 5; //Processed by unilevel endorsement
            $member->updateUnprocessedMembers();

            if(!$model->hasErrors() && !$member->hasErrors())
            {
                $audit->log_cron();
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
            $audit->log_message = $e->getMessage();
            return false;
        }
    }
        
    /**
     * Get genealogy level
     * @param type $total_members
     * @return int
     * @author jopormento
     */
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
    
    public function process_repeat_purchase_commission($distributor_purchases)
    {
        $model = new PurchasesModel();
        $reference = new ReferenceModel();
                                
        $distributor_id = $distributor_purchases['member_id'];
        $total_purchase = $distributor_purchases['total'];
        
        $model->repeat_purchase_id = $distributor_purchases['repeat_purchase_id'];
        
        //rp commission regardless of ibo or ipd endorser
        $endorsers = Networks::traceRPNetworkUpward($distributor_id);
        $cutoff_id = $reference->get_cutoff(TransactionTypes::REPEAT_PURCHASE_COMMISSION); 
        
        $model->cutoff_id = $cutoff_id;      
        $conn = $model->_connection;
        $trx = $conn->beginTransaction();
        
        try
        {
            foreach($endorsers as $endorser)
            {
	    	
                $model->endorser_id = $endorser['member_id'];
                
                $level = $endorser['level'];
                
                if(Members::getMembershipType($distributor_id) == 'distributor')
                {
                    
                    if(Members::getMembershipType($endorser['member_id']) == 'distributor')
                    {
                        $direct_count = Networks::getIPDDirectCount($endorser['member_id']);

                        if($level == 1)
                        {
                            $retention = true;
                            $rate = $reference->get_variable_value('IPD_REPEAT_PURCHASE_COMMISSION_DIRECT'); // 5 %IPD other retention - direct
                        }
                        // elseif($level > 1 && $level <= 10 && $direct_count >= 5)
                        else
                        {
                            
                            $retention = false;

                            if($direct_count >=5 && $direct_count <10 && $level <=5 )
                                $rate = $reference->get_variable_value ('IPD_REPEAT_PURCHASE_COMMISSION_5_2ND_5TH');
                            elseif($direct_count >=10 && $direct_count < 15 && $level <=7 )
                                $rate = $reference->get_variable_value ('IPD_REPEAT_PURCHASE_COMMISSION_10_2ND_7TH');
                            elseif($direct_count >=15 && $level <=10 )
                                $rate = $reference->get_variable_value ('IPD_REPEAT_PURCHASE_COMMISSION_15_2ND_10TH');

                        }

                    }
                    elseif(Members::getMembershipType($endorser['member_id']) == 'member')
                    {
                        //If IBO endorsed IPD, IBO will earn 5%
                        //If IBO endorsed IBO, IBO will earn 3%
                        //If IPD endorsed IPD, IPD will earn 5%
                        $retention = false;

                        if($level == 1)
                        {
                            $rate = $reference->get_variable_value('IBO_REPEAT_PURCHASE_COMMISSION_DIRECT'); // 5% IBO RP commission - Direct
                        }
                        elseif($level > 1 && $level <= 10)
                        {
                            
                            $rate = $reference->get_variable_value('RPC_DEFAULT_RATE'); //1% IBO RP commssion 
                        }
                    }
                }
                else
                {
                    $retention = false;
                    
                    if(Members::getMembershipType($endorser['member_id']) == 'member')
                    {

                        if($level == 1)
                        {
                            $rate = $reference->get_variable_value('IBO_REPEAT_PURCHASE_COMMISSION'); //3% IBO RP Commission - direct
                        }
                        elseif($level > 1 && $level <= 10)
                        {
                            
                            $rate = $reference->get_variable_value('RPC_DEFAULT_RATE'); //1% IBO RP commission
                        }

                    }
                }
		
		
              $commission = $total_purchase * ($rate / 100);
              $model->commission = $commission;

              if($retention) 
              {
                //Update IPD retention money
                if($model->has_retention())
                    $model->update_ipd_retention();
                else
                    $model->insert_ipd_retention();
              }
              else
              {
                 if($model->has_transaction())
                    $model->update_commission_transaction();
                 else
                    $model->insert_commission_transaction();
              }
                
                
                
            }//foreach
                        
            $model->delete_processed_purchases();
                        
            if(!$model->hasErrors())
            {
                //$model->update_repeat_purchase();
                $trx->commit();
                return array('result_code'=>0, 'result_msg'=>'Successfully process repeat purchase transactions');
            }
            else
            {
                $trx->rollback();
                return array('result_code'=>1, 'result_msg'=>$model->getErrors());
            }
        }
        catch(PDOException $e)
        {
            $trx->rollback();
            return array('result_code'=>3, 'result_msg'=>$e->getMessage());
        }
    }
    
}
?>
