<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-12-2014
------------------------*/

class GroupOverrideCommissionMember extends CFormModel
{
    public $_connection;
    
    public function __construct() 
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function getComissions($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    c.commission_id,
                    CONCAT(m.last_name, ', ', m.first_name, ' ', m.middle_name) AS member_name,
                    c.ibo_count,
                    c.amount,
                    DATE_FORMAT(c.date_created,'%M %d, %Y') AS date_created,
                    DATE_FORMAT(c.date_approved,'%M %d, %Y') AS date_approved,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS approved_by,
                    DATE_FORMAT(c.date_claimed,'%M %d, %Y') AS date_claimed,
                    CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS claimed_by,
                    c.status,
                    c.member_id,
                    c.cutoff_id
                  FROM commissions c
                    INNER JOIN member_details m
                      ON c.member_id = m.member_id
                    LEFT OUTER JOIN member_details md ON c.approved_by_id = md.member_id
                    LEFT OUTER JOIN member_details md2 ON c.claimed_by_id = md2.member_id
                  WHERE c.member_id = :member_id ORDER BY c.date_created DESC";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getPayeeDetails($member_id)
    {
        $conn = $this->_connection;
        $query = "SELECT
                        m.username,
                        DATE_FORMAT(m.date_joined,'%M %d, %Y') AS date_joined,
                        md.email,
                        md.mobile_no,
                        md.telephone_no,
                        CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS endorser_name
                    FROM members m
                      INNER JOIN member_details md
                        ON m.member_id = md.member_id
                      LEFT OUTER JOIN member_details md2
                        ON md2.member_id = m.endorser_id
                    WHERE m.member_id = :member_id;";
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        return $result;
    }
    public function getLoanBalance($member_id)
    {
        $conn = $this->_connection;
        $query = "SELECT
                    loan_balance                    
                  FROM loans l
                  WHERE l.member_id = :member_id
                  ORDER BY loan_id DESC limit 1;";
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryRow();
        return $result['loan_balance'];
    }
    public function getPrevousLoans($member_id, $from_cutoff, $to_cutoff)
    {
        $conn = $this->_connection;
        $query = "SELECT
                        SUM(l.loan_amount) AS total_loan
                    FROM loans l
                    WHERE l.status = 3
                    AND date_completed > :from_cutoff AND date_completed <= :to_cutoff
                    AND l.member_id = :member_id;"; 
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $command->bindParam(':from_cutoff', $from_cutoff);
        $command->bindParam(':to_cutoff', $to_cutoff);
        $result = $command->queryAll();
        return $result;
    }
    public function checkIfExistInCutoff($ibo_id, $from_cutoff, $to_cutoff)
    {
        $conn = $this->_connection;
        $query = "SELECT
                        m.member_id
                    FROM members m
                    WHERE m.member_id = :ibo_id
                    AND placement_date > :from_cutoff AND placement_date <= :to_cutoff;";
        $command =  $conn->createCommand($query);
        $command->bindParam(':ibo_id', $ibo_id);
        $command->bindParam(':from_cutoff', $from_cutoff);
        $command->bindParam(':to_cutoff', $to_cutoff);
        $result = $command->queryAll();
        return $result;
    }
    public function getPayeeDownlineDetails($member_id)
    {
        $conn = $this->_connection;
        $query = "SELECT
                        CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS member_name,
                        DATE_FORMAT(m.date_joined,'%M %d, %Y') AS date_joined,
                        CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS upline_name
                    FROM members m
                        INNER JOIN member_details md
                        ON m.member_id = md.member_id
                        LEFT OUTER JOIN member_details md2
                        ON md2.member_id = m.upline_id
                    WHERE m.member_id = :member_id";
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        return $result;
    }
}
?>
