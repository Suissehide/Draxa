/**
 * Script jQuery
 */

$('.datepicker').datepicker({
    language: 'fr',
    format: 'dd/mm/yyyy',
    todayBtn: true,
    todayHighlight: true,
    autoclose: true
});

/**
 * Bandeau d'assistance
 */

const banner = document.getElementById('banner');
const hideUntil = localStorage.getItem('hideNotificationUntil');
const now = Date.now();

if (!hideUntil || now >= Number(hideUntil)) {
    banner.style.display = 'block';
}

$('#banner').on('click', 'button', function () {
    banner.style.display = 'none';
    const delay = Date.now() + (60 * 60 * 1000);
    localStorage.setItem('hideNotificationUntil', delay.toString());
})

/**
* Smooth scroll
*/

$('.js-scrollTo').on('click', function () {
    const page = $(this).attr('href'); // Page cible
    const speed = 650; // Durée de l'animation (en ms)
    $('html, body').animate({ scrollTop: $(page).offset().top - 60 }, speed); // Go
    return false;
});

/**
 * Onglets
 */

$('.nav-tabs > li a[title]').tooltip();

$('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
    let target = $(e.target);
    if (target.parent().hasClass('disabled')) {
        return false;
    }
});
