<?php
require 'init.php';
require_student();
require 'db.php';
$user_id = $_SESSION['user_id'];
$message = '';
$statuses = ['Pending','Offered','Rejected','Offer Accepted','Offer Declined'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 if (!valid_token()) $message = 'Invalid request.';
 elseif (isset($_POST['add_application'])) {
  $company = (int)($_POST['company_id'] ?? 0); $position = $_POST['position'] ?? '';
  $check = $pdo->prepare('SELECT id FROM positions WHERE company_id=? AND position=?'); $check->execute([$company,$position]);
  $resume = $pdo->prepare('SELECT pdf FROM resumes WHERE user_id=?'); $resume->execute([$user_id]); $pdf=$resume->fetchColumn();
  if (!$check->fetch()) $message='Select a valid position.';
  elseif (!$pdf) $message='Upload your PDF resume before applying.';
  else { $stmt=$pdo->prepare("INSERT IGNORE INTO applications (user_id,company_id,position,application_date,status,resume_pdf) VALUES (?,?,?,CURDATE(),'Pending',?)"); $stmt->execute([$user_id,$company,$position,$pdf]); $message=$stmt->rowCount()?'Application submitted successfully.':'You have already applied for this position.'; }
 } elseif (isset($_POST['decision'])) {
  $decision=$_POST['decision'];
  if (in_array($decision,['Offer Accepted','Offer Declined'],true)) { $stmt=$pdo->prepare("UPDATE applications SET status=? WHERE id=? AND user_id=? AND status='Offered'"); $stmt->execute([$decision,(int)($_POST['application_id']??0),$user_id]); $message=$stmt->rowCount()?'Offer response saved.':'This offer is no longer available.'; }
 }
}
$companies = $pdo->query('SELECT * FROM companies ORDER BY company_name')->fetchAll(PDO::FETCH_ASSOC);
$sql = 'SELECT applications.*, companies.company_name FROM applications JOIN companies ON applications.company_id = companies.id WHERE applications.user_id = ?';
$params = [$user_id];
$filter = $_GET['status'] ?? '';
if (in_array($filter, $statuses, true)) {
    $sql .= ' AND applications.status = ?';
    $params[] = $filter;
}
$stmt = $pdo->prepare($sql . ' ORDER BY applications.id DESC');
$stmt->execute($params);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
$selected = (int) ($_GET['company_id'] ?? $_POST['company_id'] ?? 0);
$stmt = $pdo->prepare('SELECT position FROM positions WHERE company_id = ? ORDER BY position');
$stmt->execute([$selected]);
$positions = $stmt->fetchAll(PDO::FETCH_COLUMN);
$title = 'Applications';
require 'header.php';
?>
<h1>Internship applications</h1>
<p class="muted">Submit applications and respond to internship offers.</p>
<?php if ($message): ?><p class="alert alert-info"><?= e($message) ?></p><?php endif; ?>
<?php if (in_array($_GET['result'] ?? '', ['added','updated','deleted'], true)): ?><p class="alert alert-success notice">Application <?= e($_GET['result']) ?> successfully.</p><?php endif; ?>
<section class="card panel entry">
<h2>Submit an application</h2>
<form method="get">
<label class="form-label" for="company">Company</label>
<select class="form-select" id="company" name="company_id" required><option value="">Select company</option>
<?php foreach ($companies as $company): ?><option value="<?= $company['id'] ?>" <?= $selected == $company['id'] ? 'selected' : '' ?>><?= e($company['company_name']) ?></option><?php endforeach; ?></select>
<button class="btn btn-primary primary">Show available positions</button></form>
<?php if ($positions): ?>
<form method="post"><input type="hidden" name="csrf" value="<?= e(token()) ?>"><input type="hidden" name="company_id" value="<?= $selected ?>">
<label class="form-label" for="position">Position</label><select class="form-select" id="position" name="position" required><option value="">Select position</option><?php foreach ($positions as $position): ?><option><?= e($position) ?></option><?php endforeach; ?></select>
<p class="muted">Your current resume will be attached automatically.</p>
<button class="btn btn-primary primary" name="add_application">Submit application</button>
</form><?php endif; ?></section>
<section class="card panel"><h2>My applications</h2>
<p><a href="applications.php">Show all applications</a></p>
<div class="table-responsive"><table class="table table-dark align-middle"><thead><tr><th>Company</th><th>Position</th><th>Date</th><th>Status</th><th>Action</th></tr></thead><tbody>
<?php if (!$applications): ?><tr><td colspan="5">No applications found.</td></tr><?php endif; ?>
<?php foreach ($applications as $a): ?>
<tr><td><?= e($a['company_name']) ?></td><td><?= e($a['position']) ?></td><td><?= e($a['application_date']) ?></td><td>
<?= e($a['status']) ?></td><td><?php if ($a['status']==='Offered'): ?><form method="post"><input type="hidden" name="csrf" value="<?= e(token()) ?>"><input type="hidden" name="application_id" value="<?= $a['id'] ?>"><button class="btn btn-success btn-sm" name="decision" value="Offer Accepted">Accept Offer</button> <button class="btn btn-outline-danger btn-sm" name="decision" value="Offer Declined">Decline Offer</button></form><?php else: ?>—<?php endif; ?></td></tr>
<?php endforeach; ?></tbody></table></div></section>
<?php require 'footer.php'; ?>
