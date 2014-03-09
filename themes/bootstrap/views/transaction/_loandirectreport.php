<style type="text/css">
    page {font-family:Courier; font-size:10px; width:100%;}
    table#tbl-summary{font-family:Courier; font-size:12px; width:100%;}
    table, table th, table td{border:1px solid #0099FF; border-collapse: collapse; padding: 2px}
    table th {background-color: #0099FF;}
</style>
<?php
//Get Payee Details
$payee_username = $payee[0]['username'];
$date_joined = $payee[0]['date_joined'];
$payee_email = $payee[0]['email'];
$payee_mobile_no = $payee[0]['mobile_no'];
$payee_tel_no = $payee[0]['telephone_no'];
$endorser_name = $payee[0]['endorser_name'];
$curdate = date('M d, Y h:ia');
$address = $payee[0]['address1'];
$zip_code = $payee[0]['zip_code'];
$bday = $payee[0]['birth_date'];
$tin_no = $payee[0]['tin_no'];
$gender = $payee[0]['gender'];
if($gender == 1)
{
    $gender = "Male";
}
else
{
    $gender = "Female";
}
$civil_status = $payee[0]['civil_status'];
if($civil_status == 1)
{
    $civil_status = "Single";
}
else if($civil_status == 2)
{
    $civil_status = "Married";
}
else if($civil_status == 3)
{
    $civil_status = "Divorced";
}
else
{
    $civil_status = "Separated";
}
?>
<page>
    <h4>Loan Endorsement Application Form</h4>
    <table>
        <tr>
            <th width="100">Loan Amount</th>
            <td width="250"><?php echo $amount_in_words; ?></td>
            <th width="100">Php</th>
            <td width="250"><?php echo number_format($loan_amount, 2); ?></td>
        </tr>
    </table>
    <table>
        <tr>
            <td width="120" style="border: none;"><b>Purchased Product</b></td>
            <td width="200" style="border: none;"><?php echo CHtml::checkBox('chkboxProduct', false) ?> Water Filtration System - P2S</td>
            <td width="120" style="border: none;"><?php echo CHtml::checkBox('chkboxOtherProduct', false) ?> Other Product/s</td>
            <td width="150" style="border: none;">__________________________</td>
        </tr>
    </table>
    <table width="100%">
        <tr align="center">
            <th width="730" style="border:1px solid #0099FF; border-collapse: collapse; padding: 2px; font-size: 12px;">IBO's PERSONAL DETAILS</th>
        </tr>
    </table>
    <table>
        <tr>
            <th width="100">Name of Payee</th>
            <td width="250"><?php echo $member_name; ?></td>
            <th width="100">Email</th>
            <td width="250"><?php echo $payee_email; ?></td>
        </tr>
        <tr>
            <th>Username</th>
            <td><?php echo $payee_username; ?></td>
            <th>Mobile No</th>
            <td><?php echo $payee_mobile_no; ?></td>
        </tr>
        <tr>
            <th>Endorser Name</th>
            <td><?php echo $endorser_name; ?></td>
            <th>Telephone No</th>
            <td><?php echo $payee_tel_no; ?></td>
        </tr>
        <tr>
            <th>Address</th>
            <td><?php echo $address; ?></td>
            <th>Zip Code</th>
            <td><?php echo $zip_code; ?></td>
        </tr>
        <tr>
            <th>Birthday</th>
            <td><?php echo $bday; ?></td>
            <th>TIN #</th>
            <td><?php echo $tin_no; ?></td>
        </tr>
        <tr>
            <th>Gender</th>
            <td><?php echo $gender; ?></td>
            <th>Civil Status</th>
            <td><?php echo $civil_status; ?></td>
        </tr>
        <tr>
            <th>Date Joined</th>
            <td><?php echo $date_joined; ?></td>
            <th>Date Generated</th>
            <td><?php echo $curdate; ?></td>
        </tr>
    </table> 
    <table width="100%">
        <tr align="center">
            <th width="730" style="border:1px solid #0099FF; border-collapse: collapse; padding: 2px; font-size: 12px;">DIRECT ENDORSED DOWNLINE INFORMATION</th>
        </tr>
    </table>
    <table width="100%">
        <tr>
            <th>&nbsp;</th>
            <th width="250">Name of Endorsed IBO</th>
            <th width="250">Place Under</th>
            <th width="200">Date Joined</th>
        </tr>
        <?php
        $ctr = 1;
        foreach ($direct_downlines as $row) {
            ?>
            <tr>
                <td><?php echo $ctr; ?></td>
                <td><?php echo $row['member_name'] ?></td>
                <td><?php echo $row['upline_name']; ?></td>
                <td><?php echo $row['date_joined']; ?></td>
            </tr>
            <?php
            $ctr++;
        }
        ?>
    </table>
    <table width="100%">
        <tr align="center">
            <th width="730" style="border:1px solid #0099FF; border-collapse: collapse; padding: 2px; font-size: 12px;">UNDERTAKING</th>
        </tr>
    </table>
    <table width="100%">
        <tr align="justify">
            <td width="730" style="border: none;"><span style=" margin-left: 50px;">I</span>hereby certify that all the information furnished in this Application Form are true, correct and complete, and that the signature appearing herein is true and genuine.</td>
        </tr>
        <tr align="justify">
            <td width="730" style="border: none;"><span style=" margin-left: 50px;">I</span>hereby authorize 'SAGIP BUHAY' OVATION CREDIT CORPORATION to obtain such information as may required concerning the validity and veracity of the information provided in this application using any applicable methods or processes.</td>
        </tr>
        <tr align="justify">
            <td width="730" style="border: none;"><span style=" margin-left: 50px;">I</span>further agree that this application and all supporting documents by 'SAGIP BUHAY' OVATION CREDIT CORPORATION relative to this application shall remain as 'SAGIP BUHAY' OVATION CREDIT CORPORATION property whether or not the loan is granted. 'SAGIP BUHAY' OVATION CREDIT CORPORATION is authorized to use any information stated therein foe whatever legal purpose.</td>
        </tr>
        <tr align="justify">
            <td width="730" style="border: none;"><span style=" margin-left: 50px;">I</span>agree that 'SAGIP BUHAY' OVATION CREDIT CORPORATION has no obligation to furnish me the reason for such rejection.</td>
        </tr>
    </table>
    <br />
    <table width="100%">
        <tr align="center">
            <td width="365" style="border: none;">__________________________</td>
            <td width="365" style="border: none;">__________________________</td>
        </tr>
        <tr align="center">
            <td width="365" style="border: none;">Principal Borrower/IBO</td>
            <td width="365" style="border: none;">Date Signed</td>
        </tr>
        <tr align="center">
            <td width="365" style="border: none;">Signature over printed name</td>
            <td width="365" style="border: none;"></td>
        </tr>
    </table>
    <table width="100%">
        <tr align="center">
            <th width="730" style="border:1px solid #0099FF; border-collapse: collapse; padding: 2px; font-size: 12px;">ENDORSEMENT & VERIFICATION</th>
        </tr>
        <tr align="center">
            <th width="730" style="border:1px solid #0099FF; border-collapse: collapse; padding: 2px; font-size: 10px;">For P5 Personnel only</th>
        </tr>
    </table>
    <table width="100%">
        <tr align="left">
            <td width="150" style="border: none;">P5 Personnel Remark/s:</td>
            <td width="250" style="border: none;">__________________________________</td>
            <td width="100" style="border: none;">Approved Amount: </td>
            <td width="150" style="border: none;">Php <?php echo number_format($loan_amount, 2); ?></td>
        </tr>
        <tr align="left">
            <td width="150" style="border: none;"></td>
            <td width="250" style="border: none;">__________________________________</td>
            <td width="100" style="border: none;">Signature</td>
            <td width="150" style="border: none;">Date Signed</td>
        </tr>
        <tr align="left">
            <td width="150" style="border: none;">Verified By:</td>
            <td width="250" style="border: none;">__________________________________</td>
            <td width="150" style="border: none;">_______________________</td>
            <td width="150" style="border: none;">_______________________</td>
        </tr>
        <tr align="left">
            <td width="150" style="border: none;">Endorsed By:</td>
            <td width="250" style="border: none;">__________________________________</td>
            <td width="150" style="border: none;">_______________________</td>
            <td width="150" style="border: none;">_______________________</td>
        </tr>
        <tr align="left">
            <td width="150" style="border: none;">Approved By:</td>
            <td width="250" style="border: none;">__________________________________</td>
            <td width="150" style="border: none;">_______________________</td>
            <td width="150" style="border: none;">_______________________</td>
        </tr>
    </table>
</page>


