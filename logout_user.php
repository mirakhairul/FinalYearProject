<?php
session_start();
session_destroy();
header("Location: home.php?logout_success=1");
exit();
?>
