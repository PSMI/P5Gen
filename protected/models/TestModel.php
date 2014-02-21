<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-12-2014
------------------------*/

class TestModel extends CFormModel
{ 
    public $_connection;
    
    public function __construct() 
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function checkIfLoanExistWithLevel($member_id, $level_no)
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
        $command->bindParam(':member_id', $member_id);
        $command->bindParam(':level', $level_no);
        
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function checkIfLoanExistWithLevelCompletion($member_id, $level_no)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    loan_id
                  FROM loans
                  WHERE member_id = :member_id
                  AND level_no = :level
                  AND loan_type_id = 2
                  AND status IN (1, 2, 3);";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $command->bindParam(':level', $level_no);
        
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getTotalEntries($level_no)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    total_entries, loan
                  FROM ref_matrix_table
                  WHERE level_no = :level;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':level', $level_no);
        
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function insertLoan($member_id, $level, $amount, $total_members)
    {
        $conn = $this->_connection;
        
        $trans = $conn->beginTransaction();
        
        $query = "INSERT INTO loans (member_id, loan_type_id, level_no, loan_amount, ibo_count) 
                    VALUES (:member_id, 2, :level, :amount, :total_members)";
            
        $command = $conn->createCommand($query);
        $command->bindValue(':member_id', $member_id);
        $command->bindValue(':level', $level);
        $command->bindValue(':amount', $amount);
        $command->bindValue(':total_members', $total_members);

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
    
    public function insertLoanWithCompletion($member_id, $level, $amount, $total_members)
    {
        $conn = $this->_connection;
        
        $trans = $conn->beginTransaction();
        
        $query = "INSERT INTO loans (member_id, loan_type_id, level_no, loan_amount, ibo_count, date_completed) 
                    VALUES (:member_id, 2, :level, :amount, :total_members, NOW())";
            
        $command = $conn->createCommand($query);
        $command->bindValue(':member_id', $member_id);
        $command->bindValue(':level', $level);
        $command->bindValue(':amount', $amount);
        $command->bindValue(':total_members', $total_members);

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
    
    public function updateLoanCompleted($total_members, $status, $loan_id, $level, $amount)
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();
        
        $query = "UPDATE loans
                 SET ibo_count = :total_members,
                    status = :status,
                    date_completed = NOW(),
                    level_no = :level,
                    loan_amount = :amount
                WHERE loan_id = :loan_id;";
        
        $command = $conn->createCommand($query);
        
        $command->bindParam(':total_members', $total_members);
        $command->bindParam(':status', $status);
        $command->bindParam(':loan_id', $loan_id);
        $command->bindParam(':level', $level);
        $command->bindParam(':amount', $amount);

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
    
    public function updateLoanIbo($status, $loan_id, $total_members)
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();

        $query = "UPDATE loans
                SET ibo_count = :total_members,
                    status = :status
                WHERE loan_id = :loan_id;";
        
        $command = $conn->createCommand($query);
        
        $command->bindParam(':total_members', $total_members);
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
    
    public function checkIfDirectEndorse($downline_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    endorser_id
                  FROM members
                  WHERE member_id = $downline_id;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $downline_id);
        
        $result = $command->queryAll();
        
        return $result;
    }
}

?>