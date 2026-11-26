<?php
require 'init.php'; require_student(); require 'db.php';
$companies = $pdo->query('SELECT * FROM companies ORDER BY company_name')->fetchAll();
$stmt = $pdo->prepare('SELECT * FROM positions WHERE company_id = ?');
$title = 'Companies'; require 'header.php';
?>
<h1>Company directory</h1><p class="muted">Explore companies and discover available internship opportunities across different departments.</p><p class="muted">Sample opportunities for demonstration.</p>
<section class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3 my-4">
<?php foreach ($companies as $c): $stmt->execute([$c['id']]); ?>
<div class="col"><article class="card panel company-card h-100"><span class="initial"><?= e(substr($c['company_name'],0,1)) ?></span><h2><?= e($c['company_name']) ?></h2><p><?= e($c['industry']) ?> · <?= e($c['location']) ?></p><h3>Available positions</h3><ul><?php foreach ($stmt->fetchAll() as $pos): ?><li><?= e($pos['position']) ?><br><small class="muted"><?= e($pos['department']) ?></small></li><?php endforeach; ?></ul><a href="applications.php?company_id=<?= $c['id'] ?>">Apply now</a></article></div>
<?php endforeach; ?></section><?php require 'footer.php'; ?>