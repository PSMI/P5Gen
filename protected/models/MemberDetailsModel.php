<?php

/**
 * @author Noel Antonio
 * @date 01-30-2014
 */
class MemberDetailsModel extends CFormModel
{
    public $_connection;
    public $member_id;
    public $last_name;
    public $first_name;
    public $middle_name;
    public $address1;
    public $address2;
    public $address3;
    public $zip_code;
    public $gender;
    public $civil_status;
    public $birth_date;
    public $mobile_no;
    public $telephone_no;
    public $email;
    public $tin_no;
    public $company;
    public $occupation;
    public $spouse_name;
    public $spouse_contact_no;
    public $beneficiary_name;
    public $relationship;
    public $autocomplete_name;
    public $date_joined;
    
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
                array('last_name, first_name, middle_name
                        address1, civil_status
                        mobile_no, email, gender, birth_date, date_joined', 'required'),
            
                array('member_id, address2, address3, zip_code, telephone_no, tin_no
                        company, occupation, 
                        beneficiary_name, relationship, spouse_name, spouse_contact_no', 'safe'),
            
                array('email', 'email'),
            
                array('mobile_no, tin_no, spouse_contact_no', 'numerical', 'integerOnly'=>true)
            );
    }
    
    public function attributeLabels()
    {
        return array(
                'member_id' => 'ID',
                'last_name' => 'Last Name',
                'first_name' => 'First Name',
                'address1' => 'House/Unit/Street No',
                'address2' => 'Subdivision/Village/Brgy',
                'address3' => 'Municipality/City/Province',
                'zip_code' => 'Zip Code',
                'gender'=>'Gender',
                'civil_status'=>'Civil Status',
                'birth_date'=>'Birth Date',
                'mobile_no'=>'Mobile Number',
                'telephone_no'=>'Telephone Number',
                'email'=>'Email',
                'tin_no'=>'TIN',
                'company'=>'Company',
                'occupation'=>'Occupation',
                'spouse_name'=>'Spouse Name',
                'spouse_contact_no'=>'Spouse Contact Number',
                'beneficiary_name'=>'Beneficiary',
                'relationship'=>'Relationship',
                'status'=>'Status',
                'date_joined'=>'Date Joined'
        );
    }
    
    public function selectAllAdminDetails()
    {
        $connection = $this->_connection;
        
        $sql = "SELECT a.member_id, a.last_name, a.first_name, a.middle_name,
                a.birth_date, a.mobile_no, a.email, b.username
                FROM member_details a
                INNER JOIN members b ON a.member_id = b.member_id
                WHERE b.account_type_id = 2";
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function selectAdminDetailsBySearchField($searchField)
    {
        $connection = $this->_connection;
        
        $sql = "SELECT a.member_id, a.last_name, a.first_name, a.middle_name,
                a.birth_date, a.mobile_no, a.email, b.username
                FROM member_details a
                INNER JOIN members b ON a.member_id = b.member_id
                WHERE (a.last_name LIKE :searchField OR a.first_name LIKE :searchField) AND b.account_type_id IN (1, 2)";
        $command = $connection->createCommand($sql);
        $keyword = "%" . $searchField . "%";
        $command->bindParam(":searchField", $keyword);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function selectAllMemberDetails()
    {
        $connection = $this->_connection;
        
        $sql = "SELECT a.member_id, a.last_name, a.first_name, a.middle_name,
                a.birth_date, a.mobile_no, a.email, b.endorser_id, b.upline_id, b.username,
                CASE b.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Active'
                WHEN 2 THEN 'Inactive' WHEN 3 THEN 'Terminated' WHEN 4 THEN 'Banned' END AS status
                FROM member_details a
                INNER JOIN members b ON a.member_id = b.member_id
                WHERE b.account_type_id = 3
                ORDER BY a.last_name";
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function selectMemberById($id)
    {
        $connection = $this->_connection;
        
        $sql = "SELECT * FROM member_details WHERE member_id = :member_id";
        $command = $connection->createCommand($sql);
        $command->bindParam(":member_id", $id);
        $result = $command->queryRow();
        
        return $result;
    }
    
    public function updateMemberDetails()
    {
        $connection = $this->_connection;
        $beginTrans = $connection->beginTransaction();
        
        try
        {
            $sql = "UPDATE member_details SET last_name = :last_name, first_name = :first_name,
                        middle_name = :middle_name, address1 = :address1, address2 = :address2,
                        address3 = :address3, zip_code = :zip_code, gender = :gender, civil_status = :civil_status,
                        birth_date = :birth_date, mobile_no = :mobile_no, telephone_no = :telephone_no,
                        email = :email, tin_no = :tin_no, company = :company, occupation = :occupation,
                        spouse_name = :spouse_name, spouse_contact_no = :spouse_contact_no, beneficiary_name = :beneficiary_name,
                        relationship = :relationship
                    WHERE member_id = :member_id";
            $command = $connection->createCommand($sql);
            $command->bindValue(':member_id', $this->member_id);
            $command->bindValue(':last_name', $this->last_name);
            $command->bindValue(':first_name', $this->first_name);
            $command->bindValue(':middle_name', $this->middle_name);
            $command->bindValue(':address1', $this->address1);
            $command->bindValue(':address2', $this->address2);
            $command->bindValue(':address3', $this->address3);
            $command->bindValue(':zip_code', $this->zip_code);
            $command->bindValue(':gender', $this->gender);
            $command->bindValue(':civil_status', $this->civil_status);
            $command->bindValue(':birth_date', $this->birth_date);
            $command->bindValue(':mobile_no', $this->mobile_no);
            $command->bindValue(':telephone_no', $this->telephone_no);
            $command->bindValue(':email', $this->email);
            $command->bindValue(':tin_no', $this->tin_no);
            $command->bindValue(':company', $this->company);
            $command->bindValue(':occupation', $this->occupation);
            $command->bindValue(':spouse_name', $this->spouse_name);
            $command->bindValue(':spouse_contact_no', $this->spouse_contact_no);
            $command->bindValue(':beneficiary_name', $this->beneficiary_name);
            $command->bindValue(':relationship', $this->relationship);
            $command->execute();
            
            $sql2 = "UPDATE members SET date_joined = :date_joined WHERE member_id = :member_id";
            $command2 = $connection->createCommand($sql2);
            $command2->bindParam(':date_joined', $this->date_joined);
            $command2->bindParam(':member_id', $this->member_id);
            $command2->execute();
            
            //if ($rowCount > 0) {
            if(!$this->hasErrors())    
            {
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
    
    public function selectMemberDetailsBySearchField($searchField)
    {
        $connection = $this->_connection;
        
        /*$sql = "SELECT a.member_id, a.last_name, a.first_name, a.middle_name,
                a.birth_date, a.mobile_no, a.email, b.endorser_id, b.upline_id, b.username,
                CASE b.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Active'
                WHEN 2 THEN 'Inactive' WHEN 3 THEN 'Terminated' WHEN 4 THEN 'Banned' END AS status
                FROM member_details a
                INNER JOIN members b ON a.member_id = b.member_id
                WHERE (a.last_name LIKE :searchField OR a.first_name LIKE :searchField) AND b.account_type_id = 3";
        $command = $connection->createCommand($sql);
        $keyword = "%" . $searchField . "%";
        $command->bindParam(":searchField", $keyword);*/
        
        $sql = "SELECT a.member_id, a.last_name, a.first_name, a.middle_name,
                a.birth_date, a.mobile_no, a.email, b.endorser_id, b.upline_id, b.username,
                CASE b.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Active'
                WHEN 2 THEN 'Inactive' WHEN 3 THEN 'Terminated' WHEN 4 THEN 'Banned' END AS status
                FROM member_details a
                INNER JOIN members b ON a.member_id = b.member_id
                WHERE b.member_id = :member_id AND b.account_type_id = 3
                ORDER BY a.last_name";
        $command = $connection->createCommand($sql);
        $command->bindParam(":member_id", $searchField);
        
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function checkExistingEmail($email)
    {
        $connection = $this->_connection;
        
        $sql = "SELECT member_id, email FROM member_details WHERE email = :email";
        $command = $connection->createCommand($sql);
        $command->bindParam(":email", $email);
        $result = $command->queryRow();
        
        return $result;
    }
    
    public function autoCompleteSearch($filter)
    {
        $conn = $this->_connection;        
        $filter = "%".$filter."%";                      
        
        $query = "SELECT
                    m.member_id,
                    CONCAT(COALESCE(md.last_name,' '), ', ', COALESCE(md.first_name,' '), ' ', COALESCE(md.middle_name,' ')) AS member_name
                  FROM members m
                    INNER JOIN member_details md ON m.member_id = md.member_id
                  WHERE (md.last_name LIKE :filter
                    OR md.first_name LIKE :filter
                    OR md.middle_name LIKE :filter)
                    AND m.account_type_id = 3
                  ORDER BY md.last_name";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':filter', $filter);
        $result = $command->queryAll();        
        return $result;
    }
    
    public function selectDistributorDetailsBySearchField($searchField)
    {
        $connection = $this->_connection;
        
        $sql = "SELECT a.member_id, a.last_name, a.first_name, a.middle_name,
                a.birth_date, a.mobile_no, a.email, b.endorser_id, b.ipd_endorser_id, b.upline_id, b.username,
                CASE b.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Active'
                WHEN 2 THEN 'Inactive' WHEN 3 THEN 'Terminated' WHEN 4 THEN 'Banned' END AS status
                FROM member_details a
                INNER JOIN members b ON a.member_id = b.member_id
                WHERE b.member_id = :member_id AND b.account_type_id = 5
                ORDER BY a.last_name";
        $command = $connection->createCommand($sql);
        $command->bindParam(":member_id", $searchField);
        
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function selectAllDistributorDetails()
    {
        $connection = $this->_connection;
        
        $sql = "SELECT a.member_id, a.last_name, a.first_name, a.middle_name,
                a.birth_date, a.mobile_no, a.email, b.endorser_id, b.upline_id, b.username, b.ipd_endorser_id,
                CASE b.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Active'
                WHEN 2 THEN 'Inactive' WHEN 3 THEN 'Terminated' WHEN 4 THEN 'Banned' END AS status
                FROM member_details a
                INNER JOIN members b ON a.member_id = b.member_id
                WHERE b.account_type_id = 5
                ORDER BY a.last_name";
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();
        
        return $result;
    }
}
?>
