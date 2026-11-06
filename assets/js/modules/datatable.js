/**
 * DataTable Module
 */

(function($) {
    'use strict';

    function validateDataTableResponse(json) {
        if (typeof json === 'string') {
            try {
                json = JSON.parse(json);
            } catch (e) {
                console.error('Respons bukan JSON valid', json);
                if (typeof showNotification === 'function') {
                    showNotification('error', 'Respons server bukan JSON valid');
                }
                return [];
            }
        }
        if (json && json.status === false) {
            var msg = json.message || 'Gagal memuat data tabel';
            if (typeof showNotification === 'function') {
                showNotification('error', msg);
            } else {
                console.error(msg, json);
            }
            return [];
        }
        if (json && json.error) {
            if (typeof showNotification === 'function') {
                showNotification('error', json.error);
            } else {
                console.error(json.error, json);
            }
            return [];
        }
        if (!json || !Array.isArray(json.data)) {
            var invalidMsg = 'Format respons data tabel tidak valid';
            if (json && json.recordsTotal > 0) {
                invalidMsg += ' (recordsTotal=' + json.recordsTotal + ' tapi data bukan array)';
            }
            if (typeof showNotification === 'function') {
                showNotification('error', invalidMsg);
            }
            console.error(invalidMsg, json);
            return [];
        }
        return json.data;
    }
    
    function adjustDataTableColumns(tableApi) {
        if (tableApi && typeof tableApi.columns === 'object') {
            try {
                tableApi.columns.adjust();
            } catch (e) {
                /* ignore */
            }
        }
    }

    function setupDataTableScrollContainer($table) {
        var $container = $table.closest('.card-body, .table-responsive');
        if (!$container.length) {
            $container = $table.parent();
        }
        $container.addClass('dt-scroll-container');
        $table.closest('.card').addClass('card-has-datatable');
    }

    window.initDataTable = function(tableId, options) {
        var configOptions = $.extend(true, {}, options || {});
        var userInitComplete = configOptions.initComplete;
        var userDrawCallback = configOptions.drawCallback;
        delete configOptions.initComplete;
        delete configOptions.drawCallback;

        var defaults = {
            processing: true,
            serverSide: true,
            scrollX: true,
            autoWidth: false,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            language: {
                processing: "Memproses...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            },
            ajax: {
                url: '',
                type: 'POST',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                dataSrc: validateDataTableResponse,
                data: function(d) {
                    return d;
                }
            },
            columns: [],
            initComplete: function(settings, json) {
                var api = this.api();
                var $table = $('#' + tableId);
                setupDataTableScrollContainer($table);
                adjustDataTableColumns(api);
                if (typeof userInitComplete === 'function') {
                    userInitComplete.call(this, settings, json);
                }
            },
            drawCallback: function(settings) {
                var $t = $(this);
                $t.find('tbody tr').each(function() {
                    $(this).find('td:last').addClass('dataTables_action');
                });
                adjustDataTableColumns(this.api());
                if (typeof userDrawCallback === 'function') {
                    userDrawCallback.call(this, settings);
                }
            }
        };
        
        var config = $.extend(true, {}, defaults, configOptions);

        if (config.ajax && !config.ajax.error) {
            config.ajax.error = function(xhr, textStatus, errorThrown) {
                var message = 'Gagal memuat data tabel';
                if (xhr.status === 403) {
                    message = 'Akses ditolak (403)';
                } else if (xhr.status === 404) {
                    message = 'Endpoint data tidak ditemukan (404)';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    message = xhr.responseJSON.error;
                } else if (errorThrown) {
                    message += ': ' + errorThrown;
                }
                if (typeof showNotification === 'function') {
                    showNotification('error', message);
                } else {
                    console.error(message, xhr);
                }
            };
        }
        
        var table = $('#' + tableId).DataTable(config);

        var resizeTimer;
        $(window).on('resize.datatable-' + tableId, function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                adjustDataTableColumns(table);
            }, 150);
        });

        return table;
    };
    
    window.reloadDataTable = function(tableIdOrObject) {
        if (typeof tableIdOrObject === 'string') {
            $('#' + tableIdOrObject).DataTable().ajax.reload();
        } else if (tableIdOrObject && typeof tableIdOrObject.ajax === 'function') {
            tableIdOrObject.ajax.reload();
        }
    };
    
})(jQuery);
