<?php

/*
 * @author : owliber
 * @date : 2014-02-06
 */

class PlacementController extends Controller
{
    
    public $layout = "column2";
    public $showDialog = false;
    
    public function actionIndex()
    {
        $model = new PlacementModel();
        $model->upline_id = Yii::app()->user->getId();
        
        $result = $model->getPlacementForApproval($model->upline_id);
        
        $gridDataProvider = new CArrayDataProvider($result,array(
                                'keyField'=>'member_id',
                                'pagination'=>array(
                                    'pageSize'=>10,
                                ),
                            ));
        
        $this->render('index',array('gridDataProvider'=>$gridDataProvider));
    }
    
    public function actionDownlines()
    {
        $model = new PlacementModel();
        $model->endorser_id = Yii::app()->user->getId();
        
        if(Yii::app()->request->isAjaxRequest && isset($_GET['upline_id']) && isset($_GET['downline_id']))
        {
            if(!empty($_GET['upline_id']))
                $result = array('result_code'=>0,'result_msg'=>'You have successfully assigned the new member.');
            else
            {
                $result = array('result_code'=>1,'result_msg'=>'Assignment cancelled.');
            }
            
            echo CJSON::encode($result);
            Yii::app()->end();
        }
                        
        $result = $model->getUnassignedDownlines($model->endorser_id);
        
        $gridDataProvider = new CArrayDataProvider($result,array(
                                'keyField'=>'member_id',
                                'pagination'=>array(
                                    'pageSize'=>10,
                                ),
                            ));
        
        $this->render('downlines',array('gridDataProvider'=>$gridDataProvider,'model'=>$model));
    }
    
    public function actionApprove()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $member_id = $_GET['id'];
            $placement = new PlacementModel();
                        
            $placement_info = $placement->pendingPlacement($member_id);
            $upline_id = $placement_info['upline_id'];            
            
            $result = $placement->placeUnder($member_id, $upline_id);
            
            if(count($result) > 0)
                echo CJSON::encode(array('result_code'=>0, 'result_msg'=>'Your new downline is successfully assigned and approved.'));
            else
                echo CJSON::encode(array('result_code'=>1, 'result_msg'=>'A problem encountered while processing your request.'));
            
            Yii::app()->end();
        }
    }
    
    public function actionDisapprove()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $member_id = $_GET['id'];
            $model = new PlacementModel();
            
            //empty upline_id in members table then delete record from pending_placements
            $retval = $model->removePlacement($member_id);
            
            if($retval)
                echo CJSON::encode(array('result_code'=>0, 'result_msg'=>'You successfuly DISAPPROVED your downline request.'));
            else
                echo CJSON::encode(array('result_code'=>1, 'result_msg'=>'A problem encountered while processing your request.'));
            
            Yii::app()->end();
        }
    }
    
    public function actionAssign()
    {
        if(Yii::app()->request->isAjaxRequest)
        {            
            $details[] = array('downline'=>$_GET['id'],'downline_name'=>$_GET['name']);
            echo CJSON::encode($details);
        }
    }
    

}
?>
