<?php
/**
 * Generate 4 master data modules: jenis_pelayanan, jenis_antrian, produk_lab, item_pemeriksaan
 */
$base = dirname(__DIR__);

$modules = array(
    array(
        'table' => 'jenis_pelayanan',
        'model' => 'Jenis_pelayanan_model',
        'controller' => 'MasterJenisPelayanan',
        'route' => 'masterpelayanan',
        'title' => 'Jenis Pelayanan',
        'label' => 'Jenis Pelayanan',
        'view_dir' => 'jenis_pelayanan',
        'table_id' => 'jenisPelayananTable',
        'fields' => array('kode', 'nama', 'deskripsi', 'is_active'),
        'extra_form' => '',
        'extra_view' => '',
        'list_cols' => array('id', 'kode', 'nama', 'deskripsi', 'is_active', 'created_at'),
    ),
    array(
        'table' => 'jenis_antrian',
        'model' => 'Jenis_antrian_model',
        'controller' => 'MasterJenisAntrian',
        'route' => 'masterantrian',
        'title' => 'Jenis Antrian',
        'label' => 'Jenis Antrian',
        'view_dir' => 'jenis_antrian',
        'table_id' => 'jenisAntrianTable',
        'fields' => array('kode', 'nama', 'deskripsi', 'is_active'),
        'extra_form' => '',
        'extra_view' => '',
        'list_cols' => array('id', 'kode', 'nama', 'deskripsi', 'is_active', 'created_at'),
    ),
    array(
        'table' => 'produk_lab',
        'model' => 'Produk_lab_model',
        'controller' => 'MasterProdukLab',
        'route' => 'masterproduklab',
        'title' => 'Produk Lab',
        'label' => 'Produk Lab',
        'view_dir' => 'produk_lab',
        'table_id' => 'produkLabTable',
        'fields' => array('kode', 'nama', 'harga', 'deskripsi', 'is_active'),
        'extra_form' => '
            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <?= form_input([\'type\' => \'number\', \'step\' => \'0.01\', \'min\' => \'0\', \'name\' => \'harga\', \'id\' => \'harga\', \'class\' => \'form-control\', \'value\' => set_value(\'harga\', {{HARGA_DEFAULT}})]) ?>
            </div>',
        'extra_view' => '
            <tr>
                <th>Harga</th>
                <td>Rp <?= number_format($data->harga, 0, \',\', \'.\') ?></td>
            </tr>',
        'list_cols' => array('id', 'kode', 'nama', 'harga', 'deskripsi', 'is_active', 'created_at'),
        'list_format' => array(null, null, null, 'currency', null, 'status', 'date'),
    ),
    array(
        'table' => 'item_pemeriksaan',
        'model' => 'Item_pemeriksaan_model',
        'controller' => 'MasterItemPemeriksaan',
        'route' => 'masteritempemeriksaan',
        'title' => 'Item Pemeriksaan',
        'label' => 'Item Pemeriksaan',
        'view_dir' => 'item_pemeriksaan',
        'table_id' => 'itemPemeriksaanTable',
        'fields' => array('kode', 'nama', 'satuan', 'harga', 'deskripsi', 'is_active'),
        'extra_form' => '
            <div class="mb-3">
                <label for="satuan" class="form-label">Satuan</label>
                <?= form_input([\'name\' => \'satuan\', \'id\' => \'satuan\', \'class\' => \'form-control\', \'value\' => set_value(\'satuan\', {{SATUAN_DEFAULT}}), \'list\' => \'satuanList\']) ?>
                <datalist id="satuanList">
                    <?php if (!empty($satuans)): foreach ($satuans as $s): ?>
                        <option value="<?= htmlspecialchars($s->nama) ?>">
                    <?php endforeach; endif; ?>
                </datalist>
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <?= form_input([\'type\' => \'number\', \'step\' => \'0.01\', \'min\' => \'0\', \'name\' => \'harga\', \'id\' => \'harga\', \'class\' => \'form-control\', \'value\' => set_value(\'harga\', {{HARGA_DEFAULT}})]) ?>
            </div>',
        'extra_view' => '
            <tr>
                <th>Satuan</th>
                <td><?= htmlspecialchars($data->satuan ?: \'-\') ?></td>
            </tr>
            <tr>
                <th>Harga</th>
                <td>Rp <?= number_format($data->harga, 0, \',\', \'.\') ?></td>
            </tr>',
        'list_cols' => array('id', 'kode', 'nama', 'satuan', 'harga', 'deskripsi', 'is_active', 'created_at'),
        'list_format' => array(null, null, null, null, 'currency', null, 'status', 'date'),
        'load_satuan' => true,
    ),
);

foreach ($modules as $m) {
    $model_file = $base . '/application/models/' . $m['model'] . '.php';
    $fillable = implode("', '", $m['fields']);
    $model_content = <<<PHP
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class {$m['model']} extends MY_Model {

    protected \$table = '{$m['table']}';
    protected \$primary_key = 'id';
    protected \$fillable = array('{$fillable}');
    protected \$timestamps = true;

    public function __construct() {
        parent::__construct();
    }

    public function kode_exists(\$kode, \$exclude_id = null) {
        \$this->db->where('kode', \$kode);
        if (\$exclude_id) {
            \$this->db->where('id !=', \$exclude_id);
        }
        return \$this->db->get(\$this->table)->num_rows() > 0;
    }

    public function get_all_filtered(\$search = '', \$is_active = null) {
        \$this->db->from(\$this->table);
        if (!empty(\$search)) {
            \$this->db->group_start();
            \$this->db->like('kode', \$search);
            \$this->db->or_like('nama', \$search);
            \$this->db->or_like('deskripsi', \$search);
            \$this->db->group_end();
        }
        if (\$is_active !== null) {
            \$this->db->where('is_active', \$is_active);
        }
        return \$this->db->get()->result();
    }
}

PHP;
    file_put_contents($model_file, $model_content);

    $model_snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', str_replace('_model', '', $m['model'])));
    $model_var = $model_snake . '_model';
    $load_satuan = !empty($m['load_satuan']) ? "\n        \$this->load->model('master_satuan_model');" : '';
    $create_extra = !empty($m['load_satuan']) ? "\n            'satuans' => \$this->master_satuan_model->get_all(array('is_active' => 1))," : '';
    $store_fields = array();
    foreach ($m['fields'] as $f) {
        if ($f === 'is_active') {
            $store_fields[] = "            'is_active' => \$this->input->post('is_active') ? 1 : 0";
        } elseif ($f === 'harga') {
            $store_fields[] = "            'harga' => \$this->input->post('harga') ?: 0";
        } else {
            $store_fields[] = "            '{$f}' => \$this->input->post('{$f}')";
        }
    }
    $store_data = implode(",\n", $store_fields);

    $list_cols_str = "'" . implode("', '", $m['list_cols']) . "'";
    $list_row = array();
    foreach ($m['list_cols'] as $col) {
        if ($col === 'is_active') {
            $list_row[] = "\$row->is_active ? '<span class=\"badge bg-success\">Aktif</span>' : '<span class=\"badge bg-danger\">Tidak Aktif</span>'";
        } elseif ($col === 'created_at') {
            $list_row[] = "date('d/m/Y H:i', strtotime(\$row->created_at))";
        } elseif ($col === 'harga') {
            $list_row[] = "number_format(\$row->harga, 0, ',', '.')";
        } else {
            $list_row[] = "\$row->{$col}";
        }
    }
    $list_row_str = implode(",\n                ", $list_row);
    $col_count = count($m['list_cols']);
    $dt_cols = array();
    for ($i = 0; $i < $col_count; $i++) {
        $dt_cols[] = "{ data: {$i} }";
    }
    $dt_cols[] = "{ data: {$col_count}, orderable: false, searchable: false }";
    $dt_cols_str = implode(",\n            ", $dt_cols);

    $th_rows = '';
    $labels = array('id' => 'ID', 'kode' => 'Kode', 'nama' => 'Nama', 'deskripsi' => 'Deskripsi', 'satuan' => 'Satuan', 'harga' => 'Harga', 'is_active' => 'Status', 'created_at' => 'Tanggal Dibuat');
    foreach ($m['list_cols'] as $col) {
        $th_rows .= "                    <th>{$labels[$col]}</th>\n";
    }
    $th_rows .= "                    <th>Aksi</th>\n";

    $controller_file = $base . '/application/controllers/MasterData/' . $m['controller'] . '.php';
    $controller_content = <<<PHP
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class {$m['controller']} extends MY_Controller {

    public function __construct() {
        parent::__construct();
        \$this->load->model('{$model_snake}_model');{$load_satuan}
        \$this->check_permission('master', 'view');
    }

    public function index() {
        \$data = array(
            'title' => '{$m['title']}',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => '{$m['title']}')
            )
        );
        \$this->render('master_data/{$m['view_dir']}/index', \$data);
    }

    public function ajax_list() {
        \$this->check_permission('master', 'view');
        \$params = array(
            'columns' => array({$list_cols_str}),
            'search_fields' => array('kode', 'nama', 'deskripsi'),
            'where' => array()
        );
        \$result = \$this->{$model_var}->datatable(\$params);
        \$data = array();
        foreach (\$result['data'] as \$row) {
            \$data[] = array(
                {$list_row_str},
                \$this->get_action_buttons(\$row->id)
            );
        }
        \$result['data'] = \$data;
        \$this->output->set_content_type('application/json')->set_output(json_encode(\$result));
    }

    private function get_action_buttons(\$id) {
        \$buttons = '';
        if (\$this->auth->has_permission('master', 'view')) {
            \$buttons .= '<a href="' . base_url('masterdata/{$m['route']}/view/' . \$id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if (\$this->auth->has_permission('master', 'update')) {
            \$buttons .= '<a href="' . base_url('masterdata/{$m['route']}/edit/' . \$id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if (\$this->auth->has_permission('master', 'delete')) {
            \$buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . \$id . ')"><i class="fas fa-trash"></i></button>';
        }
        return \$buttons;
    }

    public function create() {
        \$this->check_permission('master', 'create');
        \$data = array(
            'title' => 'Tambah {$m['title']}',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => '{$m['title']}', 'url' => base_url('masterdata/{$m['route']}')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah {$m['label']}',
            'form_action' => base_url('masterdata/{$m['route']}/store'),
            'back_url' => 'masterdata/{$m['route']}',{$create_extra}
        );
        \$this->render('master_data/{$m['view_dir']}/create', \$data);
    }

    public function store() {
        \$this->check_permission('master', 'create');
        \$this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        \$this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if (\$this->form_validation->run() == false) {
            redirect('masterdata/{$m['route']}/create');
            return;
        }
        if (\$this->{$model_var}->kode_exists(\$this->input->post('kode'))) {
            \$this->set_flash('error', 'Kode sudah digunakan');
            redirect('masterdata/{$m['route']}/create');
            return;
        }
        \$data = array(
{$store_data}
        );
        \$insert_id = \$this->{$model_var}->insert(\$data);
        if (\$insert_id) {
            \$this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('masterdata/{$m['route']}');
        } else {
            \$this->set_flash('error', 'Gagal menambahkan data');
            redirect('masterdata/{$m['route']}/create');
        }
    }

    public function edit(\$id) {
        \$this->check_permission('master', 'update');
        \$data = \$this->{$model_var}->get_by_id(\$id);
        if (!\$data) {
            \$this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/{$m['route']}');
            return;
        }
        \$view_data = array(
            'title' => 'Edit {$m['title']}',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => '{$m['title']}', 'url' => base_url('masterdata/{$m['route']}')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit {$m['label']}',
            'form_action' => base_url('masterdata/{$m['route']}/update/' . \$id),
            'back_url' => 'masterdata/{$m['route']}',
            'data' => \$data,{$create_extra}
        );
        \$this->render('master_data/{$m['view_dir']}/edit', \$view_data);
    }

    public function update(\$id) {
        \$this->check_permission('master', 'update');
        \$data = \$this->{$model_var}->get_by_id(\$id);
        if (!\$data) {
            \$this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/{$m['route']}');
            return;
        }
        \$this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        \$this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if (\$this->form_validation->run() == false) {
            redirect('masterdata/{$m['route']}/edit/' . \$id);
            return;
        }
        if (\$this->{$model_var}->kode_exists(\$this->input->post('kode'), \$id)) {
            \$this->set_flash('error', 'Kode sudah digunakan');
            redirect('masterdata/{$m['route']}/edit/' . \$id);
            return;
        }
        \$update_data = array(
{$store_data}
        );
        if (\$this->{$model_var}->update(\$id, \$update_data)) {
            \$this->set_flash('success', 'Data berhasil diupdate');
            redirect('masterdata/{$m['route']}');
        } else {
            \$this->set_flash('error', 'Gagal mengupdate data');
            redirect('masterdata/{$m['route']}/edit/' . \$id);
        }
    }

    public function view(\$id) {
        \$this->check_permission('master', 'view');
        \$data = \$this->{$model_var}->get_by_id(\$id);
        if (!\$data) {
            \$this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/{$m['route']}');
            return;
        }
        \$view_data = array(
            'title' => 'Detail {$m['title']}',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => '{$m['title']}', 'url' => base_url('masterdata/{$m['route']}')),
                array('label' => 'Detail')
            ),
            'back_url' => 'masterdata/{$m['route']}',
            'data' => \$data
        );
        \$this->render('master_data/{$m['view_dir']}/view', \$view_data);
    }

    public function ajax_get(\$id) {
        \$this->check_permission('master', 'view');
        \$data = \$this->{$model_var}->get_by_id(\$id);
        if (\$data) {
            \$this->ajax_response(true, 'Data berhasil diambil', \$data);
        } else {
            \$this->ajax_response(false, 'Data tidak ditemukan');
        }
    }

    public function ajax_save() {
        \$id = \$this->input->post('id');
        if (\$id) {
            \$this->check_permission('master', 'update');
        } else {
            \$this->check_permission('master', 'create');
        }
        \$this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        \$this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if (\$this->form_validation->run() == false) {
            \$this->ajax_response(false, 'Validasi gagal', null, \$this->form_validation->error_array());
            return;
        }
        if (\$this->{$model_var}->kode_exists(\$this->input->post('kode'), \$id)) {
            \$this->ajax_response(false, 'Kode sudah digunakan');
            return;
        }
        \$data = array(
{$store_data}
        );
        if (\$id) {
            if (\$this->{$model_var}->update(\$id, \$data)) {
                \$this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                \$this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            \$insert_id = \$this->{$model_var}->insert(\$data);
            if (\$insert_id) {
                \$this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => \$insert_id));
            } else {
                \$this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }

    public function ajax_delete(\$id) {
        \$this->check_permission('master', 'delete');
        if (\$this->{$model_var}->delete(\$id)) {
            \$this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            \$this->ajax_response(false, 'Gagal menghapus data');
        }
    }

    public function ajax_search() {
        \$search = \$this->input->get('q');
        \$data = \$this->{$model_var}->get_all_filtered(\$search, 1);
        \$results = array();
        foreach (\$data as \$row) {
            \$results[] = array(
                'id' => \$row->id,
                'text' => \$row->nama . ' (' . \$row->kode . ')'
            );
        }
        \$this->output->set_content_type('application/json')->set_output(json_encode(array('results' => \$results)));
    }
}

PHP;
    file_put_contents($controller_file, $controller_content);

    $view_base = $base . '/application/views/master_data/' . $m['view_dir'];
    if (!is_dir($view_base)) {
        mkdir($view_base, 0755, true);
    }

    $extra_form_create = str_replace('{{HARGA_DEFAULT}}', '0', str_replace('{{SATUAN_DEFAULT}}', "''", $m['extra_form']));
    $extra_form_edit = str_replace('{{HARGA_DEFAULT}}', '$data->harga', str_replace('{{SATUAN_DEFAULT}}', '$data->satuan', $m['extra_form']));

    $index_content = <<<HTML
<div class="page-header">
    <h1 class="page-title">{$m['title']}</h1>
    <p class="page-subtitle">Kelola data {$m['label']}</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar {$m['label']}</h5>
        <?php if (can_create('master')): ?>
            <a href="<?= base_url('masterdata/{$m['route']}/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah {$m['label']}
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table id="{$m['table_id']}" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
{$th_rows}                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
\$(document).ready(function() {
    initDataTable('{$m['table_id']}', {
        ajax: { url: baseUrl + 'masterdata/{$m['route']}/ajax_list', type: 'POST' },
        columns: [
            {$dt_cols_str}
        ]
    });
});

function deleteData(id) {
    showConfirm('Apakah Anda yakin ingin menghapus data ini?', function() {
        showLoading();
        \$.ajax({
            url: baseUrl + 'masterdata/{$m['route']}/ajax_delete/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.status) {
                    showNotification('success', response.message);
                    reloadDataTable('{$m['table_id']}');
                } else {
                    showNotification('error', response.message);
                }
            },
            error: function() {
                hideLoading();
                showNotification('error', 'Gagal menghapus data');
            }
        });
    });
}
</script>

HTML;
    file_put_contents($view_base . '/index.php', $index_content);

    $create_content = <<<HTML
<div class="page-header">
    <h1 class="page-title"><?= \$title ?></h1>
</div>

<div class="card">
    <div class="card-header"><h5 class="mb-0"><?= \$form_title ?></h5></div>
    <div class="card-body">
        <?= form_open(\$form_action) ?>
            <div class="mb-3">
                <label for="kode" class="form-label">Kode <span class="text-danger">*</span></label>
                <?= form_input(['name' => 'kode', 'id' => 'kode', 'class' => 'form-control', 'value' => set_value('kode'), 'required' => true]) ?>
            </div>
            <div class="mb-3">
                <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                <?= form_input(['name' => 'nama', 'id' => 'nama', 'class' => 'form-control', 'value' => set_value('nama'), 'required' => true]) ?>
            </div>
{$extra_form_create}
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <?= form_textarea(['name' => 'deskripsi', 'id' => 'deskripsi', 'class' => 'form-control', 'rows' => 3, 'value' => set_value('deskripsi')]) ?>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <?= form_checkbox(['name' => 'is_active', 'id' => 'is_active', 'class' => 'form-check-input', 'value' => 1, 'checked' => set_checkbox('is_active', 1, true)]) ?>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url(\$back_url) ?>" class="btn btn-secondary">Batal</a>
                <?= form_submit('submit', 'Simpan', ['class' => 'btn btn-primary']) ?>
            </div>
        <?= form_close() ?>
    </div>
</div>

HTML;
    file_put_contents($view_base . '/create.php', $create_content);

    $edit_content = str_replace('set_value(\'kode\')', "set_value('kode', \$data->kode)", $create_content);
    $edit_content = str_replace('set_value(\'nama\')', "set_value('nama', \$data->nama)", $edit_content);
    $edit_content = str_replace('set_value(\'deskripsi\')', "set_value('deskripsi', \$data->deskripsi)", $edit_content);
    $edit_content = str_replace('set_checkbox(\'is_active\', 1, true)', "set_checkbox('is_active', 1, \$data->is_active == 1)", $edit_content);
    $edit_content = str_replace($extra_form_create, $extra_form_edit, $edit_content);
    file_put_contents($view_base . '/edit.php', $edit_content);

    $view_content = <<<HTML
<div class="page-header">
    <h1 class="page-title"><?= \$title ?></h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail {$m['label']}</h5>
        <a href="<?= base_url(\$back_url) ?>" class="btn btn-secondary">Kembali</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th width="200">ID</th><td><?= \$data->id ?></td></tr>
            <tr><th>Kode</th><td><?= htmlspecialchars(\$data->kode) ?></td></tr>
            <tr><th>Nama</th><td><?= htmlspecialchars(\$data->nama) ?></td></tr>
{$m['extra_view']}
            <tr><th>Deskripsi</th><td><?= htmlspecialchars(\$data->deskripsi ?: '-') ?></td></tr>
            <tr><th>Status</th><td>
                <?php if (\$data->is_active): ?>
                    <span class="badge bg-success">Aktif</span>
                <?php else: ?>
                    <span class="badge bg-danger">Tidak Aktif</span>
                <?php endif; ?>
            </td></tr>
            <tr><th>Tanggal Dibuat</th><td><?= date('d/m/Y H:i', strtotime(\$data->created_at)) ?></td></tr>
        </table>
    </div>
</div>

HTML;
    file_put_contents($view_base . '/view.php', $view_content);

    echo "Generated: {$m['controller']}\n";
}

echo "Done.\n";
