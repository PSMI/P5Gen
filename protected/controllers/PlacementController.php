<?php

/*
 * @author : owliber
 * @date : 2014-02-06
 */

class PlacementController extends Controller
{
    public $layout = "column2";
    
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
        
        $this->render('index',array('gridDataProvider'=>$gridDataProvider,'model'=>$model));
    }
    
    public function actionApprove()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            echo CJSON::encode($_GET['id']);
        }
    }
    
    public function actionDisapprove()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            echo CJSON::encode($_GET['id']);
        }
    }
}
?>
