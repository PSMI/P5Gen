<?php

/*
 * @author : owliber
 * @date : 2014-02-06
 */

class PlacementModel extends CFormModel
{
    public $_connection;
    public $member_id;
    public $upline_id;
    public $endorser_id;
    public $upline_name;
    public $downline_id;
    public $downline_name;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
            array('upline_name,upline_id', 'required','message'=>'Upline field is required to assign your downline.'),
        );
    }
    
    public function attributeLabels() {
        return array('upline_id'=>'Upline ID',
                     'member_id'=>'Member Name',
                     'upline_name'=>'Upline Name');
    }
    
    public function getPlacementForApproval()
    {
        $conn = $this->_connection;
        
        $sql = "SELECT
                m.member_id,
                CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) as member_name,
                CONCAT(md1.last_name, ', ', COALESCE(md1.first_name,''), ' ', COALESCE(md1.middle_name,'')) AS placed_by,
                date_format(m.date_created,'%M %d, %Y') as date_joined
              FROM members m
                INNER JOIN pending_placements pp
                  ON m.member_id = pp.member_id
                INNER JOIN member_details md ON m.member_id = md.member_id
                LEFT JOIN member_details md1 ON pp.endorser_id = md1.member_id
              WHERE pp.upline_id = :upline_id
                AND m.placement_status = 0";
        
        $command = $conn->createCommand($sql);
        $command->bindParam(":upline_id", $this->upline_id);
        $result = $command->queryAll();

        return $result;
    }
    
    public function pendingPlacement()
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM pending_placements
                  WHERE member_id = :member_id";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        $result = $command->queryRow();
        return $result;
    }
    
    public function selectAllPendingPlacements()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                        m.member_id,
                        CONCAT(COALESCE(md.last_name), ', ', COALESCE(md.first_name,''), ' ', COALESCE(md.middle_name,'')) as member_name,
                        CONCAT(COALESCE(md1.last_name), ', ', COALESCE(md1.first_name,''), ' ', COALESCE(md1.middle_name,'')) AS endorser,
                        CONCAT(COALESCE(md2.last_name), ', ', COALESCE(md2.first_name,''), ' ', COALESCE(md2.middle_name,'')) AS upline,
                        date_format(m.date_created,'%M %d, %Y') as date_joined
                FROM members m
                INNER JOIN pending_placements pp ON m.member_id = pp.member_id
                INNER JOIN member_details md ON m.member_id = md.member_id
                LEFT JOIN member_details md1 ON pp.endorser_id = md1.member_id
                LEFT JOIN member_details md2 ON pp.upline_id = md2.member_id
                WHERE m.account_type_id = 3;";
        
        $command = $conn->createCommand($query);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function placeUnder()
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();
        
        //Update member upline_id, placement_date, placement_status
        $query = "UPDATE members
                    SET placement_status = :status,
                        placement_date = NOW(),
                        upline_id = :upline_id
                    WHERE member_id = :member_id";
        
        $status = 1;
        
        $command = $conn->createCommand($query);
        $command->bindParam('upline_id', $this->upline_id);
        $command->bindParam(':member_id', $this->member_id);
        $command->bindParam(':status', $status);
        $result = $command->execute();
        
        try
        {
            if(count($result)>0)
            {
                $result2 = $this->addUnprocessedMembers();

                if(count($result2)>0)
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
    
    public function removePlacement()
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();
        
        $query = "UPDATE members SET upline_id = null
                  WHERE member_id = :member_id";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        
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
    
    public function getUnassignedDownlines()
    {
        $conn = $this->_connection;
        
        $sql = "SELECT
                m.member_id,
                CONCAT(md.last_name, ', ', COALESCE(md.first_name, ''), ' ', COALESCE(md.middle_name, '')) AS member_name,
                DATE_FORMAT(m.date_created, '%M %d, %Y') AS date_joined,
                (SELECT CONCAT(md1.last_name,' ',md1.first_name) FROM member_details md1 WHERE md1.member_id = pp.upline_id) AS upline_name,
                m.upline_id
              FROM members m
                INNER JOIN member_details md
                  ON m.member_id = md.member_id
                LEFT JOIN pending_placements pp ON m.member_id = pp.member_id
              WHERE m.endorser_id = :endorser_id
              AND (m.upline_id IS NULL
              OR m.placement_status = 0)";
        
        $command = $conn->createCommand($sql);
        $command->bindParam(":endorser_id", $this->endorser_id);
        $result = $command->queryAll();

        return $result;
    }
    
    public function addPlacement()
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();
        
        $query = "INSERT INTO pending_placements (member_id, endorser_id, upline_id)
                    VALUES (:member_id, :endorser_id, :upline_id)";

        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->downline_id);
        $command->bindParam(':endorser_id', $this->endorser_id);
        $command->bindParam(':upline_id', $this->upline_id);

        $result = $command->execute();

        if(count($result) > 0)
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
    
    public function selectOnlyDownlines($filter)
    {
        $conn = $this->_connection;        
        $filter = "%".$filter."%";
        
        $placeUnder = Networks::getPlaceUnder(Yii::app()->user->getId());
        $downline_lists = Networks::autoComplete($placeUnder);

        $query = "SELECT
                    m.member_id,
                    CONCAT(COALESCE(md.last_name,' '), ', ', COALESCE(md.first_name,' '), ' ', COALESCE(md.middle_name,' ')) AS member_name
                  FROM members m
                    INNER JOIN member_details md ON m.member_id = md.member_id
                  WHERE (md.last_name LIKE :filter
                    OR md.first_name LIKE :filter
                    OR md.middle_name LIKE :filter)
                    AND m.member_id IN ($downline_lists)
                    AND m.member_id != :downline_id
                  ORDER BY md.last_name";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':filter', $filter);
        $command->bindParam(':downline_id', $this->downline_id);
        $result = $command->queryAll();        
        return $result;
    }
    
    public function addUnprocessedMembers()
    {
        $conn = $this->_connection;      
        
        $query = "INSERT INTO unprocessed_members (member_id,upline_id,endorser_id)
                  VALUES (:member_id, :upline_id, :endorser_id)";
        
        $command = $conn->createCommand($query);        
        $command->bindParam(':member_id', $this->member_id);
        $command->bindParam(':upline_id', $this->upline_id);
        $command->bindParam(':endorser_id', $this->endorser_id);
        $result = $command->execute();   
        return $result;
        
    }
    
    public function updateRunningAccount()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        
        //Delete member records on pending_placements via triggers
        //Update all uplines running accounts
        $uplines = Networks::getUplines($this->member_id);
        
        if(!is_null($uplines) && count($uplines)>0)
        {
            $upline_list = implode(',',$uplines);

            $query = "UPDATE running_accounts
                       SET total_member = total_member + 1
                       WHERE member_id IN ($upline_list)";


            $command = $conn->createCommand($query);
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
    }
    
    public function getFloatingPlacements()
    {
        $model = new ReferenceModel();
        $auto_approved_interval = $model->get_variable_value('AUTO_APPROVE_INTERVAL');
        
        $conn = $this->_connection;        
        $query = "SELECT
                    *
                  FROM pending_placements pp
                    WHERE CURDATE() > DATE_ADD(pp.date_endorsed, INTERVAL ".$auto_approved_interval.")";
        
        $command = $conn->createCommand($query);
        $result = $command->queryAll();
        return $result;
        
        
    }
    
}
?>
