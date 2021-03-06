<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-11-2014
------------------------*/

class IpdRpCommission extends CFormModel
{
    public $_connection;
    public $member_id;
    public $cutoff_id;
    public $next_cutoff_date;
    public $last_cutoff_date;    
    public $autocomplete_name;
    
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
    
    public function attributeLabels() {
        return array('cutoff_id'=>'Cut-Off Date');
    }
    
    public function getIpdRpCommission()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    d.distributor_commission_id,
                    d.member_id,
                    CONCAT(m.last_name, ', ', m.first_name, ' ', m.middle_name) AS member_name,
                    m2.account_type_id,
                    d.commission_amount,
                    d.cutoff_id,
                    d.date_created,
                    DATE_FORMAT(d.date_approved,'%M %d, %Y') AS date_approved,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS approved_by,
                    DATE_FORMAT(d.date_claimed,'%M %d, %Y') AS date_claimed,
                    CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS claimed_by,
                    d.status
                  FROM distributor_commissions d
                    INNER JOIN member_details m
                      ON d.member_id = m.member_id
                    LEFT OUTER JOIN member_details md
                      ON d.approved_by_id = md.member_id
                    LEFT OUTER JOIN member_details md2
                      ON d.claimed_by_id = md2.member_id
                    LEFT OUTER JOIN members m2
                      ON d.member_id = m2.member_id
                  WHERE d.cutoff_id = :cutoff_id
                  AND m2.account_type_id = 5
                  ORDER BY md.last_name;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getPayoutTotal()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    sum(d.commission_amount) as total_amount
                  FROM distributor_commissions d
                  INNER JOIN members m
                      ON d.member_id = m.member_id
                  WHERE d.cutoff_id = :cutoff_id
                  AND m.account_type_id = 5;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $result = $command->queryRow();
        
        return $result;
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
    
    public function updateIpdRpCommissionStatus($distributor_commission_id, $status, $userid)
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();
        
        if ($status == 1)
        {
            $query = "UPDATE distributor_commissions
                        SET date_approved = NOW(),
                            status = :status,
                            approved_by_id = :userid
                        WHERE distributor_commission_id = :distributor_commission_id;";
        }
        else if ($status == 2)
        {
            $query = "UPDATE distributor_commissions
                        SET date_claimed = NOW(),
                            status = :status,
                            claimed_by_id = :userid
                        WHERE distributor_commission_id = :distributor_commission_id;";
        }
        
        $command = $conn->createCommand($query);
        
        $command->bindParam(':distributor_commission_id', $distributor_commission_id);
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
    
    public function getCommissionDetails($member_ids, $member_id, $last_cutoff_date, $next_cutoff_date)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    ps.member_id,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS member_name,
                    m.account_type_id,
                    m.ipd_endorser_id,
                    m.endorser_id,
                    DATE_FORMAT(ps.date_purchased,'%M %d, %Y') AS date_purchased,
                    p.product_name,
                    ps.quantity,
                    ps.total,
                    ps.comm_ipd_rate,
                    ps.comm_ibo_rate,
                    ps.rpc_rate
                  FROM purchased_summary ps
                    INNER JOIN member_details md
                      ON ps.member_id = md.member_id
                    LEFT OUTER JOIN members m
                      ON ps.member_id = m.member_id
                    LEFT OUTER JOIN purchased_items pi
                      ON ps.purchase_summary_id = pi.purchase_summary_id
                    LEFT OUTER JOIN products p
                      ON pi.product_id = p.product_id
                  WHERE ps.member_id IN ($member_ids)
                    AND ps.status = 1
                    AND pi.savings <> 0
                    AND ps.date_purchased >= '$last_cutoff_date'
                    AND ps.date_purchased <= '$next_cutoff_date'
                  ORDER BY member_name DESC;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getMemberRepeatPurchaseByCutoff($last_cutoff_date, $next_cutoff_date)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    ps.member_id
                  FROM purchased_summary ps
                    WHERE ps.status = 1
                    AND ps.savings <> 0
                    AND ps.date_purchased >= '$last_cutoff_date'
                    AND ps.date_purchased <= '$next_cutoff_date';";
        
        $command =  $conn->createCommand($query);
        $result = $command->queryColumn();
        
        return $result;
    }
    
    public function checkMemberPurchaseRequirement()
    {
        $reference = new ReferenceModel();
        //$rpc_requirement = $reference->get_variable_value('IPD_REPEAT_PURCHASE_REQUIREMENT');
        $rpc_requirement = 300;
        $conn = $this->_connection;
        
        $query = "SELECT
                    a.member_id,
                    a.total_purchased
                  FROM (SELECT
                      ps.member_id,
                      SUM(ps.`total`) AS total_purchased
                    FROM purchased_summary ps
                    WHERE ps.date_purchased > :last_cutoff
                    AND ps.date_purchased <= :next_cutoff
                    AND ps.status = 1
                    GROUP BY ps.member_id) AS a
                  WHERE a.total_purchased < :rpc_requirement;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':rpc_requirement', $rpc_requirement);
        $command->bindParam(':last_cutoff', $this->last_cutoff_date);
        $command->bindParam(':next_cutoff', $this->next_cutoff_date);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getMembersByCutOff($lists)
    {
        $conn = $this->_connection;
        
        $member_lists = implode(',', $lists);
        
        $query = "SELECT *
                  FROM distributor_commissions dc
                  WHERE dc.cutoff_id = :cutoff_id
                   AND member_id IN ($member_lists)
                   AND dc.status = 0";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function flush_commissions($lists)
    {
        $conn = $this->_connection;        
        $trx = $conn->beginTransaction();
        
        $member_lists = implode(',', $lists);
        
        $query = "UPDATE distributor_commissions dc"
                . " SET dc.status = 3, date_flushout = now()"
                . " WHERE dc.cutoff_id = :cutoff_id"
                . " AND dc.member_id IN ($member_lists)";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $command->execute();
        
        try 
        {
            $trx->commit();
        } 
        catch (PDOException $ex) 
        {
            $trx->rollback();
        }
    }
    
    /**
     * @author Noel Antonio
     * @date 06-04-2014
     */
    public function getIpdRpCommissionBySearchField($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    d.distributor_commission_id,
                    d.member_id,
                    CONCAT(m.last_name, ', ', m.first_name, ' ', m.middle_name) AS member_name,
                    m2.account_type_id,
                    d.commission_amount,
                    d.cutoff_id,
                    d.date_created,
                    DATE_FORMAT(d.date_approved,'%M %d, %Y') AS date_approved,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS approved_by,
                    DATE_FORMAT(d.date_claimed,'%M %d, %Y') AS date_claimed,
                    CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS claimed_by,
                    d.status
                  FROM distributor_commissions d
                    INNER JOIN member_details m
                      ON d.member_id = m.member_id
                    LEFT OUTER JOIN member_details md
                      ON d.approved_by_id = md.member_id
                    LEFT OUTER JOIN member_details md2
                      ON d.claimed_by_id = md2.member_id
                    LEFT OUTER JOIN members m2
                      ON d.member_id = m2.member_id
                  WHERE d.member_id = :member_id
                  AND m2.account_type_id = 5
                  ORDER BY md.last_name;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    /**
     * @author Noel Antonio
     * @date 06-04-2014
     */
    public function getPayoutTotalBySearchField($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    sum(d.commission_amount) as total_amount
                  FROM distributor_commissions d
                  INNER JOIN members m
                      ON d.member_id = m.member_id
                  WHERE d.member_id = :member_id
                  AND m.account_type_id = 5;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryRow();
        
        return $result;
    }
}
?>
