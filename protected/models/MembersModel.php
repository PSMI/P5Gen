<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class MembersModel extends CFormModel
{
    public $_connection;
    public $member_id;
    public $status;
    public $username;
    public $password;
    public $account_type_id;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
                array('member_id, status, username, password, account_type_id', 'required'),
            );
    }
    
    public function selectMemberMaxId()
    {
        $connection = $this->_connection;
        
        $sql = "SELECT MAX(member_id) AS member_id
                FROM members";
        $command = $connection->createCommand($sql);
        $result = $command->queryRow();
        
        return $result["member_id"] + 1;
    }
    
    public function selectMemberName($id)
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
    
    public function insertNewMemberAccount($account_type_id, $username, $password,
                        $last_name, $first_name, $middle_name, $address1, $address2, $address3,
                        $zip_code, $gender, $civil_status, $birth_date, $mobile_no, $telephone_no,
                        $email, $tin_no, $company, $occupation, $spouse_name, $spouse_contact_no,
                        $beneficiary_name, $relationship)
    {
        $connection = $this->_connection;
        $beginTrans = $connection->beginTransaction();
        
        try
        {
            $sql = "INSERT INTO members (account_type_id, username, password) 
                    VALUES (:account_type_id, :username, :password)";
            $command = $connection->createCommand($sql);
            $command->bindValue(':account_type_id', $account_type_id);
            $command->bindValue(':username', $username);
            $command->bindValue(':password', md5($password));
            $rowCount = $command->execute();
            
            if ($rowCount > 0)
            {
                $last_inserted_id = $connection->getLastInsertID();
                
                $sql2 = "INSERT INTO member_details (member_id, last_name, first_name, middle_name, address1, address2, address3,
                            zip_code, gender, civil_status, birth_date, mobile_no, telephone_no,
                            email, tin_no, company, occupation, spouse_name, spouse_contact_no,
                            beneficiary_name, relationship) 
                    VALUES (:member_id, :last_name, :first_name, :middle_name, :address1, :address2, :address3,
                            :zip_code, :gender, :civil_status, :birth_date, :mobile_no, :telephone_no,
                            :email, :tin_no, :company, :occupation, :spouse_name, :spouse_contact_no,
                            :beneficiary_name, :relationship)";
                $command2 = $connection->createCommand($sql2);
                $command2->bindValue(':member_id', $last_inserted_id);
                $command2->bindValue(':last_name', $last_name);
                $command2->bindValue(':first_name', $first_name);
                $command2->bindValue(':middle_name', $middle_name);
                $command2->bindValue(':address1', $address1);
                $command2->bindValue(':address2', $address2);
                $command2->bindValue(':address3', $address3);
                $command2->bindValue(':zip_code', $zip_code);
                $command2->bindValue(':gender', $gender);
                $command2->bindValue(':civil_status', $civil_status);
                $command2->bindValue(':birth_date', $birth_date);
                $command2->bindValue(':mobile_no', $mobile_no);
                $command2->bindValue(':telephone_no', $telephone_no);
                $command2->bindValue(':email', $email);
                $command2->bindValue(':tin_no', $tin_no);
                $command2->bindValue(':company', $company);
                $command2->bindValue(':occupation', $occupation);
                $command2->bindValue(':spouse_name', $spouse_name);
                $command2->bindValue(':spouse_contact_no', $spouse_contact_no);
                $command2->bindValue(':beneficiary_name', $beneficiary_name);
                $command2->bindValue(':relationship', $relationship);
                $rowCount2 = $command2->execute();
                
                if ($rowCount2 > 0) {
                        $beginTrans->commit();
                        return true;
                } else {
                    $beginTrans->rollback();  
                    return false;
                }
            }
        }
        catch (CDbException $e)
        {
            $beginTrans->rollback();  
            return false;
        }
    }
    
    public function selectMemberDetails($id)
    {
        $connection = $this->_connection;
        
        $sql = "SELECT *
                FROM members a 
                INNER JOIN member_details b ON a.member_id = b.member_id
                WHERE a.member_id = :member_id";
        $command = $connection->createCommand($sql);
        $command->bindParam(":member_id", $id);
        $result = $command->queryRow();
        
        return $result;
    }
    
    public function changePassword($id, $new_pass)
    {
        $connection = $this->_connection;
        $beginTrans = $connection->beginTransaction();
        
        try
        {
            $sql = "UPDATE members SET password = :password
                    WHERE member_id = :member_id";
            $command = $connection->createCommand($sql);
            
            $hashedPassword = md5($new_pass);
            
            $command->bindValue(':member_id', $id);
            $command->bindValue(':password', $hashedPassword);
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
    
    /**
     * 
     * @return type
     * @author owliber
     */
    public function getUnprocessedMembers()
    {
        $conn = $this->_connection;
        $query = "SELECT * FROM unprocessed_members LIMIT 1";
        $command = $conn->createCommand($query);
        return $command->queryAll();
    }
}
?>
