<?php
/*------------------------
 * Author: J.O. Pormento
 * Date Created: 02-11-2014
------------------------*/

class DirectEndorsementMember extends CFormModel
{
    public $_connection;
    
    public function __construct()
    {
        $this->_connection = Yii::app()->db;
    }
    
    public function getDirectEndorsement($dateFrom, $dateTo)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    d.direct_endorsement_id,
                    CONCAT(m.last_name, ', ', m.first_name, ' ', m.middle_name) AS endorser_name,
                    CONCAT(md.last_name, ', ', md.first_name, ' ', md.middle_name) AS member_name,
                    d.date_created,
                    d.date_released,
                    CONCAT(md2.last_name, ', ', md2.first_name, ' ', md2.middle_name) AS released_by,
                    d.status
                  FROM direct_endorsements d
                    INNER JOIN member_details m
                      ON d.endorser_id = m.member_id
                    LEFT OUTER JOIN member_details md
                      ON d.endorser_id = md.member_id
                    LEFT OUTER JOIN member_details md2
                      ON d.released_by_id = md2.member_id
                  WHERE date_created BETWEEN :dateFrom AND :dateTo AND d.status = 1;";
        
        $command =  $conn->createCommand($query);
        $command->bindParam(':dateFrom', $dateFrom);
        $command->bindParam(':dateTo', $dateTo);
        $result = $command->queryAll();
        
        return $result;
    }
}
?>
