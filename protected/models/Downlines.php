<?php

/*
 * @author : owliber
 * @date : 2014-02-03
 */

class Downlines extends CFormModel
{
    public $_connection;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function firstLevel($member_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                   -- endorser_id as endorser,                    
                   -- placement_id as upline,
                    member_id AS downline
                    -- count(m.member_id) AS total
                  FROM members m
                  WHERE m.placement_id = :member_id;";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $result = $command->queryAll();
        
        return $result;
        
    }
    
    public function nextLevel($member_ids)
    {
        $conn = $this->_connection;
        
        //$member_ids = implode(',', $member_ids);
        $query = "SELECT
                    -- endorser_id AS endorser,
                    -- m.placement_id AS upline,
                    m.member_id AS downline
                    -- count(m.member_id) AS total
                  FROM members m
                  WHERE m.placement_id IN (SELECT
                    m1.member_id
                  FROM members m1
                  WHERE m1.placement_id IN ($member_ids) )
                  GROUP BY m.member_id
                  HAVING COUNT(m.member_id) < 5";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_ids);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function convertToList($arr)
    {
        foreach($arr as $item)
        {
            $items[] = $item['downline'];
        }
        
        $listItems = implode(',', $items);
        return array('arrayList'=>$items,
                     'listItem'=>$listItems);
    }
    
    public function getDownlineLists($member_id)
    {
        $downlines = $this->firstLevel($member_id);
        $level = 1;

        do
        {
            
            foreach($downlines as $downline)
            {
                $result[] = array('level'=>$level,
                                  'downlines'=>$downline['downline'],
                                );
            }
            
            
            
            $rows = $this->convertToList($downlines);        
            $downlines = $this->nextLevel($rows['listItem']);
            $max_per_level = pow(count($downlines),$level);

            $level++;
            $total_downlines = count($downlines);
             

        }while($total_downlines>0 && $total_downlines>=$max_per_level);
        
        return $result;
    }
    
    public function getLevelCount($member_id)
    {
        $downlines = $this->firstLevel($member_id);
        $level = 1;

        do
        {
            $total = count($downlines);
            
            $result[] = array('level'=>$level,
                              'total'=>$total,
                        );
            
            $rows = $this->convertToList($downlines);        
            $downlines = $this->nextLevel($rows['listItem']);
            $max_per_level = pow(count($downlines),$level);

            $level++;
            $total_downlines = count($downlines);
             

        }while($total_downlines>0 && $total_downlines>=$max_per_level);
        
        return $result;
        
    }
}
?>
