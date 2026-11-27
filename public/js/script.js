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
* Smooth scroll
*/

$('.js-scrollTo').on('click', function () {
    const page = $(this).attr('href'); // Page cible
    const speed = 650; // Durée de l'animation (en ms)
    $('html, body').animate({ scrollTop: $(page).offset().top - 80 }, speed); // Go
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
