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
//        {
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
    
    public function getDownlines($ids)
    {
        $model = new Downlines();
        
        foreach($ids as $id)
            $retval[] = array('downline'=>$model->levels($ids['downline']));
        
        return $retval;
    }
    
}
?>
