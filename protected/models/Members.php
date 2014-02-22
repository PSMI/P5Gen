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
                  WHERE username = :username";
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
                    WHERE username = :username";
        
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
    
}
?>
