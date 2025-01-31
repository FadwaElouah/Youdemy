<?php
session_start();

require_once '../config/config.php';
require_once '../classes/User.php';


session_destroy();
header('Location: ../index.php');
exit;
?>

