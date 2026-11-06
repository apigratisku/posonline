<?php
$is_kasir_role = isset($is_kasir_role) ? $is_kasir_role : ($this->session->userdata('peran_kode') === 'kasir');
$penjualan_sublabel = $is_kasir_role ? 'Penjualan Anda' : 'Seluruh Kasir';
?>
<div class="dashboard-hero">
    <div class="dashboard-hero-content">
        <h2><i class="fas fa-pills me-2"></i>Point Of Sales</h2>
        <div class="tagline">Dashboard</div>
        <p>Ringkasan aktivitas penjualan dan persediaan apotek bulan ini</p>
    </div>
</div>

<div class="row mb-4 dashboard-stats">
    <div class="col-md-3 col-6 mb-3">
        <div class="card bg-gradient-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Penjualan</h5>
                <h2 id="totalPenjualan">Rp 0</h2>
                <small id="penjualanScopeLabel"><?= htmlspecialchars($penjualan_sublabel) ?> &middot; Bulan ini</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card bg-gradient-danger text-white">
            <div class="card-body">
                <h5 class="card-title">Jumlah Transaksi</h5>
                <h2 id="jumlahTransaksi">0</h2>
                <small id="transaksiScopeLabel"><?= htmlspecialchars($penjualan_sublabel) ?> &middot; Bulan ini</small>
            </div>
        </div>
    </div>
    <?php if (!$is_kasir_role): ?>
    <div class="col-md-3 col-6 mb-3">
        <div class="card bg-gradient-success text-white">
            <div class="card-body">
                <h5 class="card-title">Total Pembelian</h5>
                <h2 id="totalPembelian">Rp 0</h2>
                <small>Bulan ini</small>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="col-md-3 col-6 mb-3">
        <div class="card bg-gradient-info text-white">
            <div class="card-body">
                <h5 class="card-title">Total Produk</h5>
                <h2 id="totalProduk">0</h2>
                <small id="lowStockInfo">0 low stock</small>
            </div>
        </div>
    </div>
    <?php if (!$is_kasir_role): ?>
    <div class="col-md-3 col-6 mb-3">
        <div class="card bg-gradient-warning text-white">
            <div class="card-body">
                <h5 class="card-title">Total Kas</h5>
                <h2 id="totalKas">Rp 0</h2>
                <small>Saldo aktif</small>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="row dashboard-charts">
    <div class="col-md-<?= $is_kasir_role ? '12' : '6' ?> mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Grafik Penjualan</h5>
            </div>
            <div class="card-body">
                <canvas id="penjualanChart"></canvas>
            </div>
        </div>
    </div>
    <?php if (!$is_kasir_role): ?>
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Grafik Pembelian</h5>
            </div>
            <div class="card-body">
                <canvas id="pembelianChart"></canvas>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    if (typeof baseUrl === 'undefined') {
        window.baseUrl = '<?= base_url() ?>';
    }

    var isKasirRole = <?= $is_kasir_role ? 'true' : 'false' ?>;
    var penjualanChart = null;
    var pembelianChart = null;
    var refreshInterval = null;

    loadStatistik();
    refreshInterval = setInterval(loadStatistik, 30000);

    $(window).on('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });

    function loadStatistik() {
        $.ajax({
            url: baseUrl + 'dashboard/ajax_statistik',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response && response.status && response.data) {
                    var d = response.data;
                    $('#totalPenjualan').text(formatRupiah(d.penjualan || 0));
                    $('#jumlahTransaksi').text(d.jumlah_transaksi || 0);
                    $('#totalProduk').text(d.produk_total || 0);
                    $('#lowStockInfo').text((d.produk_low_stock || 0) + ' low stock');

                    if (!isKasirRole) {
                        $('#totalPembelian').text(formatRupiah(d.pembelian || 0));
                        $('#totalKas').text(formatRupiah(d.kas_total || 0));
                        renderChart('pembelianChart', pembelianChart, d.chart_pembelian, '#10b981', function(chart) {
                            pembelianChart = chart;
                        });
                    }

                    if (d.scope === 'kasir') {
                        $('#penjualanScopeLabel').text('Penjualan Anda · Bulan ini');
                        $('#transaksiScopeLabel').text('Penjualan Anda · Bulan ini');
                    } else {
                        $('#penjualanScopeLabel').text('Seluruh Kasir · Bulan ini');
                        $('#transaksiScopeLabel').text('Seluruh Kasir · Bulan ini');
                    }

                    renderChart('penjualanChart', penjualanChart, d.chart_penjualan, '#3b82f6', function(chart) {
                        penjualanChart = chart;
                    });
                } else if (response && !response.status) {
                    showStatistikError(response.message || 'Gagal memuat statistik dashboard.');
                }
            },
            error: function(xhr) {
                var message = 'Gagal memuat statistik dashboard.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.status === 403) {
                    message = 'Anda tidak memiliki akses untuk melihat statistik dashboard.';
                } else if (xhr.status === 0) {
                    message = 'Koneksi terputus. Periksa jaringan Anda.';
                }
                showStatistikError(message);
            }
        });
    }

    function renderChart(canvasId, existingChart, chartData, color, setter) {
        if (!chartData || typeof Chart === 'undefined') {
            return;
        }

        var ctx = document.getElementById(canvasId);
        if (!ctx) {
            return;
        }

        if (existingChart) {
            existingChart.destroy();
        }

        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    label: 'Total (Rp)',
                    data: chartData.values || [],
                    borderColor: color,
                    backgroundColor: color + '33',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + parseFloat(context.parsed.y || 0).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: { display: true, text: 'Tanggal' }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        setter(chart);
    }

    function showStatistikError(message) {
        if (typeof showNotification === 'function') {
            showNotification('error', message);
        } else {
            console.error(message);
        }
    }

    function formatRupiah(amount) {
        return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
    }
});
</script>
