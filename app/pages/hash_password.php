<?php
$password = 'Peyt52524343!';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo $hashed_password;
?>
