<?php

/*
 * @author : owliber
 * @date : 2014-02-02
 */

class ReferenceModel extends CFormModel
{
    public $_connection;
        
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function get_variable_value($param)
    {
        $conn = $this->_connection;
        $query = "SELECT variable_value FROM ref_variables WHERE variable_name = :param";
        $command = $conn->createCommand($query);
        $command->bindParam(':param', $param);
        $result = $command->queryRow();
        return $result['variable_value'];
    }
}
?>
