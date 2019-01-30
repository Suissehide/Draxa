/**
 * Script jQuery
 */

jQuery(document).ready(function ($) {

    console.log("ready!");

    $('.datepicker').datepicker({
        language: 'fr',
        format: 'dd/mm/yyyy',
        todayBtn: true,
        todayHighlight: true,
        autoclose: true,
    });

	/**
	* Smooth scroll
	*/

    $('.js-scrollTo').on('click', function () {
        var page = $(this).attr('href'); // Page cible
        var speed = 650; // Durée de l'animation (en ms)
        $('html, body').animate({ scrollTop: $(page).offset().top - 60 }, speed); // Go
        return false;
    });

	/**
	 * multi page form
	 */

    $('.nav-tabs > li a[title]').tooltip();

    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        var $target = $(e.target);
        if ($target.parent().hasClass('disabled')) {
            return false;
        }
    });
    $(".next-step").click(function (e) {
        var $active = $('.wizard .nav-tabs li.active');
        $active.next().removeClass('disabled');
        nextTab($active);
    });
    $(".prev-step").click(function (e) {
        var $active = $('.wizard .nav-tabs li.active');
        prevTab($active);
    });

    function nextTab(elem) {
        $(elem).next().find('a[data-toggle="tab"]').click();
    }
    function prevTab(elem) {
        $(elem).prev().find('a[data-toggle="tab"]').click();
    }

	/**
	  * Permet de cliquer sur une ligne du tableau
	  * Redirige vers la Vue correspondante
	  */

    $('tbody').on("click", "tr", function () {
        var npatient = $(this).attr('data-row-id');
	if (npatient == undefined)
		return;
        var pathArray = window.location.pathname.split("/");
        var url = window.location.protocol + "//" + window.location.host;
        for (var i = 0; i < pathArray.length - 1; i++) {
            url += pathArray[i] + "/";
        }
        window.location.assign(url + "vue/" + npatient);
    });
});
