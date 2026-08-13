<?php
// hrmain.php

include ROOT_PATH . '/network/connect.php';
include ROOT_PATH . '/administration/auth/index-authguard.php';
include ROOT_PATH . '/administration/auth/index-roles.php';

$allowedRoles = [ROLE_HR];
$allowedPositions = [POSITION_HEAD]; 
include ROOT_PATH . '/administration/auth/index-roleguard.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php include ROOT_PATH . '/link/top.php'; ?>
</head>
<body>
    <!-- Navigation -->
    <?php include ROOT_PATH . '/client/navigation/top.php'; ?>

</body>
</html>