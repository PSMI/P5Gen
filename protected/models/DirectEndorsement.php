<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-11-2014
------------------------*/

class DirectEndorsement extends CFormModel
{
    public $_connection;
    
    public function __construct()
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function getDirectEndorsement($dateFrom, $dateTo)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    d.direct_endorsement_id,
                    CONCAT(m.last_name, ', ', m.first_name, ' ', m.middle_name) AS endorser_name,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS member_name,
                    d.date_created,
                    d.date_released,
                    CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS released_by,
                    d.status
                  FROM direct_endorsements d
                    INNER JOIN member_details m
                      ON d.endorser_id = m.member_id
                    LEFT OUTER JOIN member_details md
                      ON d.endorser_id = md.member_id
                    LEFT OUTER JOIN member_details md2
                      ON d.released_by_id = md2.member_id
                  WHERE date_created BETWEEN :dateFrom AND :dateTo;";
        
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
        
        $query = "UPDATE direct_endorsements
                    SET date_released = NOW(),
                        status = :status,
                        released_by_id = :userid
                    WHERE direct_endorsement_id = :direct_endorsement_id;";
        
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
}
?>
