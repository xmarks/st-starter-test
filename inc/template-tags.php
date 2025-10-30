<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package ST_Starter
 */

if ( ! function_exists( 'st_starter_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function st_starter_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date. */
			esc_html_x( 'Posted on %s', 'post date', 'st-starter' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		echo '<span class="posted-on">' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
endif;

if ( ! function_exists( 'st_starter_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function st_starter_posted_by() {
		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( 'by %s', 'post author', 'st-starter' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="byline"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
endif;

if ( ! function_exists( 'st_starter_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function st_starter_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'st-starter' ) );
			if ( $categories_list ) {
				/* translators: 1: list of categories. */
				printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'st-starter' ) . '</span>', $categories_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'st-starter' ) );
			if ( $tags_list ) {
				/* translators: 1: list of tags. */
				printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'st-starter' ) . '</span>', $tags_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'st-starter' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post( get_the_title() )
				)
			);
			echo '</span>';
		}

		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'st-starter' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				wp_kses_post( get_the_title() )
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

if ( ! function_exists( 'st_starter_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 */
	function st_starter_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
			?>

			<div class="post-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div><!-- .post-thumbnail -->

		<?php else : ?>

			<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
				<?php
					the_post_thumbnail(
						'post-thumbnail',
						array(
							'alt' => the_title_attribute(
								array(
									'echo' => false,
								)
							),
						)
					);
				?>
			</a>

			<?php
		endif; // End is_singular().
	}
endif;

if ( ! function_exists( 'wp_body_open' ) ) :
	/**
	 * Shim for sites older than 5.2.
	 *
	 * @link https://core.trac.wordpress.org/ticket/12563
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
endif;

if ( ! function_exists( 'st_generate_img' ) ) :
	/**
	 * Function generates retina-ready image tags.
	 *  - Accepts either an attachment ID or ACF Image array.
	 *  - Uses array data to avoid extra DB calls when possible.
	 *
	 * @param int|array $attachment attachment ID or ACF Pro Image Array.
	 * @param string    $size attachment size.
	 * @param string    $fetch_priority image fetch priority instruction.
	 * @param string    $classes any extra classes.
	 *
	 * @return string
	 */
	function st_generate_img( $attachment, $size = 'full', $fetch_priority = 'low', $classes = '' ): string {
		if ( is_array( $attachment ) && isset( $attachment['ID'] ) ) {
			// Extract ID from ACF image array.
			$attachment_id = $attachment['ID'];
		} else {
			// Otherwise assume it's a direct ID.
			$attachment_id = $attachment;
		}

		if ( ! $attachment_id ) {
			return '';
		}

		$image_src = wp_get_attachment_image_src( $attachment_id, $size );
		if ( ! $image_src ) {
			return '';
		}

		$srcset = wp_get_attachment_image_srcset( $attachment_id, $size );
		$sizes  = wp_get_attachment_image_sizes( $attachment_id, $size );

		$alt         = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
		$image_class = 'wp-image-' . $attachment_id . ( $classes ? ' ' . esc_attr( $classes ) : '' );
		$url         = $image_src[0];
		$width       = $image_src[1];
		$height      = $image_src[2];

		$figure_class = 'wp-block-image size-' . esc_attr( $size );

		return sprintf(
			'<figure class="%s"><img fetchpriority="%s" decoding="async" width="%d" height="%d" src="%s" alt="%s" class="%s" srcset="%s" sizes="%s" /></figure>',
			esc_attr( $figure_class ),
			esc_attr( $fetch_priority ),
			esc_attr( $width ),
			esc_attr( $height ),
			esc_url( $url ),
			esc_attr( $alt ),
			esc_attr( $image_class ),
			esc_attr( $srcset ),
			esc_attr( $sizes )
		);
	}
endif;


if ( ! function_exists( 'sprite_svg' ) ) :

	/**
	 *  Echoes out SVG Icon from set sprite
	 *
	 *  Can dynamically set sprite source used when called
	 *  sprite source has to be uploaded to /assets/images/icons/ folder
	 *
	 * @param string $sprite_name sprite icon name.
	 * @param int    $svg_width sprite icon width.
	 * @param int    $svg_height sprite icon height.
	 * @param string $sprite_source sprite source file.
	 *
	 * @return void
	 *
	 * @throws Exception Throws error if sprite image directory is incorrect.
	 */
	function sprite_svg(
		string $sprite_name,
		int $svg_width = 24,
		int $svg_height = 24,
		string $sprite_source = '/assets/images/icons/icons.svg'
	): void {

		// Detect if $sprite_source contains '/images/'.
		if ( str_contains( $sprite_source, '/images/' ) ) {
			// Get the substring after '/images/'.
			$sprite_source = substr( $sprite_source, strpos( $sprite_source, '/assets/images/icons/' ) );
		} else {
			throw new Exception( 'Sprite Source Dir Incorrect! Upload to /assets/images/icons/' );
		}

		$svg = get_stylesheet_directory_uri() . '/' . $sprite_source . '?ver=' . filemtime( get_template_directory() . '/' . $sprite_source ) . '#' . $sprite_name;

		$icon_html = '<svg class="svg-icon ' . $sprite_name . '" width="' . $svg_width . '" height="' . $svg_height . '"><use xlink:href="' . $svg . '"></use></svg>';

		// Define allowed attributes for SVG.
		$allowed_html = array(
			'svg' => array(
				'class'  => true,
				'width'  => true,
				'height' => true,
			),
			'use' => array(
				'xlink:href' => true,
			),
		);

		// Sanitize the SVG HTML output using wp_kses with the allowed attributes.
		echo wp_kses( $icon_html, $allowed_html );
	}
endif;
