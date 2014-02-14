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
    
    public function get_message_template($template_id)
    {
        $conn = $this->_connection;
        $query = "SELECT * FROM ref_message_template WHERE message_template_id = :template_id";
        $command = $conn->createCommand($query);
        $command->bindParam(':template_id', $template_id);
        $result = $command->queryRow();
        return $result['message_template'];
    }
    
    public function getCutOffDate($trans_type)
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM ref_cutoffs 
                    WHERE transaction_type_id = :trans_type_id 
                    AND status = 1
                  ORDER BY cutoff_id DESC
                  LIMIT 1";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':trans_type_id', $trans_type);
        $result = $command->queryRow();
        return $result;
    }
}
?>
