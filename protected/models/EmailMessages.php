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
    
}
?>
