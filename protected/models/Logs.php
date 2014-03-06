<?php

/*
 * @author : owliber
 * @date : 2014-03-06
 */

class Logs extends CFormModel
{
    public $_connection;
    
    public function __construct()
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function log_rotate()
    {
        $conn = $this->_connection;
        $query = "TRUNCATE TABLE cronlogs";
        $command = $conn->createCommand($query);
        $command->execute();
    }
}
?>
