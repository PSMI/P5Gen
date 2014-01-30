<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class MemberDetailsModel extends CFormModel
{
    public $_connection;
    public $member_id;
    public $status;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
                array('member_id, status', 'required'),
            );
    }
    
    public function selectMemberDetailsStatus($id)
    {
        $connection = $this->_connection;
        
        $sql = "SELECT a.member_id, a.status, b.last_name, b.middle_name, b.first_name
                FROM members a 
                INNER JOIN member_details b ON a.member_id = b.member_id
                WHERE a.member_id = :member_id";
        $command = $connection->createCommand($sql);
        $command->bindParam(":member_id", $id);
        $result = $command->queryRow();
        
        return $result;
    }
    
    public function updateMemberStatus()
    {
        $connection = $this->_connection;
        $beginTrans = $connection->beginTransaction();
        
        try
        {
            $sql = "UPDATE members SET status = :status
                    WHERE member_id = :member_id";
            $command = $connection->createCommand($sql);
            $command->bindValue(':member_id', $this->member_id);
            $command->bindValue(':status', $this->status);
            $rowCount = $command->execute();
            
            if ($rowCount > 0) {
                    $beginTrans->commit();
                    return true;
            } else {
                $beginTrans->rollback();  
                return false;
            }
        }
        catch (CDbException $e)
        {
            $beginTrans->rollback();  
            return false;
        }
    }
}
?>
