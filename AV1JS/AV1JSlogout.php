<?php
session_start();
session_destroy();
header('Location: AV1JSlogin.php');
exit;
?>