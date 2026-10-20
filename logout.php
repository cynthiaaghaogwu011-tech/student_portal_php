<?php
session_start();
$_SESSION = []; //Empties all stored data.
session_destroy(); //Ends all session on the server.
header("Location: login.php");
exit;