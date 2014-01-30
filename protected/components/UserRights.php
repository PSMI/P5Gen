<?php

/*
 * @author : owliber
 * @date : 2014-01-22
 */

class UserRights extends Controller
{
    public function checkUserAccess($usertype)
    {
        $model = new AccessRights();
        
        if(!$model->IsUserAllowed($usertype) || Yii::app()->user->isGuest)
        {
            if(empty(Yii::app()->session['UserTypeID']))
                $this->redirect(array('site/login'));
            else
                $this->redirect (array('site/notallowed'));
        }
        else
        {
            return true;
        }
            
    }
}
?>
