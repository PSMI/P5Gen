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
    
    public function getIPDEndorsers($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT                 
                      ipd_endorser_id as endorser
                  FROM members m
                  WHERE m.member_id = :member_id;";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryRow();
        
        return $result;
    }
    
    public function getIPDEndorserCount($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT count(member_id) as count 
                    FROM members
                    WHERE ipd_endorser_id = :member_id 
                        AND placement_status = 1";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryRow();
        
        return $result["count"];
    }
    
}
?>
