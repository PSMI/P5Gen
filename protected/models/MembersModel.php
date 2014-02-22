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
                        $beneficiary_name, $relationship, $status)
    {
        $connection = $this->_connection;
        $beginTrans = $connection->beginTransaction();
        
        try
        {
            $hashedPassword = md5($password);
            $sql = "INSERT INTO members (account_type_id, username, password, status) 
                    VALUES (:account_type_id, :username, :password, :status)";
            $command = $connection->createCommand($sql);
            $command->bindValue(':account_type_id', $account_type_id);
            $command->bindValue(':username', $username);
            $command->bindValue(':password', $hashedPassword);
            $command->bindValue(':status', $status);
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
                        
                        $member_id = $connection->getLastInsertID();
                        $param['member_id'] = $member_id;
                        $param['plain_password'] = $password;
                        Mailer::accountCreation($param);
                        
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
        $query = "SELECT * 
                  FROM unprocessed_members 
                  WHERE status = :status
                  LIMIT 10";
        $command = $conn->createCommand($query);
        $command->bindParam(':status', $this->status);
        return $command->queryAll();
    }
    
    public function updateUnprocessedMembers()
    {

        $conn = $this->_connection;
        $query = "UPDATE unprocessed_members
                    SET status = :status
                  WHERE member_id = :member_id";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        $command->bindParam(':status', $this->status);
        $result = $command->execute();
        return $result;
    }
    
    public function getMemberNetworkCount($interval,$min_count)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    m.member_id,
                    CONCAT(COALESCE(md.last_name, ''), ' ', COALESCE(md.first_name, ''), ' ', COALESCE(md.middle_name, '')) AS member_name,
                    ra.total_member,
                    m.date_created AS date_joined,
                    DATE_ADD(m.date_created, INTERVAL ".$interval." MONTH) AS promo_end_date,
                    ra.date_last_updated AS date_completed
                  FROM members m
                    INNER JOIN member_details md
                      ON m.member_id = md.member_id
                    INNER JOIN running_accounts ra
                      ON m.member_id = ra.member_id
                  WHERE ra.total_member >= :min_member_count
                  AND ra.date_last_updated <= DATE_ADD(m.date_created, INTERVAL ".$interval." MONTH);";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':min_member_count', $min_count);
        $result = $command->queryAll();
        return $result;
    }
    
    public function get_count_with_flush_out()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    count(*) as total_direct_endorse
                  FROM members m
                  WHERE m.member_id = :member_id
                  AND m.date_created > DATE_ADD(m.date_created, INTERVAL 3 MONTH);";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        $result = $command->queryRow();
        return $result;
    }
    
    public function remove_processed()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        $query = "DELETE FROM unprocessed_members WHERE status = :status";
        $command = $conn->createCommand($query);
        $command->bindParam(':status', $this->status);
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
            throw $e;
        }
    }
    
    public function checkExistingEmailAndUsername($email, $username)
    {
        $connection = $this->_connection;
        
        $sql = "SELECT a.member_id, a.username, b.email FROM members a
            INNER JOIN member_details b ON a.member_id = b.member_id
            WHERE b.email = :email AND a.username = :username";
        $command = $connection->createCommand($sql);
        $command->bindParam(":email", $email);
        $command->bindParam(":username", $username);
        $result = $command->queryRow();
        
        return $result;
    }
}
?>
