<?php

/*
 * @author : owliber
 * @date : 2014-02-06
 */

class Mailer
{
    public $template_id = 1;
    
    public function sendVerificationLink($param)
    {
        $member_id = $param['member_id'];
        
        $reference = new ReferenceModel();
        $message_template = $reference->get_message_template($this->template_id);
                
        $members = new MembersModel();
        $result = $members->selectMemberDetailsStatus($member_id);        

        $email = $result['email'];
        $member_name = $result['first_name'] . ' ' . $result['last_name'];
        $activation_code = $result['activation_code'];
        $username = $result['username'];
        $password = $param['plain_password'];
        
        $params = array('email'=>$email,
                        'code'=>$activation_code);
        
        $verification_link = 'https://'.$_SERVER['HTTP_HOST'].  Yii::app()->createUrl('registration/verify', $params);
        
        $placeholders = array('MEMBER_NAME'=>$member_name, 
                              'VERIFICATION_LINK'=>$verification_link,
                              'USERNAME'=>$username,
                              'PASSWORD'=>$password);
        
        foreach($placeholders as $key => $value){
            $message_template = str_replace('{'.$key.'}', $value, $message_template);
        }
        
        if(count($result) > 0)
        {
            Yii::app()->mailer->Host = 'localhost';
            Yii::app()->mailer->IsHTML(TRUE);
            Yii::app()->mailer->IsMail();
            Yii::app()->mailer->From = 'noreply@p5partners.com';
            Yii::app()->mailer->FromName = 'P5 Marketing Incorporated';
            Yii::app()->mailer->AddAddress($email);
            Yii::app()->mailer->Subject = 'Important:P5 Membership Activation Required';
            Yii::app()->mailer->Body = $message_template;
            Yii::app()->mailer->Send();
        }
        
    }
}
?>
