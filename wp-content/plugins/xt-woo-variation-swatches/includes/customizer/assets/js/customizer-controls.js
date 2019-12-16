;(function( $ ) {

    'use strict';

	wp.customize.bind( 'ready', function() {

		// Detect when the Login Designer panel is expanded (or closed) so we can preview the login form easily.
		wp.customize.panel( 'xt_woovs-archives', function( section ) {

			section.expanded.bind( function( isExpanding ) {

				// Value of isExpanding will = true if you're entering the section, false if you're leaving it.
				if ( isExpanding ) {

					// Only send the previewer to the login designer page, if we're not already on it.
					var current_url = wp.customize.previewer.previewUrl();

                    if (!woovs_controls.is_shop && current_url !== woovs_controls.archive_url) {
                        wp.customize.previewer.previewUrl(woovs_controls.archive_url);
					}

				}
			} );

		});

		// Detect when the Login Designer panel is expanded (or closed) so we can preview the login form easily.
		wp.customize.panel( 'xt_woovs-single', function( section ) {

			section.expanded.bind( function( isExpanding ) {

				// Value of isExpanding will = true if you're entering the section, false if you're leaving it.
				if ( isExpanding ) {

					// Only send the previewer to the login designer page, if we're not already on it.
					var current_url = wp.customize.previewer.previewUrl();

                    if ( current_url !== woovs_controls.single_url && wp.customize.previewer.preview && !$(wp.customize.previewer.preview.targetWindow().document).find('body').hasClass('single-product')) {
                        wp.customize.previewer.previewUrl(woovs_controls.single_url);
					}

				}
			} );

		});

	} );

} )( jQuery );