<?php

/* 
 * @author : owliber
 * @date : 2014-02-14
 * @var UnprocessedMemberModel
 * 
 * JOB SCHEDULE: 5 minute interval
 * JOBS RUN SEQUENCE:
 * 1st Job: GOC - Get all unprocessed members with status = 0, update status to 1 after processing.
 * 2nd Job: Direct Endorsement - Get all unprocessed members with status = 1, update status to 2 after processing
 * 3rd Job: Loan Direct - Get all unprocessed members with status = 2, update status to 3 after processing
 * 4th Job: Loan Completion - Get all unprocessed members with status = 3, update status to 4 after processing
 * 5th Job: Unilevel - Get all unprocessed members with status = 4, update status to 5 after processing.
 * 6th Job: Delete all processed members with status = 5;
 * 
 * CRON PATH:
 * /cron/goc
 * /cron/directendorse
 * /cron/loandirect
 * /cron/loancompletion
 * /cron/unilevel
 * /cron/sendmail
 * 
 */

class CronController extends Controller
{
    const JOB_GOC = 1;
    const JOB_LOAN_COMPLETION = 2;
    const JOB_LOAN_DIRECT = 3;
    const JOB_DIRECT_ENDORSEMENT = 4;
    const JOB_UNILEVEL = 5;
    
    public $PID;
    public $PIDFile;
    public $PIDLog;
    
    /**
     * Check if PID file exist
     * @return boolean
     */
    public function PID_exists()
    {
        $file = Yii::app()->file;
        $path = Yii::app()->basePath . '\runtime\\';
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
        
    /**
     * Run GOC job
     */
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
                        $endorser_id = $list['endorser_id'];
                        $upline_id = $list['upline_id'];
        
                        $retval = Transactions::process_goc($member_id, $endorser_id, $upline_id);
                        
                        if(!$retval)
                        {
                            //add to auditlogs
                            $audit->log_message = 'GOC processing successful.';
                            $audit->log_cron();

                        }
                        else
                        {
                            //add to auditlogs
                            $audit->log_message = 'GOC processing failed.';
                            $audit->status = 2;
                            $audit->log_cron();
                            echo $audit->log_message;
                        }

                    }

                    //Delete process id
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                    $audit->log_cron();
                    
                }
                else
                {
                    
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                    $audit->log_cron();
                    
                    echo 'No new record to process.';
                    Yii::app()->end();
                }

            }
            else
            {
                
                $audit->log_message = 'GOC PID file still exist. Please wait current process to finish. ';
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
                            $audit->log_cron();

                        }
                        else
                        {
                            //add to auditlogs
                            $audit->log_message = 'Direct endorsement processing failed for MID '.$member_id.' EID '.$endorser_id;
                            $audit->status = 2;
                            $audit->log_cron();
                            echo $audit->log_message;
                        }
                    }
                    
                    //Delete process id
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                    $audit->log_cron();
                }
                else
                {
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                    $audit->log_cron();
                    
                    echo 'No new record to process.';
                    Yii::app()->end();
                }
           
            }
            else
            {
                
                $audit->log_message = 'Direct endorsement process PID file still exist. Please wait current process to finish. ';
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
                
                $model->status = 2; //Already processed by Direct endorsement job
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
                            $audit->log_cron();

                        }
                        else
                        {
                            //add to auditlogs
                            $audit->log_message = 'Loan direct processing failed.';
                            $audit->status = 2;
                            $audit->log_cron();
                            echo $audit->log_message;
                        }
                    }
                    
                    //Delete process id
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                    $audit->log_cron();
                }
                else
                {
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                    $audit->log_cron();
                    
                    echo 'No new record to process.';
                    Yii::app()->end();
                }
           
            }
            else
            {
                
                $audit->log_message = 'Loan process PID file still exist. Please wait current process to finish. ';
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
                
                $lists = $model->getUnprocessedMembers();
                
                if(count($lists)>0)
                {
                    foreach($lists as $list)
                    {
                        $member_id = $list['member_id'];         
                        $endorser_id = $list['endorser_id'];
                        $upline_id = $list['upline_id'];
                        
                        $retval = Transactions::process_loan_completion($member_id,$endorser_id,$upline_id);

                        if(!$retval)
                        {
                            //add to auditlogs
                            $audit->log_message = 'Loan completion processing successful.';
                            $audit->log_cron();

                        }
                        else
                        {
                            //add to auditlogs
                            $audit->log_message = 'Loan completion processing failed.';
                            $audit->status = 2;
                            $audit->log_cron();
                            echo $audit->log_message;
                        }
                    }
                    
                    //Delete process id
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                    $audit->log_cron();
                }
                else
                {
                    $this->PID->delete();
                    $audit->log_message = 'Deleted '.$this->PIDFile.' file';
                    $audit->log_cron();
                    
                    echo 'No new record to process.';
                    Yii::app()->end();
                }
           
            }
            else
            {
                
                $audit->log_message = 'Loan process PID file still exist. Please wait current process to finish. ';
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
                echo '<pre>'.$emails.'</pre>';
            }
            else
            {
                echo 'No mails to send.';
            }

            Yii::app()->end();
        }
        else
        {
            echo 'Mailer is currently disabled.';
        }
        
    }
    
}
?>
