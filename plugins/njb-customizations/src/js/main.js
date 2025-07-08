// filepath: /my-plugin/my-plugin/assets/js/script.js

document.addEventListener("DOMContentLoaded", function () {
    // Function to check the scroll position and update the class
    function updateStickyElements() {
        if ( ! ( document.body.classList.contains("page-template-page-no-title") || document.body.classList.contains("page-template-default") ) ) {
            document.querySelectorAll(".header-container.is-position-sticky").forEach(function (element) {
                element.classList.add("opaque");
            });
        } else {
            if ( window.scrollY > 770 ) {
                document.querySelectorAll(".header-container.is-position-sticky").forEach(function (element) {
                    element.classList.add("opaque");
                });
            } else {
                document.querySelectorAll(".header-container.is-position-sticky").forEach(function (element) {
                    element.classList.remove("opaque");
                });
            }
        }
    }

    // Check the position on page load
    updateStickyElements();

    // Add scroll event listener to update the class dynamically
    window.addEventListener("scroll", updateStickyElements);
});
