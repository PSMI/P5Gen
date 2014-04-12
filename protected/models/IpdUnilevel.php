<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-11-2014
------------------------*/

class IpdUnilevel extends CFormModel
{
    public $_connection;
    public $member_id;
    public $endorser_id;
    public $upline_id;
    public $cutoff_id;
    public $total_direct_endorse;
    public $total_members;
    public $next_cutoff_date;
    public $last_cutoff_date;
    public $status;
    
    public function __construct()
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
            array('cutoff_id','required'),
            array('status','required'),
        );
    }
    
    public function attributeLabels() {
        return array('cutoff_id'=>'Cut-Off Date');
    }
    
    public function getUnilevel()
    {
        $conn = $this->_connection;
        
        $reference = new ReferenceModel();
        $total_purchased_amt = $reference->get_variable_value('IPD_REPEAT_PURCHASE_REQUIREMENT');
        $cutoff_ipdunilevel_arr = $reference->get_cutoff_by_id($this->cutoff_id);
        $last_cutoff = $cutoff_ipdunilevel_arr['last_cutoff_date'];
        $next_cutoff = $cutoff_ipdunilevel_arr['next_cutoff_date'];
        
        if ($this->status == "1, 2")
        {
            $query = "SELECT
                        u.unilevel_id,  
                        u.cutoff_id,
                        u.distributor_id,
                        CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS member_name,
                        u.ipd_count,
                        u.amount,
                        u.date_created,
                        DATE_FORMAT(u.date_approved, '%M %d, %Y') AS date_approved,
                        CONCAT(md1.last_name, ', ', md1.first_name, ' ', md1.middle_name) AS approved_by,
                        DATE_FORMAT(u.date_claimed, '%M %d, %Y') AS date_claimed,
                        CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS claimed_by,
                        u.status
                      FROM distributor_unilevel u
                        INNER JOIN member_details md
                          ON u.distributor_id = md.member_id
                        LEFT OUTER JOIN member_details md1
                          ON u.approved_by_id = md1.member_id
                        LEFT OUTER JOIN member_details md2
                          ON u.claimed_by_id = md2.member_id
                      WHERE u.cutoff_id = :cutoff_id 
                      ORDER BY md.last_name;";
        }
        else if ($this->status == "1")
        {
            $query = "SELECT
                            u.unilevel_id,  
                            u.cutoff_id,
                            u.distributor_id,
                            CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS member_name,
                            u.ipd_count,
                            u.amount,
                            u.date_created,
                            DATE_FORMAT(u.date_approved, '%M %d, %Y') AS date_approved,
                            CONCAT(md1.last_name, ', ', md1.first_name, ' ', md1.middle_name) AS approved_by,
                            DATE_FORMAT(u.date_claimed, '%M %d, %Y') AS date_claimed,
                            CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS claimed_by,
                            u.status
                          FROM distributor_unilevel u
                            INNER JOIN member_details md
                              ON u.distributor_id = md.member_id
                            LEFT OUTER JOIN member_details md1
                              ON u.approved_by_id = md1.member_id
                            LEFT OUTER JOIN member_details md2
                              ON u.claimed_by_id = md2.member_id
                            LEFT OUTER JOIN purchased_summary ps
                              ON u.distributor_id = ps.member_id
                          WHERE u.cutoff_id = 6
                          AND ps.total >= $total_purchased_amt
                          AND ps.status = 1
                          AND ps.date_purchased <= $next_cutoff AND ps.date_purchased >= $last_cutoff
                          ORDER BY md.last_name;";
        }
        else
        {
            $query = "SELECT
                            u.unilevel_id,  
                            u.cutoff_id,
                            u.distributor_id,
                            CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS member_name,
                            u.ipd_count,
                            u.amount,
                            u.date_created,
                            DATE_FORMAT(u.date_approved, '%M %d, %Y') AS date_approved,
                            CONCAT(md1.last_name, ', ', md1.first_name, ' ', md1.middle_name) AS approved_by,
                            DATE_FORMAT(u.date_claimed, '%M %d, %Y') AS date_claimed,
                            CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS claimed_by,
                            u.status
                          FROM distributor_unilevel u
                            INNER JOIN member_details md
                              ON u.distributor_id = md.member_id
                            LEFT OUTER JOIN member_details md1
                              ON u.approved_by_id = md1.member_id
                            LEFT OUTER JOIN member_details md2
                              ON u.claimed_by_id = md2.member_id
                            LEFT OUTER JOIN purchased_summary ps
                              ON u.distributor_id = ps.member_id
                          WHERE u.cutoff_id = 6
                          AND ps.total < $total_purchased_amt
                          AND ps.status = 1
                          AND ps.date_purchased > $next_cutoff AND ps.date_purchased > $last_cutoff
                          ORDER BY md.last_name;";
        }

        $command =  $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getPayoutTotal()
    {
        $conn = $this->_connection;
        
        $reference = new ReferenceModel();
        $total_purchased_amt = $reference->get_variable_value('IPD_REPEAT_PURCHASE_REQUIREMENT');
        $cutoff_ipdunilevel_arr = $reference->get_cutoff_by_id($this->cutoff_id);
        $last_cutoff = $cutoff_ipdunilevel_arr['last_cutoff_date'];
        $next_cutoff = $cutoff_ipdunilevel_arr['next_cutoff_date'];
        
        if ($this->status == "1, 2")
        {
            $query = "SELECT
                        sum(u.amount) as total_amount,
                        sum(u.ipd_count) as total_ipd
                      FROM distributor_unilevel u
                      WHERE u.cutoff_id = :cutoff_id;";
        }
        else if ($this->status == "1")
        {
            $query = "SELECT
                        sum(u.amount) as total_amount,
                        sum(u.ipd_count) as total_ipd
                      FROM distributor_unilevel u
                      INNER JOIN purchased_summary ps
                              ON u.distributor_id = ps.member_id
                      WHERE u.cutoff_id = :cutoff_id
                      AND ps.total >= $total_purchased_amt
                      AND ps.date_purchased <= $next_cutoff AND ps.date_purchased >= $last_cutoff
                      AND ps.status = 1;";
        }
        else
        {
            $query = "SELECT
                        sum(u.amount) as total_amount,
                        sum(u.ipd_count) as total_ipd
                      FROM distributor_unilevel u
                      INNER JOIN purchased_summary ps
                              ON u.distributor_id = ps.member_id
                      WHERE u.cutoff_id = :cutoff_id
                      AND ps.total < $total_purchased_amt
                      AND ps.date_purchased > $next_cutoff AND ps.date_purchased > $last_cutoff
                      AND ps.status = 1;";
        }
            
        $command =  $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $result = $command->queryRow();
        
        return $result;
    }
    
    public function flashOut()
    {
        $conn = $this->_connection;
        
        $reference = new ReferenceModel();
        $total_purchased_amt = $reference->get_variable_value('IPD_REPEAT_PURCHASE_REQUIREMENT');
        $cutoff_ipdunilevel_arr = $reference->get_cutoff_by_id($this->cutoff_id);
        $last_cutoff = $cutoff_ipdunilevel_arr['last_cutoff_date'];
        $next_cutoff = $cutoff_ipdunilevel_arr['next_cutoff_date'];
        
        $query = "UPDATE distributor_unilevel du 
                    INNER JOIN purchased_summary ps
                        ON du.distributor_id = ps.member_id
                    SET du.status = 3, 
                        du.date_last_updated = now()
                  WHERE du.cutoff_id = :cutoff_id
                    AND ps.total < $total_purchased_amt
                    AND ps.date_purchased > $next_cutoff AND ps.date_purchased > $last_cutoff
                    AND ps.status = 1";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $result = $command->execute();
        
        return $result;
    }
    
    public function getUnilevelDetails()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    u.unilevel_id,
                    u.cutoff_id,
                    u.distributor_id,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS member_name,
                    u.ipd_count,
                    u.amount,
                    u.date_created,
                    DATE_FORMAT(u.date_approved, '%M %d, %Y') AS date_approved,
                    CONCAT(md1.last_name, ', ', md1.first_name, ' ', md1.middle_name) AS approved_by,
                    DATE_FORMAT(u.date_claimed, '%M %d, %Y') AS date_claimed,
                    CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS claimed_by,
                    u.status
                  FROM distributor_unilevel u
                    INNER JOIN members m
                      ON u.distributor_id = m.member_id
                    INNER JOIN member_details md
                      ON u.distributor_id = md.member_id
                    LEFT OUTER JOIN member_details md1
                      ON u.approved_by_id = md1.member_id
                    LEFT OUTER JOIN member_details md2
                      ON u.claimed_by_id = md2.member_id
                  WHERE u.cutoff_id = :cutoff_id
                  AND u.distributor_id = :member_id";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $command->bindParam(':member_id', $this->member_id);
        $result = $command->queryRow();
        
        return $result;
    }
    
    public function updateUnilevelStatus($unilevel_id, $status, $userid)
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();
        
        if ($status == 1)
        {
            $query = "UPDATE distributor_unilevel
                        SET date_approved = NOW(),
                            status = :status,
                            approved_by_id = :userid
                        WHERE unilevel_id = :unilevel_id;";
        }
        else if ($status == 2)
        {
            $query = "UPDATE distributor_unilevel
                        SET date_claimed = NOW(),
                            status = :status,
                            claimed_by_id = :userid
                        WHERE unilevel_id = :unilevel_id;";
        }   
        
        $command = $conn->createCommand($query);
        
        $command->bindParam(':unilevel_id', $unilevel_id);
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
    
    public function get_running_account()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    ra.member_id,
                    ra.ipd_direct_endorse,
                    ra.total_member,
                    ra.date_first_five_completed,
                    ra.with_unilevel_trx,
                    TIMESTAMPDIFF(MONTH,m.date_joined,date_first_five_completed) AS num_of_months,
                    m.account_type_id
                  FROM running_accounts ra
                    INNER JOIN members m
                      ON ra.member_id = m.member_id
                  WHERE m.member_id = :member_id";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->endorser_id);
        return $command->queryRow();
    }
    
    public function update_transaction($payout)
    {
        $conn = $this->_connection;
        
        //$payout_rate = ReferenceModel::get_payout_rate(TransactionTypes::UNILEVEL);
        
        $query = "UPDATE distributor_unilevel 
                    SET ipd_count = ipd_count + 1, 
                        amount = amount + :payout,
                        date_last_updated = now()
                  WHERE cutoff_id = :cutoff_id
                    AND distributor_id = :member_id
                    AND status = 0";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->endorser_id);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $command->bindParam(':payout', $payout);
        $result = $command->execute();     
        return $result;
    }
    
    public function insert_first_transaction($payout)
    {
        $conn = $this->_connection;
        
//        $payout_rate = ReferenceModel::get_payout_rate(TransactionTypes::UNILEVEL);
//        $payout = $this->total_direct_endorse * $payout_rate;
        
        $query = "INSERT INTO distributor_unilevel (distributor_id, cutoff_id, ipd_count, amount)
                   VALUES (:member_id, :cutoff_id, :total_direct_endorse, :payout)";        
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->upline_id);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $command->bindParam(':total_direct_endorse', $this->total_direct_endorse);
        $command->bindParam(':payout', $payout);
        $result = $command->execute();        
        
        if(count($result)>0)
        {
            //Update running account
            $query2 = "UPDATE running_accounts
                        SET with_unilevel_trx = 1
                        WHERE member_id = :member_id";
            $command2 = $conn->createCommand($query2);
            $command2->bindParam(':member_id', $this->upline_id);
            $result2 = $command2->execute();
            
            if(count($result2)>0)
                return true;
            else
                return false;
        }
        else
        {
            return false;
        }
        
    }
    
    public function new_transaction($payout)
    {
        $conn = $this->_connection;
        
        //$payout_rate = ReferenceModel::get_payout_rate(TransactionTypes::UNILEVEL);
        
        $query = "INSERT INTO distributor_unilevel (distributor_id, cutoff_id, ipd_count, amount)
                   VALUES (:member_id, :cutoff_id, 1, :payout)";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->endorser_id);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
                $command->bindParam(':payout', $payout);
        $result = $command->execute();        
        return $result;
        
    }
    
    public function check_transaction()
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM distributor_unilevel
                   WHERE cutoff_id = :cutoff_id
                        AND distributor_id = :member_id
                        AND status = 0";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $command->bindParam(':member_id', $this->endorser_id);
        $result = $command->queryAll();
        return $result;
    }
    
    public function is_first_transaction()
    {
        $conn = $this->_connection;
        
        $query = "SELECT count(*) as total FROM distributor_unilevel 
                    WHERE distributor_id = :member_id";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        $result = $command->queryRow();
        $trx_count = $result['total'];
        if($trx_count == 1)
            return true;
        else
            return false; 
    }
    
    
    
    public function getTotalPurchasedAmount()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    SUM(total) AS total
                  FROM purchased_summary ps
                  WHERE ps.member_id = :member_id";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->upline_id);
        $result = $command->queryRow();
        
        return $result;
        
//        $result = $command->queryRow();
//
//        if ($result['total'] > 251)
//        {
//            return true;
//        }
//        else
//        {
//            return false;
//        }
    }
}
?>
