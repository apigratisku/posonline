/**
 * Main Application JavaScript
 * Sistem Manajemen Apotek
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Sidebar Toggle (desktop only)
        $('#sidebarToggle').on('click', function() {
            if (window.innerWidth < 992) return;
            $('#sidebar').toggleClass('collapsed');
            localStorage.setItem('sidebarCollapsed', $('#sidebar').hasClass('collapsed'));
        });
        
        // Restore sidebar state (desktop only)
        if (window.innerWidth >= 992 && localStorage.getItem('sidebarCollapsed') === 'true') {
            $('#sidebar').addClass('collapsed');
        }
        
        // User Menu Toggle (desktop)
        $('#userMenu').on('click', function(e) {
            e.stopPropagation();
            if (window.innerWidth < 992) {
                $('#mobileNavAccount').trigger('click');
                return;
            }
            var dd = $('#userDropdown');
            dd.toggle();
        });
        
        $(document).on('click', function() {
            $('#userDropdown').hide();
        });
        
        // Sidebar Menu Toggle (for submenus) — CSS class only, no slideToggle
        $('.sidebar-menu > ul > li > a').on('click', function(e) {
            var $submenu = $(this).next('ul.submenu');
            if ($submenu.length > 0) {
                e.preventDefault();
                $submenu.removeAttr('style');
                $(this).parent().toggleClass('active');
            }
        });
        
        // Global AJAX Error Handler
        $(document).ajaxError(function(event, xhr, settings, thrownError) {
            if (xhr.status === 401) {
                window.location.href = (typeof baseUrl !== 'undefined' ? baseUrl : '') + 'auth/login';
            } else if (xhr.status === 403) {
                if (typeof showNotification !== 'undefined') {
                    showNotification('error', 'Anda tidak memiliki akses untuk melakukan aksi ini');
                } else {
                    alert('Anda tidak memiliki akses untuk melakukan aksi ini');
                }
            } else if (xhr.status === 500) {
                if (typeof showNotification !== 'undefined') {
                    showNotification('error', 'Terjadi kesalahan pada server');
                } else {
                    alert('Terjadi kesalahan pada server');
                }
            }
        });

        // Pastikan loading overlay selalu ditutup setelah setiap AJAX selesai
        $(document).ajaxComplete(function() {
            if (typeof hideLoading === 'function') {
                hideLoading();
            }
        });
    });
    
    // Loading Overlay
    window.showLoading = function() {
        $('#loadingOverlay').addClass('show');
    };
    
    window.hideLoading = function() {
        $('#loadingOverlay').removeClass('show');
    };
    
})(jQuery);

