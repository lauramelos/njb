/**
 * WordPress dependencies
 */
import { store, getContext } from '@wordpress/interactivity';

const { state } = store( 'create-block', {
	state: {
		isOpen: false,
	},
	actions: {
		openModal() {
			const context = getContext();
			context.isOpen = ! context.isOpen;
		},
		closeModal() {
			const context = getContext();
			context.isOpen = false;
		},
		handleEscClose(e) {
			if ( e.key === 'Escape' || e.keyCode === 27 ) {
				const context = getContext();
				context.isOpen = false;
			}
		}
	},
	callbacks: {
		handleEscClose(e) {
			if ( e.key === 'Escape' || e.keyCode === 27 ) {
				const context = getContext();
				context.isOpen = false;
		}
		},
		handleClickOutside(event) {
			const context = getContext();
			if ( ! context.isOpen ) {
				return;
			}

			if ( !event.target.closest('.modal__content') &&
			  	!event.target.closest('.js-show-modal')
			) {
			  context.isOpen = false;
			}
		}
	}
} );
