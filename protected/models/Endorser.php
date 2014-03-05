<?php

/*
 * @author : owliber
 * @date : 2014-02-06
 */

class Endorser extends CFormModel
{
    public $_connection;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function getEndorsers($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT                 
                      endorser_id as endorser
                  FROM members m
                  WHERE m.member_id = :member_id;";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryRow();
        
        return $result;
    }
    
    
}
?>
