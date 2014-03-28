<?php

/**
 * @author Noel Antonio
 * @date 03-26-2014
 */

class DistributorForm extends CFormModel
{
    public $_connection;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    
    public function selectDistributorName($id)
    {
        $connection = $this->_connection;
        
        $sql = "SELECT a.distributor_id, a.status, b.last_name, b.middle_name, b.first_name
                FROM distributors a 
                INNER JOIN distributor_details b ON a.distributor_id = b.distributor_id
                WHERE a.distributor_id = :id";
        $command = $connection->createCommand($sql);
        $command->bindParam(":id", $id);
        $result = $command->queryRow();
        
        return $result;
    }
    
    public function selectDistributorDetails($id)
    {
        $connection = $this->_connection;
        
        $sql = "SELECT *
                FROM distributors a 
                INNER JOIN distributor_details b ON a.distributor_id = b.distributor_id
                WHERE a.distributor_id = :distributor_id";
        $command = $connection->createCommand($sql);
        $command->bindParam(":distributor_id", $id);
        $result = $command->queryRow();
        
        return $result;
    }
}
?>
