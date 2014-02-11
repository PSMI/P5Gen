<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-11-2014
------------------------*/

class Unilevel extends CFormModel
{
    public $_connection;
    
    public function __construct()
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function getUnilevel($dateFrom, $dateTo)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    u.unilevel_id,
                    u.level_no,
                    u.amount,
                    u.date_created,
                    u.date_released,
                    CONCAT(m.last_name, ', ', m.first_name, ' ', m.middle_name) AS released_by,
                    u.status
                  FROM unilevel u
                    LEFT OUTER JOIN member_details m
                      ON u.released_by_id = m.member_id
                  WHERE date_created BETWEEN :dateFrom AND :dateTo;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':dateFrom', $dateFrom);
        $command->bindParam(':dateTo', $dateTo);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function updateUnilevelStatus($unilevel_id, $status, $userid)
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();
        
        $query = "UPDATE unilevel
                    SET date_released = NOW(),
                        status = :status,
                        released_by_id = :userid
                    WHERE unilevel_id = :unilevel_id;";
        
        $command = $conn->createCommand($query);
        
        $command->bindParam(':unilevel_id', $unilevel_id);
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
