<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-11-2014
------------------------*/

class Bonus extends CFormModel
{
    public $_connection;
    
    public function __construct()
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function getBonus($dateFrom, $dateTo)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    pr.promo_redemption_id,
                    p.promo_name,
                    CONCAT(m.last_name, ', ', m.first_name, ' ', m.middle_name) AS member_name,
                    pr.ibp_count,
                    pr.date_redeeemd,
                    pr.date_released,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS released_by,
                    pr.status
                  FROM promo_redemption pr
                    INNER JOIN promos p
                      ON pr.promo_id = p.promo_id
                    LEFT OUTER JOIN member_details m
                      ON pr.member_id = m.member_id
                    LEFT OUTER JOIN member_details md
                      ON pr.released_by_id = md.member_id
                  WHERE date_redeeemd BETWEEN :dateFrom AND :dateTo;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':dateFrom', $dateFrom);
        $command->bindParam(':dateTo', $dateTo);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function updateBonusStatus($promo_redemption_id, $status, $userid)
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();
        
        $query = "UPDATE promo_redemption
                    SET date_released = NOW(),
                        status = :status,
                        released_by_id = :userid
                    WHERE promo_redemption_id = :promo_redemption_id;";
        
        $command = $conn->createCommand($query);
        
        $command->bindParam(':promo_redemption_id', $promo_redemption_id);
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