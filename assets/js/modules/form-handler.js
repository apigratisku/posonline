/**
 * Form Handler Module
 */

(function($) {
    'use strict';
    
    window.handleFormSubmit = function(formId, options) {
        var defaults = {
            url: '',
            method: 'POST',
            dataType: 'json',
            beforeSubmit: function() {
                showLoading();
            },
            success: function(response) {
                hideLoading();
                if (response.status) {
                    showNotification('success', response.message);
                    
                    // Auto reload table if provided
                    if (config.table) {
                        if (typeof config.table === 'string') {
                            reloadDataTable(config.table);
                        } else if (config.table && typeof config.table.ajax === 'function') {
                            config.table.ajax.reload();
                        }
                    } else if (config.tableId) {
                        reloadDataTable(config.tableId);
                    }
                    
                    // Auto close modal if provided
                    if (config.modal) {
                        $(config.modal).modal('hide');
                    }
                    
                    if (options.onSuccess) {
                        options.onSuccess(response);
                    }
                } else {
                    showNotification('error', response.message);
                    if (response.errors) {
                        displayValidationErrors(response.errors);
                    }
                }
            },
            error: function() {
                hideLoading();
                showNotification('error', 'Terjadi kesalahan saat memproses data');
            }
        };
        
        var config = $.extend(true, {}, defaults, options);
        
        $('#' + formId).on('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            
            if (config.beforeSubmit) {
                config.beforeSubmit();
            }
            
            $.ajax({
                url: config.url,
                type: config.method,
                data: formData,
                processData: false,
                contentType: false,
                dataType: config.dataType,
                success: config.success,
                error: config.error
            });
        });
    };
    
    window.displayValidationErrors = function(errors) {
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        // Display new errors
        $.each(errors, function(field, message) {
            var fieldElement = $('[name="' + field + '"]');
            fieldElement.addClass('is-invalid');
            fieldElement.after('<div class="invalid-feedback">' + message + '</div>');
        });
    };
    
    window.resetForm = function(formId) {
        $('#' + formId)[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    };
    
})(jQuery);

