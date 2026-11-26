<?php
require 'init.php'; require_student(); require 'db.php';
$id = $_SESSION['user_id'];
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_FILES['resume'] ?? null;
    if (!valid_token() || !$file || $file['error'] !== UPLOAD_ERR_OK || $file['size'] > 2*1024*1024 || !is_uploaded_file($file['tmp_name']) || (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) !== 'application/pdf') {
        $message = 'Please select a valid PDF file up to 2 MB.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO resumes (user_id, filename, pdf) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE filename = VALUES(filename), pdf = VALUES(pdf)');
        $stmt->execute([$id, substr(basename($file['name']),0,255), file_get_contents($file['tmp_name'])]);
        $message = 'Resume saved successfully.';
    }
}
$stmt = $pdo->prepare('SELECT filename, pdf FROM resumes WHERE user_id = ?');
$stmt->execute([$id]); $resume = $stmt->fetch();
if (isset($_GET['download']) && $resume) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="resume.pdf"');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: private, no-store');
    echo $resume['pdf']; exit;
}
$title = 'Resume'; require 'header.php';
?>
<h1>My resume</h1><p class="muted">Keep your latest resume ready for internship applications.</p>
<section class="card panel entry">
<?php if ($message): ?><p role="status"><?= e($message) ?></p><?php endif; ?>
<?php if ($resume): ?><p>Current resume: <?= e($resume['filename']) ?></p><a href="resume.php?download=1">Download / view resume</a><?php else: ?><p>No resume uploaded yet.</p><?php endif; ?>
<form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?= e(token()) ?>"><label class="form-label" for="resume">PDF resume (maximum 2 MB)</label><input class="form-control" id="resume" type="file" name="resume" accept="application/pdf,.pdf" required><button class="btn btn-primary primary"><?= $resume ? 'Replace resume' : 'Upload resume' ?></button></form>
</section><?php require 'footer.php'; ?>
