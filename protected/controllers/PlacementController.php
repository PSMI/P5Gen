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
        
        $result = $model->getPlacementForApproval();
        
        $gridDataProvider = new CArrayDataProvider($result,array(
                                'keyField'=>'member_id',
                                'pagination'=>array(
                                    'pageSize'=>10,
                                ),
                            ));
        
        $this->render('index',array('gridDataProvider'=>$gridDataProvider));
    }
    
    public function actionAssign()
    {
        $model = new PlacementModel();
        $model->endorser_id = Yii::app()->user->getId();
        
        if(Yii::app()->request->isAjaxRequest && isset($_GET['upline_id']) && isset($_GET['downline_id']))
        {
            //Update new member upline & add to pending placement
            if(!empty($_GET['upline_id']))
            {
                $model->downline_id = $_GET['downline_id'];
                $model->upline_id = $_GET['upline_id'];
                
                $retval = $model->addPlacement();
                
                if(count($retval)>0)
                {
                    $param['upline_id'] = $model->upline_id;
                    $param['new_member_id'] = $model->downline_id;
                    $param['endorser_id'] = $model->endorser_id;
                    Mailer::sendUplineNotification($param);
                    
                    $result = array('result_code'=>0,'result_msg'=>'You have successfully assigned the new member.');
                }
                else
                {
                    $result = array('result_code'=>1,'result_msg'=>'Problem encountered while assigning the new member.');
                }
            }
            else
            {
                $result = array('result_code'=>1,'result_msg'=>'You did not select an upline, please assign again.');
            }
            
            echo CJSON::encode($result);
            Yii::app()->end();
        }
                        
        $result = $model->getUnassignedDownlines2();
        
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
            $model = new PlacementModel();  
             
            $member_id = $_GET['id'];
            $model->member_id = $member_id;            
                                 
            $placement = $model->pendingPlacement();
            
            $upline_id = $placement['upline_id'];            
            $endorser_id = $placement['endorser_id'];
            $model->upline_id = $upline_id;
            $model->endorser_id = $endorser_id;
            
            $result = $model->placeUnder();
            
            if($result)
            {
                $param['upline_id'] = $model->upline_id;
                $param['new_member_id'] = $model->member_id;
                $param['endorser_id'] = $model->endorser_id;
            
                Mailer::sendApproveNotification($param); 
                Mailer::sendMemberNotification($param);
                
                $retval = $model->updateRunningAccount();
                
                if($retval)
                    echo CJSON::encode(array('result_code'=>0, 'result_msg'=>'Your new downline is successfully assigned and approved.'));
                else
                    echo CJSON::encode(array('result_code'=>1, 'result_msg'=>$model->getErrors()));
            }
            else
            {
                echo CJSON::encode(array('result_code'=>2, 'result_msg'=>'A problem encountered while processing your request.'));
            }
                        
            Yii::app()->end();
        }
    }
    
    public function actionDisapprove()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $model = new PlacementModel();
            $member_id = $_GET['id'];
            $model->member_id = $member_id;
            $row = $model->pendingPlacement();
            
            $param['upline_id'] = $row['upline_id'];
            $param['new_member_id'] = $model->member_id;
            $param['endorser_id'] = $row['endorser_id'];            
            
            //empty upline_id in members table then delete record from pending_placements
            $model->removePlacement();    
            Mailer::sendDisapproveNotification($param);
            
            if(!$model->hasErrors())
                echo CJSON::encode(array('result_code'=>0, 'result_msg'=>'You successfuly DISAPPROVED your downline request.'));
            else
                echo CJSON::encode(array('result_code'=>1, 'result_msg'=>'A problem encountered while processing your request.'));
            
            Yii::app()->end();
        }
    }
    
    public function actionAssignForm()
    {
        if(Yii::app()->request->isAjaxRequest)
        {            
            $details[] = array('downline'=>$_GET['id'],'downline_name'=>$_GET['name']);
            echo CJSON::encode($details);
        }
    }
    
    public function actionDownlines()
    {
        if(Yii::app()->request->isAjaxRequest && isset($_GET['term']) && isset($_GET['id']))
        {
            $model = new PlacementModel();
            $model->downline_id = $_GET['id'];

            $result = $model->selectOnlyDownlines($_GET['term']);
            
            if(count($result)>0)
            {
                foreach($result as $row)
                {
                    $arr[] = array(
                        'id'=>$row['member_id'],
                        'value'=>$row['member_name'],
                        'label'=>$row['member_name'],
                    );
                }

                echo CJSON::encode($arr);
                Yii::app()->end();
            }
            
        }
    }
    

}
?>
