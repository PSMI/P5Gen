<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 04-05-2014
------------------------*/

class IpdRetention extends CFormModel
{
    public $_connection;
    public $member_id;
    
    public function __construct()
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function rules()
    {
//        return array(
//            array('cutoff_id','required'),
//        );
    }
    
    public function attributeLabels() {
        //return array('cutoff_id'=>'Cut-Off Date');
    }
    
    public function getIpdRetentionMoney()
    {
        $conn = $this->_connection;
        
        $query = "SELECT 
                    dr.distributor_retention_id,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS member_name,
                    dr.member_id,
                    dr.purchase_retention,
                    dr.other_retention,
                    (dr.purchase_retention + dr.other_retention) as total_retention
                    FROM distributor_retentions dr
                        INNER JOIN member_details md
                          ON dr.member_id = md.member_id
                          INNER JOIN members m ON dr.member_id = m.member_id
                    WHERE dr.status = 0;";
        
        $command =  $conn->createCommand($query);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getPayoutTotal()
    {
        $conn = $this->_connection;

        $query = "SELECT
                    sum(dr.purchase_retention) AS total_purchase_retention,
                    sum(dr.other_retention) AS total_other_retention,
                    (sum(dr.purchase_retention) + sum(dr.other_retention)) AS total_retentions
                  FROM distributor_retentions dr
                    WHERE dr.status = 0;";

        $command =  $conn->createCommand($query);
        $result = $command->queryRow();
        
        return $result;
    }
    
    public function getProductsPurchased($member_ids)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS member_name,
                    m.account_type_id,
                    DATE_FORMAT(ps.date_purchased,'%M %d, %Y') AS date_purchased,
                    p.product_name,
                    pi.quantity,
                    pi.srp,
                    pi.savings
                  FROM purchased_summary ps
                    INNER JOIN member_details md
                      ON ps.member_id = md.member_id
                    LEFT OUTER JOIN members m
                      ON ps.member_id = m.member_id
                    LEFT OUTER JOIN purchased_items pi
                      ON ps.purchase_summary_id = pi.purchase_summary_id
                    LEFT OUTER JOIN products p
                      ON pi.product_id = p.product_id
                  WHERE ps.member_id IN (:member_ids)
                    AND ps.status = 1
                  ORDER BY ps.date_purchased DESC;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_ids', $member_ids);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getProductsPurchasedTotal($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    SUM(ps.total) AS total_price,
                    SUM(ps.savings) AS total_savings
                  FROM purchased_summary ps
                  WHERE ps.member_id = :member_id
                    AND ps.status = 1
                  ORDER BY ps.date_purchased DESC;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function updateIpdRetentionStatus($distributor_retention_id, $status, $userid)
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();
        
        $query = "UPDATE distributor_retentions
                    SET status = :status
                    WHERE distributor_retention_id = :distributor_retention_id;";  
        
        $command = $conn->createCommand($query);
        
        $command->bindParam(':distributor_retention_id', $distributor_retention_id);
        $command->bindParam(':status', $status);

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
    
    public function getMemberName($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS member_name
                  FROM members m
                    INNER JOIN member_details md
                        ON m.member_id = md.member_id
                  WHERE m.member_id = :member_id;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    
    
    public function getCommissionDetailsFivePercent($member_ids, $member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS member_name,
                    m.account_type_id,
                    DATE_FORMAT(ps.date_purchased,'%M %d, %Y') AS date_purchased,
                    p.product_name,
                    pi.quantity,
                    pi.srp,
                    pi.total,
                    pi.savings
                  FROM purchased_summary ps
                    INNER JOIN member_details md
                      ON ps.member_id = md.member_id
                    LEFT OUTER JOIN members m
                      ON ps.member_id = m.member_id
                    LEFT OUTER JOIN purchased_items pi
                      ON ps.purchase_summary_id = pi.purchase_summary_id
                    LEFT OUTER JOIN products p
                      ON pi.product_id = p.product_id
                  WHERE ps.member_id IN (:member_ids)
                    AND ps.status = 1
                    AND pi.savings <> 0
                    AND m.account_type_id = 5
                    AND m.ipd_endorser_id = :member_id
                  ORDER BY member_name DESC;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_ids', $member_ids);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getCommissionDetailsFivePercentTotal($member_ids, $member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    SUM(pi.total) AS total_price,
                    SUM(pi.savings) AS total_savings
                  FROM purchased_summary ps
                    INNER JOIN purchased_items pi
                      ON ps.purchase_summary_id = pi.purchase_summary_id
                    LEFT OUTER JOIN members m
                      ON ps.member_id = m.member_id
                  WHERE ps.member_id IN (:member_ids)
                    AND ps.status = 1
                    AND ps.savings <> 0
                    AND m.account_type_id = 5
                    AND m.ipd_endorser_id = :member_id;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_ids', $member_ids);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getCommissionDetailsThreePercent($member_ids, $member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS member_name,
                    m.account_type_id,
                    DATE_FORMAT(ps.date_purchased,'%M %d, %Y') AS date_purchased,
                    p.product_name,
                    pi.quantity,
                    pi.srp,
                    pi.total,
                    pi.savings
                  FROM purchased_summary ps
                    INNER JOIN member_details md
                      ON ps.member_id = md.member_id
                    LEFT OUTER JOIN members m
                      ON ps.member_id = m.member_id
                    LEFT OUTER JOIN purchased_items pi
                      ON ps.purchase_summary_id = pi.purchase_summary_id
                    LEFT OUTER JOIN products p
                      ON pi.product_id = p.product_id
                  WHERE ps.member_id IN (:member_ids)
                    AND ps.status = 1
                    AND pi.savings <> 0
                    AND m.account_type_id = 3
                    AND m.ipd_endorser_id = :member_id
                  ORDER BY member_name DESC;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_ids', $member_ids);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getCommissionDetailsThreePercentTotal($member_ids, $member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    SUM(pi.total) AS total_price,
                    SUM(pi.savings) AS total_savings
                  FROM purchased_summary ps
                    INNER JOIN purchased_items pi
                      ON ps.purchase_summary_id = pi.purchase_summary_id
                    LEFT OUTER JOIN members m
                      ON ps.member_id = m.member_id
                  WHERE ps.member_id IN (:member_ids)
                    AND ps.status = 1
                    AND ps.savings <> 0
                    AND m.account_type_id = 3
                    AND m.ipd_endorser_id = :member_id;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_ids', $member_ids);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getCommissionDetailsOnePercent($member_ids)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS member_name,
                    m.account_type_id,
                    DATE_FORMAT(ps.date_purchased,'%M %d, %Y') AS date_purchased,
                    p.product_name,
                    pi.quantity,
                    pi.srp,
                    pi.total,
                    pi.savings
                  FROM purchased_summary ps
                    INNER JOIN member_details md
                      ON ps.member_id = md.member_id
                    LEFT OUTER JOIN members m
                      ON ps.member_id = m.member_id
                    LEFT OUTER JOIN purchased_items pi
                      ON ps.purchase_summary_id = pi.purchase_summary_id
                    LEFT OUTER JOIN products p
                      ON pi.product_id = p.product_id
                  WHERE ps.member_id IN (:member_ids)
                    AND ps.status = 1
                    AND pi.savings <> 0
                  ORDER BY member_name DESC;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_ids', $member_ids);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getCommissionDetailsOnePercentTotal($member_ids)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    SUM(pi.total) AS total_price,
                    SUM(pi.savings) AS total_savings
                  FROM purchased_summary ps
                    INNER JOIN purchased_items pi
                      ON ps.purchase_summary_id = pi.purchase_summary_id
                    LEFT OUTER JOIN members m
                      ON ps.member_id = m.member_id
                  WHERE ps.member_id IN (:member_ids)
                    AND ps.status = 1
                    AND ps.savings <> 0;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_ids', $member_ids);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getMemberRepeatPurchaseByCutoff()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    ps.member_id
                  FROM purchased_summary ps
                    WHERE ps.status = 1
                    AND ps.savings <> 0;";
        
        $command =  $conn->createCommand($query);
        $result = $command->queryColumn();
        
        return $result;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function get_running_account()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    ra.member_id,
                    ra.direct_endorse,
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
        $command->bindParam(':member_id', $this->upline_id);
        return $command->queryRow();
    }
    
    public function update_transaction($payout)
    {
        $conn = $this->_connection;
        
        //$payout_rate = ReferenceModel::get_payout_rate(TransactionTypes::UNILEVEL);
        
        $query = "UPDATE distributor_unilevel 
                    SET ibo_count = ibo_count + 1, 
                        amount = amount + :payout,
                        date_last_updated = now()
                  WHERE cutoff_id = :cutoff_id
                    AND distributor_id = :member_id
                    AND status = 0";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->upline_id);
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
        
        $query = "INSERT INTO distributor_unilevel (distributor_id, cutoff_id, ibo_count, amount)
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
        
        $query = "INSERT INTO distributor_unilevel (distributor_id, cutoff_id, ibo_count, amount)
                   VALUES (:member_id, :cutoff_id, 1, :payout)";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->upline_id);
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
        $command->bindParam(':member_id', $this->upline_id);
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
    
    
    
    public function getTotalPurchaseAmount()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    SUM(total) AS total
                  FROM distributor_purchased_items d
                  WHERE d.distributor_id = :member_id";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->upline_id);
        
        $result = $command->queryRow();

        if ($result['total'] > 251)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
?>
