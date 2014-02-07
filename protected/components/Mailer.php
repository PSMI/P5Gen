<?php

/*
 * @author : owliber
 * @date : 2014-02-06
 */

class Mailer
{
    CONST VERIFY_ACCOUNT_TMPL = 1;
    CONST UPLINE_NOTIFY_TMPL = 2;
    
    public function sendVerificationLink($param)
    {
        
        $member_id = $param['member_id'];
        
        $reference = new ReferenceModel();
        $message_template = $reference->get_message_template(self::VERIFY_ACCOUNT_TMPL);
                
        $members = new MembersModel();
        $result = $members->selectMemberDetails($member_id);        

        $email = $result['email'];
        $member_name = $result['first_name'] . ' ' . $result['last_name'];
        $activation_code = $result['activation_code'];
        $username = $result['username'];
        $password = $param['plain_password'];
        
        $params = array('email'=>$email,
                        'code'=>$activation_code);
        
        $verification_link = 'https://'.$_SERVER['HTTP_HOST'].  Yii::app()->createUrl('activation/verify', $params);
        
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
    
    public function sendUplineNotification($param)
    {
        $downline_id = $param['new_member_id'];
        $upline_id = $param['upline_id'];
        $endorser_id = $param['endorser_id'];
        
        $reference = new ReferenceModel();
        $message_template = $reference->get_message_template(self::UPLINE_NOTIFY_TMPL);
                
        $members = new MembersModel();
        
        $downline_info = $members->selectMemberDetails($downline_id); 
        $upline_info = $members->selectMemberDetails($upline_id);
        $endorser_info = $members->selectMemberDetails($endorser_id);
        
        $upline_email = $upline_info['email'];
        $upline_name = $upline_info['first_name'] . ' ' . $upline_info['last_name'];
        $downline_name = $downline_info['first_name'] . ' ' . $downline_info['last_name'];
        $endorser_name = $endorser_info['first_name'] . ' ' . $endorser_info['last_name'];
        
        $placeholders = array('MEMBER_NAME'=>$upline_name, 
                              'ENDORSER_NAME'=>$endorser_name,
                              'DOWNLINE_NAME'=>$downline_name);
        
        foreach($placeholders as $key => $value){
            $message_template = str_replace('{'.$key.'}', $value, $message_template);
        }
 
        Yii::app()->mailer->Host = 'localhost';
        Yii::app()->mailer->IsHTML(TRUE);
        Yii::app()->mailer->IsMail();
        Yii::app()->mailer->From = 'noreply@p5partners.com';
        Yii::app()->mailer->FromName = 'P5 Marketing Incorporated';
        Yii::app()->mailer->AddAddress($upline_email);
        Yii::app()->mailer->Subject = 'Important:New Downline For Approval';
        Yii::app()->mailer->Body = $message_template;
        Yii::app()->mailer->Send();
        
    }
}
?>
