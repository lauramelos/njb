// filepath: /my-plugin/my-plugin/assets/js/script.js
import Splide from '@splidejs/splide';
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

    function carrousel() {
        if( ! document.querySelector( '.is-style-carousel' ) ) {
            return;
        }
        // Initialize Splide carousel
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
    } 
    carrousel();

    const observer = new MutationObserver(function(mutationsList, observer) {
        if (document.querySelectorAll('#contact input[type="checkbox"]').length) {
            restrictSelectors();
            observer.disconnect(); // Stop observing once the checkboxes are found
        }
    });

    observer.observe(document.body, { childList: true, subtree: true });
    function restrictSelectors() {

        const whatapp = document.querySelectorAll('.wc-block-components-address-form__njb-whatsapp_number');
    
        const checkboxes = document.querySelectorAll('#contact input[type="checkbox"]');
        if (!checkboxes.length) return;
        
        if ( whatapp.length ) {
            // Insert a text after the WhatsApp input
            const whatsappInput = whatapp[0];
            const whatsappText = document.createElement('p');
            whatsappText.textContent = 'Please select three options for the whatapp groups to subscribe: ';
            whatsappText.style.marginBottom = '0';
            whatsappText.style.gridColumn = '1 / span 4';
            //append the text after the WhatsApp input
            whatsappInput.insertAdjacentElement('afterend', whatsappText);
        }
        // Clear selections if more than 3 checkboxes are selected
        const checked = document.querySelectorAll('#contact input[type="checkbox"]:checked');
        if ( checked.length > 3 ) {

            setTimeout(() => {
             checked.forEach(cb => {
                cb.click();
            })
             }, 0);
        }

        let message = document.getElementById('checkbox-limit-message');
        if (!message) {
            message = document.createElement('div');
            message.id = 'checkbox-limit-message';
            message.style.color = 'red';
            message.style.marginBottom = '10px';
            message.style.gridColumn = '1 / span 4';
            const form = document.querySelector('#contact');
            if (form) form.append(message);
        }

        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const checked = document.querySelectorAll('#contact input[type="checkbox"]:checked');
                if ( checked.length > 3 ) {
                    checkbox.click();
                    message.textContent = 'You can only select up to 3 options. Please uncheck one of the selected options to select a new one.';
                } else {
                    message.textContent = '';
                }
            });
        });
    }
});
