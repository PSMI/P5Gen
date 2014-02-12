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
        
        //If upline is the same as logged user
        if($this->upline_id == Yii::app()->user->getId())
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
        
        //$upline_list = implode(',',$new_upline);
        
        /** if current date is between cutoff dates, get cutoff_id,
         *  UPDATE current transaction in commissions table
         *  else add NEW transaction
         */   
        $cutoff = GOCModel::cut_off_dates();
        
        if($cutoff !== false)
        {
            
            $cutoff_id = $cutoff['cutoff_id'];            
             
            //Check if all uplines has existing records, add new otherwise
            $retval = GOCModel::check_transactions($upline_list,$cutoff_id);
            
            //Check if uplines has current transactions
            if(is_array($retval) && count($retval)> 0 )
            {
                
                //Uplines with valid and existing transactions
                $uplines_wt = implode(',',$retval);
                //Update current transaction, +1 to current ibo_count. NOTE: MUST BE LOGGED TO AUDIT TRAIL FOR BACK TRACKING
                
                $new_list = array_diff($upline_list,array($uplines_wt));
                
                //if(count($new_list)>0)
                   // $uplines_wot = array_merge(array('cutoff_id'=>$cutoff_id,'upline_wot'=>$new_list));

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
                
                foreach($upline_list as $upline)
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
        else // If not within cutoff, insert to transactions and process later by jobs
        {
            foreach($upline_list as $upline)
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
    
    public function cut_off_dates()
    {
        //Get last and next cutoff date
        $reference = new ReferenceModel();
        
        $result = $reference->getCutOffDate(TransactionTypes::GOC);
        $this->last_cutoff_date = date('Y-m-d',strtotime($result['last_cutoff_date']));
        $this->next_cutoff_date = date('Y-m-d',strtotime($result['next_cutoff_date']));
        
        if($this->current_date > $this->last_cutoff_date && $this->current_date <= $this->next_cutoff_date)
            return $result;
        else
            return false;
        
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
        
        $query = "UPDATE commissions 
                    SET ibo_count = ibo_count + 1,
                        amount = amount + :payout_rate,
                        date_last_updated = now()
                    WHERE member_id IN ($uplines)
                 -- AND date_created BETWEEN :last_cutoff AND :next_cutoff
                    AND cutoff_id = :cutoff_id AND status = 0";
        
        $command = $conn->createCommand($query);
//        $command->bindParam(':next_cutoff', $this->next_cutoff_date);
//        $command->bindParam(':last_cutoff', $this->last_cutoff_date);
        $command->bindParam(':payout_rate', $this->payout_rate);
        $command->bindParam(':cutoff_id', $cutoff_id);
        $result = $command->execute();
        return $result;
    }
    
    public function add_transactions($upline_id, $cutoff_id)
    {
        $conn = $this->_connection;
             
        /*
        $values = "";
        
        foreach($uplines as $key => $upline) 
            
            $values .= '('.$upline . ',1,100'.'),';
         
        $values = rtrim($values,',');
        
        $query = "INSERT INTO commissions (member_id,ibo_count,amount)
                  VALUES $values";
         
        $command = $conn->createCommand($query);
        //$command->bindParam(':values', $values);
        $result = $command->execute();
        return $result;
         * 
         */
        
        $query = "INSERT INTO commissions (cutoff_id,member_id,ibo_count,amount)
                  VALUES (:cutoff_id, :upline_id, 1, :payout_rate)";
         
        $command = $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $cutoff_id);
        $command->bindParam(':upline_id', $upline_id);
        $command->bindParam(':payout_rate', $this->payout_rate);
        $result = $command->execute();
        return $result;
    }
    
    
}
?>

