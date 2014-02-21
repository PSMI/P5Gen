<?php

/*
 * @author : owliber
 * @date : 2014-02-02
 */

class ReferenceModel extends CFormModel
{
    public $_connection;
    public $current_date;
    public $last_cutoff_date;
    public $next_cutoff_date;
            
    public function __construct() {
        $this->_connection = Yii::app()->db;
        $this->current_date = date('Y-m-d');
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
    
    public function get_cutoff_dates($trans_type_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM ref_cutoffs 
                    WHERE transaction_type_id = :trans_type_id 
                    AND status = 1
                  ORDER BY cutoff_id DESC
                  LIMIT 1";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':trans_type_id', $trans_type_id);
        $result = $command->queryRow();
        return $result;
    }
    
    public function get_cutoff($trans_type_id)
    {
        $conn = $this->_connection;
        
        $result = ReferenceModel::check_valid_cutoff($trans_type_id);
        $goc_cutoff = ReferenceModel::get_variable_value('GOC_CUTOFF_INTERVAL');
        $unilevel_cutoff = ReferenceModel::get_variable_value('UNILEVEL_CUTOFF_INTERVAL');
        $direct_cutoff = ReferenceModel::get_variable_value('DIRECT_CUTOFF_INTERVAL');
        
        switch($trans_type_id)
        {
            case 1: //GOC
                $interval = " ".$goc_cutoff;
                break;
            case 2: //Unilevel
                $interval = " ".$unilevel_cutoff;
                break;
            case 6: //Direct Endorsement
                $interval = " ".$direct_cutoff;
                break;
            
        }
        
        if($result === false)
        {
            
            //Update last valid cutoff
            $query = "UPDATE ref_cutoffs SET status = 2
                      WHERE transaction_type_id = :trans_type_id
                        AND status = 1";
            $command = $conn->createCommand($query);
            $command->bindParam(':trans_type_id', $trans_type_id);
            $result = $command->execute();
            
           
            if(count($result)>0)
            {
                $query2 = "INSERT INTO ref_cutoffs (transaction_type_id, last_cutoff_date, next_cutoff_date)
                            SELECT
                              rc.transaction_type_id,
                              rc.next_cutoff_date AS last_cutoff_date,
                              DATE_ADD(rc.next_cutoff_date, INTERVAL ".$interval.") AS next_cutoff_date
                            FROM ref_cutoffs rc
                            WHERE rc.transaction_type_id = :trans_type_id AND rc.status = 2
                            ORDER BY rc.cutoff_id DESC LIMIT 1;";

                $command2 = $conn->createCommand($query2);
                $command2->bindParam(':trans_type_id', $trans_type_id);
                $result2 = $command2->execute();

                if(count($result2)>0)
                {
                    return $conn->getLastInsertID();
                }
                else
                {
                    return false;
                }
            }
            
        }
        else
        {
            return $result['cutoff_id'];
        }

        
    }
    
    public function check_valid_cutoff($trans_type_id)
    {
        $result = ReferenceModel::get_cutoff_dates($trans_type_id);
        
        if(count($result)> 0)
        {
            $this->last_cutoff_date = date('Y-m-d',strtotime($result['last_cutoff_date']));
            $this->next_cutoff_date = date('Y-m-d',strtotime($result['next_cutoff_date']));

            if($this->current_date > $this->last_cutoff_date && $this->current_date <= $this->next_cutoff_date)
                return $result['cutoff_id'];
            else
                return false;
        }
    }
    
    public function get_payout_rate($trans_type_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM ref_payout_rate 
                    WHERE transaction_type_id = :trans_type_id 
                    AND status = 1
                    ORDER BY payout_rate_id DESC 
                    LIMIT 1";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':trans_type_id', $trans_type_id);
        $result = $command->queryRow();
        return $result['amount'];
    }
    
}
?>
