<?php
/**
 * Sheila The Writer — Admin Logout
 */
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Destroy admin session data
$_SESSION['admin_id']    = null;
$_SESSION['admin_name']  = null;
$_SESSION['admin_email'] = null;
$_SESSION['admin_role']  = null;
unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_email'], $_SESSION['admin_role']);

// Full session destroy
session_destroy();

// Redirect to admin login
header('Location: admin-login.php');
exit;
