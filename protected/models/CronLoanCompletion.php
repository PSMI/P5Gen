<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-12-2014
------------------------*/

class CronLoanCompletion extends CFormModel
{  
    public $_connection;
    public $member_id;
    public $level_no;
    public $target_level;
    public $loan_amount;
    public $loan_id;
    public $status;
    public $total_members;
    
    public function __construct() 
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function getTotalMembers()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    total_member
                  FROM running_accounts
                  WHERE member_id = :member_id;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function checkIfLoanExistWithLevel()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    loan_id
                  FROM loans
                  WHERE member_id = :member_id
                  AND level_no = :level
                  AND loan_type_id = 2
                  AND status = 0;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        $command->bindParam(':level', $this->level_no);
        
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function insertLoan()
    {
        $conn = $this->_connection;
        
        $trans = $conn->beginTransaction();
        
        $query = "INSERT INTO loans (member_id, loan_type_id, level_no, loan_amount, ibo_count) 
                    VALUES (:member_id, 2, :level, :amount, :total_members)";
            
        $command = $conn->createCommand($query);
        $command->bindValue(':member_id', $this->member_id);
        $command->bindValue(':level', $this->level_no);
        $command->bindValue(':amount', $this->loan_amount);
        $command->bindValue(':total_members', $this->total_members);

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
    
    public function getTotalEntries()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    total_entries, loan
                  FROM ref_matrix_table
                  WHERE level_no = :level;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':level', $this->level_no);
        
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function updateLoanCompleted()
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();
        
        $query = "UPDATE loans
                 SET ibo_count = :total_members,
                    status = :status,
                    date_completed = NOW(),
                    level_no = :target_level,
                    loan_amount = :loan_amount
                WHERE loan_id = :loan_id;";
        
        $command = $conn->createCommand($query);
        
        $command->bindParam(':total_members', $this->total_members);
        $command->bindParam(':status', $this->status);
        $command->bindParam(':loan_id', $this->loan_id);
        $command->bindParam(':target_level', $this->target_level);
        $command->bindParam(':loan_amount', $this->loan_amount);

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
    
    public function updateLoanIbo()
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();

        $query = "UPDATE loans
                SET ibo_count = ibo_count + 1,
                    status = :status
                WHERE loan_id = :loan_id;";

        $command = $conn->createCommand($query);
        
        //$command->bindParam(':total_members', $this->total_members);
        $command->bindParam(':status', $this->status);
        $command->bindParam(':loan_id', $this->loan_id);

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
