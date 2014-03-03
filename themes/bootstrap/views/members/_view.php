<?php

/**
 * @author Noel Antonio
 * @date 01-28-2014
 */

$this->widget('bootstrap.widgets.TbGridView', array(
        'id'=>'placement-grid',
        'type'=>'striped bordered condensed',
        'dataProvider'=>$dataProvider,
        'enablePagination' => true,
        'columns' => array(
                        /*array('name'=>'MemberID',
                            'header'=>'Member ID',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["member_id"])',
                            'htmlOptions' => array('style' => 'text-align:center'),    
                        ),*/ 
                        array('name'=>'Username',
                            'header'=>'User Name',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["username"])',
                            'htmlOptions' => array('style' => 'text-align:left'),  
                            'headerHtmlOptions' => array('style' => 'text-align:left'),  
                        ),
                        array('name'=>'FullName',
                            'header'=>'Full Name',
                            'type'=>'raw',
                            'value'=>'CHtml::link(CHtml::encode($data["last_name"] . ", " . $data["first_name"] . " " . $data["middle_name"]), 
                                array("members/genealogy", "id"=>$data["member_id"])
                            )',
                            'htmlOptions' => array('style' => 'text-align:left'),  
                            'headerHtmlOptions' => array('style' => 'text-align:left'),  
                        ),
                        /*array('name'=>'LastName',
                            'header'=>'Last Name',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["last_name"])',
                            'htmlOptions' => array('style' => 'text-align:center'),  
                            'headerHtmlOptions' => array('style' => 'text-align:center'),  
                        ),
                        array('name'=>'FirstName',
                            'header'=>'First Name',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["first_name"])',
                            'htmlOptions' => array('style' => 'text-align:center'),  
                            'headerHtmlOptions' => array('style' => 'text-align:center'),  
                        ),
                        array('name'=>'MiddleName',
                            'header'=>'Middle Name',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["middle_name"])',
                            'htmlOptions' => array('style' => 'text-align:center'), 
                            'headerHtmlOptions' => array('style' => 'text-align:center'),  
                        ),*/
                        array('name'=>'Endorser',
                            'header'=>'Endorsed By',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["endorser"])',
                            'htmlOptions' => array('style' => 'text-align:left'),   
                            'headerHtmlOptions' => array('style' => 'text-align:left'),  
                        ),
                        array('name'=>'Upline',
                            'header'=>'Placed Under',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["upline"])',
                            'htmlOptions' => array('style' => 'text-align:left'),    
                            'headerHtmlOptions' => array('style' => 'text-align:left'),  
                        ),
                        /*array('name'=>'BirthDate',
                            'header'=>'Birth Date',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["birth_date"])',
                            'htmlOptions' => array('style' => 'text-align:center'),    
                        ),
                        array('name'=>'MobileNo',
                            'header'=>'Mobile No',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["mobile_no"])',
                            'htmlOptions' => array('style' => 'text-align:center'),    
                        ),
                        array('name'=>'Email',
                            'header'=>'Email',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["email"])',
                            'htmlOptions' => array('style' => 'text-align:center'),    
                        ),*/
                        array('name'=>'Status',
                            'header'=>'Status',
                            'type'=>'raw',
                            'value'=>'CHtml::link($data["status"], array("members/terminate", "id"=>$data["member_id"]))',
                            'htmlOptions' => array('style' => 'text-align:left; width: 5%;'),  
                            'headerHtmlOptions' => array('style' => 'text-align:left'),  
                        ),
                        array('name'=>'Action',
                            'header'=>'',
                            'type'=>'raw',
                            'value'=>'CHtml::link("Update", array("members/update", "id"=>$data["member_id"]))',
                            'htmlOptions' => array('style' => 'text-align:center; width: 5%;'),    
                            'headerHtmlOptions' => array('style' => 'text-align:center'),  
                        ),
        )
        ));
?>

