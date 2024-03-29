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
* Close alerts
*/

var alert_pos = 60;
setInterval(fadeOutAlert, 1000);

function fadeOutAlert() {
    $('.alert').delay(2000).fadeOut(2000, function () {
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
    const div = $(this).parents('div');
    div.css('opacity', '0');
    setTimeout(function () {
        div.css('display', 'none');
    }, 600);
    div.remove();
})