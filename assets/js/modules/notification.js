/**
 * Notification Module
 */

(function($) {
    'use strict';
    
    window.showNotification = function(type, message, title) {
        var icon = '';
        var color = '';
        
        switch(type) {
            case 'success':
                icon = 'success';
                color = '#28a745';
                break;
            case 'error':
            case 'danger':
                icon = 'error';
                color = '#dc3545';
                break;
            case 'warning':
                icon = 'warning';
                color = '#ffc107';
                break;
            case 'info':
                icon = 'info';
                color = '#17a2b8';
                break;
            default:
                icon = 'info';
                color = '#17a2b8';
        }
        
        Swal.fire({
            icon: icon,
            title: title || '',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    };
    
    window.showConfirm = function(message, callback, title) {
        Swal.fire({
            title: title || 'Konfirmasi',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#667eea',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed && callback) {
                callback();
            }
        });
    };
    
    window.showAlert = function(message, title, type) {
        Swal.fire({
            title: title || 'Pemberitahuan',
            text: message,
            icon: type || 'info',
            confirmButtonColor: '#667eea'
        });
    };
    
})(jQuery);

