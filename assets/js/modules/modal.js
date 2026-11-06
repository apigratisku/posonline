/**
 * Modal Module
 */

(function($) {
    'use strict';
    
    window.openModal = function(modalId, options) {
        var defaults = {
            title: '',
            content: '',
            size: 'lg',
            showFooter: true,
            footerButtons: []
        };
        
        var config = $.extend(true, {}, defaults, options);
        
        var modal = $('#' + modalId);
        
        // Set title
        modal.find('.modal-title').text(config.title);
        
        // Set content
        modal.find('.modal-body').html(config.content);
        
        // Set size
        modal.find('.modal-dialog').removeClass('modal-sm modal-lg modal-xl').addClass('modal-' + config.size);
        
        // Set footer
        if (config.showFooter) {
            var footer = modal.find('.modal-footer');
            footer.empty();
            
            if (config.footerButtons.length > 0) {
                $.each(config.footerButtons, function(index, button) {
                    footer.append(button);
                });
            } else {
                footer.append('<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>');
            }
        } else {
            modal.find('.modal-footer').hide();
        }
        
        // Show modal
        var bsModal = new bootstrap.Modal(modal[0]);
        bsModal.show();
    };
    
    window.closeModal = function(modalId) {
        var modal = $('#' + modalId);
        var bsModal = bootstrap.Modal.getInstance(modal[0]);
        if (bsModal) {
            bsModal.hide();
        }
    };
    
    window.loadModalContent = function(modalId, url) {
        showLoading();
        $.get(url)
            .done(function(response) {
                hideLoading();
                $('#' + modalId + ' .modal-body').html(response);
            })
            .fail(function() {
                hideLoading();
                showNotification('error', 'Gagal memuat konten');
            });
    };
    
})(jQuery);

