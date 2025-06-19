<?php
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit();
?>
<a href="logout.php">Logout</a>