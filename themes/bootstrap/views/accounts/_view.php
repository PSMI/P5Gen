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
                        array('name'=>'UserName',
                            'header'=>'User Name',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["username"])',
                            'htmlOptions' => array('style' => 'text-align:left'),    
                        ),
                        array('name'=>'FullName',
                            'header'=>'Admin Name',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["last_name"] . ", " . $data["first_name"] . " " . $data["middle_name"])',
                            'htmlOptions' => array('style' => 'text-align:left'),  
                        ),
                        array('name'=>'Email',
                            'header'=>'Email',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["email"])',
                            'htmlOptions' => array('style' => 'text-align:left'),    
                        ),
                        array('name'=>'AccountTypeID',
                            'header'=>'Account Type',
                            'type'=>'raw',
                            'value'=>'CHtml::encode($data["account_type_name"])',
                            'htmlOptions' => array('style' => 'text-align:left'),    
                        ),
                        array('class'=>'bootstrap.widgets.TbButtonColumn',
                            'template'=>'{changepass}{update}',
                            'buttons'=>array
                            (
                                'changepass'=>array
                                (
                                    'label'=>'Change Password',
                                    'icon'=>'icon-lock',
                                    'url'=>'Yii::app()->createUrl("/accounts/changePassword", array("id" =>$data["member_id"]))',
                                    'options' => array(
                                        'class'=>"btn btn-small",
                                    ),
                                    array('id' => 'send-link-'.uniqid())
                                ),
                                'update'=>array
                                (
                                    'label'=>'Update Profile',
                                    'icon'=>'icon-edit',
                                    'url'=>'Yii::app()->createUrl("/accounts/update", array("id" =>$data["member_id"]))',
                                    'options' => array(
                                        'class'=>"btn btn-small",
                                    ),
                                    array('id' => 'send-link-'.uniqid())
                                ),
                            ),
                            'header'=>'Action',
                            'htmlOptions'=>array('style'=>'width:80px;text-align:center'),
                        ),
        )
        ));
?>

