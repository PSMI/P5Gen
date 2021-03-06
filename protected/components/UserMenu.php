<?php

/*
 * @author : owliber
 * @date : 2014-01-31
 */

class UserMenu extends Controller
{
    public static function userMenus($account_type_id)
    {
        $model = new AccessRights();
        
        if(Yii::app()->user->isSuperAdmin())
        {
            $menus = $model->getAllMenus();
            
            foreach($menus as $menu)
            {
                $menu_id = $menu['menu_id'];

                $submenus = $model->getAllSubMenus($menu_id); 
                $sub_items = array();

                if(!empty($submenus))
                {
                    foreach($submenus as $submenu)
                    {

                        $sub_items[] = array(
                            'label' => $submenu['submenu_name'],
                            'url'   => array($submenu['submenu_link']),
                        );
                    }

                }

                 $items[] = array(
                    'label' => $menu['menu_name'],
                    'icon'  => $menu['menu_icon'],
                    'url'   => array($menu['menu_link']),
                    'items' => $sub_items,
                );   

            }
        }
        else
        {
            
        
            $menus = $model->getMenus($account_type_id);

            foreach($menus as $menu)
            {
                $menu_id = $menu['menu_id'];

                $submenus = $model->getSubMenus($menu_id, $account_type_id); 
                $sub_items = array();

                if(!empty($submenus))
                {
                    foreach($submenus as $submenu)
                    {

                        $sub_items[] = array(
                            'label' => $submenu['submenu_name'],
                            'url'   => array($submenu['submenu_link']),
                        );
                        if(in_array($submenu['submenu_id'],array(45,11,7)))
                            $sub_items[] .= array('options'=>array('class'=>'divider'));
                    }

                }

                 $items[] = array(
                    'label' => $menu['menu_name'],
                    'icon'  => $menu['menu_icon'],
                    'url'   => array($menu['menu_link']),
                    'items' => $sub_items,
                );   

            }
        }
        
        
        
        //$this->menu = $items;
        //return $this->menu;       
        return $items;
        
    }
    
}
?>
