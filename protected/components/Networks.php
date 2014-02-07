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
        
        $downlines = $model->firstLevel($member_id);
        
        if(count($downlines) == 0)
        {
            $result[] = array('level'=>1,
                              'downlines'=>$member_id,
                            );
        }
        else
        {
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
        }
        
        return $result;
    }
    
    public static function getLevelCount($member_id)
    {
        $model = new Downlines();
        
        $downlines = $model->firstLevel($member_id);
        
        if(count($downlines) == 0)
        {
            $result[] = array('level'=>1,
                              'total'=>0,
                        );
        }
        else
        {
            $level = 1;
        
            do
            {
                $total = count($downlines);

                $result[] = array('level'=>$level,
                              'total'=>$total,
                        );

                $rows = Networks::convertToList($downlines);        
                $downlines = $model->nextLevel($rows['listItem']);
                $max_per_level = pow(count($downlines),$level);

                $level++;
                $total_downlines = count($downlines);




            }while(count($downlines)>0 && $total_downlines>0 && $total_downlines>=$max_per_level);

        }
        
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
    
    
}
?>
