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
    
    public function getDirectEndorse()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    direct_endorse
                  FROM running_accounts
                  WHERE member_id = :member_id;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        
        $result = $command->queryRow();
        
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
                
        $query = "INSERT INTO loans (member_id, loan_type_id, level_no, loan_amount, ibo_count) 
                    VALUES (:member_id, 1, 1, 5000.00, 1)";
            
        $command = $conn->createCommand($query);
        $command->bindValue(':member_id', $this->member_id);

        $result = $command->execute();
        return $result;
        
       
    }
    
    public function updateLoanDirectCompleted()
    {
        $conn = $this->_connection;
        
        $query = "UPDATE loans
                SET status = :status,
                    date_completed = NOW()
                WHERE loan_id = :loan_id;";

        $command = $conn->createCommand($query);
        
        $command->bindParam(':status', $this->status);
        $command->bindParam(':loan_id', $this->loan_id);

        $result = $command->execute();
        
        return $result;
    }
    
    //public function updateLoanDirectIbo($ibo_count, $status, $loan_id)
    public function updateLoanDirectIbo()
    {
        $conn = $this->_connection;

        $query = "UPDATE loans
                SET ibo_count = ibo_count + 1,
                    status = :status
                WHERE loan_id = :loan_id;";

        $command = $conn->createCommand($query);

        //$command->bindParam(':ibo_count', $this->ibo_count);
        $command->bindParam(':status', $this->status);
        $command->bindParam(':loan_id', $this->loan_id);

        $result = $command->execute();
        
        return $result;
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