<?php
require_once __DIR__ . '/functions.php';

$_SESSION = [];
session_destroy();
session_start();                    
set_flash('You have been logged out.', 'info');
header('Location: index.php');
exit;
