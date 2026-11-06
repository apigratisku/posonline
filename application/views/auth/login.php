<?php
$toko_nama = isset($profil_toko) && !empty($profil_toko->nama) ? $profil_toko->nama
    : (isset($profil_apotek) && !empty($profil_apotek->nama) ? $profil_apotek->nama : 'Toko POS');
$toko_data = isset($profil_toko) ? $profil_toko : (isset($profil_apotek) ? $profil_apotek : null);
$toko_logo = ($toko_data && !empty($toko_data->logo) && is_file(FCPATH . 'assets/uploads/logo/' . $toko_data->logo))
    ? base_url('assets/uploads/logo/' . $toko_data->logo) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Login - <?= htmlspecialchars($toko_nama) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="<?= base_url('assets/css/custom.css') ?>" rel="stylesheet">
</head>
<body>
    <div class="login-page">
        <div class="login-hero">
            <div class="login-hero-content">
                <div class="login-brand" style="margin-bottom: 2rem; gap: 0.75rem;">
                <?php if ($toko_logo): ?>
                    <img src="<?= $toko_logo ?>" alt="<?= htmlspecialchars($toko_nama) ?>" class="brand-logo brand-logo-login" style="height: 44px; max-height: 44px;">
                <?php else: ?>
                    <i class="fas fa-store" style="font-size: 2.25rem;"></i>
                <?php endif; ?>
                <span style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.5px;"><?= htmlspecialchars($toko_nama) ?></span>
            </div>
            <div class="tagline">Sistem Kasir Online</div>
            <?php if (!empty($toko_data->alamat ?? '')): ?>
                <p class="mb-1"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($toko_data->alamat) ?></p>
            <?php endif; ?>
            <?php if (!empty($toko_data->telepon ?? '')): ?>
                <p class="mb-2"><i class="fas fa-phone me-1"></i><?= htmlspecialchars($toko_data->telepon) ?></p>
            <?php endif; ?>
            <p>Manajemen penjualan produk dengan teknologi terdepan dan interface yang user-friendly</p>
            </div>
        </div>

        <div class="login-form-section">
            <div class="login-card">
                <h2>Selamat Datang</h2>
                <p class="subtitle">Silakan login untuk melanjutkan</p>
            
                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $this->session->flashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            
                <form id="loginForm" method="POST" action="<?= site_url('auth/login') ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" required autofocus>
                        </div>
                    </div>
                
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>
                
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-rocket me-2"></i>Masuk
                    </button>
                </form>

                <div class="mt-4 p-3 bg-light rounded border text-start">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold text-muted"><i class="fas fa-info-circle me-1"></i>Akun Demo:</span>
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;" onclick="$('#username').val('demo');$('#password').val('demo123');">Gunakan</button>
                    </div>
                    <div class="small text-secondary">
                        Username: <code>demo</code><br>
                        Password: <code>demo123</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');
            
            var loginUrl = form.attr('action');
            
            $.ajax({
                url: loginUrl,
                type: 'POST',
                data: formData,
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response && response.status) {
                        window.location.href = (response.data && response.data.redirect) ? response.data.redirect : '<?= site_url('dashboard') ?>';
                    } else {
                        var message = (response && response.message) ? response.message : 'Username atau password salah';
                        alert(message);
                        submitBtn.prop('disabled', false).html('<i class="fas fa-rocket me-2"></i>Masuk');
                    }
                },
                error: function(xhr, status, error) {
                    var errorMsg = 'Terjadi kesalahan saat login';
                    
                    if (xhr.responseText) {
                        try {
                            var errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse.message) {
                                errorMsg = errorResponse.message;
                            }
                        } catch(e) {
                            if (xhr.status === 404) {
                                errorMsg = 'Halaman login tidak ditemukan (404).';
                            } else if (xhr.status === 500) {
                                errorMsg = 'Terjadi kesalahan server (500).';
                            } else if (xhr.status === 0) {
                                errorMsg = 'Tidak dapat terhubung ke server.';
                            }
                        }
                    }
                    
                    alert(errorMsg);
                    submitBtn.prop('disabled', false).html('<i class="fas fa-rocket me-2"></i>Masuk');
                }
            });
        });
    </script>
</body>
</html>
