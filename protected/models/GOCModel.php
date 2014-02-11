<?php

/*
 * @author : owliber
 * @date : 2014-02-09
 */

class GOCModel extends CFormModel
{
    
    public $member_id;
    public $upline_id;
    public $level_no;
    public $_connection;
    public $current_date;
    public $last_cutoff_date;
    public $next_cutoff_date;
    public $payout_rate = 100;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
        $this->current_date = date('Y-m-d');
    }
    
    public function process()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        
        //If upline is not the same as logged user
        if($this->upline_id != Yii::app()->user->getId())
        {
            /** Get the 2nd parent upline id up to the root of the 
             *  member to be placed under the assigned upline.
             */
                        
            $uplines = Networks::getUplines($this->upline_id);
            $upline_list = implode(',',$uplines);
           
            
            /** if current date is between cutoff dates,
             *  UPDATE current transaction in commissions table
             *  else add NEW transaction
             */
            
                       
            if(GOCModel::cut_off_dates())
            {
                              
                //Check if all uplines has existing records, add new otherwise
                $retval = $this->check_transactions($upline_list);
                //Uplines with valid and existing transactions
                $uplines_wt = implode(',',$retval);
                //Update current transaction, +1 to current ibo_count. NOTE: MUST BE LOGGED TO AUDIT TRAIL FOR BACK TRACKING
                
                $update = GOCModel::update_transactions($uplines_wt);
                
                try 
                {
                    if(count($update) > 0)
                    {
                        //Add new commission to uplines without transactions
                        $uplines_wot = array_diff($uplines, $uplines_wt);
                        
                            $insert = GOCModel::add_transactions($uplines_wot);

                            if(count($insert) > 0)
                            {
                                $trx->commit();
                                return true;
                            }
                            else
                            {
                                $trx->rollback();
                                return false;
                            }
                    }
                } 
                catch (PDOException $e) 
                {
                    $trx->rollback();
                    return false;
                }
                
                
            }
            else // Add new transactions
            {
                
            }
            
        }
        else
        {
            return false;
        }
        
    }
    
    public function cut_off_dates()
    {
        //Get last and next cutoff date
        $reference = new ReferenceModel();
        
        $result = $reference->getCutOffDate(TransactionTypes::GOC);
        $this->last_cutoff_date = date('Y-m-d',strtotime($result['last_cutoff_date']));
        $this->next_cutoff_date = date('Y-m-d',strtotime($result['next_cutoff_date']));
        
        if($this->current_date > $this->last_cutoff_date && $this->current_date < $this->next_cutoff_date)
            return true;
        else
            return false;
        
    }
    
    public function check_transactions($uplines)
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM commissions 
                  WHERE member_id IN ($uplines)
                  AND date_created BETWEEN :last_cutoff AND :next_cutoff
                  AND status = 0";
        $command = $conn->createCommand($query);
        $command->bindParam(':next_cutoff', $this->next_cutoff_date);
        $command->bindParam(':last_cutoff', $this->last_cutoff_date);
        $result = $command->queryAll();
        
        foreach($result as $val)
        {
            $retval[] = $val['member_id'];
        }
        
        return $retval;
        
    }
    
    public function update_transactions($uplines)
    {
        $conn = $this->_connection;
        
        $query = "UPDATE commissions 
                    SET ibo_count = ibo_count + 1,
                        amount = amount + :payout_rate,
                        date_last_updated = now()
                    WHERE member_id IN ($uplines)
                    AND date_created BETWEEN :last_cutoff AND :next_cutoff
                    AND status = 0";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':next_cutoff', $this->next_cutoff_date);
        $command->bindParam(':last_cutoff', $this->last_cutoff_date);
        $command->bindParam(':payout_rate', $this->payout_rate);
        $result = $command->execute();
        return $result;
    }
    
    public function add_transactions($uplines)
    {
        $conn = $this->_connection;
        
        $values = "";
        
        foreach($uplines as $upline) $values .= '('.$upline . ',1,100'.'),';
         
        $values = rtrim($values,',');
        
        $query = "INSERT INTO commissions (member_id,ibo_count,amount)
                  VALUES $values";
        
        $command = $conn->createCommand($query);
        //$command->bindParam(':values', $values);
        $result = $command->execute();
        return $result;
    }
    
    
}
?>

