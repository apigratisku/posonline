<div class="page-header">
    <h1 class="page-title">Jadwal Kerja</h1>
    <p class="page-subtitle">Kelola jadwal kerja karyawan</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Jadwal Kerja</h5>
    </div>
    <div class="card-body">
        <table id="jadwalTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Nama Karyawan</th>
                    <th>Username</th>
                    <th>Jadwal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = initDataTable('jadwalTable', {
        ajax: {
            url: baseUrl + 'manajemen/jadwal/ajax_list',
            type: 'POST'
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3, orderable: false, searchable: false }
        ]
    });
});
</script>
