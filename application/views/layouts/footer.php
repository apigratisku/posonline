<?php
$toko_nama = isset($profil_toko) && !empty($profil_toko->nama) ? $profil_toko->nama
    : (isset($profil_apotek) && !empty($profil_apotek->nama) ? $profil_apotek->nama : 'Toko POS');
$footer_profil = isset($profil_toko) ? $profil_toko : (isset($profil_apotek) ? $profil_apotek : null);
$footer_info = array();
if (!empty($footer_profil->telepon ?? '')) {
    $footer_info[] = htmlspecialchars($footer_profil->telepon);
}
if (!empty($footer_profil->alamat ?? '')) {
    $footer_info[] = htmlspecialchars($footer_profil->alamat);
}
?>
<!-- Footer -->
<footer class="footer mt-auto py-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <span class="text-muted">© <?= date('Y') ?> <?= htmlspecialchars($toko_nama) ?>. All rights reserved.</span>
                <?php if (!empty($footer_info)): ?>
                    <br><small class="text-muted"><?= implode(' · ', $footer_info) ?></small>
                <?php endif; ?>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="text-muted">Version 1.0.0</span>
            </div>
        </div>
    </div>
</footer>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>

<!-- Custom Scripts -->
<script>
window.baseUrl = '<?= base_url() ?>';

<?php if ($this->config->item('csrf_protection')): ?>
$.ajaxSetup({
    beforeSend: function(xhr, settings) {
        if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
            xhr.setRequestHeader('X-CSRF-TOKEN', '<?= $this->security->get_csrf_hash() ?>');
        }
    },
    data: {
        '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
    }
});
<?php endif; ?>
</script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<script src="<?= base_url('assets/js/mobile-nav.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/datatable.js?v=' . (is_file(FCPATH . 'assets/js/modules/datatable.js') ? filemtime(FCPATH . 'assets/js/modules/datatable.js') : time())) ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script src="<?= base_url('assets/js/modules/form-handler.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/modal.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/notification.js') ?>"></script>

<?php if (isset($page_js)): ?>
    <?php foreach ($page_js as $js): ?>
        <script src="<?= base_url('assets/js/pages/' . $js) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($page_scripts)): ?>
    <?= $page_scripts ?>
<?php endif; ?>
