// filepath: /my-plugin/my-plugin/assets/js/script.js

document.addEventListener("DOMContentLoaded", function () {
    // Function to check the scroll position and update the class
    function updateStickyElements() {

        if (window.scrollY > 770 ) {
            document.querySelectorAll(".is-position-sticky").forEach(function (element) {
                element.classList.add("opaque");
            });
        } else {
            document.querySelectorAll(".is-position-sticky").forEach(function (element) {
                element.classList.remove("opaque");
            });
        }
    }

    // Check the position on page load
    updateStickyElements();

    // Add scroll event listener to update the class dynamically
    window.addEventListener("scroll", updateStickyElements);
});
