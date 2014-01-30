<?php

/*
 * @author : owliber
 * @date : 2014-01-15
 */

class AccessRights extends CFormModel
{
    public function hasAccess($account_type_id)
    {
        $link = $this->getControllerAction();
        
        $query = "SELECT
                    *
                  FROM access_rights ar
                    INNER JOIN menus m ON ar.menu_id = m.menu_id
                    WHERE m.link = :link
                    AND ar.account_type_id = :account_type_id
                    AND m.status = 1";
        
        $sql = Yii::app()->db->createCommand($query);
        $sql->bindParam(":account_type_id", $account_type_id);
        $sql->bindParam(":link", $link);
        $result = $sql->queryAll();
              
        if(count($result)>0)
            return true;
        else
            return false;
         
    }


    public static function getMenus($account_type_id)
    {
        
        $query = "SELECT
                    DISTINCT(m.menu_id), m.menu_name, m.menu_link, m.menu_icon, ar.default_menu_id, m.status
                  FROM access_rights ar
                    INNER JOIN menus m ON ar.menu_id = m.menu_id
                    WHERE ar.account_type_id = :account_type_id 
                        AND m.status = 1 ;";
        
        $sql = Yii::app()->db->createCommand($query);
        $sql->bindParam(":account_type_id", $account_type_id);
        $result = $sql->queryAll();
              
        return $result; 
        
    }
    
    public static function getSubMenus($menu_id, $account_type_id)
    {
        
        $query = "SELECT
                    DISTINCT(sm.submenu_id), ar.menu_id, sm.submenu_name, sm.submenu_link, sm.status
                  FROM access_rights ar
                    INNER JOIN submenus sm ON ar.menu_id = sm.menu_id
                    WHERE ar.account_type_id = :account_type_id 
                        AND sm.menu_id = :menu_id
                        AND sm.status = 1 ;";
        
        $sql = Yii::app()->db->createCommand($query);
        $sql->bindParam(":account_type_id", $account_type_id);
        $sql->bindParam(":menu_id", $menu_id);
        $result = $sql->queryAll();
              
        return $result; 
        
    }
    
    public function getControllerAction()
    {
        return Yii::app()->controller->getUniqueId() .'/'. Yii::app()->controller->action->id;
    }
    
}
?>
