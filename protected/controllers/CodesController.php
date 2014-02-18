<?php

/**
 * @author Noel Antonio
 * @date 01-27-2014
 */

class CodesController extends Controller 
{
    public $msg = '';
    public $title = '';
    public $showDialog = false;
    public $showConfirm = false;
    public $showRedirect = false;
    
    public $layout = 'column2';
    
    public function actionIndex()
    {
        if(!Yii::app()->user->hasUserAccess() && !Yii::app()->user->isSuperAdmin()) 
                $this->redirect(array('site/404'));
        
        $model = new ActivationCodeModel();
        
        $rawData = $model->selectAll();
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));
        
        $this->render('index', array('model'=>$model, 'dataProvider'=>$dataProvider));
    }
            
    public function actionCreate()
    {
        $model = new ActivationCodeModel();
        
        if (isset($_POST["ActivationCodeModel"]))
        { 
            $model->attributes = $_POST["ActivationCodeModel"];
            $quantity = $model->quantity;

            if ($model->validate())
            {
                if ($quantity <= 0) {
                    $this->title = "NOTIFICATION";
                    $this->msg = "Zero value and below are not accepted. Please try again.";
                    $this->showDialog = true;
                }
                else if ($quantity > 1000)
                {
                    $this->title = "NOTIFICATION";
                    $this->msg = "Maximum of 1000 codes are allowed to be generated.";
                    $this->showDialog = true;
                }
                else {
                    $this->title = "CONFIRMATION";
                    $this->msg = "Are you sure you want to generate " . $quantity . " activation code(s)?";
                    $this->showConfirm = true;
                }
            }
        }
        else if (isset($_POST["hiddenQty"]))
        {
            $quantity = $_POST["hiddenQty"];
            $aid = 1; // Yii::app()->session['AID'];
            $ipaddr = $_SERVER['REMOTE_ADDR'];

            $retval = $model->generateActivationCodes($quantity, $aid, $ipaddr);

            if ($retval) {
                $this->title = "SUCCESSFUL";
                $this->msg = "Activation code successfully generated!";
            }
            else {
                $this->title = "NOTIFICATION";
                $this->msg = $retval;
            }
            
            $this->showRedirect = true;
        }

        $this->render('_create', array('model'=>$model));
    }
    
    public function actionCodes()
    {
        $model = new ActivationCodeModel();
        
        $batchId = $_GET['id'];
        
        $rawData = $model->selectAllCodesByBatchId($batchId);
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));
        
        $this->render('_codes', array('model'=>$model, 'dataProvider'=>$dataProvider, 'batchId'=>$batchId));
    }
    
    public function actionPdf()
    {
        $model = new ActivationCodeModel();
        $batchId = $_POST['batch_id'];
        $rawData = $model->selectAllCodesByBatchId($batchId);

        $content .= '<table cellspacing="50"><tr>';
        foreach($rawData as $key => $val) {
            $content .= '<tr>';
            $content .= '<td>' . $val["activation_code"] . '</td>';
            $content .= '<td>' . $val["status"] . '</td>';
            $content .= '</tr>';           
        }
        $content .= '</tr></table>';
  
        /*$pdf = CTCPDF::c_getInstance();
        $pdf->c_commonReportFormat();
        $pdf->c_setHeader('Activation Codes');
        $pdf->SetFontSize(10);
        $pdf->c_generatePDF('Activation_Codes_' . date('Y-m-d') . '.pdf'); */
        
        $html2pdf = Yii::app()->ePdf->HTML2PDF();
        $html2pdf->WriteHTML($content);
        $html2pdf->Output('Activation_Codes_' . date('Y-m-d') . '.pdf', 'D'); 

    }
}
?>