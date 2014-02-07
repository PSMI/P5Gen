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
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function getPlacementForApproval()
    {
        $conn = $this->_connection;
        
        $sql = "SELECT
                m.member_id,
                concat(md.last_name, ', ', md.first_name, ' ', md.middle_name) as member_name,
                concat(md1.last_name, ', ', md1.first_name, ' ', md1.middle_name) as placed_by,
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
    
    public function placeUnder($member_id, $upline_id, $action)
    {
        $conn = $this->_connection;
        
        $trx = $conn->beginTransaction();
        
        //Update member upline_id, placement_date, placement_status
        $query = "UPDATE members
                    SET placement_status = :status,
                        placement_date = NOW(),
                        upline_id = :upline_id
                    WHERE member_id = :member_id";
        
        $command = $conn->createCommand($query);
        $command->bindParam('upline_id', $upline_id);
        $command->bindParam(':member_id', $member_id);
        $result = $command->execute();
        
        try
        {
            if(count($result)>0)
            {
                
            }
        }
        catch(PDOException $e)
        {
            
        }
        
    }
}
?>
