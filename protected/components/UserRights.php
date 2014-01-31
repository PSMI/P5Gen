<?php

/*
 * @author : owliber
 * @date : 2014-01-22
 */

class UserRights extends CWebUser
{
   
    public function hasUserAccess()
    {
        $model = new AccessRights();
        
        
        if(!$model->checkUserAccess($this->accountType()) || Yii::app()->user->isGuest)
            return false;
        else
            return true;
            
    }
    
    public function accountType()
    {
        return Yii::app()->session['account_type_id'];
    }
    
    public function isSuperAdmin()
    {
        if($this->accountType() == 1)
            return true;
        else
            return false;
    }
}
?>
