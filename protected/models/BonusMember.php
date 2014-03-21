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
    
    public function getBonus($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    pr.promo_redemption_id,
                    p.promo_name,
                    CONCAT(m.last_name, ', ', m.first_name, ' ', m.middle_name) AS member_name,
                    pr.ibo_count,
                    DATE_FORMAT(pr.date_approved,'%M %d, %Y') AS date_approved,
                    DATE_FORMAT(pr.date_claimed,'%M %d, %Y') AS date_claimed,
                    DATE_FORMAT(pr.date_completed,'%M %d, %Y') AS date_completed,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS approved_by,
                    CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS claimed_by,
                    pr.status
                  FROM promo_redemption pr
                    INNER JOIN promos p
                      ON pr.promo_id = p.promo_id
                    LEFT OUTER JOIN member_details m
                      ON pr.member_id = m.member_id
                    LEFT OUTER JOIN member_details md
                      ON pr.approved_by_id = md.member_id
                    LEFT OUTER JOIN member_details md2
                      ON pr.claimed_by_id = md2.member_id WHERE pr.member_id = :member_id ORDER BY pr.date_claimed DESC;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getActivePromo()
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM promos
                    WHERE status = 1
                     ORDER BY promo_id DESC
                      LIMIT 1";
        
        $command = $conn->createCommand($query);
        return $command->queryRow();
    }
}
?>
