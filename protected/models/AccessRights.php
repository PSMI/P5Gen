<?php

/*
 * @author : owliber
 * @date : 2014-01-15
 */

class AccessRights extends CFormModel
{
    private $_connection;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function checkUserAccess($account_type_id)
    {
        $link = $this->getControllerAction();
        
        $sql = "SELECT
                    *
                  FROM access_rights ar
                    INNER JOIN menus m ON ar.menu_id = m.menu_id
                    WHERE m.menu_link = :link
                    AND ar.account_type_id = :account_type_id
                    AND m.status = 1";
        
        $command = $this->_connection->createCommand($sql);
        $command->bindParam(":account_type_id", $account_type_id);
        $command->bindParam(":link", $link);
        $result = $command->queryAll();
              
        if(count($result)>0)
            return true;
        else
            return false;
         
    }


    public function getMenus($account_type_id)
    {
        
        $sql = "SELECT
                    DISTINCT(m.menu_id), m.menu_name, m.menu_link, m.menu_icon, ar.default_menu_id, m.status
                  FROM access_rights ar
                    INNER JOIN menus m ON ar.menu_id = m.menu_id
                    WHERE ar.account_type_id = :account_type_id 
                        AND m.status = 1 ;";
        
        $command = $this->_connection->createCommand($sql);
        $command->bindParam(":account_type_id", $account_type_id);
        $result = $command->queryAll();
              
        return $result; 
        
    }
    
    public function getSubMenus($menu_id, $account_type_id)
    {
        
        $sql = "SELECT
                    DISTINCT(sm.submenu_id), ar.menu_id, sm.submenu_name, sm.submenu_link, sm.status
                  FROM access_rights ar
                    INNER JOIN submenus sm ON ar.menu_id = sm.menu_id
                    WHERE ar.account_type_id = :account_type_id 
                        AND sm.menu_id = :menu_id
                        AND sm.status = 1 ;";
        
        $command = $this->_connection->createCommand($sql);
        $command->bindParam(":account_type_id", $account_type_id);
        $command->bindParam(":menu_id", $menu_id);
        $result = $command->queryAll();
              
        return $result; 
        
    }
    
    public function getControllerAction()
    {
        return Yii::app()->controller->getUniqueId() .'/'. Yii::app()->controller->action->id;
    }
    
}
?>
