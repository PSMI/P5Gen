<?php

/* 
 * @author : owliber
 * @date : 2014-02-14
 * @var UnprocessedMemberModel
 * Jobs schedule to run hourly
 */

class CronController extends Controller
{
    const JOB_GOC = 1;
    const JOB_LOAN = 2;
    const JOB_DIRECT = 3;
    const JOB_UNILEVEL = 4;
    
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
        
    /**
     * Run GOC job
     */
    public function actionGOC()
    {
        //Instantiate models
        $model = new UnprocessedMemberModel();
        $goc = new GOCModel();
         
        $this->PIDFile = 'GOCJOB.pid';
        $model->job_id = self::JOB_GOC;
        
        if(!$this->PID_exists())
        {
            //add to auditlogs
            $model->log_message = 'Started processing GOC job.';
            $model->log();
            
            //Create pid file      
            $this->createPID();
            $model->log_message = 'Created '.$this->PIDFile.' file';
            $model->log();
                        
            $lists = $model->getList();

            foreach($lists as $list)
            {
                $goc->member_id = $list['member_id'];
                $goc->endorser_id = $list['endorser_id'];
                $goc->upline_id = $list['upline_id'];

                $retval = $goc->process();

                if(!$retval)
                {
                    //add to auditlogs
                    $model->log_message = 'GOC processing successful.';
                    $model->log();

                }
                else
                {
                    //add to auditlogs
                    $model->log_message = 'GOC processing failed.';
                    $model->status = 2;
                    $model->log();
                }

            }
            
            //Delete process id
            $this->PID->delete();
            $model->log_message = 'Deleted '.$this->PIDFile.' file';
            $model->log();
            
        }
        $model->log_message = 'Processing job has ended.';
        $model->log();
        
        echo 'GOC job has finished processing members : '. CJSON::encode($lists);
    }
    
    public function actionSendmail()
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

                Mailer::sendMails($sender, $sender_name, $recipient, $subject, $message_body);
            }

            $model->update_message_status($message_ids);
        }
        
        echo 'All queued mails were sent.';
        
    }
    
}
?>
