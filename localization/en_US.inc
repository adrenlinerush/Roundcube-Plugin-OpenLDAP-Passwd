<?php

$labels = array();
$labels['changepasswd']  = 'Change Password';
$labels['curpasswd']  = 'Current Password:';
$labels['newpasswd']  = 'New Password:';
$labels['confpasswd']  = 'Confirm New Password:';

$messages = array();
$messages['nopassword'] = 'Please input new password.';
$messages['nocurpassword'] = 'Please input current password.';
$messages['passwordincorrect'] = 'Current password incorrect.';
$messages['passwordinconsistency'] = 'Passwords do not match, please try again.';
$messages['crypterror'] = 'Could not save new password. Encrypt function missing.';
$messages['connecterror'] = 'Could not save new password. Connection error.';
$messages['internalerror'] = 'Could not save new password.';
$messages['passwordvalfail'] = "Password failed requirements:\n 8 characters,\n 1 uppercase,\n 1 lowercase,\n 1 number,\n No sequences longer than 2 characters";


?>
