<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('tutorial_steps')) {

    /**
     * Daftar langkah panduan.
     * Setiap langkah: judul, penjelasan, poin-poin aksi, screenshot (jika ada).
     */
    function tutorial_steps() {
        $img = base_url('assets/uploads/tutorial/');
        $base = base_url();

        $steps = array();

        $steps[] = array(
            'no' => 1,
            'judul' => 'Membuka Aplikasi & Login',
            'ringkas' => 'Langkah pertama untuk masuk ke aplikasi.',
            'langkah' => array(
                'Buka browser (Chrome / Edge / Firefox) di komputer.',
                'Ketik alamat aplikasi yang diberikan admin, contoh: ' . $base . 'auth/login',
                'Isi kolom <strong>Nama Pengguna</strong> dengan akun Anda. Untuk akun percobaan: <code>demo</code>.',
                'Isi kolom <strong>Kata Sandi</strong>. Untuk akun percobaan: <code>demo123</code>.',
                'Klik tombol <strong>Masuk</strong>.',
                'Jika berhasil, Anda akan langsung dibawa ke halaman utama (Dashboard).'
            ),
            'tips' => 'Jika muncul pesan "Nama pengguna atau kata sandi salah", periksa kembali huruf besar/kecil. Hubungi admin jika tetap gagal.',
            'screenshot' => $img . '01-login.png',
            'url' => $base . 'auth/login'
        );

        $steps[] = array(
            'no' => 2,
            'judul' => 'Mengenal Halaman Utama (Dashboard)',
            'ringkas' => 'Ringkasan kondisi toko dalam satu layar.',
            'langkah' => array(
                'Setelah masuk, halaman yang pertama tampil adalah <strong>Dashboard</strong>.',
                'Di bagian atas terdapat kartu ringkasan: Total Penjualan, Jumlah Transaksi, Total Pembelian, Total Produk, dan Total Kas.',
                'Di bagian bawah ada grafik penjualan per hari agar mudah melihat naik-turunnya omset.',
                'Menu di sebelah kiri digunakan untuk berpindah ke bagian lain aplikasi.'
            ),
            'tips' => 'Gunakan Dashboard setiap pagi untuk cek cepat: penjualan kemarin, sisa kas, dan produk yang stoknya menipis.',
            'screenshot' => $img . '02-dashboard.png',
            'url' => $base . 'dashboard'
        );

        $steps[] = array(
            'no' => 3,
            'judul' => 'Melayani Pembeli (Menu Kasir)',
            'ringkas' => 'Mencatat penjualan dan mencetak struk.',
            'langkah' => array(
                'Klik menu <strong>Penjualan</strong>, lalu pilih <strong>Kasir</strong>.',
                'Ketik atau pindai (scan) kode barang pada kolom pencarian produk.',
                'Klik produk yang terjual untuk memasukkannya ke daftar belanja.',
                'Atur jumlah barang jika pembeli membeli lebih dari satu.',
                'Klik <strong>Bayar</strong>, pilih metode pembayaran (Tunai / Kartu / QRIS / Transfer), lalu masukkan jumlah uang.',
                'Klik <strong>Selesai</strong> untuk menyimpan transaksi dan mencetak struk.'
            ),
            'tips' => 'Simpan struk untuk pelanggan. Jika perlu membatalkan transaksi, gunakan tombol batal sebelum transaksi disimpan.',
            'screenshot' => $img . '03-kasir.png',
            'url' => $base . 'penjualan/kasir'
        );

        $steps[] = array(
            'no' => 4,
            'judul' => 'Melihat Riwayat Penjualan',
            'ringkas' => 'Mencari transaksi yang sudah terjadi.',
            'langkah' => array(
                'Klik menu <strong>Penjualan</strong>, lalu <strong>Daftar Penjualan</strong>.',
                'Gunakan kotak pencarian untuk mencari nomor struk atau nama pelanggan.',
                'Pilih rentang tanggal di bagian atas untuk memfilter periode tertentu.',
                'Klik salah satu baris untuk melihat detail transaksi.'
            ),
            'tips' => 'Menu ini berguna saat pelanggan datang mengajukan komplain atau menukar barang.',
            'screenshot' => $img . '04-daftar-penjualan.png',
            'url' => $base . 'penjualan/daftar'
        );

        $steps[] = array(
            'no' => 5,
            'judul' => 'Mengelola Data Produk',
            'ringkas' => 'Menambah dan mengubah barang yang dijual.',
            'langkah' => array(
                'Klik menu <strong>Master Data</strong>, lalu <strong>Master Produk</strong>.',
                'Klik tombol <strong>Tambah Produk</strong> untuk membuat barang baru.',
                'Lengkapi nama, kategori, satuan, harga beli, harga jual, dan stok.',
                'Klik <strong>Simpan</strong>.',
                'Untuk mengubah data, klik ikon pensil pada baris produk. Untuk menghapus, klik ikon tempat sampah.'
            ),
            'tips' => 'Isi <strong>stok minimal</strong> agar aplikasi bisa mengingatkan saat barang hampir habis.',
            'screenshot' => $img . '05-master-produk.png',
            'url' => $base . 'masterdata/masterproduk'
        );

        $steps[] = array(
            'no' => 6,
            'judul' => 'Mengatur Kategori, Satuan & Rak',
            'ringkas' => 'Merapikan pengelompokan barang.',
            'langkah' => array(
                'Buka <strong>Master Data</strong>, lalu pilih <strong>Master Kategori</strong> untuk mengelompokkan barang (contoh: Obat, Vitamin, Alat Kesehatan).',
                'Pilih <strong>Master Satuan</strong> untuk mengatur satuan jual (box, strip, botol, pcs).',
                'Pilih <strong>Master Rak</strong> untuk menandai lokasi penyimpanan barang di toko.'
            ),
            'tips' => 'Pengelompokan yang rapi membuat pencarian produk di kasir jauh lebih cepat.',
            'screenshot' => $img . '06-master-kategori.png',
            'url' => $base . 'masterdata/masterkategori'
        );

        $steps[] = array(
            'no' => 7,
            'judul' => 'Memantau Stok Barang',
            'ringkas' => 'Mengecek jumlah stok yang tersedia.',
            'langkah' => array(
                'Klik menu <strong>Persediaan</strong>, lalu <strong>Daftar Produk</strong>.',
                'Lihat kolom stok untuk mengetahui jumlah barang yang masih ada.',
                'Gunakan kolom pencarian untuk menemukan produk tertentu.'
            ),
            'tips' => 'Lakukan pengecekan ini minimal seminggu sekali agar data stok selalu akurat.',
            'screenshot' => $img . '07-persediaan-produk.png',
            'url' => $base . 'persediaan/produk'
        );

        $steps[] = array(
            'no' => 8,
            'judul' => 'Mengecek Barang Mendekati Kadaluarsa',
            'ringkas' => 'Menghindari menjual barang kedaluwarsa.',
            'langkah' => array(
                'Klik menu <strong>Persediaan</strong>, lalu <strong>Stok Kadaluarsa</strong>.',
                'Aplikasi menampilkan daftar barang yang tanggal kedaluwarsanya sudah dekat.',
                'Segera lakukan penjualan khusus, retur ke supplier, atau pemusnahan sesuai aturan toko.'
            ),
            'tips' => 'Periksa menu ini setiap awal bulan. Barang kadaluarsa tidak boleh dijual kembali.',
            'screenshot' => $img . '08-stok-kadaluarsa.png',
            'url' => $base . 'persediaan/kadaluarsa'
        );

        $steps[] = array(
            'no' => 9,
            'judul' => 'Melakukan Stok Opname',
            'ringkas' => 'Menyesuaikan stok sistem dengan stok fisik.',
            'langkah' => array(
                'Klik menu <strong>Persediaan</strong>, lalu <strong>Stok Opname</strong>.',
                'Buat opname baru dan masukkan jumlah barang hasil hitungan fisik.',
                'Simpan. Aplikasi akan menghitung selisih antara stok sistem dan stok nyata.',
                'Setujui penyesuaian agar stok di sistem diperbarui.'
            ),
            'tips' => 'Lakukan stok opname saat toko sepi, misalnya malam hari atau di luar jam sibuk.',
            'screenshot' => $img . '09-stok-opname.png',
            'url' => $base . 'persediaan/opname'
        );

        $steps[] = array(
            'no' => 10,
            'judul' => 'Membuat Pesanan Pembelian',
            'ringkas' => 'Mengatur barang yang akan dibeli dari supplier.',
            'langkah' => array(
                'Klik menu <strong>Pembelian</strong>, lalu <strong>Pesanan Pembelian</strong>.',
                'Klik <strong>Tambah Pesanan</strong>.',
                'Pilih supplier, lalu tambahkan produk beserta jumlah yang dipesan.',
                'Simpan sebagai draf, lalu kirim ke supplier bila sudah sesuai.'
            ),
            'tips' => 'Pesanan pembelian membantu menghindari pembelian berlebih dan mengunci harga dari supplier.',
            'screenshot' => $img . '10-pesanan-pembelian.png',
            'url' => $base . 'pembelian/pesanan'
        );

        $steps[] = array(
            'no' => 11,
            'judul' => 'Mencatat Faktur Pembelian (Barang Masuk)',
            'ringkas' => 'Memasukkan barang ke dalam stok.',
            'langkah' => array(
                'Klik menu <strong>Pembelian</strong>, lalu <strong>Faktur Pembelian</strong>.',
                'Klik <strong>Tambah Faktur</strong> dan pilih supplier.',
                'Masukkan produk yang diterima beserta jumlah dan harga.',
                'Simpan. Stok barang akan otomatis bertambah.'
            ),
            'tips' => 'Cocokkan isi faktur dengan barang fisik sebelum menyimpan agar stok tidak salah.',
            'screenshot' => $img . '11-faktur-pembelian.png',
            'url' => $base . 'pembelian/faktur'
        );

        $steps[] = array(
            'no' => 12,
            'judul' => 'Mengelola Data Pelanggan',
            'ringkas' => 'Menyimpan data pembeli setia.',
            'langkah' => array(
                'Klik menu <strong>Kontak</strong>, lalu <strong>Pelanggan</strong>.',
                'Klik <strong>Tambah Pelanggan</strong>.',
                'Isi nama, nomor telepon, dan alamat.',
                'Simpan. Data pelanggan bisa dipakai saat transaksi di kasir.'
            ),
            'tips' => 'Data pelanggan berguna untuk program poin, pengumuman promo, atau pengiriman.',
            'screenshot' => $img . '12-pelanggan.png',
            'url' => $base . 'kontak/pelanggan'
        );

        $steps[] = array(
            'no' => 13,
            'judul' => 'Mengelola Data Supplier',
            'ringkas' => 'Menyimpan daftar pemasok barang.',
            'langkah' => array(
                'Klik menu <strong>Kontak</strong>, lalu <strong>Supplier</strong>.',
                'Klik <strong>Tambah Supplier</strong>.',
                'Lengkapi nama supplier, telepon, alamat, dan catatan bila perlu.',
                'Simpan.'
            ),
            'tips' => 'Supplier yang terdaftar rapi memudahkan saat membuat pesanan pembelian.',
            'screenshot' => $img . '13-supplier.png',
            'url' => $base . 'kontak/supplier'
        );

        $steps[] = array(
            'no' => 14,
            'judul' => 'Mengatur Akun Kas & Buku Kas',
            'ringkas' => 'Mencatat uang masuk dan keluar.',
            'langkah' => array(
                'Klik menu <strong>Keuangan</strong>, lalu <strong>Daftar Akun Kas</strong> untuk menambah kasir/laci uang.',
                'Pilih <strong>Buku Kas</strong> untuk mencatat pemasukan dan pengeluaran harian.',
                'Klik tambah transaksi, pilih jenis (masuk/keluar), isi nominal dan keterangan.',
                'Simpan. Saldo kas akan terhitung otomatis.'
            ),
            'tips' => 'Tutup laci kas setiap akhir shift dan cocokkan dengan saldo yang tampil di Buku Kas.',
            'screenshot' => $img . '14-akun-kas.png',
            'url' => $base . 'keuangan/akun-kas'
        );

        $steps[] = array(
            'no' => 15,
            'judul' => 'Melihat Laporan',
            'ringkas' => 'Melihat rekap penjualan, pembelian, stok, dan keuangan.',
            'langkah' => array(
                'Klik menu <strong>Laporan</strong>.',
                'Pilih <strong>Laporan Penjualan</strong> untuk rekap omzet per periode.',
                'Pilih <strong>Laporan Pembelian</strong> untuk rekap barang masuk.',
                'Pilih <strong>Laporan Persediaan</strong> untuk rekap stok dan nilai persediaan.',
                'Pilih <strong>Laporan Keuangan</strong> untuk rekap arus kas.',
                'Tentukan rentang tanggal, lalu klik <strong>Tampilkan</strong> atau <strong>Export</strong>.'
            ),
            'tips' => 'Unduh laporan setiap akhir bulan untuk keperluan pembukuan dan pelaporan.',
            'screenshot' => $img . '16-laporan-penjualan.png',
            'url' => $base . 'laporan/penjualan'
        );

        $steps[] = array(
            'no' => 16,
            'judul' => 'Mengelola Pengguna & Hak Akses',
            'ringkas' => 'Menambah karyawan dan membatasi menu yang boleh diakses.',
            'langkah' => array(
                'Klik menu <strong>Manajemen Pengguna</strong>, lalu <strong>Daftar Pengguna</strong>.',
                'Klik <strong>Tambah Pengguna</strong>, isi nama, username, sandi, dan pilih peran (Kasir / Admin / Owner).',
                'Simpan. Karyawan kini bisa login dengan akunnya sendiri.',
                'Buka <strong>Peran &amp; Hak Akses</strong> untuk mengatur menu apa saja yang boleh dilihat tiap peran.'
            ),
            'tips' => 'Batasi akses kasir hanya ke menu Kasir dan Daftar Penjualan agar data keuangan lebih aman.',
            'screenshot' => $img . '20-pengguna.png',
            'url' => $base . 'manajemen/pengguna'
        );

        $steps[] = array(
            'no' => 17,
            'judul' => 'Melihat Log Aktivitas',
            'ringkas' => 'Memantau siapa melakukan apa di aplikasi.',
            'langkah' => array(
                'Klik menu <strong>Manajemen Pengguna</strong>, lalu <strong>Log Aktivitas</strong>.',
                'Lihat daftar aktivitas: login, tambah data, ubah data, hapus data.',
                'Gunakan filter tanggal dan pengguna untuk mencari kejadian tertentu.'
            ),
            'tips' => 'Log aktivitas membantu melacak kesalahan input atau tindakan yang tidak semestinya.',
            'screenshot' => $img . '22-log-aktivitas.png',
            'url' => $base . 'manajemen/log'
        );

        $steps[] = array(
            'no' => 18,
            'judul' => 'Mengatur Profil Toko',
            'ringkas' => 'Mengubah nama toko, alamat, dan logo.',
            'langkah' => array(
                'Klik menu <strong>Pengaturan</strong>, lalu <strong>Profil Toko</strong>.',
                'Ubah nama toko, alamat, nomor telepon, dan unggah logo.',
                'Klik <strong>Simpan</strong>. Nama dan logo akan langsung tampil di aplikasi dan struk.'
            ),
            'tips' => 'Logo berukuran persegi (1:1) akan tampil paling rapi di sidebar dan struk.',
            'screenshot' => $img . '23-profil-toko.png',
            'url' => $base . 'pengaturan/profil'
        );

        $steps[] = array(
            'no' => 19,
            'judul' => 'Mengatur Metode Pembayaran',
            'ringkas' => 'Menentukan cara pembeli boleh membayar.',
            'langkah' => array(
                'Klik menu <strong>Pengaturan</strong>, lalu <strong>Metode Pembayaran</strong>.',
                'Aktifkan metode yang dipakai toko: Tunai, Kartu Debit/Kredit, QRIS, Transfer, atau E-Wallet.',
                'Tambahkan nomor rekening atau informasi pembayaran bila diperlukan.',
                'Simpan.'
            ),
            'tips' => 'Metode yang aktif akan muncul sebagai pilihan saat kasir melakukan pembayaran.',
            'screenshot' => $img . '24-metode-pembayaran.png',
            'url' => $base . 'pengaturan/metode-pembayaran'
        );

        $steps[] = array(
            'no' => 20,
            'judul' => 'Keluar dari Aplikasi (Logout)',
            'ringkas' => 'Mengakhiri sesi dengan aman.',
            'langkah' => array(
                'Klik nama pengguna di pojok kanan atas.',
                'Pilih <strong>Logout</strong>.',
                'Anda akan kembali ke halaman login.'
            ),
            'tips' => 'Selalu logout saat meninggalkan komputer, terutama jika kasir dipakai bersama.',
            'screenshot' => $img . '01-login.png',
            'url' => $base . 'auth/login'
        );

        return $steps;
    }
}
