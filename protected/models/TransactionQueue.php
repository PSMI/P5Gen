<?php

/*
 * @author : owliber
 * @date : 2014-03-10
 */

class TransactionQueue extends CFormModel
{
    public $_connection;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function get_pending_transactions()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    um.unprocessed_log_id,
                    CONCAT(COALESCE(md.last_name, ''), ', ', COALESCE(md.first_name, '')) AS member_name,
                    CONCAT(COALESCE(md1.last_name, ''), ', ', COALESCE(md1.first_name, '')) AS endorser_name,
                    CONCAT(COALESCE(md2.last_name, ''), ', ', COALESCE(md2.first_name, '')) AS upline_name,
                    CASE `um`.status WHEN 0 THEN 'For commission processing' 
                    WHEN 1 THEN 'For direct endorsement processing.' 
                    WHEN 2 THEN 'For unilevel processing.' 
                    WHEN 3 THEN 'For loan direct processing.'
                    WHEN 4 THEN 'For loan completion processing.' 
                    WHEN 5 THEN 'Completed'
                    END `status`
                  FROM unprocessed_members um
                    INNER JOIN member_details md
                      ON um.member_id = md.member_id
                    LEFT JOIN member_details md1
                      ON um.endorser_id = md1.member_id
                    LEFT JOIN member_details md2
                      ON um.upline_id = md2.member_id
                  WHERE um.`status` < 5
                    ORDER BY um.unprocessed_log_id;";
        $command = $conn->createCommand($query);
        $result = $command->queryAll();
        return $result;
    }
}
?>
