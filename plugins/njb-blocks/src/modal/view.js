/**
 * WordPress dependencies
 */
import { store } from '@wordpress/interactivity';
const { state } = store( 'create-block', {
	state: {
		isOpen: false,
	},
	actions: {
		openModal() {
			//const context = getContext();
			state.isOpen = ! state.isOpen;
		},
		closeModal() {
			//const context = getContext();
			state.isOpen = false;
			console.log( `Is closed: ${ state.isOpen }` );
		},
		handleEscClose(e) {
			if ( e.key === 'Escape' || e.keyCode === 27 ) {
				//const context = getContext();
				state.isOpen = false;
				console.log( `Modal closed with ESC` );
			}
		}
	},
	callbacks: {
		handleEscClose(e) {
			if ( e.key === 'Escape' || e.keyCode === 27 ) {
				//const context = getContext();
				state.isOpen = false;
				console.log( `Modal closed with ESC` );
		}
		},
		handleClickOutside(event) {
			//const context = getContext();
			if ( ! state.isOpen ) {
				return;
			}

			if ( ! event.target.closest('.modal__content') &&
			  	! event.target.closest('.js-show-modal')
			) {
				state.isOpen = false;
				console.log( `Modal closed by clicking outside` );
			}
		}
	}
} );
