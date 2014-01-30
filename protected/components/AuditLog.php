<?php

/**
 * @author owliber
 * @date Oct 2, 2012
 * @filename AuditLog.php
 * 
 */

class AuditLog extends CFormModel
{
        
    /**
     * 
     * @param int $AID
     * @param int $auditFunctionID
     * @param string $transDetails
     */
    public static function logTransactions($auditFunctionID,$details=NULL)
    {
        $conn = Yii::app()->db;
            
        $remoteIP = $_SERVER['REMOTE_ADDR'];
                
        $UserID = Yii::app()->session['UserID'];
        
        $message = AuditLog::logMessage($auditFunctionID) . " " . $details;
        $query = "INSERT INTO auditlogs (UserID,AuditFunctionID,Details,DateCreated,RemoteIP)
                  VALUE (:UserID,:auditFunctionID,:details,now(),:remoteIP)";

        $sql = $conn->createCommand($query);  
        $sql->bindValues(array(
                    ":UserID"=>$UserID,
                    ":auditFunctionID"=>$auditFunctionID,
                    ":details"=>$message,
                    ":remoteIP"=>$remoteIP,
        ));
        $sql->execute();
       
    }
    
    public static function logMessage($auditFunctionID)
    {
        
        $conn = Yii::app()->db;
        
        $query = "SELECT Name FROM ref_auditfunctions
                  WHERE AuditFunctionID =:auditFunctionID";
        
        $sql = $conn->createCommand($query);
        $sql->bindParam(":auditFunctionID", $auditFunctionID);
        $result = $sql->queryRow();
        
        return $result["Name"];
    }
    
}
?>
