<?php

/*
 * @author : owliber
 * @date : 2014-03-30
 */

class DistributorsController extends Controller
{
    public function actionSearch()
    {
        if(Yii::app()->request->isAjaxRequest && isset($_GET['term']))
        {
            $model = new DistributorForm();

            $result = $model->autoCompleteSearch($_GET['term']);

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
