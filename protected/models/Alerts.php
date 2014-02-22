<?php

/*
 * @author : owliber
 * @date : 2014-02-22
 */

class Alerts extends CFormModel
{
    public $_connection;
    public $member_id;
    
    public function __construct()
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function check_new_placements()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    count(*) as total
                  FROM pending_placements pp
                  WHERE pp.upline_id = :upline_id;";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':upline_id', $this->member_id);
        $result = $command->queryAll();
        return $result[0]['total'];
    }
    
    public function check_floating_downline()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    count(*) as total
                  FROM members m
                  WHERE m.endorser_id = :endorser_id
                  AND m.upline_id IS NULL
                  AND m.placement_status = 0;";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':endorser_id', $this->member_id);
        $result = $command->queryAll();
        return $result[0]['total'];
    }
}
?>
