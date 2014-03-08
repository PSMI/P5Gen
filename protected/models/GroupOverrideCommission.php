<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-09-2014
------------------------*/

class GroupOverrideCommission extends CFormModel
{
    public $_connection;
    public $payout_rate;
    public $cutoff_id;
    public $uplines;
    public $upline_id;
    
    public function __construct()
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
            array('cutoff_id','required'),
        );
    }

    public function attributeLabels() 
    {
        return array('cutoff_id'=>'Cut-Off Date&nbsp;');
    }
    
    public function getComissions()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    c.commission_id,
                    CONCAT(m.last_name, ', ', m.first_name, ' ', m.middle_name) AS member_name,
                    c.ibo_count,
                    c.amount,
                    c.date_created,
                    DATE_FORMAT(c.date_approved,'%d-%m-%Y') AS date_approved,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS approved_by,
                    DATE_FORMAT(c.date_claimed,'%d-%m-%Y') AS date_claimed,
                    CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS claimed_by,
                    c.status,
                    c.member_id,
                    c.cutoff_id
                  FROM commissions c
                    INNER JOIN member_details m
                      ON c.member_id = m.member_id
                    LEFT OUTER JOIN member_details md ON c.approved_by_id = md.member_id
                    LEFT OUTER JOIN member_details md2 ON c.claimed_by_id = md2.member_id
                  WHERE c.cutoff_id = :cutoff_id ORDER BY m.last_name;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getCommissionsTotal()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    sum(c.ibo_count) AS total_ibo,
                    sum(c.amount) AS total_amount
                  FROM commissions c
                  WHERE c.cutoff_id = :cutoff_id";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $result = $command->queryRow();
        
        return $result;
    }
    
    public function updateCommisionStatus($comm_id, $status, $userid)
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();
        
        if ($status == 1)
        {
            $query = "UPDATE commissions
                        SET date_approved = NOW(),
                            status = :status,
                            approved_by_id = :userid
                        WHERE commission_id = :comm_id;";
        }
        else if ($status == 2)
        {
            $query = "UPDATE commissions
                        SET date_claimed = NOW(),
                            status = :status,
                            claimed_by_id = :userid
                        WHERE commission_id = :comm_id;";
        }
            
        
        $command = $conn->createCommand($query);
        
        $command->bindParam(':comm_id', $comm_id);
        $command->bindParam(':status', $status);
        $command->bindParam(':userid', $userid);

        $result = $command->execute();
        
        try
        {
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
        catch(PDOException $e)
        {
            $trx->rollback();
            return false;
        }
    }
    
    /**
     * Check current member GOC transactions
     * @param type $uplines
     * @param type $cutoff_id
     * @return type
     * @author owliber
     */
    public function check_transactions()
    {
        $conn = $this->_connection;
        
        $uplines = implode(',',$this->uplines);
        
        $query = "SELECT * FROM commissions 
                  WHERE member_id IN ($uplines) 
                      AND cutoff_id = :cutoff_id 
                      AND status = 0;";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
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
    
    /**
     * UPDATE existing GOC transactions
     * @param type $uplines
     * @param type $cutoff_id
     * @return type
     * @author owliber
     */
    public function update_transactions()
    {
        $conn = $this->_connection;        
        //$uplines = $this->uplines;
        
//        $query = "UPDATE commissions 
//                    SET ibo_count = ibo_count + 1,
//                        amount = amount + :payout_rate,
//                        date_last_updated = now()
//                    WHERE member_id IN ($uplines)
//                    AND cutoff_id = :cutoff_id AND status = 0";
        
         $query = "UPDATE commissions 
                    SET ibo_count = ibo_count + 1,
                        amount = amount + :payout_rate,
                        date_last_updated = now()
                    WHERE member_id = :upline_id
                    AND cutoff_id = :cutoff_id AND status = 0";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':payout_rate', $this->payout_rate);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $command->bindParam(':upline_id', $this->upline_id);
        $result = $command->execute();
        return $result;
    }
    
    /**
     * Add new GOC transactions
     * @param type $uplines
     * @param type $cutoff_id
     * @return type
     * @author owliber
     */
    public function add_transactions()
    {
        $conn = $this->_connection;
        
        $values = array();
        $uplines = $this->uplines;
        
        $query = "INSERT INTO commissions (cutoff_id,member_id,ibo_count,amount) VALUES ";
        
        foreach ($uplines as $upline) {
            $values[] = '('.$this->cutoff_id.','.$upline.',1,'.$this->payout_rate.')';
        }
        
        if (!empty($values)) {
            $query .= implode(', ', $values);
        }
         
        $command = $conn->createCommand($query);
        $result = $command->execute();        
        return $result;
        
    }
    
    public function getPayeeDetails($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                        m.username,
                        DATE_FORMAT(m.date_created,'%d-%m-%Y') AS date_created,
                        md.email,
                        md.mobile_no,
                        md.telephone_no,
                        CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS endorser_name
                    FROM members m
                      INNER JOIN member_details md
                        ON m.member_id = md.member_id
                      LEFT OUTER JOIN member_details md2
                        ON md2.member_id = m.endorser_id
                    WHERE m.member_id = :member_id;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getPayeeDownlineDetails($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                        CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS member_name,
                        DATE_FORMAT(m.date_joined,'%d-%m-%Y') AS date_joined,
                        CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS upline_name
                    FROM members m
                        INNER JOIN member_details md
                        ON m.member_id = md.member_id
                        LEFT OUTER JOIN member_details md2
                        ON md2.member_id = m.upline_id
                    WHERE m.member_id = :member_id";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function checkIfExistInCutoff($ibo_id, $from_cutoff, $to_cutoff)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                        m.member_id
                    FROM members m
                    WHERE m.member_id = :ibo_id
                    AND placement_date > :from_cutoff AND placement_date <= :to_cutoff;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':ibo_id', $ibo_id);
        $command->bindParam(':from_cutoff', $from_cutoff);
        $command->bindParam(':to_cutoff', $to_cutoff);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getPrevousLoans($member_id, $from_cutoff, $to_cutoff)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                        SUM(l.loan_amount) AS total_loan
                    FROM loans l
                    WHERE l.status = 3
                    AND date_completed > :from_cutoff AND date_completed <= :to_cutoff
                    AND l.member_id = :member_id;"; 
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $command->bindParam(':from_cutoff', $from_cutoff);
        $command->bindParam(':to_cutoff', $to_cutoff);
        $result = $command->queryAll();
        
        return $result;
    }
}
?>
