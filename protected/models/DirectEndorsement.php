<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-11-2014
------------------------*/

class DirectEndorsement extends CFormModel
{
    public $_connection;
    public $member_id;
    public $endorser_id;
    public $cutoff_id;
    
    public function __construct()
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function getDirectEndorsement($dateFrom, $dateTo)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    d.direct_endorsement_id,
                    CONCAT(md.last_name, ', ', md.first_name) AS member_name,
                    DATE_FORMAT(d.date_created,'%d %b %Y') AS date_created,
                    DATE_FORMAT(d.date_approved,'%d %b %Y') AS date_approved,
                    CONCAT(md2.last_name, ', ', md2.first_name) AS approved_by,
                    DATE_FORMAT(d.date_claimed,'%d %b %Y') AS date_claimed,
                    CONCAT(md3.last_name, ', ', md3.first_name) AS claimed_by,
                    CONCAT(md4.last_name, ', ', md4.first_name) AS endorser_name,
                    d.status,
                    COUNT(d.endorser_id) AS ibo_count,
                    d.cutoff_id
                  FROM direct_endorsements d
                    LEFT OUTER JOIN member_details md
                      ON d.member_id = md.member_id
                    LEFT OUTER JOIN member_details md2
                      ON d.approved_by_id = md2.member_id
                    LEFT OUTER JOIN member_details md3
                      ON d.claimed_by_id = md3.member_id
                    LEFT OUTER JOIN member_details md4
                      ON d.endorser_id = md4.member_id
                  WHERE d.date_created BETWEEN :dateFrom AND :dateTo
                  GROUP BY d.endorser_id
                  ORDER BY d.date_created DESC;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':dateFrom', $dateFrom);
        $command->bindParam(':dateTo', $dateTo);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function updateDirectEndorsementStatus($direct_endorsement_id, $status, $userid)
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();
        
        if ($status == 1)
        {
            $query = "UPDATE direct_endorsements
                        SET date_approved = NOW(),
                            status = :status,
                            approved_by_id = :userid
                        WHERE direct_endorsement_id = :direct_endorsement_id;";
        }
        else if ($status == 2)
        {
            $query = "UPDATE direct_endorsements
                        SET date_claimed = NOW(),
                            status = :status,
                            claimed_by_id = :userid
                        WHERE direct_endorsement_id = :direct_endorsement_id;";
        }
        
        ////***get cutoff id and endorser_id****////
//        UPDATE direct_endorsements de SET de.status = 1
//  where de.endorser_id = 5 and de.cutoff_id = 4;
        
        $command = $conn->createCommand($query);
        
        $command->bindParam(':direct_endorsement_id', $direct_endorsement_id);
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
    public function getDirectEndoserCountByID($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    COUNT(*) AS total
                  FROM members m
                  WHERE m.endorser_id = :member_id
                  GROUP BY m.endorser_id;;";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryRow();
        
        return $result;
    }
    
    public function getDirectEndorser($member_id)
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
    
    public function check_transactions($endorsers,$cutoff_id)
    {
        $conn = $this->_connection;
        
        $member_ids = implode(',',$endorsers);
        
        $query = "SELECT * FROM direct_endorsements 
                    WHERE member_id IN ($member_ids)
                        AND cutoff_id = :cutoff_id
                        AND status = 0
                    ";
        $command = $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $cutoff_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function add_transactions()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
                
        $query = "INSERT INTO direct_endorsements (cutoff_id,endorser_id,member_id) 
                        VALUES (:cutoff_id, :endorser_id, :member_id)";
                 
        $command = $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $command->bindParam(':endorser_id', $this->endorser_id);
        $command->bindParam(':member_id', $this->member_id);
        $result = $command->execute();        
        
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
    
}
?>
