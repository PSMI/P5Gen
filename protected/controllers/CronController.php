<?php

/* 
 * @author : owliber
 * @date : 2014-02-14
 * @var UnprocessedMemberModel
 * 
 * JOB SCHEDULE: 5 minute interval
 * ---------------------------------------------------------------------------------------------------------------
 * JOBS RUN SEQUENCE:
 * 1st Job: GOC - Get all unprocessed members with status = 0, update status to 1 after processing.
 * 2nd Job: Direct Endorsement - Get all unprocessed members with status = 1, update status to 2 after processing
 * 3th Job: Unilevel - Get all unprocessed members with status = 4, update status to 5 after processing.
 * 4rd Job: Loan Direct - Get all unprocessed members with status = 2, update status to 3 after processing
 * 5th Job: Loan Completion - Get all unprocessed members with status = 3, update status to 4 after processing
 * 6th Job: Delete all processed members with status = 5;
 * 7th Job: Promo Checking. Insert a record into promo_redemptions once a member has meet all promo mechanics
 * 8th Job: Auto-approve placements under N interval
 * ----------------------------------------------------------------------------------------------------------------
 * CRON PATH:
 * /cron/goc
 * /cron/directendorse
 * /cron/unilevel
 * /cron/loandirect
 * /cron/loancompletion
 * /cron/promocheck
 * /cron/sendmail
 * /cron/autoapprove
 * 
 */

class CronController extends Controller
{
    const JOB_GOC = 1;
    const JOB_DIRECT_ENDORSEMENT = 2;
    const JOB_UNILEVEL = 3;    
    const JOB_LOAN_DIRECT = 4;
    const JOB_LOAN_COMPLETION = 5;
    const JOB_PROMO = 6;
    const JOB_REPEAT_PURCHASE = 7;
    const JOB_IPD_DIRECT = 8;
    const JOB_IPD_UNILEVEL = 9;
    
    public $PID;
    public $PIDFile;
    public $PIDLog;
    
    public $_curdate;
    
    public function __construct() {
        $this->_curdate = date('Y-m-d H:i:s');
    }
    
    /**
     * Check if PID file exist
     * @return boolean
     */
    public function PID_exists()
    {
        $file = Yii::app()->file;
        $path = Yii::app()->basePath . '/runtime/';
        $this->PIDLog = $path . $this->PIDFile;
        
        if($file->set($this->PIDLog)->exists)
            return true;
        else
            return false;
    }
    
    /**
     * Create the PID file
     */
    public function createPID()
    {
        $file = Yii::app()->file;
        //Create pid file
        $pid = $file->set($this->PIDLog);
        $this->PID = $pid;
        
        $pid->create();
        $pid->setContents('1', true);  
    }
    
    public function job_enabled()
    {
        $model = new ReferenceModel();
        $retval = $model->get_variable_value('JOB_SCHEDULER');
        
        if($retval == 1)
            return true;
        else
            return false;
    }
    
    public function mailer_on()
    {
        $model = new ReferenceModel();
        $retval = $model->get_variable_value('MAILER');
        
        if($retval == 1)
            return true;
        else
            return false;
    }
        
    public function actionRun()
    {
        //$audit = new AuditLog();
        echo $this->_curdate . ' : Cron job started.<br />';
        
        $this->PIDFile = 'LoanCompletion.pid';
        if(!$this->PID_exists())
        {
            $this->actionGOC();
            $this->PIDFile = 'GOC.pid';
            if(!$this->PID_exists())
            {
                $this->actionDirectEndorse ();
                $this->PIDFile = 'DirectEndorse.pid';
                if(!$this->PID_exists())
                {
                    $this->actionUnilevel();                    
                    $this->PIDFile = 'Unilevel.pid';
                    if(!$this->PID_exists())
                    {
                        $this->actionLoanDirect();
                        $this->PIDFile = 'LoanDirect.pid';
                        
                        if(!$this->PID_exists())
                        {
                            
                            $this->actionLoanCompletion();
                            echo $this->_curdate . ' : Cron job has ended.<br />';
                            /*
                            $this->PIDFile = 'LoanCompletion.pid';
                            
                            if(!$this->PID_exists())
                            {
                                $this->PIDFile = 'CLEANUP.pid';
                                $audit->job_id = self::JOB_CLEAN_UP;
                                $this->createPID();

                                $retval = $this->cleanUp();
                                if($retval)
                                {
                                    $this->PID->delete();
                                    $audit->log_message = 'Cron job finished running';
                                    $audit->log_cron();
                                    echo $this->_curdate . ' : ' . $audit->log_message . '<br />';
                                }
                                else
                                {
                                    $audit->log_message = 'Clean up process halted. Pending PID file is created.';
                                    $audit->log_cron();
                                    echo $this->_curdate . ' : ' . $audit->log_message . '<br />';
                                }
                            }
                             * 
                             */
                            
                        }
                        
                        
                    }
                }
            }
        }
        
    }
    
    public function actionGOC()
    {
        if($this->job_enabled())
        {
            //Instantiate models
            $model = new MembersModel();
            $audit = new AuditLog();
            
            $this->PIDFile = 'GOC.pid';
            $audit->job_id = self::JOB_GOC;

            if(!$this->PID_exists())
            {
                
                //add to auditlogs
                $audit->log_message = 'Started processing GOC job.';
                $audit->log_cron();

                //Create pid file      
                $this->createPID();
                $audit->log_message = 'Created '.$this->PIDFile.' file';
                $audit->log_cron();

                $model->status = 0; //Pending
                $lists = $model->getUnprocessedMembers();
                
                if(count($lists)>0)
                {                    
                    foreach($lists as $list)
                    {
                        $member_id = $list['member_id'];
                        $upline_id = $list['upline_id'];
        
                        $retval = Transactions::process_goc($member_id, $upline_id);
                        
                        if($retval['result_code'] >= 0 && $retval['result_code'] < 3)
                        {
                            //add to auditlogs
                            $audit->log_message = $retval['result_msg'];

                        }
                        elseif($retval['result_code'] == 3)
                        {
                            //add to auditlogs
                            $audit->log_message = $retval['result_msg'];
                            $audit->status = 2;
                        }

                    }

                    //Delete process id
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                    
                }
                else
                {
                    
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                    
                }

            }
            else
            {
                
                $audit->log_message = 'GOC PID file still exist. Please wait current process to finish. ';
            }
            
            $audit->log_message = ' GOC job process has ended.';

        }
        else
        {
            $audit->log_message = 'Job scheduler is disabled.';
            
        }
        $audit->log_cron();
        echo $this->_curdate . ' : ' . $audit->log_message . '<br />';
        
    }
    
    public function actionDirectEndorse()
    {
        if($this->job_enabled())
        {
            $model = new MembersModel();
            $audit = new AuditLog();
            
            $this->PIDFile = 'DirectEndorse.pid';
            $audit->job_id = self::JOB_DIRECT_ENDORSEMENT;

            if(!$this->PID_exists())
            {
                               
                //add to auditlogs
                $audit->log_message = 'Started processing Direct Endorsement job.';
                $audit->log_cron();

                //Create pid file      
                $this->createPID();
                $audit->log_message = 'Created '.$this->PIDFile.' file';
                $audit->log_cron();
                
                $model->status = 1; //Processed by GOC
                $lists = $model->getUnprocessedMembers();
                
                if(count($lists)>0)
                {
                    foreach($lists as $list)
                    {
                        $member_id = $list['member_id'];         
                        $endorser_id = $list['endorser_id'];
                        
                        $retval = Transactions::process_direct_endorsement($member_id,$endorser_id);

                        if($retval)
                        {
                            //add to auditlogs
                            $audit->log_message = 'Direct endorsement processing  successful for MID '.$member_id.' EID '.$endorser_id;

                        }
                        else
                        {
                            //add to auditlogs
                            $audit->log_message = 'Direct endorsement processing failed for MID '.$member_id.' EID '.$endorser_id;
                            $audit->status = 2;
                        }
                    }
                    
                    //Delete process id
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                }
                else
                {
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                }
           
            }
            else
            {
                
                $audit->log_message = 'Direct endorsement process PID file still exist. Please wait current process to finish. ';
            }
            
            $audit->log_message = 'Direct endorse job process has ended.';
        }
        else
        {
            $audit->log_message = 'Job scheduler is disabled.';
        }
        
        $audit->log_cron();
        echo $this->_curdate . ' : ' . $audit->log_message . '<br />';
        
    }
    
    public function actionUnilevel()
    {
        if($this->job_enabled())
        {
            $model = new MembersModel();
            $audit = new AuditLog();
            
            $this->PIDFile = 'Unilevel.pid';
            $audit->job_id = self::JOB_UNILEVEL;

            if(!$this->PID_exists())
            {
                               
                //add to auditlogs
                $audit->log_message = 'Started processing Unilevel job.';
                $audit->log_cron();

                //Create pid file      
                $this->createPID();
                $audit->log_message = 'Created '.$this->PIDFile.' file';
                $audit->log_cron();
                
                $model->status = 2; //Processed by Direct Endorse
                $lists = $model->getUnprocessedMembers();
                
                if(count($lists)>0)
                {
                    foreach($lists as $list)
                    {
                        $member_id = $list['member_id'];   
                        
                        $retval = Transactions::process_unilevel($member_id);

                        if($retval['result_code'] == 0)
                        {
                            //add to auditlogs
                            $audit->log_message = 'Unilevel processing  successful for member '.$member_id.' uplines.';

                        }
                        elseif($retval['result_code'] == 1)
                        {
                            //add to auditlogs
                            $audit->log_message = 'Unilevel processing failed for member '.$member_id.' uplines.';
                            $audit->status = 2;
                        }
                        elseif($retval['result_code'] == 2)
                        {
                            $audit->log_message = 'Direct endorse count for member '.$member_id. ' is not valid for unilevel entry.';
                            $audit->status = 2;
                        }
                        elseif($retval['result_code'] == 3)
                        {
                            
                            $audit->log_message = $retval['result_msg'];
                            $audit->status = 2;
                        }
                    }
                    
                    //Delete process id
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                }
                else
                {
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                }
           
            }
            else
            {
                $audit->log_message = 'Unilevel process PID file still exist. Please wait current process to finish. ';
            }
            
            $audit->log_message = 'Unilevel job process has ended.';
        }
        else
        {
            $audit->log_message = 'Job scheduler is disabled.';
            
        }
        $audit->log_cron();
        echo $this->_curdate . ' : ' . $audit->log_message . '<br />';
    }
        
    public function actionLoanDirect()
    {
        if($this->job_enabled())
        {
            $model = new MembersModel();
            $audit = new AuditLog();
            
            $this->PIDFile = 'LoanDirect.pid';
            $audit->job_id = self::JOB_LOAN_DIRECT;

            if(!$this->PID_exists())
            {
                //add to auditlogs
                $audit->log_message = 'Started processing Loan direct job.';
                $audit->log_cron();

                //Create pid file      
                $this->createPID();
                $audit->log_message = 'Created '.$this->PIDFile.' file';
                $audit->log_cron();
                
                $model->status = 3; //Already processed by Unilevel job
                $lists = $model->getUnprocessedMembers();
                
                if(count($lists)>0)
                {
                    foreach($lists as $list)
                    {
                        $member_id = $list['member_id'];         
                        $endorser_id = $list['endorser_id'];
                        $upline_id = $list['upline_id'];
                        
                        $retval = Transactions::process_loan_direct($member_id,$endorser_id,$upline_id);

                        if(!$retval)
                        {
                            //add to auditlogs
                            $audit->log_message = 'Loan direct processing successful.';

                        }
                        else
                        {
                            //add to auditlogs
                            $audit->log_message = 'Loan direct processing failed.';
                            $audit->status = 2;
                        }
                    }
                    
                    //Delete process id
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                }
                else
                {
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                    
                }
           
            }
            else
            {
                
                $audit->log_message = 'Loan process PID file still exist. Please wait current process to finish. ';
                echo $audit->log_message;
            }
            
            $audit->log_message = 'Loan direct processing job has ended.';
            
        }
        else
        {
            echo 'Job scheduler is disabled.';
        }
        
        $audit->log_cron();
        echo $this->_curdate . ' : ' . $audit->log_message . '<br />';
    }
    
    public function actionLoanCompletion()
    {
        
        if($this->job_enabled())
        {
            $model = new MembersModel();
            $audit = new AuditLog();
            
            $this->PIDFile = 'LoanCompletion.pid';
            $audit->job_id = self::JOB_LOAN_COMPLETION;

            if(!$this->PID_exists())
            {
                               
                //add to auditlogs
                $audit->log_message = 'Started processing Loan completion job.';
                $audit->log_cron();

                //Create pid file      
                $this->createPID();
                $audit->log_message = 'Created '.$this->PIDFile.' file';
                $audit->log_cron();
                
                $model->status = 4;//Already processed by loan direct job
                $lists = $model->getUnprocessedMembers();
                
                if(count($lists)>0)
                {
                    foreach($lists as $list)
                    {
                        $member_id = $list['member_id'];         
                        
                        $retval = Transactions::process_loan_completion($member_id);
                        
                        if(!$retval)
                        {
                            //add to auditlogs
                            $audit->log_message = 'Loan completion processing successful.';

                        }
                        else
                        {
                            //add to auditlogs
                            $audit->log_message = 'Loan completion processing failed.';
                            $audit->status = 2;
                        }
                    }
                    
                    //Delete process id
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                }
                else
                {
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                    
                }
           
            }
            else
            {
                
                $audit->log_message = 'Loan process PID file still exist. Please wait current process to finish. ';
                echo $audit->log_message;
            }
            
            $audit->log_message = 'Loan completion processing job has ended.';

        }
        else
        {
            echo 'Job scheduler is disabled.';
        }
        
        $audit->log_cron();
        echo $this->_curdate . ' : ' . $audit->log_message . '<br />';

    }
    
    public function actionPromoCheck()
    {
        if($this->job_enabled())
        {
            $model = new MembersModel();
            $audit = new AuditLog();
            $promo = new Bonus();
            
            $this->PIDFile = 'Promo.pid';
            $audit->job_id = self::JOB_PROMO;

            if(!$this->PID_exists())
            {
                               
                //add to auditlogs
                $audit->log_message = 'Started running promo checker.';
                $audit->log_cron();

                //Create pid file      
                $this->createPID();
                $audit->log_message = 'Created '.$this->PIDFile.' file';
                $audit->log_cron();
                
                //Get active promo
                $promos = $promo->getActivePromo();
                
                if(count($promos)>0)
                {
                    //Get option ids for mechanics ei. minimum member count in X months
                    $min_count = $promos[0]['option_id_1']; //minimum ibo count
                    $interval = $promos[0]['option_id_2']; //duration in months
                    $promo->promo_id = $promos[0]['promo_id'];
                    
                    $result = $model->getMemberNetworkCount($interval, $min_count);
                    
                    if(count($result)>0)
                    {
                        foreach($result as $row)
                            $retval = $promo->redeemPromo($row);
                        
                        if(count($retval)>0)
                        {
                            //add to auditlogs
                            $audit->log_message = 'A member has completely satisfied the promo requirements.';
                            $audit->log_cron();

                        }
                        else
                        {
                            //add to auditlogs
                            $audit->log_message = 'Promo check failed to process and redeem the promo. '. $promo->getErrors();
                            $audit->status = 2;
                            $audit->log_cron();
                            echo $audit->log_message;
                            Yii::app()->end();
                        }
                    }
                }
                else
                {
                    
                    $audit->log_message = 'No active promos found.';
                    $audit->log_cron();
                    echo $audit->log_message;
                    Yii::app()->end();
                }
                                   
                //Delete process id
                $this->PID->delete();
                $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                $audit->log_cron();
                
                
            }
            else
            {
                $audit->log_message = 'Promo process still exist. Please wait current process to finish. ';
                $audit->log_cron();                
                echo $audit->log_message;
                Yii::app()->end();
            }
           
            
            $audit->log_message = 'Processing job has ended.';
            $audit->log_cron();
            echo $audit->log_message;
            Yii::app()->end();
        }
        else
        {
            echo 'Job scheduler is disabled.';
            Yii::app()->end();
        }
    }
    
    public function actionSendmail()
    {
        
        if($this->mailer_on())
        {
            $model = new EmailMessages();
        
            $queue = $model->get_email_queue();

            if(count($queue)>0)
            {
                foreach($queue as $email)
                {
                    $message_ids[] = $email['email_message_id'];
                    $sender = $email['sender'];
                    $sender_name = $email['sender_name'];
                    $recipient = $email['recipient'];
                    $subject = $email['email_subject'];
                    $message_body = $email['message_body'];
                    $emails[] = $email['recipient'];

                    Mailer::sendMails($sender, $sender_name, $recipient, $subject, $message_body);
                }

                $model->update_message_status($message_ids);
                
                echo 'All queued mails were sent.<br />';
                echo 'Email lists:<br />';
                echo '<pre>'.print_r($emails).'</pre>';
            }
            else
            {
                echo 'No mails to send.';
            }

        }
        else
        {
            echo 'Mailer is currently disabled.';
        }
        
    }
    
    /**
     * Automatically approve new placement not approved within N days of placements
     */
    public function actionAutoApprove()
    {
        if($this->job_enabled())
        {
            $model = new PlacementModel();
            $audit = new AuditLog();
            
            $unapproved_members = $model->getFloatingPlacements();
            
            if(count($unapproved_members)>0)
            {
                foreach($unapproved_members as $row)
                {
                    $model->member_id = $row['member_id'];
                    $model->upline_id = $row['upline_id'];    
                    $model->endorser_id = $row['endorser_id'];
                    $model->placeUnder();
                    if(!$model->hasErrors())
                    {
                        $audit->log_message = "Member ".$row['member_id']." was auto-approved on ".$this->_curdate." under upline ".$row['upline_id'].".";
                    }
                    else
                    {
                        $audit->log_message = $model->getErrors();
                    }
                        
                }
                
                $audit->log_cron();
                echo $audit->log_message;
            }
            else
            {
                echo 'No members found to auto-approve.';
            }
        }
    }
    
    public function actionUpdateRunningAccounts()
    {
       $member_model = new MembersModel();
       $model = new DirectEndorsement();
       
       $members = $member_model->getAllMembersByID();
       $member_model->alterTable1();
       foreach($members as $member)
       {
            $member_id = $member['member_id'];
            $member_model->member_id = $member_id;
            
            $direct = $model->getDirectEndoserCountByID($member_id);
            $downlines = Networks::getDownlines($member_id);
            
            $direct_count = $direct['total'];
            if(empty($direct_count)) $direct_count = 0;
            $downline_count = count($downlines);
            if(empty($downline_count)) $downline_count = 0;
            $member_model->updateRunningAccount($direct_count, $downline_count);

       }
       $member_model->alterTable2();
       echo 'Update done';       
       
    }
    
    public function actionRotateLog()
    {
        $model = new Logs();
        $model->log_rotate();
        
        if(!$model->hasErrors())
            echo 'Log rotation completed';
        else
            echo $model->getErrors ();
    }
    
    public function actionRepeatPurchase()
    {
        if($this->job_enabled())
        {
            $model = new PurchasesModel();
            $audit = new AuditLog();
            
            $this->PIDFile = 'RepeatPurchase.pid';
            $audit->job_id = self::JOB_REPEAT_PURCHASE;

            if(!$this->PID_exists())
            {
                               
                //add to auditlogs
                $audit->log_message = 'Started processing repeat purchase commission.';
                $audit->log_cron();

                //Create pid file      
                $this->createPID();
                $audit->log_message = 'Created '.$this->PIDFile.' file';
                $audit->log_cron();
                
                $lists = $model->get_unprocessed_purchases();
                if(count($lists)>0)
                {
                    foreach($lists as $list)
                    {
                        $distributor_purchases['distributor_id'] = $list['distributor_id'];   
                        $distributor_purchases['total'] = $list['total'];
                        $distributor_purchases['purchase_id'] = $list['purchase_id'];
                        
                        $retval = Transactions::process_repeat_purchase_commission($distributor_purchases);
                        
                        if(!$retval)
                        {
                            //add to auditlogs
                            $audit->log_message = 'Repeat purchase commission processing successful.';

                        }
                        else
                        {
                            //add to auditlogs
                            $audit->log_message = 'Repeat purchase commission processing failed.';
                            $audit->status = 2;
                        }
                    }
                    
                    //Delete process id
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                }
                else
                {
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                    
                }
           
            }
            else
            {
                
                $audit->log_message = 'Repeat purchase PID file still exist. Please wait current process to finish. ';
                echo $audit->log_message;
            }
            
            $audit->log_message = 'Repeat purchase processing job has ended.';

        }
        else
        {
            echo 'Job scheduler is disabled.';
        }
        
        $audit->log_cron();
        echo $this->_curdate . ' : ' . $audit->log_message . '<br />';
    }
    
}
?>
