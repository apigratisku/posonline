<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= isset($title) ? $title . ' - ' : '' ?><?= htmlspecialchars(isset($profil_toko) && !empty($profil_toko->nama) ? $profil_toko->nama : (isset($profil_apotek) && !empty($profil_apotek->nama) ? $profil_apotek->nama : 'Toko POS')) ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/custom.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/mobile-nav.css') ?>" rel="stylesheet">
    
    <?php if (isset($page_css)): ?>
        <?php foreach ($page_css as $css): ?>
            <link href="<?= base_url('assets/css/' . $css) ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="<?= isset($body_class) ? $body_class : 'has-mobile-nav pos-app' ?>">
    <?php $this->load->view('layouts/sidebar'); ?>
    
    <div class="main-content">
        <?php $this->load->view('layouts/header'); ?>
        
        <div class="content-wrapper">
            <?php if (isset($content)): ?>
                <?php echo display_flash_message(); ?>
                <?= $content ?>
            <?php endif; ?>
        </div>
        
        <?php $this->load->view('layouts/footer'); ?>
    </div>

    <?php $this->load->view('layouts/mobile_bottom_nav'); ?>
    <?php $this->load->view('layouts/mobile_menu_sheet'); ?>
</body>
</html>
