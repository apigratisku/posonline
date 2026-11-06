<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Barcode — <?= htmlspecialchars($produk->nama) ?></title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: monospace;
            background: #f5f5f5;
        }
        .controls {
            background: #fff;
            padding: 16px 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }
        .controls label { font-size: 13px; color: #555; }
        .controls input[type="number"] {
            width: 60px;
            padding: 5px 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 13px;
        }
        .controls button {
            padding: 7px 16px;
            font-size: 13px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .btn-print { background: #0d6efd; color: #fff; }
        .btn-back { background: #6c757d; color: #fff; }

        .print-area {
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-start;
        }

        .label-card {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 8px 10px;
            text-align: center;
            width: 160px;
            page-break-inside: avoid;
        }
        .label-card .prod-name {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 4px;
            max-height: 28px;
            overflow: hidden;
            line-height: 1.3;
        }
        .label-card .prod-price {
            font-size: 11px;
            font-weight: bold;
            color: #333;
            margin-bottom: 2px;
        }
        .label-card .barcode-code {
            font-size: 9px;
            color: #555;
            margin-top: 2px;
        }
        .label-card svg {
            max-width: 100%;
        }

        @media print {
            .controls { display: none; }
            body { background: #fff; }
            .print-area { padding: 5px; gap: 6px; }
            .label-card {
                border-color: #999;
                padding: 5px 6px;
            }
        }
    </style>
</head>
<body>
    <?php
    // Gunakan KODE BARANG sebagai nilai barcode (tidak perlu field baru di database)
    $kode_barang = $produk->kode;
    $harga = number_format($produk->harga_jual, 0, ',', '.');
    ?>

    <div class="controls">
        <button class="btn-back" onclick="window.history.back()">← Kembali</button>
        <label>Jumlah label:</label>
        <input type="number" id="jumlahLabel" value="1" min="1" max="200">
        <button class="btn-print" onclick="generateAndPrint()">🖨️ Cetak Label</button>
        <span style="font-size:13px; color:#888;">
            Produk: <strong><?= htmlspecialchars($produk->nama) ?></strong>
            | Kode: <strong><?= htmlspecialchars($kode_barang) ?></strong>
        </span>
    </div>

    <div class="print-area" id="printArea">
        <!-- Labels will be generated here -->
    </div>

    <script>
    var produkNama = <?= json_encode($produk->nama) ?>;
    var kodeBarang = <?= json_encode($kode_barang) ?>; // Kode barang = nilai barcode
    var harga = "Rp <?= $harga ?>";

    function createLabel() {
        var card = document.createElement('div');
        card.className = 'label-card';

        var nameEl = document.createElement('div');
        nameEl.className = 'prod-name';
        nameEl.textContent = produkNama;
        card.appendChild(nameEl);

        var priceEl = document.createElement('div');
        priceEl.className = 'prod-price';
        priceEl.textContent = harga;
        card.appendChild(priceEl);

        var svgEl = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        card.appendChild(svgEl);

        var codeEl = document.createElement('div');
        codeEl.className = 'barcode-code';
        codeEl.textContent = kodeBarang;
        card.appendChild(codeEl);

        try {
            JsBarcode(svgEl, kodeBarang, {
                format: 'CODE128',
                width: 1.5,
                height: 40,
                displayValue: false,
                margin: 2
            });
        } catch (e) {
            try {
                JsBarcode(svgEl, kodeBarang, {
                    format: 'CODE39',
                    width: 1.2,
                    height: 40,
                    displayValue: false,
                    margin: 2
                });
            } catch (e2) {
                var errEl = document.createElement('p');
                errEl.style = 'color:red;font-size:10px;';
                errEl.textContent = 'Barcode error: ' + e2.message;
                card.replaceChild(errEl, svgEl);
            }
        }

        return card;
    }

    function generateLabels(count) {
        var area = document.getElementById('printArea');
        area.innerHTML = '';
        for (var i = 0; i < count; i++) {
            area.appendChild(createLabel());
        }
    }

    function generateAndPrint() {
        var count = parseInt(document.getElementById('jumlahLabel').value) || 1;
        if (count < 1) count = 1;
        if (count > 200) count = 200;
        generateLabels(count);
        setTimeout(function() { window.print(); }, 300);
    }

    // Preview awal
    generateLabels(1);
    </script>
</body>
</html>
