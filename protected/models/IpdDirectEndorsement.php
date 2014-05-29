<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 03-31-2014
------------------------*/

class IpdDirectEndorsement extends CFormModel
{
    public $_connection;
    public $member_id;
    public $endorser_id;
    public $cutoff_id;
    public $payout_rate;
    public $date_claimed;
    public $direct_endorsement_id;
    
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
        return array('cutoff_id'=>'Cut-Off Date&nbsp;',
                     'date_claimed'=>'Date Claimed');
    }
    
    public function getDirectEndorsement()
    {
//        $model = new ReferenceModel();
//        $payout_rate = $model->get_payout_rate(TransactionTypes::DIRECT_ENDORSE);
        
        $conn = $this->_connection;
        
        $query = "SELECT
                    d.direct_endorsement_id,
                    d.endorser_id,
                    d.cutoff_id,
                    CONCAT(md.last_name, ', ', md.first_name) AS member_name,
                    DATE_FORMAT(d.date_created, '%M %d, %Y') AS date_created,
                    DATE_FORMAT(d.date_approved, '%M %d, %Y') AS date_approved,
                    CONCAT(md2.last_name, ', ', md2.first_name) AS approved_by,
                    DATE_FORMAT(d.date_claimed, '%M %d, %Y') AS date_claimed,
                    CONCAT(md3.last_name, ', ', md3.first_name) AS claimed_by,
                    CONCAT(md4.last_name, ', ', md4.first_name) AS endorser_name,                    
                    COUNT(d.endorser_id) AS ipd_count,
                    FORMAT(SUM(d.amount),2) AS total_payout,
                    d.status
                  FROM distributor_endorsements d
                    LEFT OUTER JOIN member_details md
                      ON d.distributor_id = md.member_id
                    LEFT OUTER JOIN member_details md2
                      ON d.approved_by_id = md2.member_id
                    LEFT OUTER JOIN member_details md3
                      ON d.claimed_by_id = md3.member_id
                    LEFT OUTER JOIN member_details md4
                      ON d.endorser_id = md4.member_id
                  WHERE d.cutoff_id = :cutoff_id
                  GROUP BY d.endorser_id
                  ORDER BY md4.last_name;
                ";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
//        $command->bindParam(':payout_rate', $payout_rate);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getEndorseeByCutoff()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    d.direct_endorsement_id,
                    CONCAT(md.last_name, ', ', md.first_name) AS member_name,
                    CONCAT(md1.last_name, ', ', md1.first_name) AS upline_name,
                    DATE_FORMAT(m.date_joined, '%M %d, %Y') AS date_joined
                  FROM distributor_endorsements d
                    INNER JOIN member_details md
                      ON d.distributor_id = md.member_id
                    INNER JOIN members m ON d.distributor_id = m.member_id
                    LEFT OUTER JOIN member_details md1
                      ON m.upline_id = md1.member_id
                  WHERE d.cutoff_id = :cutoff_id
                  AND d.endorser_id = :endorser_id
                  ORDER BY md.last_name;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $command->bindParam(':endorser_id', $this->endorser_id);
        $result = $command->queryAll();        
        return $result;
    }
    
    public function getEndorsementTotalAmount()
    {
        
        $conn = $this->_connection;
        
        $query = "SELECT
                    SUM(amount) AS total_amount,
                    count(*) AS total_ibo
                  FROM distributor_endorsements d
                  WHERE d.cutoff_id = :cutoff_id
                  AND d.endorser_id = :endorser_id
                  GROUP BY d.endorser_id";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $command->bindParam(':endorser_id', $this->endorser_id);
        $result = $command->queryRow();        
        return $result;
    }
    
    public function getPayoutTotal()
    {
        
        $conn = $this->_connection;
        
        $query = "SELECT
                    SUM(amount) AS total_amount,
                    COUNT(*) AS total_ipd
                  FROM distributor_endorsements d
                  WHERE d.cutoff_id = :cutoff_id";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $result = $command->queryRow();        
        return $result;
    }
    
    public function updateDirectEndorsementStatus($endorser_id, $cutoff_id, $status, $userid, $date_claimed=null)
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();
        
        if ($status == 1)
        {
            $query = "UPDATE distributor_endorsements
                        SET date_approved = NOW(),
                            status = :status,
                            approved_by_id = :userid
                        WHERE endorser_id = :endorser_id 
                        AND cutoff_id = :cutoff_id;";
        }
        else if ($status == 2)
        {
            if($date_claimed != null)
                $query = "UPDATE distributor_endorsements
                            SET date_claimed = '$date_claimed',
                                status = :status,
                                claimed_by_id = :userid
                            WHERE endorser_id = :endorser_id 
                            AND cutoff_id = :cutoff_id;";
            else
                $query = "UPDATE distributor_endorsements
                        SET date_claimed = NOW(),
                            status = :status,
                            claimed_by_id = :userid
                        WHERE endorser_id = :endorser_id 
                        AND cutoff_id = :cutoff_id;";
        }
        
        $command = $conn->createCommand($query);
        
        $command->bindParam(':status', $status);
        $command->bindParam(':userid', $userid);
        $command->bindParam(':endorser_id', $endorser_id);
        $command->bindParam(':cutoff_id', $cutoff_id);

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
    public function getDirectEndoserCountByID($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    COUNT(*) AS total
                  FROM members m
                  WHERE m.endorser_id = :member_id
                  GROUP BY m.endorser_id;;";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryRow();
        
        return $result;
    }
    
    public function getDirectEndorser($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT                 
                      endorser_id as endorser
                  FROM members m
                  WHERE m.member_id = :member_id;";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryRow();
        
        return $result;
    }
    
    public function check_transactions($endorsers,$cutoff_id)
    {
        $conn = $this->_connection;
        
        $member_ids = implode(',',$endorsers);
        
        $query = "SELECT * FROM distributor_endorsements 
                    WHERE distributor_id IN ($member_ids)
                        AND cutoff_id = :cutoff_id
                        AND status = 0
                    ";
        $command = $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $cutoff_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function add_transactions()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
                
        $query = "INSERT INTO distributor_endorsements (cutoff_id,endorser_id,distributor_id,amount) 
                        VALUES (:cutoff_id, :endorser_id, :member_id, :payout_rate)";
                 
        $command = $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $command->bindParam(':endorser_id', $this->endorser_id);
        $command->bindParam(':member_id', $this->member_id);
        $command->bindParam(':payout_rate', $this->payout_rate);
        $result = $command->execute();        
        
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
    
    public function count_transactions()
    {
        $conn = $this->_connection;
        
        $query = "SELECT count(*) as trans_count"
                . " FROM distributor_endorsements"
                . " WHERE endorser_id = :endorser_id";
        $command = $conn->createCommand($query);
        $command->bindParam(':endorser_id', $this->endorser_id);
        $result = $command->queryRow();
        
        return $result['trans_count'];
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
        $command->bindParam(':member_id', $this->member_id);
        return $command->queryRow();
    }
}
?>
