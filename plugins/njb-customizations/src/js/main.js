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
