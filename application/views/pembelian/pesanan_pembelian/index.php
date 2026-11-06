<div class="page-header">
    <h1 class="page-title">Pesanan Pembelian</h1>
    <p class="page-subtitle">Kelola pembelian barang — terima pesanan untuk memperbarui stok</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">Daftar Pesanan Pembelian</h5>
        <?php if (can_create('pembelian')): ?>
            <button type="button" class="btn btn-primary" onclick="addData()">
                <i class="fas fa-plus"></i> Tambah Pembelian
            </button>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="pesananpembelianTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Supplier</th>
                        <th>Gudang</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form PO -->
<div class="modal fade" id="pesananPembelianModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="poModalTitle">Tambah Pesanan Pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="pesananPembelianForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="po_id">
                    <input type="hidden" name="no_po" id="no_po">
                    <input type="hidden" name="status" id="po_status" value="draft">

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select class="form-select" name="supplier_id" id="supplier_id" required>
                                <option value="">Pilih Supplier</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?= $supplier->id ?>"><?= htmlspecialchars($supplier->nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gudang <span class="text-danger">*</span></label>
                            <select class="form-select" name="gudang_id" id="gudang_id" required>
                                <option value="">Pilih Gudang</option>
                                <?php foreach ($gudangs as $gudang): ?>
                                    <option value="<?= $gudang->id ?>"><?= htmlspecialchars($gudang->nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" name="keterangan" id="keterangan" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Item Barang</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItemRow()">
                            <i class="fas fa-plus"></i> Tambah Baris
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" id="poItemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width:200px">Produk</th>
                                    <th style="width:100px">Qty</th>
                                    <th style="width:130px">Harga Beli</th>
                                    <th style="width:110px">Diskon</th>
                                    <th style="width:130px">Subtotal</th>
                                    <th style="width:50px"></th>
                                </tr>
                            </thead>
                            <tbody id="poItemsBody"></tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end mt-3">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <strong id="poSubtotalDisplay">Rp 0</strong>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label small">Diskon Global</label>
                                    <input type="number" class="form-control form-control-sm" name="diskon" id="diskon" value="0" min="0" step="1">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Pajak</label>
                                    <input type="number" class="form-control form-control-sm" name="pajak" id="pajak" value="0" min="0" step="1">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-2">
                                <span><strong>Total</strong></span>
                                <strong class="text-primary" id="poTotalDisplay">Rp 0</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer flex-wrap gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <?php if (can_create('pembelian') || can_update('pembelian')): ?>
                        <button type="button" class="btn btn-outline-primary" onclick="submitPoForm('draft')">
                            <i class="fas fa-save"></i> Simpan Draft
                        </button>
                        <button type="button" class="btn btn-primary" onclick="submitPoForm('sent')">
                            <i class="fas fa-paper-plane"></i> Simpan & Kirim
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View PO -->
<div class="modal fade" id="pesananPembelianViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pesanan Pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-sm-6"><strong>No. PO:</strong> <span id="viewNoPo">-</span></div>
                    <div class="col-sm-6"><strong>Status:</strong> <span id="viewStatus">-</span></div>
                    <div class="col-sm-6"><strong>Supplier:</strong> <span id="viewSupplier">-</span></div>
                    <div class="col-sm-6"><strong>Gudang:</strong> <span id="viewGudang">-</span></div>
                    <div class="col-sm-6"><strong>Tanggal:</strong> <span id="viewTanggal">-</span></div>
                    <div class="col-sm-12"><strong>Keterangan:</strong> <span id="viewKeterangan">-</span></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Diskon</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="viewItemsBody"></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total</th>
                                <th class="text-end" id="viewTotal">Rp 0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success d-none" id="btnReceiveFromView" onclick="receiveFromView()">
                    <i class="fas fa-check"></i> Terima Barang
                </button>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="poProdukOptions"><?= json_encode(array_map(function ($p) {
    return array('id' => (int) $p->id, 'kode' => $p->kode, 'nama' => $p->nama, 'harga_beli' => (float) ($p->harga_beli ?? 0));
}, $produks)) ?></script>
