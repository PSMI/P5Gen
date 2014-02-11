<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-12-2014
------------------------*/

class LoanMember extends CFormModel
{   
    public $_connection;
    
    public function __construct() 
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function getLoanTransactions($dateFrom, $dateTo)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    l.loan_id,
                    CONCAT(m.last_name, ', ', m.first_name, ' ', m.middle_name) AS member_name,
                    l.level_no,
                    l.loan_amount,
                    l.date_created,
                    l.date_approved,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS approved_by,
                    l.status
                  FROM loans l
                    INNER JOIN member_details m
                      ON l.member_id = m.member_id
                    LEFT OUTER JOIN member_details md ON l.approved_by_id = md.member_id
                  WHERE date_created BETWEEN :dateFrom AND :dateTo AND l.status = 1;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':dateFrom', $dateFrom);
        $command->bindParam(':dateTo', $dateTo);
        $result = $command->queryAll();
        
        return $result;
    }
}
?>
