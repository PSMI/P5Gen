<?php

/**
 * @author Noel Antonio
 * @date 01-27-2014
 */

class ActivationCodeModel extends CFormModel
{
    public $_connection;
    public $quantity;
    
    public function rules()
    {
        return array(
            array('quantity','required'),
        );
    }
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    
    public function getNextBatchId()
    {
        $connection = $this->_connection;
        
        $sql = "SELECT MAX(activation_code_batch_id) AS LastId FROM activation_code_batch;";
        $command = $connection->createCommand($sql);
        $result = $command->queryRow();
        
        return $result["LastId"] + 1;
    }
    
    public function generateActivationCodes($quantity, $aid, $ipaddr)
    {
        $connection = $this->_connection;
        $beginTrans = $connection->beginTransaction();
        $codes = array();
            
        try 
        {
                // insert into activation_code_batch
                $sql = "INSERT INTO activation_code_batch (batch_quantity, generated_by_id, generated_from_ip) VALUES (:qty, :aid, :ip);";
                $command = $connection->createCommand($sql);
                $command->bindValue(':qty', $quantity);
                $command->bindValue(':aid', $aid);
                $command->bindValue(':ip', $ipaddr);
                $rowCount = $command->execute();            
                $last_inserted_id = $connection->getLastInsertId();

                // generate the codes
                $activationCodes = CodeGenerator::generateCode(18, $quantity);
                for($i = 0; $i < $quantity; $i++)
                {
                    $codes[] = "('".$activationCodes[$i]."'," . $last_inserted_id . ")";
                }
                $finalCodes = implode(",", $codes); 

                // insert into activation_codes
                $sql2 = "INSERT INTO activation_codes (activation_code, activation_code_batch_id) VALUES " . $finalCodes;
                $command2 = $connection->createCommand($sql2);
                $rowCount2 = $command2->execute();

                if ($rowCount > 0 && $rowCount2 > 0) {
                    $beginTrans->commit();
                    return true;
                } else {
                    $beginTrans->rollback();  
                    return false;
                }
        } 
        catch (CDbException $e) 
        {
                $beginTrans->rollback();            
                return $e->getMessage();
        }
    }
    
    
    public function selectAll()
    {
        $connection = $this->_connection;
        
        $sql = "SELECT a.activation_code_batch_id, a.batch_quantity, a.date_generated, a.generated_from_ip,
                        CONCAT(b.first_name, ' ', b.last_name) AS member_name
                FROM activation_code_batch a
                INNER JOIN member_details b ON a.generated_by_id = b.member_id
                ORDER BY a.date_generated DESC;";
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();
        
        return $result;
    }
}
?>
