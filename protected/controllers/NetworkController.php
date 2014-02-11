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
        
        $member_name = $this->getMemberName($member_id);
        
        $rawData = Networks::getDownlines($member_id);
        $final = Networks::arrangeLevel($rawData);
        
        $dataProvider = new CArrayDataProvider($final, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 20,
                    ),
        ));
        
        $this->render('_genealogy', array('dataProvider'=>$dataProvider, 'member_name'=>$member_name));
    }
    
    public function actionGenealogy()
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
        
        $member_name = $this->getMemberName($member_id);
        
        $rawData = Networks::getDownlines($member_id);
        $final = Networks::arrangeLevel($rawData);
        
        $dataProvider = new CArrayDataProvider($final, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 20,
                    ),
        ));
        
        $this->render('_genealogy', array('dataProvider'=>$dataProvider, 'member_name'=>$member_name));
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
        
        $member_name = $this->getMemberName($member_id);
        
        $rawData = Networks::getUnilevel($member_id);
        $final = Networks::arrangeLevel($rawData);
        
        $dataProvider = new CArrayDataProvider($final, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));
        
        $this->render('_unilevel', array('dataProvider'=>$dataProvider, 'member_name'=>$member_name));
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
    
    public function actionLoan()
    {
        $rawData = array();
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));
        
        $this->render('_loan', array('dataProvider'=>$dataProvider));
    }
    
    public function actionGOC()
    {
        $rawData = array();
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));
        
        $this->render('_goc', array('dataProvider'=>$dataProvider));
    }
    
    public function actionGenealogyDownlines()
    {
        if (isset($_POST["postData"])) 
        {
            $member_ids = $_POST["postData"];
            Yii::app()->session['ids'] = $member_ids;
            /*$fp = fopen("selection.txt", "wb");
            fwrite($fp, $member_ids);
            fclose($fp);*/
        }
        else if (Yii::app()->request->isAjaxRequest) {
            //$member_ids = file_get_contents('selection.txt');
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
            /*$fp = fopen("selection.txt", "wb");
            fwrite($fp, $member_ids);
            fclose($fp);*/
        }
        else if (Yii::app()->request->isAjaxRequest) {
            //$member_ids = file_get_contents('selection.txt');
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
    
    public function getMemberName($member_id)
    {
        $model = new MembersModel();
        $info = $model->selectMemberName($member_id);
        $member_name = $info["last_name"] . ", " . $info["first_name"] . " " . $info["middle_name"];
        $member_name = strtoupper($member_name);
        
        return $member_name;
    }
}
?>
