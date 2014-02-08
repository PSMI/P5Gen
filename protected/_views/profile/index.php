<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<style>
    table {
        width: 70%;
        text-align: left;
    }
</style>
<h3>I. Personal Information</h3>

<table>
    <tr>
        <th>Username:</th><td><?php echo $data["username"]; ?></td>
    </tr>
    <tr>
        <th>Password:</th><td><?php echo CHtml::link('Change Password?', '', array('onclick'=>'$("#change-password").dialog("open");')); ?></td>
    </tr>
    <tr>
        <th>Name:</th><td><?php echo $data["last_name"] . ", " . $data["first_name"] . " " . $data["middle_name"]; ?></td>
    </tr>
    <tr>
        <th>Gender:</th><td><?php echo $data["gender"]; ?></td>
        <th>Birth Date:</th><td><?php echo $data["birth_date"]; ?></td>
    </tr>
    <tr>
        <th>Civil Status:</th><td><?php echo $data["civil_status"]; ?></td>
        <th>Spouse Name:</th><td><?php echo $data["spouse_name"]; ?></td>
    </tr>
    <tr>
        <th>Beneficiary:</th><td><?php echo $data["beneficiary_name"]; ?></td>
        <th>Relationship:</th><td><?php echo $data["relationship_name"]; ?></td>
    </tr>
    <tr>
        <th>TIN Number:</th><td><?php echo $data["tin_no"]; ?></td>
        <th>Company:</th><td><?php echo $data["company"]; ?></td>
    </tr>
    <tr>
        <th>Occupation:</th><td><?php echo $data["occupation_name"]; ?></td>
    </tr>
</table>
<br/>

<h3>II. Contact Information</h3>

<table>
    <tr>
        <th>Email Address:</th><td><?php echo $data["email"]; ?></td>
    </tr>
    <tr>
        <th>Address:</th><td><?php echo $data["address1"]; ?></td>
    </tr>
    <tr>
        <th>Telephone Number:</th><td><?php echo $data["telephone_no"]; ?></td>
        <th>Mobile Number:</th><td><?php echo $data["mobile_no"]; ?></td>
    </tr>
    <tr>
        <th>Spouse Contact Number:</th><td><?php echo $data["spouse_contact_no"]; ?></td>
    </tr>
</table>
<br/>

<h3>III. Endorser Information</h3>

<table style="width: 40%">
    <tr>
        <th>Endorser Name:</th><td><?php echo $endorser["last_name"] . ", " . $endorser["first_name"] . " " . $endorser["middle_name"]; ?></td>
    </tr>
</table>
<br/>

<h3>IV. Placement Information</h3>

<table style="width: 45%">
    <tr>
        <th>Place Under:</th><td><?php echo $upline["last_name"] . ", " . $upline["first_name"] . " " . $upline["middle_name"]; ?></td>
    </tr>
</table>

<!-- dialog box -->
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id'=>'change-password',
        'options'=>array(
            'title'=>'Change Password',
            'modal'=>true,
            'width'=>'500',
            'height'=>'auto',
            'resizable'=>false,
            'autoOpen'=>false,
        ),
)); ?>
<br />
<?php $this->renderPartial('_changepassword'); ?>
<br />
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>


<?php 
$trigger = false;
if ($this->showDialog) 
{
    $buttons = array(
                'OK'=>'js:function(){
                    $(this).dialog("close");
                }'
            );
    $trigger = $this->showDialog;
}
else if ($this->showRedirect)
{
    $buttons = array(
                'OK'=>'js:function(){
                    location.href = "'. Yii::app()->createUrl('profile/index') . '";
                }'
            );
    $trigger = $this->showRedirect;
}
else if ($this->reOpenDialog)
{
    $buttons = array(
                'OK'=>'js:function(){
                    $(this).dialog("close");
                    $("#change-password").dialog("open");
                }'
            );
    $trigger = $this->reOpenDialog;
}
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id'=>'dialog-box',
        'options'=>array(
            'title'=>$this->title,
            'modal'=>true,
            'width'=>'350',
            'height'=>'auto',
            'resizable'=>false,
            'autoOpen'=>$trigger,
            'buttons'=>$buttons
        ),
)); ?>
<br />
<?php echo $this->msg; ?>
<br />
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>