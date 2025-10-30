/**
 * File admin.js
 *
 * Main Admin Theme scripts.
 *
 * @package ST_Starter
 */

/**
 * Handles the dismissal of the plugin notice in the WordPress admin.
 * When the "Don't remind me" link is clicked, it triggers an AJAX request
 * to store the dismissal action for the current user and then hides the notice.
 *
 * @see wp_localize_script() - The st_starter_dismiss_plugin_notice object containing:
 *      - ajax_url (string): The URL for making AJAX requests to WordPress admin.
 *      - nonce (string): A security nonce to validate the AJAX request.
 *
 * @var {object} st_starter_plugin_notice The localized object containing the AJAX URL and nonce.
 * @var {string} st_starter_plugin_notice.ajax_url The URL for making AJAX requests.
 * @var {string} st_starter_plugin_notice.nonce The nonce for security verification of the AJAX request.
 *
 * @package ST_Starter
 */

jQuery( document ).ready( function ( $ ) {
    // Event handler for the "Don't remind me" button click.
    $( '.st-dismiss-plugin-notice' ).on( 'click', function ( e ) {
        e.preventDefault(); // Prevents the default action of the anchor tag.

        // Send AJAX request to dismiss the plugin notice for the current user.
        $.post( st_starter_plugin_notice.ajax_url, {
            action: 'st_starter_dismiss_plugin_notice', // The action triggered in the server-side callback.
            nonce: st_starter_plugin_notice.nonce // Security nonce for the request.
        } );

        // Hide the plugin notice after the AJAX request.
        $( this ).closest( '.st-plugin-notice' ).fadeOut();
    } );
} );


/** ---------------------------------------------------------------
 *  Image field for nav‑menu items
 *  ---------------------------------------------------------------
 *  • Opens the WP media modal when the “Select/Upload” button
 *    ( .upload-menu-image ) is clicked.
 *  • Stores the chosen attachment ID in the hidden input
 *    ( .menu-item-image-id ).
 *  • Shows a thumb in the <span class="menu-image-preview">.
 * ----------------------------------------------------------------
 */

jQuery( function ( $ ) {

    /* -------------------------------------------------
       SET / CHANGE  ( .upload-menu-image )
    ------------------------------------------------- */
    $( document ).on( 'click', '.upload-menu-image', function ( e ) {
        e.preventDefault();

        const $upload = $( this );                       // clicked button
        const $field = $upload.closest( '.field-image' );
        const $input = $field.find( '.menu-item-image-id' );
        const $preview = $field.find( '.menu-image-preview' );
        const $remove = $field.find( '.remove-menu-image' );

        /* ── Get—or build—our media‑frame once ───────────────────── */
        let frame = $upload.data( 'media‑frame' );
        if (!frame) {
            frame = wp.media( {
                title: 'Select menu image',
                library: {type: 'image'},
                button: {text: 'Use this image'},
                multiple: false
            } );

            /* When the user clicks “Use this image” */
            frame.on( 'select', () => {
                const attachment = frame.state().get( 'selection' ).first().toJSON();

                // store the ID
                $input.val( attachment.id ).trigger( 'change' );

                // tiny preview (thumb if we have it)
                const thumb = attachment.sizes?.thumbnail?.url || attachment.url;
                $preview.html( `<img src="${thumb}" alt="">` );

                $field.addClass( 'has-image' );
                $upload.text( 'Change' );
                $remove.show();
            } );

            // keep a reference so we can reopen the same frame next time
            $upload.data( 'media‑frame', frame );
        }

        /* ── Before each open: highlight the *current* image ─────── */
        frame.off( 'open' ).on( 'open', () => {
            const selection = frame.state().get( 'selection' );
            selection.reset();                               // clear any old one

            const id = parseInt( $input.val(), 10 );
            if (id) {
                const att = wp.media.attachment( id );
                att.fetch();
                selection.add( att );
            }
        } );

        frame.open();
    } );

    /* -------------------------------------------------
       REMOVE  ( .remove-menu-image )
    ------------------------------------------------- */
    $( document ).on( 'click', '.remove-menu-image', function ( e ) {
        e.preventDefault();

        const $field = $( this ).closest( '.field-image' );
        const $input = $field.find( '.menu-item-image-id' );
        const $preview = $field.find( '.menu-image-preview' );
        const $upload = $field.find( '.upload-menu-image' );

        $input.val( '' ).removeAttr( 'value' );   // wipe meta
        $preview.empty();                     // no thumbnail
        $field.removeClass( 'has-image' );

        $upload.text( 'Set image' );
        $( this ).hide();
    } );
} );
