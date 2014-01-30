<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
                        
                $members = Members::model()->findByAttributes(array('username'=>$this->username));
                
                //Get user status
                $status = Members::getUserStatus($this->username);
            		
                if($members===null)
                {
                    $this->errorCode=self::ERROR_USERNAME_INVALID;
                }
                else
                {
                    if($members->password !== $members->hashPassword($this->password))
                        $this->errorCode=self::ERROR_PASSWORD_INVALID;
                    else
                    {
                        if($status == 1)
                            $this->errorCode=self::ERROR_NONE;
                        else
                            $this->errorCode=self::ERROR_USER_INACTIVE;
                    }
                }
                   
		return !$this->errorCode;
            
	}
}