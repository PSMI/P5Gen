<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-20-2014
------------------------*/

class UnilevelMember extends CFormModel
{
    public $_connection;
    public $member_id;
    public $endorser_id;
    public $upline_id;
    public $cutoff_id;
    public $total_direct_endorse;
    
    public function __construct()
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function getUnilevel($dateFrom, $dateTo, $member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    u.unilevel_id,
                    CONCAT(m.last_name, ', ', m.first_name, ' ', m.middle_name) AS member_name,
                    u.ibo_count,
                    u.amount,
                    u.date_created,
                    u.date_approved,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS approved_by,
                    u.date_claimed,
                    CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS claimed_by,
                    u.status
                  FROM unilevel u
                    INNER JOIN member_details m
                      ON u.member_id = m.member_id
                    LEFT OUTER JOIN member_details md
                      ON u.approved_by_id = md.member_id
                    LEFT OUTER JOIN member_details md2
                      ON u.claimed_by_id = md2.member_id
                  WHERE u.date_created BETWEEN :dateFrom AND :dateTo AND u.member_id = :member_id; ORDER BY u.date_created DESC;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':dateFrom', $dateFrom);
        $command->bindParam(':dateTo', $dateTo);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
    }
}
?>
