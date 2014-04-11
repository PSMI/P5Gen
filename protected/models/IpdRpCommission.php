<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-11-2014
------------------------*/

class IpdRpCommission extends CFormModel
{
    public $_connection;
    public $member_id;
    public $cutoff_id;
    public $next_cutoff_date;
    public $last_cutoff_date;    
    
    public function __construct()
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
            array('cutoff_id','required'),
        );
    }
    
    public function attributeLabels() {
        return array('cutoff_id'=>'Cut-Off Date');
    }
    
    public function getIpdRpCommission()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    d.distributor_commission_id,
                    d.member_id,
                    CONCAT(m.last_name, ', ', m.first_name, ' ', m.middle_name) AS member_name,
                    d.commission_amount,
                    d.cutoff_id,
                    d.date_created,
                    DATE_FORMAT(d.date_approved,'%M %d, %Y') AS date_approved,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS approved_by,
                    DATE_FORMAT(d.date_claimed,'%M %d, %Y') AS date_claimed,
                    CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS claimed_by,
                    d.status
                  FROM distributor_commissions d
                    INNER JOIN member_details m
                      ON d.member_id = m.member_id
                    LEFT OUTER JOIN member_details md
                      ON d.approved_by_id = md.member_id
                    LEFT OUTER JOIN member_details md2
                      ON d.claimed_by_id = md2.member_id
                    LEFT OUTER JOIN members m2
                      ON d.member_id = m2.member_id
                  WHERE d.cutoff_id = :cutoff_id
                  AND m2.account_type_id = 5
                  ORDER BY md.last_name;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function getPayoutTotal()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    sum(d.commission_amount) as total_amount
                  FROM distributor_commissions d
                  INNER JOIN members m
                      ON d.member_id = m.member_id
                  WHERE d.cutoff_id = :cutoff_id
                  AND m.account_type_id = 5;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $result = $command->queryRow();
        
        return $result;
    }
}
?>
