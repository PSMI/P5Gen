<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class AccountTypes extends CFormModel
{
    public $_connection;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function selectAllAccountTypes()
    {
        $connection = $this->_connection;
        
        $sql = "SELECT * FROM account_types";
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();
        
        return $result;
    }
}
?>
