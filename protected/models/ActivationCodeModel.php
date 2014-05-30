<?php

/**
 * @author Noel Antonio
 * @date 01-27-2014
 */

class ActivationCodeModel extends CFormModel
{
    public $_connection;
    public $quantity;
    public $activation_code;
    public $distribution_tag_id;
    
    public function rules()
    {
        return array(
            array('quantity, distribution_tag_id', 'required'),
            array('quantity', 'numerical', 'integerOnly'=>true)
        );
    }
    public function attributeLabels()
    {
        return array('distribution_tag_id'=>'Distribution Tag');
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
    
    public function generateActivationCodes($distribution_tag_id, $quantity, $aid, $ipaddr)
    {
        $connection = $this->_connection;
        $beginTrans = $connection->beginTransaction();
            
        try 
        {
            // insert into activation_code_batch
            $sql = "INSERT INTO activation_code_batch (batch_quantity, distribution_tag_id, generated_by_id, generated_from_ip) VALUES (:qty, :tag, :aid, :ip);";
            $command = $connection->createCommand($sql);
            $command->bindValue(':qty', $quantity);
            $command->bindValue(':tag', $distribution_tag_id);
            $command->bindValue(':aid', $aid);
            $command->bindValue(':ip', $ipaddr);
            $rowCount = $command->execute();

            if ($rowCount > 0)
            {
                $last_inserted_id = $connection->getLastInsertId();

                $finalCodes = CodeGenerator::generate_str_codes($last_inserted_id, $quantity);

                // insert into activation_codes
                $sql2 = "INSERT IGNORE INTO activation_codes (activation_code, activation_code_batch_id) VALUES " . $finalCodes;
                $command2 = $connection->createCommand($sql2);
                $rowCount2 = $command2->execute();

                // if there are duplicate codes, generate the remaining codes
                if ($rowCount2 < $quantity)
                {
                    $remaining_qty = $quantity - $rowCount2;
                    $retval = $this->regenerate_codes($last_inserted_id, $remaining_qty);
                    
                    if ($retval)
                    {
                        $beginTrans->commit();
                        return true;
                    }
                    else
                    {
                        $beginTrans->rollback();  
                        return false;
                    }
                }
                else
                {
                    if ($rowCount2 > 0) 
                    {
                        $beginTrans->commit();
                        return true;
                    } 
                    else
                    {
                        $beginTrans->rollback();  
                        return false;
                    }
                }
            }
            else
            {
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
    
    public function regenerate_codes($last_inserted_id, $remaining_qty)
    {
        $connection = $this->_connection;
        $beginTrans = $connection->beginTransaction();
        
        $finalCodes = CodeGenerator::generate_str_codes($last_inserted_id, $remaining_qty);
        
        try
        {
            $sql2 = "INSERT IGNORE INTO activation_codes (activation_code, activation_code_batch_id) VALUES " . $finalCodes;
            $command2 = $connection->createCommand($sql2);
            $rowCount2 = $command2->execute();
            
            if ($rowCount2 < $remaining_qty && $rowCount2 != 0)
            {
                $remaining_qty = $remaining_qty - $rowCount2;
                $this->regenerate_codes($last_inserted_id, $remaining_qty);
            }
            else if ($rowCount2 == $remaining_qty)
            {
                $beginTrans->commit();
                $retval = true;
            }
            else if ($rowCount2 == 0)
            {
                $beginTrans->rollback();
                $retval = false;            
            }
        }
        catch (CDbException $e)
        {
            $beginTrans->rollback();
            $retval = false;     
        }
        
        return $retval;
    }
    
    
    public function selectAll()
    {
        $connection = $this->_connection;
        
        $sql = "SELECT a.activation_code_batch_id, a.batch_quantity, a.date_generated, a.generated_from_ip,
                                CONCAT(b.first_name, ' ', b.last_name) AS member_name,
                                CASE a.distribution_tag_id WHEN 1 THEN 'IBO' WHEN 2 THEN 'IPD' END AS distribution_tag_id,
                                (SELECT count(activation_code_id) FROM activation_codes WHERE activation_code_batch_id = a.activation_code_batch_id
                                AND Status = 0) AS available_codes
                FROM activation_code_batch a
                INNER JOIN member_details b ON a.generated_by_id = b.member_id
                ORDER BY a.date_generated DESC;";
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function selectAllCodesByBatchId($batchId)
    {
        $connection = $this->_connection;
        
        $sql = "SELECT a.activation_code, 
                CASE a.status WHEN 0 THEN 'Available' WHEN 1 THEN 'Used' END AS status
                FROM activation_codes a
                INNER JOIN activation_code_batch b ON a.activation_code_batch_id = b.activation_code_batch_id
                WHERE b.activation_code_batch_id = :batchId
                ORDER BY 2";
        $command = $connection->createCommand($sql);
        $command->bindParam(":batchId", $batchId);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function updateActivationCodeStatus($activation_code, $distribution_tag_id)
    {
        $conn = $this->_connection;
        
        $query = "UPDATE activation_codes a 
                  INNER JOIN activation_code_batch b ON a.activation_code_batch_id = b.activation_code_batch_id
                  SET a.status = 1
                  WHERE a.activation_code = :activation_code
                        AND a.status = 0 AND b.distribution_tag_id = :distribution_tag_id";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':activation_code', $activation_code);        
        $command->bindParam(':distribution_tag_id', $distribution_tag_id);
        $result = $command->execute();
        return $result;
        
    }
    
    public function validateActivationCode($activation_code, $distribution_tag_id)
    {
        $conn = $this->_connection;
                
        $query = "SELECT a.activation_code_id FROM activation_codes a
                    INNER JOIN activation_code_batch b ON a.activation_code_batch_id = b.activation_code_batch_id
                    WHERE a.activation_code = :activation_code
                        AND a.status = 0 AND b.distribution_tag_id = :distribution_tag_id";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':activation_code', $activation_code);
        $command->bindParam(':distribution_tag_id', $distribution_tag_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    /**
     * @author Noel Antonio
     * @date 05-30-2014
     * @param type $activation_code
     * @return type
     */
    public function checkUsedCodeByMembers($activation_code)
    {
        $conn = $this->_connection;
                
        $query = "SELECT COUNT(member_id) AS exist_member_code
                    FROM members WHERE activation_code = :activation_code";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':activation_code', $activation_code);
        $result = $command->queryRow();
        
        return $result['exist_member_code'];
    }
}
?>
