/**
*   scroll top
*/

(function () {
    // Back to Top - by CodyHouse.co
    var backTop = document.getElementsByClassName('js-cd-top')[0],
        // browser window scroll (in pixels) after which the "back to top" link is shown
        offset = 300,
        //browser window scroll (in pixels) after which the "back to top" link opacity is reduced
        scrollDuration = 700
    scrolling = false;
    if (backTop) {
        //update back to top visibility on scrolling
        window.addEventListener("scroll", function (event) {
            if (!scrolling) {
                scrolling = true;
                (!window.requestAnimationFrame) ? setTimeout(checkBackToTop, 250) : window.requestAnimationFrame(checkBackToTop);
            }
        });
        //smooth scroll to top
        backTop.addEventListener('click', function (event) {
            event.preventDefault();
            (!window.requestAnimationFrame) ? window.scrollTo(0, 0) : scrollTop(scrollDuration);
        });
    }

    function checkBackToTop() {
        var windowTop = window.scrollY || document.documentElement.scrollTop;
        (windowTop > offset) ? addClass(backTop, 'cd-top--show') : removeClass(backTop, 'cd-top--show');
        scrolling = false;
    }

    function scrollTop(duration) {
        var start = window.scrollY || document.documentElement.scrollTop,
            currentTime = null;

        var animateScroll = function (timestamp) {
            if (!currentTime) currentTime = timestamp;
            var progress = timestamp - currentTime;
            var val = Math.max(Math.easeInOutQuad(progress, start, -start, duration), 0);
            window.scrollTo(0, val);
            if (progress < duration) {
                window.requestAnimationFrame(animateScroll);
            }
        };

        window.requestAnimationFrame(animateScroll);
    }

    Math.easeInOutQuad = function (t, b, c, d) {
        t /= d / 2;
        if (t < 1) return c / 2 * t * t + b;
        t--;
        return -c / 2 * (t * (t - 2) - 1) + b;
    };

    //class manipulations - needed if classList is not supported
    function hasClass(el, className) {
        if (el.classList) return el.classList.contains(className);
        else return !!el.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)'));
    }
    function addClass(el, className) {
        var classList = className.split(' ');
        if (el.classList) el.classList.add(classList[0]);
        else if (!hasClass(el, classList[0])) el.className += " " + classList[0];
        if (classList.length > 1) addClass(el, classList.slice(1).join(' '));
    }
    function removeClass() {
        var classList = Array.from(arguments);
        el = classList[0]
        if (el.classList) el.classList.remove(classList[1]);
        else if (hasClass(el, classList[1])) {
            var reg = new RegExp('(\\s|^)' + classList[1] + '(\\s|$)');
            el.className = el.className.replace(reg, ' ');
        }
        if (classList.length > 2) removeClass(el, classList.slice(2).join(' '));
    }
})();

/*
* Nav Top
*/
window.onscroll = function () { navTop() };

function navTop() {
    var header = document.getElementById("navMenu");
    if (!header)
        return;
    var sticky = header.offsetTop;
    if (window.pageYOffset > sticky) {
        header.classList.add("sticky");
        $('.title').css("visibility", "hidden");
        $('.nav-container').css("display", "none");
    } else {
        header.classList.remove("sticky");
        $('.title').css("visibility", "visible");
        $('.nav-container').css("display", "flex");
    }
}

/*
 * Messages (succès / erreur) et confirmation : composants partagés par toutes les pages.
 * Les succès s'effacent seuls ; les erreurs restent jusqu'à ce qu'on les ferme.
 */
var alert_pos = 60 + 72 * $('.alert-row .alert').length;
setInterval(fadeOutAlert, 1000);

function fadeOutAlert() {
    $('.alert:not(.alert-danger)').delay(2000).fadeOut(2000, function () {
        if (alert_pos > 60) {
            alert_pos -= 72;
        }
        $(this).remove();
    });
}

$('body').on('click', '.closebtn', function () {
    if (alert_pos > 60) {
        alert_pos -= 72;
    }
    $(this).closest('.alert').remove();
});

function showAlert(type, message) {
    var alert = $('<div class="row alert text-center box-shadow" role="alert"></div>')
        .addClass('alert-' + type)
        .css('top', alert_pos + 'px')
        .append('<button type="button" class="closebtn" aria-label="Fermer le message">&times;</button>')
        .append(document.createTextNode(message));
    $('body').prepend(alert);
    alert_pos += 72;
}

function errorAlert(message) {
    showAlert('danger', message || 'L\'action n\'a pas pu être effectuée. Rechargez la page et réessayez.');
}

function successAlert(message) {
    showAlert('success', message);
}

/*
 * Modale de confirmation commune (markup dans layout.html.twig) : jamais de confirm() natif.
 * confirmAction({message, onConfirm, title?, confirmLabel?})
 */
function confirmAction(options) {
    var modal = $('#confirmModal');
    modal.find('.js-confirm-title').text(options.title || 'Suppression');
    modal.find('.js-confirm-message').text(options.message);
    modal.find('.js-confirm-label').text(options.confirmLabel || 'Supprimer');
    modal.find('.js-confirm-ok').off('click').one('click', function () {
        modal.modal('hide');
        options.onConfirm();
    });
    modal.one('shown.bs.modal', function () {
        modal.find('.js-confirm-cancel').trigger('focus');
    });
    modal.modal('show');
}

// Empêche le double envoi d'un formulaire classique (ex. patient créé deux fois sur un double clic).
// Les formulaires gérés en AJAX (preventDefault) ne sont pas concernés ; on ne désactive pas le bouton
// pour que son nom reste envoyé (isClicked côté Symfony).
$(document).on('submit', 'form', function (e) {
    if (e.isDefaultPrevented()) {
        return;
    }
    if ($(this).data('submitting')) {
        e.preventDefault();
        return;
    }
    const form = $(this).data('submitting', true);
    // Réautorisé après quelques secondes : téléchargement (export CSV) ou retour arrière du navigateur
    setTimeout(function () {
        form.removeData('submitting');
    }, 3000);
});

function escapeHtml(s) { return s == null ? '' : String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }
// bootgrid renders unformatted string cells as raw HTML: escape them globally
if (window.jQuery && jQuery.fn.bootgrid) jQuery.fn.bootgrid.Constructor.defaults.converters.string.to = escapeHtml;
// Jeton CSRF envoyé avec chaque appel AJAX jQuery (vérifié par App\EventSubscriber\AjaxCsrfSubscriber)
$.ajaxSetup({ headers: { 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') } });

/*
 * Calendriers : jours à mettre en évidence ou à bloquer, précalculés au lieu d'être
 * recalculés pour chaque case (le calendrier appelle beforeShowDay ~42 fois par mois affiché).
 * ponytail: mémo de 500 ms (le temps d'un affichage) car les listes changent au fil des actions ;
 * passer à une invalidation explicite si ça devient gênant.
 */
var calendarDayCache = [];

function calendarDayKey(date) {
    var d = date.getDate(), m = date.getMonth() + 1;
    return (d < 10 ? '0' : '') + d + '/' + (m < 10 ? '0' : '') + m + '/' + date.getFullYear();
}

function calendarDays(list, kind, build) {
    var now = Date.now();
    for (var i = 0; i < calendarDayCache.length; i++) {
        var c = calendarDayCache[i];
        if (c.list === list && c.kind === kind) {
            if (now - c.time < 500 && c.length === list.length) {
                return c.days;
            }
            calendarDayCache.splice(i, 1);
            break;
        }
    }
    var days = build(list);
    calendarDayCache.push({list: list, kind: kind, length: list.length, time: now, days: days});
    return days;
}

// Jour avec au moins un créneau ayant encore des places ({date: 'jj/mm/aaaa', place, rendezVous}).
function isFreeSlotDay(list, date) {
    if (!list || !list.length) {
        return false;
    }
    return calendarDays(list, 'free', function (l) {
        var days = {};
        for (var i = 0; i < l.length; i++) {
            if (l[i].place - l[i].rendezVous > 0) {
                days[l[i].date] = true;
            }
        }
        return days;
    })[calendarDayKey(date)] === true;
}

// Jour couvert par une semaine existante (7 jours à partir de sa date de début 'jj/mm/aaaa').
function isInExistingWeek(list, date) {
    if (!list || !list.length) {
        return false;
    }
    return calendarDays(list, 'week', function (l) {
        var days = {};
        for (var i = 0; i < l.length; i++) {
            var p = String(l[i].date).split('/');
            for (var j = 0; j < 7; j++) {
                days[calendarDayKey(new Date(+p[2], +p[1] - 1, +p[0] + j))] = true;
            }
        }
        return days;
    })[calendarDayKey(date)] === true;
}

/*
 * Appel AJAX refusé parce que la session a expiré (401) ou que la page date d'avant une mise à jour
 * ou d'une ancienne session (403, jeton CSRF) : sans ce message, les listes restent vides sans explication.
 */
function showReloadBanner(message) {
    if (document.getElementById('reload-banner')) {
        return;
    }
    var banner = $('<div id="reload-banner" class="reload-banner" role="alert"></div>');
    banner.append($('<span></span>').text(message));
    banner.append($('<button type="button" class="btn btn-default btn-sm">Recharger la page</button>').on('click', function () {
        window.location.reload();
    }));
    $('body').append(banner);
}

$(document).ajaxError(function (event, xhr, settings) {
    if (xhr.statusText === 'abort') {
        return;
    }
    if (xhr.status === 401) {
        showReloadBanner('Votre session a expiré. Rechargez la page pour vous reconnecter.');
    } else if (xhr.status === 403) {
        showReloadBanner('Action refusée : la page est restée ouverte trop longtemps ou date d\'avant une mise à jour. Rechargez la page pour continuer.');
    } else if (!settings.error) {
        // Appel sans gestion d'erreur propre : sans ce message, l'échec passerait inaperçu.
        errorAlert('L\'action n\'a pas été enregistrée (erreur serveur). Rechargez la page et réessayez ; si le problème persiste, contactez l\'assistance.');
    }
});
