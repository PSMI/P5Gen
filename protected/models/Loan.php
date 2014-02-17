<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-06-2014
------------------------*/

class Loan extends CFormModel
{   
    public $_connection;
    
    public function __construct() 
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function getLoanApplications()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    l.loan_id,
                    CONCAT(m.last_name, ', ', m.first_name, ' ', m.middle_name) AS member_name,
                    l.loan_type_id,
                    l.level_no,
                    l.loan_amount,
                    l.ibo_count,
                    l.date_created,
                    l.date_completed,
                    l.date_approved,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS approved_by,
                    l.status
                  FROM loans l
                    INNER JOIN member_details m
                      ON l.member_id = m.member_id
                    LEFT OUTER JOIN member_details md ON l.approved_by_id = md.member_id
                  WHERE status IN (1, 2, 3);";
        
        $command =  $conn->createCommand($query);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function updateLoanStatus($loan_id, $status, $userid)
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();
        
        $query = "UPDATE loans
                    SET date_approved = NOW(),
                        status = :status,
                        approved_by_id = :userid
                    WHERE loan_id = :loan_id;";
        
        $command = $conn->createCommand($query);
        
        $command->bindParam(':loan_id', $loan_id);
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
