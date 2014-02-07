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
              WHERE pp.upline_id = :upline_id";
        
        $command = $conn->createCommand($sql);
        $command->bindParam(":upline_id", $this->upline_id);
        $result = $command->queryAll();

        return $result;
    }
    
    public function pendingPlacement($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM pending_placements
                  WHERE member_id = :member_id";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryRow();
        return $result;
    }
    
    public function placeUnder($member_id, $upline_id)
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
        $command->bindParam('upline_id', $upline_id);
        $command->bindParam(':member_id', $member_id);
        $command->bindParam(':status', $status);
        $result = $command->execute();
        
        try
        {
            if(count($result)>0)
            {
                //Delete member records on pending_placements via triggers
                //Update all uplines running accounts
                $uplines = Networks::getUplines($upline_id);
                
                $upline_list = implode(',',$uplines);
                
                $query2 = "UPDATE running_accounts
                           SET total_member = total_member + 1
                           WHERE member_id IN ($upline_list)";
                
                
                $command2 = $conn->createCommand($query2);
                $result2 = $command2->execute();
                
                if(count($result2) >0 )
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
        catch(PDOException $e)
        {
            $trx->rollback();
            return false;
        }
        
    }
    
    public function removePlacement($member_id)
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();
        
        $query = "UPDATE members SET upline_id = null
                  WHERE member_id = :member_id";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        
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
                CONCAT(md.last_name, ', ', COALESCE(md.first_name,''), ' ', COALESCE(md.middle_name,'')) AS member_name,
                DATE_FORMAT(m.date_created, '%M %d, %Y') AS date_joined
              FROM members m
                INNER JOIN member_details md
                  ON m.member_id = md.member_id
              WHERE m.endorser_id = :endorser_id";
        
        $command = $conn->createCommand($sql);
        $command->bindParam(":endorser_id", $this->endorser_id);
        $result = $command->queryAll();

        return $result;
    }
    
}
?>
