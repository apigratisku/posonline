/**
 * Mobile Navigation — Apotek POS
 */
(function ($) {
    'use strict';

    function openSheet(sheetId, overlayId) {
        $('#' + overlayId).addClass('is-open').attr('aria-hidden', 'false');
        $('#' + sheetId).addClass('is-open');
        $('body').css('overflow', 'hidden');
    }

    function closeSheet(sheetId, overlayId) {
        $('#' + overlayId).removeClass('is-open').attr('aria-hidden', 'true');
        $('#' + sheetId).removeClass('is-open');
        $('body').css('overflow', '');
    }

    function closeAllSheets() {
        closeSheet('mobileMenuSheet', 'mobileMenuOverlay');
        closeSheet('mobileAccountSheet', 'mobileAccountOverlay');
    }

    $(document).ready(function () {
        $('#mobileNavMenu').on('click', function () {
            closeSheet('mobileAccountSheet', 'mobileAccountOverlay');
            openSheet('mobileMenuSheet', 'mobileMenuOverlay');
        });

        $('#mobileNavAccount').on('click', function () {
            closeSheet('mobileMenuSheet', 'mobileMenuOverlay');
            openSheet('mobileAccountSheet', 'mobileAccountOverlay');
        });

        $('#mobileMenuClose, #mobileMenuOverlay').on('click', function () {
            closeSheet('mobileMenuSheet', 'mobileMenuOverlay');
        });

        $('#mobileAccountClose, #mobileAccountOverlay').on('click', function () {
            closeSheet('mobileAccountSheet', 'mobileAccountOverlay');
        });

        $('.mobile-sheet-item').on('click', function () {
            closeAllSheets();
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') {
                closeAllSheets();
            }
        });
    });

    window.updateCartBadge = function (count) {
        var badge = $('#mobileNavCartBadge');
        count = parseInt(count, 10) || 0;
        badge.attr('data-count', count);
        badge.text(count > 0 ? count : '');
    };

    $(document).on('cart:updated', function (e, count) {
        window.updateCartBadge(count);
    });

})(jQuery);
