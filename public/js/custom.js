// to get current year
function getYear() {
    var currentDate = new Date();
    var currentYear = currentDate.getFullYear();
    var displayYear = document.querySelector("#displayYear");
    if (displayYear) {
        displayYear.innerHTML = currentYear;
    }
}

getYear();


// client section owl carousel
$(".client_owl-carousel").owlCarousel({
    loop: true,
    margin: 0,
    dots: false,
    nav: true,
    navText: [],
    autoplay: true,
    autoplayHoverPause: true,
    navText: [
        '<i class="fa fa-angle-left" aria-hidden="true"></i>',
        '<i class="fa fa-angle-right" aria-hidden="true"></i>'
    ],
    responsive: {
        0: {
            items: 1
        },
        600: {
            items: 1
        },
        1000: {
            items: 2
        }
    }
});



/** google_map js **/
function myMap() {
    var mapProp = {
        center: new google.maps.LatLng(40.712775, -74.005973),
        zoom: 18,
    };
    var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
}

document.addEventListener('DOMContentLoaded', function () {
    // Video modal — /video page
    var videoModal = document.getElementById('videoModal');
    if (videoModal) {
        $(videoModal).on('show.bs.modal', function (e) {
            var btn = $(e.relatedTarget);
            $('#videoModalTitle').text(btn.data('video-title'));
            $('#videoIframe').attr('src', btn.data('embed-url'));
        });
        $(videoModal).on('hidden.bs.modal', function () {
            $('#videoIframe').attr('src', '');
            $('#videoModalTitle').text('');
        });
    }

    // Video modal — homepage
    var homeVideoModal = document.getElementById('homeVideoModal');
    if (homeVideoModal) {
        $(homeVideoModal).on('show.bs.modal', function (e) {
            var btn = $(e.relatedTarget);
            $('#homeVideoModalTitle').text(btn.data('video-title'));
            $('#homeVideoIframe').attr('src', btn.data('embed-url'));
        });
        $(homeVideoModal).on('hidden.bs.modal', function () {
            $('#homeVideoIframe').attr('src', '');
            $('#homeVideoModalTitle').text('');
        });
    }

    // Auto-hide alerts — /contact page
    var alerts = document.querySelectorAll('.alert');
    if (alerts.length > 0) {
        setTimeout(function () {
            alerts.forEach(function (alert) {
                $(alert).alert('close');
            });
        }, 5000);
    }

    // Ask question modal — /specialists page
    var askModal = document.getElementById('askQuestionModal');
    if (askModal) {
        document.querySelectorAll('.btn-ask-question').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var specialistId = this.dataset.specialistId || '';
                var select = document.getElementById('modal-specialist-id');
                if (select) {
                    select.value = specialistId;
                }
            });
        });
    }
});