<?php
require 'init.php';
require 'db.php';
if (!empty($_SESSION['role'])) { header('Location: dashboard.php'); exit; }
$title='Login'; $error=''; $email='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 $email=trim($_POST['email']??''); $password=$_POST['password']??'';
 // Find the registered account.
 $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
 $stmt->execute([$email]);
 $account = $stmt->fetch();
 if (!valid_token()) $error='Invalid request. Please try again.';
 elseif ($email==='' || $password==='') $error='Please complete all fields.';
 elseif ($account && password_verify($password,$account['password'])) {
  session_regenerate_id(true); $_SESSION['name']=$account['name']; $_SESSION['user_id']=$account['id']; $_SESSION['role']=$account['role']; $_SESSION['company_id']=$account['company_id']; header('Location: dashboard.php'); exit;
 } else $error='Invalid email or password.';
}
require 'header.php'; ?>
<section class="card auth panel"><p class="eyebrow">INTERNSHIP PORTAL</p><h1>Welcome back</h1><p class="muted">Sign in to track your internship applications.</p>
<?php if ($error): ?><p class="alert alert-danger error" role="alert"><?= e($error) ?></p><?php endif; ?>
<?php if (isset($_GET['registered'])): ?><p class="alert alert-success notice">Registration successful. Please sign in.</p><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?= e(token()) ?>"><label class="form-label" for="email">Email address</label><input class="form-control" id="email" name="email" type="email" value="<?= e($email) ?>" required><label class="form-label" for="password">Password</label><input class="form-control" id="password" name="password" type="password" required><button class="btn btn-primary primary">Sign in</button></form><p>New student? <a href="register.php">Create an account</a></p></section><?php require 'footer.php'; ?>
