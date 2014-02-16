<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-15-2014
------------------------*/

class CronLoanDirect extends CFormModel
{  
    public $_connection;
    public $member_id;
    public $ibo_count;
    public $status;
    public $loan_id;
    
    public function __construct() 
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function getDirectEndorse($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    direct_endorse
                  FROM running_accounts
                  WHERE member_id = :member_id;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function checkIfLoanExist()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    loan_id, ibo_count
                  FROM loans
                  WHERE member_id = :member_id
                  AND loan_type_id = 1
                  AND status = 0;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function insertLoan()
    {
        $conn = $this->_connection;
        
        $trans = $conn->beginTransaction();
        
        $query = "INSERT INTO loans (member_id, loan_type_id, level_no, loan_amount, ibo_count) 
                    VALUES (:member_id, 1, 1, 5000.00, 1)";
            
        $command = $conn->createCommand($query);
        $command->bindValue(':member_id', $this->member_id);

        $command->execute();
        
        try
        {
            $trans->commit();
            
            return true;
        }
        catch (CDbException $e)
        {
            $trans->rollback();
            
            return false;
        }
    }
    
    public function updateLoanDirectCompleted($ibo_count, $status, $loan_id)
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();

        $query = "UPDATE loans
                SET ibo_count = :ibo_count,
                    status = :status,
                    date_completed = NOW()
                WHERE loan_id = :loan_id;";

        $command = $conn->createCommand($query);
        
        $command->bindParam(':ibo_count', $ibo_count);
        $command->bindParam(':status', $status);
        $command->bindParam(':loan_id', $loan_id);

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
    
    public function updateLoanDirectIbo($ibo_count, $status, $loan_id)
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();

        $query = "UPDATE loans
                SET ibo_count = :ibo_count,
                    status = :status
                WHERE loan_id = :loan_id;";

        $command = $conn->createCommand($query);
        
        $command->bindParam(':ibo_count', $ibo_count);
        $command->bindParam(':status', $status);
        $command->bindParam(':loan_id', $loan_id);

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
    
    public function getOverallIboCount()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    SUM(ibo_count) AS total_ibo
                  FROM loans
                  WHERE member_id = :member_id
                  AND loan_type_id = 1
                  AND status IN (0, 1, 2, 3);";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        
        $result = $command->queryAll();
        
        return $result;
    }
}
?>