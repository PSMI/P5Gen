<?php

/*
 * @author : owliber
 * @date : 2014-02-14
 */

class UnprocessedMemberModel extends CFormModel
{    
    public $_connection;
    public $status;
    public $job_id;
    public $log_message;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function getList()
    {
        $conn = $this->_connection;
        $query = "SELECT * FROM unprocessed_members LIMIT 1";
        $command = $conn->createCommand($query);
        return $command->queryAll();
    }
    
    public function log()
    {
        $conn = Yii::app()->db;
        $query = "INSERT INTO cronlogs (job_id, log_message, status) 
                    VALUES (:job_id, :log_message, :status)";
        
        if(isset($this->status)) $this->status = 1;
        
        $sql = $conn->createCommand($query);
        $sql->bindValue(":job_id", $this->job_id);
        $sql->bindValue(":log_message", $this->log_message);
        $sql->bindValue(":status", $this->status);
        $sql->execute();
        
    }
}
?>
