<?php

/*
 * @author : owliber
 * @date : 2014-02-15
 */

class EmailMessages extends CFormModel
{
    public $_connection;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
        
    }
    
    public function get_email_queue()
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM email_messages 
                    WHERE status = 0
                    LIMIT 50";
        $command = $conn->createCommand($query);
        $result = $command->queryAll();
        return $result;
    }
    
    public function update_message_status($message_ids)
    {
        $conn = $this->_connection;
        
        $message_ids = implode(',',$message_ids);
        
        $query = "UPDATE email_messages 
                    SET status = 1, 
                        date_sent = now()
                  WHERE email_message_id IN ($message_ids)
                    AND status = 0";
        
        $command = $conn->createCommand($query);
        $command->execute();
    }
    
    public function log_messages($sender, $sender_name, $recipient, $subject, $message_body)
    {
        $conn = $this->_connection;
        
        $query = "INSERT INTO email_messages (sender, sender_name, recipient, email_subject, message_body)
                   VALUES (:sender, :sender_name, :recipient, :email_subject, :message_body)";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':sender', $sender);
        $command->bindParam(':sender_name', $sender_name);
        $command->bindParam(':recipient', $recipient);
        $command->bindParam(':email_subject', $subject);
        $command->bindParam(':message_body', $message_body);
        $command->execute();
    }
    
}
?>
