<?php

/**
 * @author Noel Antonio
 * @date 02/11/2014
 */

class NetworkController extends Controller
{
    public $layout = "column2";
    
    public function actionIndex()
    {
        $this->showGenealogy();
    }
    
    public function actionGenealogy()
    {
        $this->showGenealogy();
    }
    
    public function showGenealogy()
    {
        if (isset($_POST["hidden_member_id"])) {
            $member_id = $_POST["hidden_member_id"];
            Yii::app()->session['hidden_member_id'] = $member_id;
        }
        else if (Yii::app()->request->isAjaxRequest) {
            $member_id = Yii::app()->session['hidden_member_id'];
        }
        else {
            $member_id = Yii::app()->user->getId();
        }
        
        $member_name = Networks::getMemberName($member_id);
        
        $rawData = Networks::getDownlines($member_id);
        $final = Networks::arrangeLevel($rawData);
        $count = $final['total'];
        
        $dataProvider = new CArrayDataProvider($final['network'], array(
                        'keyField' => false,
                        'pagination' => false,
        ));
        
        return $this->render('_genealogy', array('dataProvider'=>$dataProvider, 'member_name'=>$member_name, 'counter'=>$count));
    }
    
    public function actionUnilevel()
    {
        if (isset($_POST["hidden_member_id"])) {
            $member_id = $_POST["hidden_member_id"];
            Yii::app()->session['hidden_member_id'] = $member_id;
        }
        else if (Yii::app()->request->isAjaxRequest) {
            $member_id = Yii::app()->session['hidden_member_id'];
        }
        else {
            $member_id = Yii::app()->user->getId();
        }
        
        $member_name = Networks::getMemberName($member_id);
        
        $rawData = Networks::getUnilevel($member_id);
        $final = Networks::arrangeLevel($rawData);
        $count = $final['total'];
        
        $dataProvider = new CArrayDataProvider($final['network'], array(
                        'keyField' => false,
                        'pagination' => false,
        ));
        
        $this->render('_unilevel', array('dataProvider'=>$dataProvider, 'member_name'=>$member_name, 'counter'=>$count));
    }
    
    public function actionDirectEndorse()
    {
        $model = new NetworksModel();
        
        $member_id = Yii::app()->user->getId();
        
        $rawData = $model->getDirectEndorse($member_id);
        
        $count = count($rawData);
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));
        
        $this->render('_directEndorse', array('dataProvider'=>$dataProvider, 'counter'=>$count));
    }
    
    public function actionGenealogyDownlines()
    {
        if (isset($_POST["postData"])) 
        {
            $member_ids = $_POST["postData"];
            Yii::app()->session['ids'] = $member_ids;
        }
        else if (Yii::app()->request->isAjaxRequest) {
            $member_ids = Yii::app()->session['ids'];
        }
        
        $array = Networks::getGenealogyDownlines($member_ids);

        $dataProvider = new CArrayDataProvider($array, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));

        $this->renderPartial('_downlines', array('dataProvider'=>$dataProvider));
    }
    
    public function actionUnilevelDownlines()
    {
        if (isset($_POST["postData"])) 
        {
            $member_ids = $_POST["postData"];
            Yii::app()->session['ids'] = $member_ids;
        }
        else if (Yii::app()->request->isAjaxRequest) {
            $member_ids = Yii::app()->session['ids'];
        }
        
        $array = Networks::getUnilevelDownlines($member_ids);

        $dataProvider = new CArrayDataProvider($array, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));

        $this->renderPartial('_downlines', array('dataProvider'=>$dataProvider));
    }
}
?>
