<?php

/*
 * @author : owliber
 * @date : 2014-01-14
 */

class Members extends CActiveRecord
{
    
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }
    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'members';
    }
    
    //perform one-way encryption on the password before we store it in the database
    protected function afterValidate()
    {
        parent::afterValidate();
        $this->Password = $this->hashPassword($this->Password);
    }
    
    public function getUserStatus($username)
    {
        $query = "SELECT status FROM members 
                  WHERE username = :username
                    AND account_type_id IN (1,2,3,4)";
        $sql = Yii::app()->db->createCommand($query);
        $sql->bindParam(":username",$username);
        $result = $sql->queryRow();
        
        if(count($result)> 0)
        {
            return $result['status'];
        }
    }
    
    public function hashPassword($value)
    {
        return md5($value);
    }
    
    public static function checkUsername($username)
    {
        $query = "SELECT * FROM members
                    WHERE username = :username
                        AND account_type_id IN (1,2,3,4)";
        
        $sql = Yii::app()->db->createCommand($query);
        $sql->bindParam(":username",$username);
        $result = $sql->queryAll();
        
        if(count($result)> 0)
            return true;
        else
            return false;
    }
    
    public function getMemberName($id)
    {
        $query = "SELECT CONCAT(last_name, ' ', first_name) as member_name 
                    FROM member_details
                    WHERE member_id = :member_id";
        $command = Yii::app()->db->createCommand($query);
        $command->bindParam(':member_id', $id);
        $result = $command->queryRow();
        return $result['member_name'];
    }
    
    
    public function getPlacementStatus($username)
    {
        $query = "SELECT placement_status FROM members 
                  WHERE username = :username";
        $sql = Yii::app()->db->createCommand($query);
        $sql->bindParam(":username",$username);
        $result = $sql->queryRow();
        
        if(count($result)> 0)
        {
            return $result['placement_status'];
        }
    }
    
    
    public function getUplineDetailsByUserName($username)
    {
        $query = "SELECT
                        m.member_id,
                        CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) as upline_name,
                        md.mobile_no, md.telephone_no
                FROM members m
                INNER JOIN pending_placements pp ON m.member_id = pp.member_id
                INNER JOIN member_details md ON pp.upline_id = md.member_id
                WHERE m.username = :username";
        $command = Yii::app()->db->createCommand($query);
        $command->bindParam(':username', $username);
        $result = $command->queryRow();
        
        return $result;
    }
    
    public function getAccountType($username)
    {
        $query = "SELECT account_type_id FROM members 
                  WHERE username = :username";
        $sql = Yii::app()->db->createCommand($query);
        $sql->bindParam(":username",$username);
        $result = $sql->queryRow();
        
        if(count($result)> 0)
        {
            return $result['account_type_id'];
        }
    }
    
    public function getMembershipType($member_id)
    {
        
        $query = "SELECT account_type_id FROM members 
                  WHERE member_id = :member_id";
        $sql = Yii::app()->db->createCommand($query);
        $sql->bindParam(":member_id",$member_id);
        $result = $sql->queryRow();
        
        if($result['account_type_id'] == 3)
            return 'member';
        
        if($result['account_type_id'] == 5)
            return 'distributor';
    }
    
}
?>
