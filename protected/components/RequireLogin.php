<?php

/*
 * @author : owliber
 * @date : 2014-02-13
 */

class RequireLogin extends CBehavior
{
    public function attach($owner)
    {
        $owner->attachEventHandler('onBeginRequest', array($this, 'handleBeginRequest'));
    }
    
    public function handleBeginRequest($event)
    {
        $controller = Yii::app()->request->getPathInfo();
        $allowed = array(
            'site/login',
            'activation/verify',
            'activation/success',
            'activation/error',
            'cron/goc',
        );
        if (Yii::app()->user->isGuest && !in_array($controller, $allowed))
        {
            Yii::app()->user->loginRequired();
        }
    }
}

?>
