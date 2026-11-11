<?php
// ================================================================
// FILE: includes/alerts.php
// ================================================================

$flash = flash('dashboard');
if ($flash):
?>
<div class="alert alert-<?= e($flash['type']) ?>">
    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
    <span><?= e($flash['message']) ?></span>
</div>
<?php endif; ?>