<?php
require 'init.php';
require 'db.php';
if (!empty($_SESSION['role'])) { header('Location: dashboard.php'); exit; }
$title='Register'; $error=''; $name=''; $email='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 $name=trim($_POST['name']??''); $email=strtolower(trim($_POST['email']??'')); $password=$_POST['password']??'';
 $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
 $check->execute([$email]);
 if (!valid_token()) $error='Invalid request.';
 elseif ($name==='' || $email==='' || $password==='') $error='Please complete all fields.';
 elseif (!filter_var($email,FILTER_VALIDATE_EMAIL)) $error='Invalid email address.';
 elseif (strlen($password)<6) $error='Password must contain at least 6 characters.';
 elseif ($check->fetch()) $error='Email already registered.';
 else { $stmt = $pdo->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)');
 $stmt->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]); header('Location: login.php?registered=1'); exit; }
}
require 'header.php'; ?>
<section class="card auth panel"><p class="eyebrow">STUDENT PORTAL</p><h1>Create an account</h1><p class="muted">Create your student account. Your details are saved in the database.</p><?php if ($error): ?><p class="alert alert-danger error" role="alert"><?= e($error) ?></p><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?= e(token()) ?>"><label class="form-label" for="name">Full name</label><input class="form-control" id="name" name="name" value="<?= e($name) ?>" required><label class="form-label" for="email">Email address</label><input class="form-control" id="email" type="email" name="email" value="<?= e($email) ?>" required><label class="form-label" for="password">Password</label><input class="form-control" id="password" type="password" name="password" minlength="6" required><button class="btn btn-primary primary">Register</button></form><p>Already registered? <a href="login.php">Sign in</a></p></section><?php require 'footer.php'; ?>
