<?php require 'init.php'; if ($_SERVER['REQUEST_METHOD']==='POST' && valid_token()) { $_SESSION = []; session_regenerate_id(true); } header('Location: login.php'); exit;
