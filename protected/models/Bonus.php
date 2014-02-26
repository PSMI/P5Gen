<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-11-2014
------------------------*/

class Bonus extends CFormModel
{
    public $_connection;
    public $member_id;
    public $promo_id;
    
    public function __construct()
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function getBonus()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    pr.promo_redemption_id,
                    p.promo_name,
                    CONCAT(m.last_name, ', ', m.first_name, ' ', m.middle_name) AS member_name,
                    pr.ibo_count,
                    pr.date_approved,
                    pr.date_claimed,
                    pr.date_completed,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS approved_by,
                    CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS claimed_by,
                    pr.status,
                    pr.member_id
                  FROM promo_redemption pr
                    INNER JOIN promos p
                      ON pr.promo_id = p.promo_id
                    LEFT OUTER JOIN member_details m
                      ON pr.member_id = m.member_id
                    LEFT OUTER JOIN member_details md
                      ON pr.approved_by_id = md.member_id
                    LEFT OUTER JOIN member_details md2
                      ON pr.claimed_by_id = md2.member_id ORDER BY pr.date_claimed DESC;";
        
        $command =  $conn->createCommand($query);
        
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function updateBonusStatus($promo_redemption_id, $status, $userid)
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();
        
        if ($status == 2)
        {
            $query = "UPDATE promo_redemption
                        SET date_approved = NOW(),
                            status = :status,
                            approved_by_id = :userid
                        WHERE promo_redemption_id = :promo_redemption_id;";
        }
        else if ($status == 3)
        {
            $query = "UPDATE promo_redemption
                        SET date_claimed = NOW(),
                            status = :status,
                            claimed_by_id = :userid
                        WHERE promo_redemption_id = :promo_redemption_id;";
        }
        
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
    
    public function getActivePromo()
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM promos WHERE status = 1";
        $command = $conn->createCommand($query);
        $result = $command->queryAll();
        return $result;
    }
    
    public function redeemPromo($details)
    {
        $conn = $this->_connection;        
        $trx = $conn->beginTransaction();
        
        $promo_id = $this->promo_id;
        $member_id = $details['member_id'];
        $member_count = $details['total_member'];
        $date_joined = $details['date_joined'];
        $date_completed = $details['date_completed'];
        $promo_end_date = $details['promo_end_date'];
                
        $query = "INSERT IGNORE INTO promo_redemption (promo_id, member_id, ibo_count, date_joined, date_completed, promo_end_date)
                  VALUES (:promo_id, :member_id, :ibo_count, :date_joined, :date_completed, :promo_end_date)";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':promo_id', $promo_id);
        $command->bindParam(':member_id', $member_id);
        $command->bindParam(':ibo_count', $member_count);
        $command->bindParam(':date_joined', $date_joined);
        $command->bindParam(':date_completed', $date_completed);
        $command->bindParam(':promo_end_date', $promo_end_date);
        $result = $command->execute();
        
        try
        {
            if(count($result)>0)
            {
                $trx->commit ();
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
