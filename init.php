<?php
session_name("INTERNTRACKV3"); session_start();
function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function require_login() { if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) { header('Location: login.php'); exit; } }
function require_student() { require_login(); if (($_SESSION['role'] ?? '') !== 'student') { header('Location: employer.php'); exit; } }
function token() { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function valid_token() { return hash_equals(token(), $_POST['csrf'] ?? ''); }
