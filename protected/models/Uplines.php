<?php

/*
 * @author : owliber
 * @date : 2014-02-06
 */

class Uplines extends CFormModel
{
    public $_connection;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function getUplines($upline_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT                 
                      upline_id as upline
                  FROM members m
                  WHERE m.member_id = :upline_id;";
        $command = $conn->createCommand($query);
        $command->bindParam(':upline_id', $upline_id);
        $result = $command->queryRow();
        
        return $result;
    }
    
    
}
?>
