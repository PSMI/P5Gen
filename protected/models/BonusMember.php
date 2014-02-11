<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-11-2014
------------------------*/

class BonusMember extends CFormModel
{
    public $_connection;
    
    public function __construct()
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function getBonus($dateFrom, $dateTo, $member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    pr.promo_redemption_id,
                    p.promo_name,
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
                  WHERE date_redeeemd BETWEEN :dateFrom AND :dateTo AND pr.member_id = :member_id;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':dateFrom', $dateFrom);
        $command->bindParam(':dateTo', $dateTo);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
    }
}
?>
