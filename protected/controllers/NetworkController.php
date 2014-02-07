<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class NetworkController extends Controller
{
    public $layout = "column2";
    
    public function actionIndex()
    {
        $rawData = array();
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));
        
        $this->render('index', array('dataProvider'=>$dataProvider));
    }
    
    public function actionGenealogy()
    {
        $rawData = array();
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));
        
        $this->render('_genealogy', array('dataProvider'=>$dataProvider));
    }
    
    public function actionUnilevel()
    {
        $rawData = array();
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));
        
        $this->render('_unilevel', array('dataProvider'=>$dataProvider));
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
}
?>
