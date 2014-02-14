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
        
        if(count($downlines)>0 && count($downlines) < 5)
        {

            //include all direct endorse
            $direct_endorsed = $model->getDirectEndorsed();

            if(count($direct_endorsed) < 0 || is_null($direct_endorsed) || empty($direct_endorsed))
                $downlines = array('downline'=>$model->member_id);
            else
                $downlines = array_merge(array('downline'=>$model->member_id), $direct_endorsed);
        }

        $level = 1;

        do
        {

            foreach($downlines as $downline)
            {
                $result[] = array(//'level'=>$level,
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


        }while($total_downlines>0 && $total_downlines>=$max_per_level);
        
        
        return $result;
    }
    
    
    /**
     * @author owliber
     * @param type $member_id
     * @return type
     */
    public function getUplines($member_id)
    {
        $model = new Uplines();
        do
        {
            if(!is_null($member_id)) $uplines[] = $member_id;
             
             $result = $model->getUplines($member_id);    
             $member_id = $result['upline'];
        }while(!empty($result) && !is_null($result));
            
        return $uplines;
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
        $downlines = $model->firstLevel();
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
     * This function is used to arrange the array by level and
     * sort it reversibly.
     * @author Noel Antonio
     * @date 02/7/2014
     * @param array $array the array to arrange
     * @return array $genealogy
     */
    public function arrangeLevel($array)
    {
        $genealogy = array();
        
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
                $temp["Total"] = count($ids);
                $temp["Members"] = implode(",", $ids);
                $temp["Level"] = $levels;

                $genealogy[] = $temp;
            }

            krsort($genealogy);
        }
        
        return $genealogy;
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
            $temp["ID"] = $val["member_id"];
            $temp["Count"] = $count;
            $temp["Name"] = strtoupper($val["last_name"]) . ", " . $val["first_name"] . " " . $val["middle_name"];
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
            $temp["ID"] = $val["member_id"];
            $temp["Count"] = $count;
            $temp["Name"] = strtoupper($val["last_name"]) . ", " . $val["first_name"] . " " . $val["middle_name"];
            $array[] = $temp;
        }
        
        return $array;
    }
    
}
?>
