<?php
// ================================================================
// FILE: includes/alerts.php
// Flash messages renderer (shared kwa roles zote)
// ================================================================
?>
<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="sa-content alert alert-success" style="background:#ECFDF5;color:#047857;border-left:4px solid #10B981;padding:0.9rem 1.1rem;border-radius:14px;margin-bottom:1.25rem;font-size:0.9rem;font-weight:500;display:flex;align-items:center;gap:0.7rem;">
        <i class="fas fa-circle-check"></i>
        <span><?= e($_SESSION['flash_success']) ?></span>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="sa-content alert alert-danger" style="background:#FEF2F2;color:#DC2626;border-left:4px solid #EF4444;padding:0.9rem 1.1rem;border-radius:14px;margin-bottom:1.25rem;font-size:0.9rem;font-weight:500;display:flex;align-items:center;gap:0.7rem;">
        <i class="fas fa-circle-exclamation"></i>
        <span><?= e($_SESSION['flash_error']) ?></span>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_warning'])): ?>
    <div class="sa-content alert alert-warning" style="background:#FFFBEB;color:#92400E;border-left:4px solid #F59E0B;padding:0.9rem 1.1rem;border-radius:14px;margin-bottom:1.25rem;font-size:0.9rem;font-weight:500;display:flex;align-items:center;gap:0.7rem;">
        <i class="fas fa-triangle-exclamation"></i>
        <span><?= e($_SESSION['flash_warning']) ?></span>
    </div>
    <?php unset($_SESSION['flash_warning']); ?>
<?php endif; ?>