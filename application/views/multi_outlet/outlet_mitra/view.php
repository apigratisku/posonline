<div class="page-header"><h1 class="page-title"><?= $title ?></h1></div>
<div class="card"><div class="card-body">
<table class="table table-bordered">
<tr><th width="200">Kode</th><td><?= htmlspecialchars($data->kode ?? '') ?></td></tr>
<tr><th>Nama</th><td><?= htmlspecialchars($data->nama ?? '') ?></td></tr>
<tr><th>Alamat</th><td><?= htmlspecialchars($data->alamat ?? '') ?></td></tr>
<tr><th>Telepon</th><td><?= htmlspecialchars($data->telepon ?? '') ?></td></tr>
<tr><th>Email</th><td><?= htmlspecialchars($data->email ?? '') ?></td></tr>
</table>
<a href="<?= base_url($back_url) ?>" class="btn btn-secondary">Kembali</a>
</div></div>
