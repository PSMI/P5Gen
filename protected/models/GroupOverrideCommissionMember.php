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
    
    public function getComissions($dateFrom, $dateTo)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    c.commission_id,
                    CONCAT(m.last_name, ', ', m.first_name, ' ', m.middle_name) AS member_name,
                    c.ibo_count,
                    c.amount,
                    c.date_created,
                    c.date_processed,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS processed_by,
                    c.status
                  FROM commissions c
                    INNER JOIN member_details m
                      ON c.member_id = m.member_id
                    LEFT OUTER JOIN member_details md ON c.processed_by_id = md.member_id
                  WHERE date_created BETWEEN :dateFrom AND :dateTo AND c.status = 1;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':dateFrom', $dateFrom);
        $command->bindParam(':dateTo', $dateTo);
        $result = $command->queryAll();
        
        return $result;
    }
}
?>
