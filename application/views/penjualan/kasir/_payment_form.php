<form id="paymentForm">
    <div class="mb-3 d-none">
        <label class="form-label">Sales</label>
        <select class="form-select" id="sales_id" name="sales_id">
            <option value="">Pilih Sales</option>
            <?php foreach ($sales as $s): ?>
                <option value="<?= $s->id ?>"><?= $s->nama ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3 d-none">
        <label class="form-label">Shift</label>
        <select class="form-select" id="shift_id" name="shift_id">
            <option value="">Pilih Shift</option>
            <?php foreach ($shifts as $shift): ?>
                <option value="<?= $shift->id ?>"><?= $shift->nama ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
        <select class="form-select" id="metode_pembayaran_id" name="metode_pembayaran_id" required>
            <option value="">Pilih Metode</option>
            <?php foreach ($metode_pembayarans as $mp): ?>
                <option value="<?= $mp->id ?>"><?= $mp->nama ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <hr>

    <div class="pos-total-row">
        <span>Subtotal</span>
        <strong id="subtotalDisplay">Rp 0</strong>
    </div>

    <div class="mb-3 d-none">
        <label class="form-label">Diskon Transaksi</label>
        <input type="number" class="form-control" id="diskon" name="diskon" value="0" min="0" step="100" onchange="calculateTotal()">
    </div>

    <div class="mb-3 d-none">
        <label class="form-label">Pajak</label>
        <input type="number" class="form-control" id="pajak" name="pajak" value="0" min="0" step="100" onchange="calculateTotal()">
    </div>

    <div class="pos-total-row grand">
        <span>Total</span>
        <strong id="totalDisplay">Rp 0</strong>
    </div>

    <div class="mb-2">
        <label class="form-label">Bayar <span class="text-danger">*</span></label>
        <input type="number" class="form-control pos-pay-input-lg" id="bayar" name="bayar" value="" min="0" step="100" inputmode="numeric" pattern="[0-9]*" required>
    </div>

    <div class="pos-quick-pay">
        <button type="button" onclick="setQuickPay(50000)">50rb</button>
        <button type="button" onclick="setQuickPay(100000)">100rb</button>
        <button type="button" onclick="setExactPay()">Pas</button>
    </div>

    <div class="pos-total-row kembalian" id="kembalianRow">
        <span>Kembalian</span>
        <span class="value" id="kembalianDisplay">Rp 0</span>
    </div>

    <div class="mb-3 mt-2">
        <label class="form-label">Keterangan</label>
        <textarea class="form-control" id="keterangan" name="keterangan" rows="2"></textarea>
    </div>

    <button type="button" class="pos-btn-pay" id="btnProcessPayment">
        <i class="fas fa-check-circle me-1"></i> Proses Pembayaran
    </button>

    <button type="button" class="pos-btn-reset" id="btnResetCart">
        <i class="fas fa-redo me-1"></i> Reset Keranjang
    </button>
</form>
