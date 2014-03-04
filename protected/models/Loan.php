<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-06-2014
------------------------*/

class Loan extends CFormModel
{   
    public $_connection;
    
    public function __construct() 
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function getLoanApplications()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    l.loan_id,
                    CONCAT(m.last_name, ', ', m.first_name, ' ', m.middle_name) AS member_name,
                    l.loan_type_id,
                    l.level_no,
                    l.loan_amount,
                    DATE_FORMAT(l.date_created,'%d-%m-%Y') AS date_created,
                    DATE_FORMAT(l.date_completed,'%d-%m-%Y') AS date_completed,
                    DATE_FORMAT(l.date_approved,'%d-%m-%Y') AS date_approved,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS approved_by,
                    DATE_FORMAT(l.date_claimed,'%d-%m-%Y') AS date_claimed,
                    CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS claimed_by,
                    l.status,
                    l.member_id
                  FROM loans l
                    INNER JOIN member_details m
                      ON l.member_id = m.member_id
                    LEFT OUTER JOIN member_details md ON l.approved_by_id = md.member_id
                    LEFT OUTER JOIN member_details md2 ON l.claimed_by_id = md2.member_id
                  WHERE l.status IN (1, 2, 3) ORDER BY l.date_completed DESC;";
        
        $command =  $conn->createCommand($query);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function updateLoanStatus($loan_id, $status, $userid)
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();
        
        if ($status == 2)
        {
            $query = "UPDATE loans
                        SET date_approved = NOW(),
                            status = :status,
                            approved_by_id = :userid
                        WHERE loan_id = :loan_id;";
        }
        else if ($status == 3)
        {
            $query = "UPDATE loans
                        SET date_claimed = NOW(),
                            status = :status,
                            claimed_by_id = :userid
                        WHERE loan_id = :loan_id;";
        }
            
        
        $command = $conn->createCommand($query);
        
        $command->bindParam(':loan_id', $loan_id);
        $command->bindParam(':status', $status);
        $command->bindParam(':userid', $userid);

        $result = $command->execute();
        
        try
        {
            if(count($result)>0)
            {
                $trx->commit();
                return true;
            }
            else
            {
                $trx->rollback();
                return false;
            }
        }
        catch(PDOException $e)
        {
            $trx->rollback();
            return false;
        }
    }
    
    public function getLoanCompletionDownlines($downline_ids)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                        CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS member_name,
                        DATE_FORMAT(m.date_created,'%d-%m-%Y') AS date_created,
                        CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS endorser_name
                    FROM members m
                        INNER JOIN member_details md
                        ON m.member_id = md.member_id
                        LEFT OUTER JOIN member_details md2
                        ON md2.member_id = m.endorser_id
                    WHERE m.member_id IN ($downline_ids);";
        
        $command =  $conn->createCommand($query);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getPayeeDetails($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                        m.username,
                        DATE_FORMAT(m.date_created,'%d-%m-%Y') AS date_created,
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
    
    public function getPreviousLoans($member_id, $loan_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                        l.loan_id
                    FROM loans l
                    WHERE l.loan_type_id = 1
                    AND loan_id < :loan_id
                    AND member_id = :member_id;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $command->bindParam(':loan_id', $loan_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getLoanDirectEndorsementDownlines($member_id, $limit)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                        CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS member_name,
                        DATE_FORMAT(m.date_created,'%d-%m-%Y') AS date_created,
                        CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS endorser_name
                    FROM members m
                        INNER JOIN member_details md
                        ON m.member_id = md.member_id
                        LEFT OUTER JOIN member_details md2
                        ON md2.member_id = m.endorser_id
                    WHERE m.endorser_id = :member_id
                    AND placement_status != 0
                    ORDER BY placement_date ASC LIMIT :limit, 5;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $command->bindParam(':limit', $limit);
        $result = $command->queryAll();
        
        return $result;
    }
}
?>
