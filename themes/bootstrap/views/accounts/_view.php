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
                        ), */
                        array('name'=>'UserName',
                            'header'=>'Username',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["username"])',
                            'htmlOptions' => array('style' => 'text-align:center'),    
                        ),
                        array('name'=>'LastName',
                            'header'=>'Last Name',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["last_name"])',
                            'htmlOptions' => array('style' => 'text-align:center'),    
                        ),
                        array('name'=>'FirstName',
                            'header'=>'First Name',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["first_name"])',
                            'htmlOptions' => array('style' => 'text-align:center'),    
                        ),
                        array('name'=>'MiddleName',
                            'header'=>'Middle Name',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["middle_name"])',
                            'htmlOptions' => array('style' => 'text-align:center'),    
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
                        ),*/
                        array('name'=>'Email',
                            'header'=>'Email',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["email"])',
                            'htmlOptions' => array('style' => 'text-align:center'),    
                        ),
                        array('name'=>'ChangePassword',
                            'header'=>'',
                            'type'=>'raw',
                            'value'=>'CHtml::link("Change Password", array("accounts/changePassword", "id"=>$data["member_id"]))',
                            'htmlOptions' => array('style' => 'text-align:center'),    
                        ),
                        array('name'=>'Action',
                            'header'=>'',
                            'type'=>'raw',
                            'value'=>'CHtml::link("Update", array("accounts/update", "id"=>$data["member_id"]))',
                            'htmlOptions' => array('style' => 'text-align:center'),    
                        ),
        )
        ));
?>

