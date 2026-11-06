<div class="page-header"><h1 class="page-title"><?= $title ?></h1></div>
<div class="card"><div class="card-body">
    <table class="table table-bordered">
        <tr><th width="200">Kode</th><td><?= htmlspecialchars($data->kode ?? '') ?></td></tr>
        <tr><th>Nama</th><td><?= htmlspecialchars($data->nama ?? '') ?></td></tr>
        <tr><th>Deskripsi</th><td><?= htmlspecialchars($data->deskripsi ?? '') ?></td></tr>
        <tr><th>Periode</th><td><?= htmlspecialchars(($data->tanggal_mulai ?? '') . ' - ' . ($data->tanggal_selesai ?? '')) ?></td></tr>
        <tr><th>Status</th><td><?= !empty($data->is_active) ? 'Aktif' : 'Tidak Aktif' ?></td></tr>
    </table>
    <a href="<?= base_url($back_url) ?>" class="btn btn-secondary">Kembali</a>
</div></div>
