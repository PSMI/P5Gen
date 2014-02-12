<?php

/*
 * @author : owliber
 * @date : 2014-02-06
 */

class Helpers extends Controller
{
    /**
     * 
     * @param type $member_id
     * @param type $firstname
     * @param type $lastname
     * @return string
     */
    public function generate($member_id, $firstname, $lastname)
    {
        $string = substr($firstname, 0, 1) . $lastname;
        $username = strtolower($string);
        
        $retval = Members::checkUsername($username);
       
        //validate username if already in used, append member_id if true
        if($retval) $username = $username . '_' . $member_id;
        return $username;
            
    }
    
    /**
     * 
     * @param type $length
     * @return type
     */
    public function randomPassword($length) {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); 
        $alphaLength = strlen($alphabet) - 1; 
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
    
    /**
     * Convert array into comma-separated list
     * @author owliber
     * @param type $arr
     * @return type
     */
    public static function convertToList($arr)
    {
        foreach($arr as $item)
        {
            $items[] = $item['downline'];
        }
        
        $listItems = implode(',', $items);
        return array('arrayList'=>$items,
                     'listItem'=>$listItems);
    }
   
}
?>
