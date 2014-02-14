<?php

/*
 * @author : owliber
 * @date : 2014-02-09
 */

class GOCModel extends CFormModel
{
    
    const TRANS_TYPE = 1;
    
    public $member_id;
    public $upline_id;
    public $endorser_id;
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
                
        //If upline is the same as logged user
        if($this->upline_id == $this->endorser_id)
        {
            /** Get the parent upline id up to the root of the 
             *  member to be placed under the assigned upline.
             */
                        
            $uplines = Networks::getUplines($this->upline_id);
              
        }
        else
        {
           /** Get the 2nd parent upline id up to the root of the 
            *  downline to be placed under the assigned upline.
            */
            
            $uplines = Networks::getUplines($this->member_id);

        }
                
        if(count($uplines == 1))
            $upline_list = array($this->upline_id);
        else
            $upline_list = array_diff($uplines, array($this->upline_id));
                
        /** if current date is between cutoff dates, get cutoff_id,
         *  UPDATE current transaction in commissions table
         *  else add NEW transaction
         */   
        $cutoff_id = GOCModel::get_cutoff();
        
        //Check if all uplines has existing records, add new otherwise
        $retval = GOCModel::check_transactions($upline_list,$cutoff_id);

        //Check if uplines has current transactions
        if(is_array($retval) && count($retval)> 0 )
        {

            //Uplines with valid and existing transactions
            $uplines_wt = implode(',',$retval);
            //Update current transaction, +1 to current ibo_count. NOTE: MUST BE LOGGED TO AUDIT TRAIL FOR BACK TRACKING

            $new_list = array_diff($upline_list,array($uplines_wt));


            $update = GOCModel::update_transactions($uplines_wt, $cutoff_id);

            try 
            {
                if(count($update) > 0 && count($new_list)>0)
                {
                    //Add new commission to uplines without transactions

                    foreach($new_list as $upline)
                    {
                        $result[] = GOCModel::add_transactions($upline,$cutoff_id);
                    }

                    if(count($result) == count($new_list))
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
        else
        {                

            $result = GOCModel::add_transactions($upline_list,$cutoff_id);                

            if(count($result)>0)
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
    
    public function check_transactions($uplines,$cutoff_id)
    {
        $conn = $this->_connection;
        
        $uplines = implode(',',$uplines);
        
        $query = "SELECT * FROM commissions 
                  WHERE member_id IN ($uplines) 
                      AND cutoff_id = :cutoff_id 
                      AND status = 0;";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $cutoff_id);
        $result = $command->queryAll();
        
        $retval = array();
        
        if(count($result)>0)
        {
            foreach($result as $val)
            {
                $retval[] = $val['member_id'];
            }
            
            
        }
        
        return $retval;
        
        
    }
    
    public function update_transactions($uplines, $cutoff_id)
    {
        $conn = $this->_connection;
        
        $model = new UnprocessedMemberModel();
        $model->log_message = "Members ".$uplines." has been updated.";
        $model->log();
        
        $query = "UPDATE commissions 
                    SET ibo_count = ibo_count + 1,
                        amount = amount + :payout_rate,
                        date_last_updated = now()
                    WHERE member_id IN ($uplines)
                    AND cutoff_id = :cutoff_id AND status = 0";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':payout_rate', $this->payout_rate);
        $command->bindParam(':cutoff_id', $cutoff_id);
        $result = $command->execute();
        return $result;
    }
    
    public function add_transactions($uplines, $cutoff_id)
    {
        $conn = $this->_connection;
        
        $model = new UnprocessedMemberModel();
        $model->log_message = "New transaction for members ".$uplines." is added.";
        $model->log();
        
        $values = array();
        
        $query = "INSERT INTO commissions (cutoff_id,member_id,ibo_count,amount) VALUES ";
        
        foreach ($uplines as $upline) {
            $values[] = '('.$cutoff_id.','.$upline.',1,'.$this->payout_rate.')';
        }
        
        if (!empty($values)) {
            $query .= implode(', ', $values);
        }
         
        $command = $conn->createCommand($query);
        $result = $command->execute();        
        return $result;
        
    }
    
    public function check_valid_cutoff()
    {
        $model = new ReferenceModel();
        $result = $model->getCutOffDate(TransactionTypes::GOC);
        
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
    
    public function get_cutoff()
    {
        $conn = $this->_connection;
        
        $result = GOCModel::check_valid_cutoff();
        
        if($result === false)
        {
            //Update last valid cutoff
            $query = "UPDATE ref_cutoffs SET status = 2
                      WHERE transaction_type_id = 1
                        AND status = 1";
            $command = $conn->createCommand($query);
            $result = $command->execute();
            
            if(count($result)>0)
            {
                $query2 = "INSERT INTO ref_cutoffs (transaction_type_id, last_cutoff_date, next_cutoff_date)
                            SELECT
                              rc.transaction_type_id,
                              rc.next_cutoff_date AS last_cutoff_date,
                              DATE_ADD(rc.next_cutoff_date, INTERVAL 3 MONTH) AS next_cutoff_date
                            FROM ref_cutoffs rc
                            WHERE rc.transaction_type_id = 1 AND rc.status = 2
                            ORDER BY rc.cutoff_id DESC LIMIT 1;";

                $command2 = $conn->createCommand($query2);
                $result2 = $command2->execute();
                
                if(count($result2)>0)
                {
                    
                    return $conn->getLastInsertID();
                }
            }
        }
        else
        {
            return $result['cutoff_id'];
        }

        
    }
        
    
}
?>

