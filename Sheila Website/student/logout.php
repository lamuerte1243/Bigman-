<?php
/**
 * Sheila The Writer — Student Logout
 */
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Clear student session data
unset($_SESSION['student_id'], $_SESSION['student_name'], $_SESSION['student_email']);

// Full session destroy
session_destroy();

// Redirect to student login
header('Location: login.php');
exit;
