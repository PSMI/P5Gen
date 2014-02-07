<?php

/*
 * @author : owliber
 * @date : 2014-02-03
 */

class Downlines extends CFormModel
{
    public $_connection;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function firstLevel($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                   -- endorser_id as endorser,                    
                   -- upline_id as upline,
                    member_id AS downline
                    -- count(m.member_id) AS total
                  FROM members m
                  WHERE m.upline_id = :member_id;";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
        
    }
    
    public function nextLevel($member_ids)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    -- endorser_id AS endorser,
                    -- m.upline_id AS upline,
                    m.member_id AS downline
                    -- count(m.member_id) AS total
                  FROM members m
                  WHERE m.upline_id IN (SELECT
                    m1.member_id
                  FROM members m1
                  WHERE m1.upline_id IN ($member_ids) )";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_ids);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function nextLessFiveLevel($member_ids)
    {
        $conn = $this->_connection;
        $query = "SELECT
                    -- endorser_id AS endorser,
                    -- m.upline_id AS upline,
                    m.member_id AS downline
                    -- count(m.member_id) AS total
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
