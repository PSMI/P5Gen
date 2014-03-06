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
                    DATE_FORMAT(c.date_created,'%d-%m-%Y') AS date_created,
                    DATE_FORMAT(c.date_approved,'%d-%m-%Y') AS date_approved,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS approved_by,
                    DATE_FORMAT(c.date_claimed,'%d-%m-%Y') AS date_claimed,
                    CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS claimed_by,
                    c.status
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
    
}
?>
