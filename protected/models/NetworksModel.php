<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class NetworksModel extends CFormModel
{
    public $_connection;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function getProfileInfo($member_id)
    {
        $connection = $this->_connection;
        
        $sql = "SELECT a.member_id, a.username, a.password, b.last_name, b.first_name, b.middle_name, 
                CASE b.gender WHEN 1 THEN 'Male' WHEN 2 THEN 'Female' END AS gender,
                CASE b.civil_status WHEN 1 THEN 'Single' WHEN 2 THEN 'Married' WHEN 3 THEN 'Divorced'
                WHEN 4 THEN 'Separated' WHEN 5 THEN 'Widow' END AS civil_status,
                b.birth_date, b.spouse_name, b.spouse_contact_no, b.beneficiary_name,
                b.company, b.tin_no, b.email, b.address1, b.telephone_no, b.mobile_no, c.occupation_name,
                d.relationship_name, a.endorser_id, a.upline_id
                FROM members a
                INNER JOIN member_details b ON a.member_id = b.member_id
                LEFT JOIN ref_occupations c ON b.occupation_id = c.occupation_id
                LEFT JOIN ref_relationships d ON b.relationship_id = d.relationship_id
                WHERE a.member_id = :member_id";
        
        $command = $connection->createCommand($sql);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryRow();
        
        return $result;
    }
    
    public function getDirectEndorse($member_id)
    {
        $connection = $this->_connection;
        
        $sql = "SELECT a.member_id, b.last_name, b.first_name, b.middle_name, a.date_created
                FROM members a
                INNER JOIN member_details b ON a.member_id = b.member_id
                WHERE a.upline_id = :member_id";
        $command = $connection->createCommand($sql);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
    }
}
?>
