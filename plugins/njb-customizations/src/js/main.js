// filepath: /my-plugin/my-plugin/assets/js/script.js

document.addEventListener("DOMContentLoaded", function () {
    // Function to check the scroll position and update the class
    function updateStickyElements() {
        if ( window.scrollY > 770 ) {
            document.querySelectorAll(".header-container.is-position-sticky").forEach(function (element) {
                if ( ! element.classList.contains("opaque")) {
                    element.classList.add("opaque");
                }
            });
        } else {
            document.querySelectorAll(".header-container.is-position-sticky").forEach(function (element) {
                if ( ! element.classList.contains("keep-opaque")) {
                    element.classList.remove("opaque");
                }
            });
        }
    }

    // Check the position on page load
    updateStickyElements();

    // Add scroll event listener to update the class dynamically
    window.addEventListener("scroll", updateStickyElements);
});
import Splide from '@splidejs/splide';

document.addEventListener( 'DOMContentLoaded', function() {
    var splide = new Splide( '.is-style-carousel', {
        type: 'loop',
        perPage: 3,
        perMove: 1,
        gap: 0,
        pagination: true,
        breakpoints: {
            '640': {
                perPage: 1,
            },
            '768': {
                perPage: 2,
            },
            '1340': {
                perPage: 3,
            }
        },
        arrowPath:"M25.6665 25L0.666504 50V0L25.6665 25Z",
    } );
    splide.mount();
} );
