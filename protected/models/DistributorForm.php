<?php

/**
 * @author Noel Antonio
 * @date 03-26-2014
 */

class DistributorForm extends CFormModel
{
    public $_connection;
    public $distributor_id;
    public $distributor_name;
    public $activation_code;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    
    public function selectDistributorName($distributor_id)
    {
        $connection = $this->_connection;
        
        $sql = "SELECT a.member_id, a.status, b.last_name, b.middle_name, b.first_name
                FROM members a 
                INNER JOIN member_details b ON a.member_id = b.member_id
                WHERE a.member_id = :member_id
                    AND account_type_id = 5";
        $command = $connection->createCommand($sql);
        $command->bindParam(":member_id", $distributor_id);
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
    public function autoCompleteSearch($filter)
    {
        $conn = $this->_connection;        
        $filter = "%".$filter."%";                      
        $query = "SELECT
                    m.member_id,
                    CONCAT(COALESCE(md.last_name,' '), ', ', COALESCE(md.first_name,' '), ' ', COALESCE(md.middle_name,' ')) AS member_name
                  FROM members m
                    INNER JOIN member_details md ON m.member_id = md.member_id
                  WHERE (md.last_name LIKE :filter
                    OR md.first_name LIKE :filter
                    OR md.middle_name LIKE :filter)
                    AND m.account_type_id = 5
                  ORDER BY md.last_name";
        $command = $conn->createCommand($query);
        $command->bindParam(':filter', $filter);
        $result = $command->queryAll();        
        return $result;
    }
}
?>
