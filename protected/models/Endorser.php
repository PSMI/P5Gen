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
        
//        if(Members::getAccountType($member_id) == 'distributor')
//            $endorser_id = 'ipd_endorser_id';
//        else
//            $endorser_id = 'endorser_id';
        
        $query = "SELECT count(member_id) as count 
                    FROM members
                    WHERE ipd_endorser_id = :member_id 
                        AND placement_status = 1";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryRow();
        
        return $result["count"];
    }
    
    /**
     * This function is used for get the information of the upline
     * of the particular member (either IBO or IPD).
     * @param int $member_id member id.
     * @return array resultset.
     */
    public function getEndorserForIPDUnilevel($member_id)
    {
        $conn = $this->_connection;
        $query = "SELECT endorser_id, ipd_endorser_id FROM members WHERE member_id = :member_id";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryRow();
       
            $endorser_id = $result["ipd_endorser_id"];
       
        if (is_null($endorser_id) || empty($endorser_id))
        {
            $endorser_id = $result["endorser_id"];
        }
        
        $query1 = "SELECT member_id, account_type_id
                  FROM members m
                  WHERE m.member_id = :endorser_id AND placement_status = 1
                  ORDER BY placement_date ASC;";
        $command1 = $conn->createCommand($query1);
        $command1->bindParam(':endorser_id', $endorser_id);
        $result1 = $command1->queryRow();
        return $result1;
    }
    
    /**
     * This function is used to trace the network of a particular IPD.
     * If there are IBO in between, that IBO network should be
     * prioritized in the tracing.
     * @param int $member_id member id.
     * @return array resultset.
     */
    public function getEndorserForRP($member_id)
    {
        $conn = $this->_connection;
        $query = "SELECT account_type_id, endorser_id, ipd_endorser_id 
                    FROM members WHERE member_id = :member_id";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryRow();
        
        $account_type_id = $result['account_type_id'];
        if ($account_type_id == 3) // IBO
        {
            $endorser_id = $result["endorser_id"];
        }
        else
        {
            $endorser_id = $result["ipd_endorser_id"];
        }
        
        $query1 = "SELECT member_id, account_type_id
                  FROM members m
                  WHERE m.member_id = :endorser_id AND placement_status = 1
                  ORDER BY placement_date ASC;";
        $command1 = $conn->createCommand($query1);
        $command1->bindParam(':endorser_id', $endorser_id);
        $result1 = $command1->queryRow();
        
        return $result1;
    }
}
?>
