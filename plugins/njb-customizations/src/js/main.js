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
        attachBirthInputListener(); // Ensure the birth input listener is attached
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
            message.style.color = '#cc1818';
            message.style.marginBottom = '10px';
            message.style.gridColumn = '1 / span 4';
            const form = document.querySelector('#contact');
            if (form) form.append(message);
        }
		const submitButton = document.querySelector('.wc-block-components-checkout-place-order-button');
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const checked = document.querySelectorAll('#contact input[type="checkbox"]:checked');
                if ( checked.length > 3 ) {
                    checkbox.click();
                    message.textContent = 'You can only select up to 3 options. Please uncheck one of the selected options to select a new one.';
					submitButton.disabled = true;
                } else {
                    message.textContent = '';
					submitButton.disabled = false;
                }
            });
        });
    }

    function attachBirthInputListener() {
        var birthInput = document.getElementById('contact-njb-birth_date');
        if ( birthInput && !birthInput.dataset.listenerAttached ) {
            birthInput.type = 'date';
            birthInput.max = new Date().toISOString().split('T')[0];

            function validateYoungAdultAge() {
                const cartItems = document.querySelectorAll('.wc-block-components-product-name');
                const isYoungAdultMembership = Array.from(cartItems).some(item => item.textContent.includes('Young Adult Membership'));
                const submitButton = document.querySelector('.wc-block-components-checkout-place-order-button');
                submitButton.disabled = true;
                let ageMessage = document.getElementById('age-limit-message');
                if ( ! isYoungAdultMembership ) {
                    if ( ageMessage ) ageMessage.remove();
                    if ( submitButton ) submitButton.disabled = false;
                    return;
                }

                const birthDate = new Date(birthInput.value);
                if ( !birthInput.value || isNaN(birthDate) ) {
                    if ( ageMessage ) ageMessage.remove();
                    if ( submitButton ) submitButton.disabled = false;
                    return;
                }

                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;

                if (age >= 30) {
                    if (!ageMessage) {
                        ageMessage = document.createElement('div');
                        ageMessage.id = 'age-limit-message';
                        ageMessage.style.color = '#cc1818';
                        ageMessage.style.marginBottom = '10px';
                        birthInput.parentNode.insertBefore(ageMessage, birthInput.nextSibling);
                    }
                    ageMessage.innerHTML = 'You must be less than 30 years old to subscribe to the Young Adults Membership.<br />Return to cart and remove the item to add the Regular membership.';
                    if (submitButton) submitButton.disabled = true;
                    birthInput.parentElement.classList.add('has-error');
                } else {
                    if (ageMessage) ageMessage.remove();
                    birthInput.parentElement.classList.remove('has-error');
                    if (submitButton) submitButton.disabled = false;
                }
            }

            birthInput.addEventListener( 'change', validateYoungAdultAge );
            
            // Ejecuta el chequeo al cargar la página
            validateYoungAdultAge();

            birthInput.dataset.listenerAttached = "true";
        }
    }
});


jQuery( function( $ ) {

	if ( typeof wc_country_select_params === 'undefined' ) {
		return false;
	}

	// Select2 Enhancement if it exists
	if ( $().selectWoo ) {
		var getEnhancedSelectFormatString = function() {
			return {
				'language': {
					errorLoading: function() {
						// Workaround for https://github.com/select2/select2/issues/4355 instead of i18n_ajax_error.
						return wc_country_select_params.i18n_searching;
					},
					inputTooLong: function( args ) {
						var overChars = args.input.length - args.maximum;

						if ( 1 === overChars ) {
							return wc_country_select_params.i18n_input_too_long_1;
						}

						return wc_country_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
					},
					inputTooShort: function( args ) {
						var remainingChars = args.minimum - args.input.length;

						if ( 1 === remainingChars ) {
							return wc_country_select_params.i18n_input_too_short_1;
						}

						return wc_country_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
					},
					loadingMore: function() {
						return wc_country_select_params.i18n_load_more;
					},
					maximumSelected: function( args ) {
						if ( args.maximum === 1 ) {
							return wc_country_select_params.i18n_selection_too_long_1;
						}

						return wc_country_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
					},
					noResults: function() {
						return wc_country_select_params.i18n_no_matches;
					},
					searching: function() {
						return wc_country_select_params.i18n_searching;
					}
				}
			};
		};
	}

	/* State/Country select boxes */
	var states_json       = wc_country_select_params.countries.replace( /&quot;/g, '"' ),
		states            = JSON.parse( states_json ),
		wrapper_selectors = '.address-section';

	$( document.body ).on( 'change refresh', '.country_to_state select', function() {
		// Grab wrapping element to target only stateboxes in same 'group'
		var $wrapper = $( this ).closest( wrapper_selectors );

		if ( ! $wrapper.length ) {
			$wrapper = $( this ).closest('.form-row').parent();
		}

		var country     = $( this ).val(),
			$statebox     = $wrapper.find( '.business-state input, .business-state select' ),
			$parent       = $statebox.closest( '.acf-input' ),
			input_name    = $statebox.attr( 'name' ),
			input_id      = $statebox.attr('id'),
			input_classes = $statebox.attr('data-input-classes'),
			value         = $statebox.val(),
			placeholder   = $statebox.attr( 'placeholder' ) || $statebox.attr( 'data-placeholder' ) || '',
			$newstate;

		if ( placeholder === wc_country_select_params.i18n_select_state_text ) {
			placeholder = '';
		}
		if ( states[ country ] ) {
			if ( $.isEmptyObject( states[ country ] ) ) {
				$newstate = $( '<input type="hidden" />' )
					.prop( 'id', input_id )
					.prop( 'name', input_name )
					.attr( 'data-input-classes', input_classes )
					.addClass( 'hidden ' + input_classes );
				$parent.hide().find( '.select2-container' ).remove();
				$statebox.replaceWith( $newstate );
				$( document.body ).trigger( 'country_to_state_changed', [ country, $wrapper ] );
			} else {
				var state          = states[ country ],
					$defaultOption = $( '<option value=""></option>' ).text( wc_country_select_params.i18n_select_state_text );

				if ( ! placeholder ) {
					placeholder = wc_country_select_params.i18n_select_state_text;
				}

				$parent.show();

				if ( $statebox.is( 'input' ) ) {
					$newstate = $( '<select></select>' )
						.prop( 'id', input_id )
						.prop( 'name', input_name )
						.data( 'placeholder', placeholder )
						.attr( 'data-input-classes', input_classes )
						.addClass( 'state_select ' + input_classes );
					$statebox.replaceWith( $newstate );
					$statebox = $wrapper.find( '.business-state select' );
				}
				$statebox.empty().append( $defaultOption );

				$.each( state, function( index ) {
					var $option = $( '<option></option>' )
						.prop( 'value', state[ index ] )
						.text( state[ index ] );
					$statebox.append( $option );
				} );

				$statebox.val( value ).trigger( 'change' );

				$( document.body ).trigger( 'country_to_state_changed', [country, $wrapper ] );
			}
		} else {
			if ( $statebox.is( 'select, input[type="hidden"]' ) ) {
				$newstate = $( '<input type="text" />' )
					.prop( 'id', input_id )
					.prop( 'name', input_name )
					.prop( 'placeholder', placeholder )
					.attr( 'data-input-classes', input_classes )
					.addClass( 'input-text  ' + input_classes );
				$parent.show().find( '.select2-container' ).remove();
				$statebox.replaceWith( $newstate );
				$( document.body ).trigger( 'country_to_state_changed', [country, $wrapper ] );
			}
		}

		$( document.body ).trigger( 'country_to_state_changing', [country, $wrapper ] );
	});

	$( document.body ).on( 'wc_address_i18n_ready', function() {
		// Init country selects with their default value once the page loads.
		$( wrapper_selectors ).each( function() {
			var $country_input = $( this ).find( '#billing_country, #shipping_country, #calc_shipping_country' );

			if ( 0 === $country_input.length || 0 === $country_input.val().length ) {
				return;
			}

			$country_input.trigger( 'refresh' );
		});
	});

    $('.country_to_state select').trigger('change');
    
});
