<?php

/*
 * @author : owliber
 * @date : 2014-02-03
 */

class Downlines extends CFormModel
{
    public $_connection;
    public $endorser_id;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }   
    
    public function levels($member_id)
    {
        $conn = $this->_connection;
//        if(count($member_id)>1)
//            $member_id = implode(',',$member_id);
        
        $query = "SELECT
                    member_id AS downline
                  FROM members m
                  WHERE m.upline_id IN ($member_id);";
        
        $command = $conn->createCommand($query);
        //$command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();        
        
        return $result;
    }
    
    public function firstLevel($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    member_id AS downline
                  FROM members m
                  WHERE m.upline_id = :member_id;";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();        
        
        return $result;
    }
    
    public function firstFive($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    member_id AS downline
                  FROM members m
                  WHERE m.upline_id = :member_id
                    LIMIT 5;";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();        
        
        if(count($result) < 5)
        {   //include all direct endorse
            $direct_endorsed = Downlines::getDirectEndorsed($member_id);
            $result = array_merge(array('downline'=>$member_id), $direct_endorsed);
        }
        
        return $result;
    }
    
    public function getDirectEndorsed($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    member_id AS downline
                  FROM members m
                  WHERE m.upline_id = :member_id
                    OR m.endorser_id = :member_id;";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function nextLevel($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    m.member_id AS downline
                 FROM members m
                  WHERE m.upline_id IN (SELECT
                    m1.member_id
                  FROM members m1
                  WHERE m1.upline_id IN ($member_id) )";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function nextLessFiveLevel($member_ids)
    {
        $conn = $this->_connection;
        $query = "SELECT
                    m.member_id AS downline
                  FROM members m
                  WHERE m.upline_id IN (SELECT
                    m1.member_id
                  FROM members m1
                  WHERE m1.upline_id IN ($member_ids) )
                  GROUP BY m.member_id
                  HAVING COUNT(m.member_id) < 5";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_ids);
        $result = $command->queryAll();
        
        return $result;
    }
    
}
?>
