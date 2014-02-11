<?php

/*
 * @author : owliber
 * @date : 2014-02-06
 */

class Networks extends Controller
{
        
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
        
    public static function getLessFiveDownlines($member_id)
    {
        $model = new Downlines();
        
        $downlines = $model->firstFive($member_id);
                
//        if(count($downlines) < 5)
//        {
//            $downlines = implode(',',$model->getDirectEndorsed($member_id));
//            //$downlines = array_merge(array('downline'=>$member_id),$direct);
//            
//            $result[] = array('level'=>1,
//                              'downlines'=>$downlines,
//                            );
//        }
//        else
//	  {
            $level = 1;

            do
            {

                foreach($downlines as $downline)
                {
                    $result[] = array('level'=>$level,
                                      'downlines'=>$downline['downline'],
                                    );
                }

                $rows = Networks::convertToList($downlines);        
                $downlines = $model->nextLessFiveLevel($rows['listItem']);
                $max_per_level = pow(count($downlines),$level);

                $level++;
                $total_downlines = count($downlines);


            }while($total_downlines>0 && $total_downlines>=$max_per_level);
//        }
        
        return $result;
    }
    
    public static function getLevelCount($member_id)
    {
        $model = new Downlines();
        
//        $downlines = $model->firstLevel($member_id);
        
            $level = 1;
            $downlines = $model->firstLevel($member_id);
            do
            {
                
                
                $total = count($downlines);

                $result[] = array
                        (
                          'level'=>$level,
                          'total'=>$total,
                        );

                if($total>=1)
                {
                    $rows = Networks::convertToList($downlines);        
                    $downlines = $model->nextLevel($rows['listItem']);
                }
                
                //$max_per_level = pow(count($downlines),$level);

                $level++;
                $total_downlines = count($downlines);

            }while(!is_null($total) && $total_downlines > 0 ); //>= $max_per_level);
        
        return $result;
        
    }
    
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
        
        $i = 0;
        $level++;
        $downlines = $model->firstLevel($member_id);
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
            $children = array_merge($children, Networks::getDownlines($downlines[$key]["downline"], $level));
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
        
        return $genealogy;
    }
}
?>
