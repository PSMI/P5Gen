<?php

/**
 * 
 */
class AccountsModel extends CFormModel
{
    public $_connection;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function selectAllMemberDetails()
    {
        $connection = $this->_connection;
        
        $sql = "SELECT a.member_id, a.last_name, a.first_name, a.middle_name,
                a.birth_date, a.mobile_no, a.email
                FROM member_details a
                INNER JOIN members b ON a.member_id = b.member_id
                WHERE b.account_type_id = 2";
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();
        
        return $result;
    }
}
?>
