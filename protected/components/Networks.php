<?php

/*
 * @author : owliber
 * @date : 2014-02-06
 */

class Networks extends Controller
{
      
    /**
     * @author owliber
     * @param type $member_id
     * @return int
     */
    public static function getLessFiveDownlines($member_id)
    {
        $model = new Downlines();
        $model->member_id = $member_id;
        
        $downlines = $model->firstFive();
        
        if(count($downlines) <= 5)
        {    
            //include all direct endorse
            $direct_endorsed = $model->getDirectEndorsed();
            
            if(count($direct_endorsed) < 0 && is_null($direct_endorsed) && empty($direct_endorsed))
                $downlines = array('downline'=>$member_id);
            else
                $downlines = array_merge(array('0'=>array('downline'=>$model->member_id)), $direct_endorsed);   
        }

        $level = 1;

        do
        {

            foreach($downlines as $downline)
            {
                $result[] = array(
                                  'downline'=>$downline['downline'],
                                );
            }

            $rows = Helpers::convertToList($downlines);        
            $downlines = $model->nextLessFiveLevel($rows['listItem']);

            if(count($downlines) < 0)
            {
                $downlines = array('downline'=>$downlines['downline']);
            }

            $max_per_level = pow(count($downlines),$level);

            $level++;
            $total_downlines = count($downlines);


        }while($total_downlines>0 && $total_downlines<=$max_per_level);
        
        
        return $result;
    }
    
    
    /**
     * @author owliber
     * @param type $member_id
     * @return type
     */
    public function getUplines($member_id)
    {
        $uplines = array();
        $model = new Uplines();
        do
        {

             $result = $model->getUplines($member_id);    
             $member_id = $result['upline'];
             
              if(!is_null($member_id)) $uplines[] = $member_id;
             
        }while(!is_null($member_id));
            
        return $uplines;
    }
    
    
    /**
     * @author Noel Antonio
     * @param type $member_id
     * @return type
     */
    public function getEndorser($member_id)
    {
        $endorser = array();
        $model = new Endorser();
        do
        {
             
             $result = $model->getEndorsers($member_id);    
             $member_id = $result['endorser'];
             
             if(!is_null($member_id)) $endorser[] = $member_id;
             
        }while(!is_null($member_id));
            
        return $endorser;
    }
    
    
    /**
     * This recursive function is used to retrieve the downlines
     * of the logged-in member.
     * @author Noel Antonio
     * @date 02/7/2014
     * @param int $member_id id of the logged-in member
     * @param int $level level of genealogy; default is 0.
     * @return array $finalTree
     */
    public function getDownlines($member_id, $level = 0)
    {
        $model = new Downlines();
        $parent = array();
        $children = array();
        $model->member_id = $member_id;
        
        $i = 0;
        $level++;
        // $downlines = $model->officialFirstLevel();
        $downlines = $model->countTempFiveDownlines();
        foreach ($downlines as $key => $val)
        {
            $parent[$i][$level] = $downlines[$key]["downline"];
            $children = array_merge($children, Networks::getDownlines($downlines[$key]["downline"], $level));
            $i++;
        }
        
        $finalTree = array_merge($parent, $children);
        
        return $finalTree;
    }
        
    
    /**
     * This function is used to retrieve the member's direct endorsements
     * to be used for unilevel genealogy.
     * @author Noel Antonio
     * @date 02/7/2014
     * @param int $member_id id of the logged-in member
     * @param int $level level of genealogy; default is 0.
     * @return array $finalTree
     */
    public function getUnilevel($member_id, $level = 0)
    {
        $model = new Downlines();
        $parent = array();
        $children = array();
        
        $i = 0;
        $level++;
        $downlines = $model->directEndorse($member_id);
        foreach ($downlines as $key => $val)
        {
            $parent[$i][$level] = $downlines[$key]["downline"];
            $children = array_merge($children, Networks::getUnilevel($downlines[$key]["downline"], $level));
            $i++;
        }
        
        $finalTree = array_merge($parent, $children);
        
        return $finalTree;
    }
    
    
    /**
     * This function is used to retrieve the member's direct endorsements
     * to be used for unilevel genealogy up to 10th level only.
     * @author Noel Antonio
     * @date 02/7/2014
     * @param int $member_id id of the logged-in member
     * @param int $level level of genealogy; default is 0.
     * @return array $finalTree
     */
    public function getUnilevel10thLevel($member_id, $level = 0)
    {
        $model = new Downlines();
        $parent = array();
        $children = array();
        
        $i = 0;
        $level++;
        
        if ($level <= 10)
        {
            $downlines = $model->directEndorse($member_id);
            foreach ($downlines as $key => $val)
            {
                $parent[$i][$level] = $downlines[$key]["downline"];
                $children = array_merge($children, Networks::getUnilevel10thLevel($downlines[$key]["downline"], $level));
                $i++;
            }
        }
        
        $finalTree = array_merge($parent, $children);
        
        return $finalTree;
    }
    
    
    /**
     * This function is used to arrange the array by level and
     * sort it reversibly.
     * @author Noel Antonio
     * @date 02/7/2014
     * @param array $array the array to arrange
     * @return array $genealogy
     */
    public function arrangeLevel($array, $sort='DESC')
    {
        $genealogy = array();
        $total_downlines = 0;
        
        if (is_array($array) && count($array) > 0)
        {
            foreach ($array as $key => $val) 
            {
                foreach ($val as $level => $id) 
                {
                    $final[$level][$id] = $id;
                }
            }

            foreach ($final as $levels => $ids)
            {
                $total_downlines += count($ids);
                $temp["Total"] = count($ids);
                $temp["Members"] = implode(",", $ids);
                $temp["Level"] = $levels;

                $genealogy[] = $temp;
            }
            if($sort == 'DESC')
                krsort($genealogy);
            else
                ksort ($genealogy);
        }
        
        return array('network'=>$genealogy, 'total'=>$total_downlines);
    }
    
    
    /**
     * @author Noel Antonio
     * @date 02/12/2014
     */
    public function getGenealogyDownlines($member_ids)
    {
        $model = new Downlines();
        $rawData = $model->downlineInfo($member_ids);
        foreach ($rawData as $key => $val)
        {
            $count = $model->getDownlineCount($val["member_id"]);
            $temp["Count"] = $count;
            $temp["ID"] = $val["member_id"];
            $temp["Name"] = strtoupper($val["last_name"]) . ", " . $val["first_name"] . " " . $val["middle_name"];
            $temp["DateEnrolled"] = date("F d, Y", strtotime($val["date_enrolled"]));
            $temp["Placement_Date"] = date("F d, Y", strtotime($val["placement_date"]));
            $temp["Upline"] = Networks::getMemberName($val["upline_id"]);
            $temp["Endorser"] = Networks::getMemberName($val["endorser_id"]);
            $array[] = $temp;
        }
        
        return $array;
    }
    
    
    /**
     * @author Noel Antonio
     * @date 02/12/2014
     */
    public function getUnilevelDownlines($member_ids)
    {
        $model = new Downlines();
        $rawData = $model->downlineInfo($member_ids);
        foreach ($rawData as $key => $val)
        {
            
            $count = $model->getUnilevelCount($val["member_id"]);
            $temp["Count"] = $count;
            $temp["ID"] = $val["member_id"];
            $temp["Placement_Date"] = date("F d, Y", strtotime($val["placement_date"]));
            $temp["Name"] = strtoupper($val["last_name"]) . ", " . $val["first_name"] . " " . $val["middle_name"];
            $temp["DateEnrolled"] = date("F d, Y", strtotime($val["date_enrolled"]));
            $temp["Upline"] = Networks::getMemberName($val["upline_id"]);
            $temp["Endorser"] = Networks::getMemberName($val["endorser_id"]);
            $array[] = $temp;
        }
        
        return $array;
    }
    
    /**
     * @author Noel Antonio
     * @param type $member_ids
     * @param type $date_to
     * @return type
     */
    public function getUnilevelDownlinesByDate($member_ids, $date_to)
    {
        $model = new Downlines();
        $rawData = $model->downlineInfo($member_ids);
        foreach ($rawData as $key => $val)
        {
            $placement_date = date('Y-m-d',strtotime($val['placement_date']));
            if($placement_date <= $date_to)
            {
                $count = $model->getUnilevelCount($val["member_id"]);
                $temp["Count"] = $count;
                $temp["ID"] = $val["member_id"];
                $temp["Placement_Date"] = date("F d, Y", strtotime($val["placement_date"]));
                $temp["Name"] = strtoupper($val["last_name"]) . ", " . $val["first_name"] . " " . $val["middle_name"];
                $temp["DateEnrolled"] = date("F d, Y", strtotime($val["date_enrolled"]));
                $temp["Upline"] = Networks::getMemberName($val["upline_id"]);
                $temp["Endorser"] = Networks::getMemberName($val["endorser_id"]);
                $array[] = $temp;
            }
        }
        
        return $array;
    }
    
    
    /**
     * @author Noel Antonio
     * @param type $member_id
     * @return type
     */
    public function getDirectEndorser($member_id)
    {
        $model = new DirectEndorsement();
        
        do
        {            
             $result = $model->getDirectEndorser($member_id);    
             $member_id = $result['endorser'];
             
             if(!is_null($member_id)) $endorsers[] = $member_id;
             
        }while(!empty($result) && !is_null($result));
            
        return $endorsers;
    }
    
    
    /**
     * This function is used to get the placed under of the member
     * @param type $member_id
     * @return type
     */
    public function getPlaceUnder($member_id)
    {
        $model = new Downlines();
        $model->member_id = $member_id;
        $member_array = array();
        
        $downlines = $model->officialFirstLevel();
        if (count($downlines) < 5) {
            $member_array[] = $member_id;
        }
        
        for ($i = 0; $i < count($downlines); $i++)
        {
            $downline_id = $downlines[$i]["downline"];
            $member_array = array_merge($member_array, Networks::getPlaceUnder($downline_id));
        }
        
        return $member_array;
    }
    
    
    /**
     * This function is used to get the placed under of the member
     * @param type $member_id
     * @return type
     */
    public function getPlaceUnderWithUnapprovedMember($member_id)
    {
        $model = new Downlines();
        $model->member_id = $member_id;
        $member_array = array();
        
        $downlines = $model->countTempFiveDownlines();
        if (count($downlines) < 5) {
            $member_array[] = $member_id;
        }
        
        for ($i = 0; $i < count($downlines); $i++)
        {
            $downline_id = $downlines[$i]["downline"];
            $member_array = array_merge($member_array, Networks::getPlaceUnderWithUnapprovedMember($downline_id));
        }
        
        return $member_array;
    }
    
    
    /**
     * This function is used to arrange the array 
     * and prepare it for autocomplete
     * @param type $array
     * @return type
     */
    public function autoComplete($array)
    {
        $downlines = '';
        $final = array();
        
        if (is_array($array) && count($array) > 0)
        {
            for ($i = 0; $i < count($array); $i++) 
            {
                $id = $array[$i];
                $final[$id] = $id;
            }
            
            $downlines = implode(",", $final);
        }
        
        return $downlines;
    }
    
    
    /**
     * This function is used to get the member name of a particular
     * member id param.
     * @param type $member_id
     * @return string member name
     */
    public function getMemberName($member_id)
    {
        $model = new MembersModel();
        $info = $model->selectMemberName($member_id);
        if(empty($info['last_name']) && empty($info['first_name']) && empty($info['middle_name'])) 
            $member_name = 'None';
        else
            $member_name = $info["last_name"] . ", " . $info["first_name"] . " " . $info["middle_name"];
        $member_name = strtoupper($member_name);
        
        return $member_name;
    }
    
    
    /**
     * @author owliber
     * @param type $member_id
     * @param type $downline_id
     * @return type
     */
    public function getLevel($member_id, $downline_id)
    {
                
       $rawData = Networks::getUnilevel($member_id);
       $levels = Networks::arrangeLevel($rawData,'ASC');
       
        if (count($levels['network']) > 0)
        {
            foreach ($levels['network'] as $row)
            {
                $arr_ids = explode(",", $row['Members']);
                
                if(in_array($downline_id,$arr_ids))
                {
                    return $row['Level'];
                }
                
            }
        }
        
    }
    
    
    /**
     * @author jopormento
     * @param type $member_id
     * @param type $downline_id
     * @return type
     */
    public function getLevelIpd($member_id, $downline_id)
    {
       $rawData = Networks::getIPDUnilevel10thLevel($member_id);
       $levels = Networks::arrangeLevel($rawData,'ASC');
       
        if (count($levels['network']) > 0)
        {
            foreach ($levels['network'] as $row)
            {
                $arr_ids = explode(",", $row['Members']);
                
                if(in_array($downline_id,$arr_ids))
                {
                    return $row['Level'];
                }
                
            }
        }
        
    }
    
    
    /**
     * @author owliber
     * @param type $member_ids
     * @param type $date_from
     * @param type $date_to
     * @return type
     */
    public function getUnilevelDownlinesByCutOff($member_ids,$date_from,$date_to)
    {
        $model = new Downlines();
        $rawData = $model->downlineInfo($member_ids);
        foreach ($rawData as $key => $val)
        {
            
            $placement_date = date('Y-m-d',strtotime($val['placement_date']));
            if($placement_date > $date_from && $placement_date <= $date_to)
            {
                $count = $model->getUnilevelCount($val["member_id"]);
                $temp["ID"] = $val["member_id"];
                $temp["Count"] = $count;
                $temp["Placement_Date"] = $val['placement_date'];
                $temp["Name"] = strtoupper($val["last_name"]) . ", " . $val["first_name"] . " " . $val["middle_name"];
                $temp["DateEnrolled"] = date("F d, Y", strtotime($val["date_enrolled"]));
                $temp["Upline"] = Networks::getMemberName($val["upline_id"]);
                $temp["Endorser"] = Networks::getMemberName($val["endorser_id"]);
                $array[] = $temp;
            }
        }
        
        return $array;
    }
    
    
    /**
     * @author owliber
     * @param type $member_ids
     * @param type $date_completed
     * @return type
     */
    public function getUnilevelDownlinesByFlushOut($member_ids,$date_completed)
    {
        $model = new Downlines();
        $rawData = $model->downlineInfo($member_ids);
        
        foreach ($rawData as $key => $val)
        {
            $date_completed = date('Y-m-d H:i',strtotime($date_completed));
            $placement_date = date('Y-m-d H:i',strtotime($val['placement_date']));
            
            if($placement_date >= $date_completed )
            {
                $count = $model->getUnilevelCount($val["member_id"]);
                $temp["ID"] = $val["member_id"];
                $temp["Count"] = $count;
                $temp["Placement_Date"] = $val['placement_date'];
                $temp["Name"] = strtoupper($val["last_name"]) . ", " . $val["first_name"] . " " . $val["middle_name"];
                $temp["DateEnrolled"] = date("F d, Y", strtotime($val["date_enrolled"]));
                $temp["Upline"] = Networks::getMemberName($val["upline_id"]);
                $temp["Endorser"] = Networks::getMemberName($val["endorser_id"]);
                $array[] = $temp;
            }
        }
        
        return $array;
    }
    
    
    /**
     * @author owliber
     * @param type $member_ids
     * @param type $date_from
     * @param type $date_to
     * @return type
     */
    public function getIPDUnilevelDownlinesByCutOff($member_ids,$date_from,$date_to)
    {
        $model = new Downlines();
        $rawData = $model->downlineInfo($member_ids);
        foreach ($rawData as $key => $val)
        {
            
            $placement_date = date('Y-m-d',strtotime($val['placement_date']));
            if($placement_date > $date_from && $placement_date <= $date_to)
            {
                $count = $model->getUnilevelCount($val["member_id"]);
                $temp["ID"] = $val["member_id"];
                $temp["Count"] = $count;
                $temp["Placement_Date"] = $val['placement_date'];
                $temp["Name"] = strtoupper($val["last_name"]) . ", " . $val["first_name"] . " " . $val["middle_name"];
                $temp["DateEnrolled"] = date("F d, Y", strtotime($val["date_enrolled"]));
                $temp["Upline"] = Networks::getMemberName($val["ipd_upline_id"]);
                $temp["Endorser"] = Networks::getMemberName($val["ipd_endorser_id"]);
                $array[] = $temp;
            }
        }
        
        return $array;
    }
    
    
    /**
     * This function is used to get the distributor name of a particular
     * member id param.
     * @param type $member_id
     * @return string member name
     */
    public function getDistributorName($member_id)
    {
        $model = new DistributorForm();
        $info = $model->selectDistributorName($member_id);
        if(empty($info['last_name']) && empty($info['first_name']) && empty($info['middle_name'])) 
            $distributor_name = 'None';
        else
            $distributor_name = $info["last_name"] . ", " . $info["first_name"] . " " . $info["middle_name"];
        $distributor_name = strtoupper($distributor_name);
        return $distributor_name;
    }
    
    
    /**
     * This fucntion is used to retrieve the unilevel network of a member up to 10th level.
     * @param type $member_id
     * @param type $level
     * @return type
     */
    public function getIPDUnilevel10thLevel($member_id, $level = 0)
    {
        $model = new Downlines();
        $parent = array();
        $children = array();
        
        $i = 0;
        $level++;
        
        if ($level <= 10)
        {
            $downlines = $model->getIPDDirectEndorse($member_id);
            foreach ($downlines as $key => $val)
            {
                $parent[$i][$level] = $downlines[$key]["downline"];
                $children = array_merge($children, Networks::getIPDUnilevel10thLevel($downlines[$key]["downline"], $level));
                $i++;
            }
        }
        
        $finalTree = array_merge($parent, $children);
        
        return $finalTree;
    }
    
    
    /**
     * This function is used to get the qualified IPD.
     * @author Noel Antonio
     * @date 02/12/2014
     */
    public function getQualifiedIPD()
    {
        $model = new Downlines();
        $temp_array = array();
        
        $ipd_list = $model->getAllIPD();
        for ($a = 0; $a < count($ipd_list); $a++)
        {
            $ipd_id = $ipd_list[$a]["member_id"];
            $temp_array[] = $ipd_id;
        }
        
        return $temp_array;
    }
    
    
    /**
     * This function is used to get the IPD unilevel downlines
     * @author Noel Antonio
     * @date 02/12/2014
     */
    public function getIPDUnilevelDownlines($member_ids)
    {
        $model = new Downlines();
        $rawData = $model->downlineInfo($member_ids);
        foreach ($rawData as $key => $val)
        {
            $count = $model->getIPDUnilevelCount($val["member_id"]);
            $temp["Count"] = $count;
            $temp["ID"] = $val["member_id"];
            $temp["Placement_Date"] = date("F d, Y", strtotime($val["placement_date"]));
            $temp["Name"] = strtoupper($val["last_name"]) . ", " . $val["first_name"] . " " . $val["middle_name"];
            $temp["DateEnrolled"] = date("F d, Y", strtotime($val["date_enrolled"]));
            $temp["Upline"] = Networks::getMemberName($val["ipd_upline_id"]);
            $temp["Endorser"] = Networks::getMemberName($val["ipd_endorser_id"]);
            $array[] = $temp;
        }
        
        return $array;
    }
    
    
    /**
     * This is a recursive function in locating the immediate IBO.
     * @author Noel Antonio
     * @param type $member_id
     * @return type
     */
    public function getImmediateIBOEndorser($member_id)
    {
        $model = new Downlines();
        
        $rawData = $model->findImmediateIBO($member_id);
        $account_type = $rawData["account_type_id"];
        
        if ($account_type == 3)
        {
            return $rawData["member_id"];
            Yii::app()->end();
        }
        else
        {
            return Networks::getImmediateIBOEndorser($rawData["ipd_endorser_id"]);
        }
    }
    
    
    /**
     * This function is used to get the IPD endorsers.
     * @author Noel Antonio
     * @param type $member_id
     * @return type
     */
    public function getIPDEndorser($member_id)
    {
        $endorser = array();
        $model = new Endorser();
        do
        {
             $result = $model->getIPDEndorsers($member_id);    
             $member_id = $result['endorser'];
             
             if(!is_null($member_id)) $endorser[] = $member_id;
             
        }while(!is_null($member_id));
            
        return $endorser;
    }
    
    
    /**
     * This function is used to get IPD direct count
     * @param type $member_id
     * @return type
     */
    public function getIPDDirectCount($member_id)
    {
        $model = new Endorser();
        $count = $model->getIPDEndorserCount($member_id);
        return $count;
    }
    
    
    /**
     * This function is used to get the placed under of the member.
     * @author Noel Antonio
     * @param type $member_id
     * @return type
     */
    public function getPlaceUnderIPD($member_id)
    {
        $model = new Downlines();
        $model->member_id = $member_id;
        $member_array = array();
        
        $member_array[] = $member_id;
        
        $downlines = $model->selectIPDforPlaceUnder();
        for ($i = 0; $i < count($downlines); $i++)
        {
            $downline_id = $downlines[$i]["downline"];
            $member_array = array_merge($member_array, Networks::getPlaceUnderIPD($downline_id));
        }
        
        return $member_array;
    }
    
    
    /**
     * This function is used to trace the unilevel network of a newly
     * registered IPD upto 10th level.
     * It trace whatever account type the member is. Either it's IBO or IPD
     * it should be included in the unilevel network.
     * This will be used for payout purposes.
     */
    public function getIPD10thUnilevelNetworkForPayout($member_id, $level = 0, $index = 0)
    {
        $model = new Endorser();
        $parent = array();
        $child = array();
        $uplineInfo = array();

        $level++;
        if ($level <= 10)
        {
            $uplineInfo = $model->getEndorserForIPDUnilevel($member_id);

            if (is_array($uplineInfo))
            {
                $parent[$index]['member_id'] = $uplineInfo['member_id'];
                $parent[$index]['account_type_id'] = $uplineInfo['account_type_id'];
                $parent[$index]['level'] = $level;
            }
            $index++;
            $child = array_merge($child, Networks::getIPD10thUnilevelNetworkForPayout($uplineInfo['member_id'], $level, $index));
        }
        
        return array_merge($parent, $child);
    }
    
    
    /**
     * @author Noel Antonio
     * @param type $member_id
     * @param type $level
     * @return type
     */
    public function getDownlines2($member_id, $level = 0)
    {
        $model = new Downlines();
        $parent = array();
        $children = array();
        $model->member_id = $member_id;
        
        $i = 0;
        $level++;
        $downlines = $model->getIPDDownlines2();
        foreach ($downlines as $key => $val)
        {
            $parent[$i][$level] = $downlines[$key]["downline"];
            $children = array_merge($children, Networks::getDownlines2($downlines[$key]["downline"], $level));
            $i++;
        }
        
        $finalTree = array_merge($parent, $children);
        
        return $finalTree;
    }
    
    
    /**
     * @author Noel Antonio
     * @date 05-07-2014
     */
    public function getRPCMembersForPDF($tracing_id, $array)
    {
        $checking = array();
        $rawData = array();
        
        foreach ($array as $ipd_id)
        {
            
            $checking = Networks::traceIPDEndorserUpward($tracing_id, $ipd_id, $ipd_id);
            if (!empty($checking))
            {
                $rawData[] = $checking;
            }
            
        }
        
        return $rawData;
    }
    
    
    /**
     * This function is used to trace the unilevel network (upward) of each 
     * IPD member that purchased products per cut off
     * @author Noel Antonio
     * @date 05-07-2014
     */
    public function traceIPDEndorserUpward($endorser_id, $buyer_id, $member_id, $level = 0)
    {
        $model = new Endorser();
        $array = array();
        
        $level++;
        if ($level <= 10)
        {
            $uplineInfo = $model->getEndorserForIPDUnilevel($member_id);
            
            // if endorser was traced as part of the network then -
            if ($endorser_id == $uplineInfo['member_id'])
            {
                // return the buyer id
                return array('member_id'=>$buyer_id, 'level'=>$level);
            }
            
            $array = Networks::traceIPDEndorserUpward($endorser_id, $buyer_id, $uplineInfo['member_id'], $level);
        }
        
        return $array;
    }
    
    
    /**
     * This function is used to trace the network of the IPD
     * for repeat purchase payout. If there are IBO in the middle of the
     * network, the IBO genealogy should be followed.
     * @param type $member_id
     * @param type $level
     * @param type $index
     * @return type
     */
    public function traceRPNetworkUpward($member_id, $level = 0, $index = 0)
    {
        $model = new Endorser();
        $parent = array();
        $child = array();
        $uplineInfo = array();

        $level++;
        if ($level <= 10)
        {
            $uplineInfo = $model->getEndorserForRP($member_id);

            if (is_array($uplineInfo))
            {
                $parent[$index]['member_id'] = $uplineInfo['member_id'];
                $parent[$index]['account_type_id'] = $uplineInfo['account_type_id'];
                $parent[$index]['level'] = $level;
            }
            $index++;
            $child = array_merge($child, Networks::traceRPNetworkUpward($uplineInfo['member_id'], $level, $index));
        }
        
        return array_merge($parent, $child);
    }
}
?>
