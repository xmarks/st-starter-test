<?php
/**
 * Block template file: block-render.php
 *
 * @var array $block The block settings and attributes.
 *
 * @var string $content The block inner HTML (empty).
 * @var bool $is_preview True during AJAX preview.
 * @var (int|string) $post_id The post ID this block is saved to.
 *
 * @package ST_Starter
 */

// Create id attribute allowing for custom "anchor" value.
$block_name = str_replace( 'st/', '', $block['name'] );
$block_id   = $block_name . '-section-' . $block['id'];

if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = "{$block_name}-section builder-section";
if ( ! empty( $block['className'] ) ) {
	$classes .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$classes .= ' align' . $block['align'];
}

// ACF Fields.
$fields = get_field( $block_name );
$style  = $fields[ "{$block_name}__style" ];
$items  = $fields[ "{$block_name}-items" ];

// Extra classes.
$classes .= ' some-class';

// Do something for Editor Preview only.
if ( $is_preview ) {
	$test = 'something...';
}

// Image used for the block preview.
if ( isset( $block['data']['preview_image_help'] ) ) :
	$file_url = str_replace( esc_url( get_stylesheet_directory() ), '', __DIR__, ); ?>
	<img src="
	<?php
	echo esc_url( get_stylesheet_directory_uri() )
	. esc_url( $file_url ) . '/'
	. wp_kses_post( $block['data']['preview_image_help'] )
	?>
	" style="width:100%; height:auto;" alt="preview_image_help"/>
	<?php
else :
	?>
	<section <?php echo wp_kses_post( st_get_block_attributes( $block_id, $classes ) ); ?>>
		<div class="container">
			<div class="<?php echo esc_html( $block_name ); ?>-section-wrapper">
				<div id="splide-<?php echo esc_attr( $block_id ); ?>"
					class="splide <?php echo esc_html( $block_name ); ?>-section-splide enter-view">
					<div class="splide__track <?php echo esc_html( $block_name ); ?>-section-splide__track">
						<ul class="splide__list <?php echo esc_html( $block_name ); ?>-section-splide__list">
							<?php
							foreach ( $items as $key => $item ) :
								$item_title = $item[ "{$block_name}-item__title" ] ?? null;
								$item_image = $item[ "{$block_name}-item__image" ] ?? null;
								?>
								<li class="splide__slide <?php echo esc_html( $block_name ); ?>-section-splide__slide">
									<?php
									if ( $item_title ) :
										?>
										<h4 class="<?php echo esc_html( $block_name ); ?>-section-splide__title animate__animated opacity-0"
											data-animate-class="animate__fadeIn"
											style="animation-delay: <?php echo esc_html( 200 + ( 100 * $key ) ); ?>ms">
											<?php echo wp_kses_post( $item_title ); ?>
										</h4>
										<?php
									endif;
									?>

									<?php
									if ( $item_image ) :
										?>
										<div class="<?php echo esc_html( $block_name ); ?>-section-splide__image animate__animated opacity-0"
											data-animate-class="animate__fadeIn"
											style="animation-delay: <?php echo esc_html( 100 + ( 100 * $key ) ); ?>ms">
											<?php echo wp_kses_post( st_generate_img( $item_image ) ); ?>
										</div>
										<?php
									endif;
									?>
								</li>
								<?php
							endforeach;
							?>
						</ul>
					</div>

					<div class="splide__arrows <?php echo esc_html( $block_name ); ?>-section-splide__arrows">
						<button class="splide__arrow <?php echo esc_html( $block_name ); ?>-section-splide__arrow splide__arrow--prev <?php echo esc_html( $block_name ); ?>-section-splide__arrow--prev animate__animated opacity-0" type="button" aria-label="<?php esc_html_e( 'Previous slide', 'st-starter' ); ?>"
								data-animate-class="animate__fadeIn"
								style="animation-delay: 400ms"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" width="40" height="40" focusable="false"><path d="m15.5 0.932-4.3 4.38 14.5 14.6-14.5 14.5 4.3 4.4 14.6-14.6 4.4-4.3-4.4-4.4-14.6-14.6z"></path></svg></button>

						<button class="splide__arrow <?php echo esc_html( $block_name ); ?>-section-splide__arrow splide__arrow--next <?php echo esc_html( $block_name ); ?>-section-splide__arrow--next animate__animated opacity-0" type="button" aria-label="<?php esc_html_e( 'Next slide', 'st-starter' ); ?>"
								data-animate-class="animate__fadeIn"
								style="animation-delay: 450ms"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" width="40" height="40" focusable="false"><path d="m15.5 0.932-4.3 4.38 14.5 14.6-14.5 14.5 4.3 4.4 14.6-14.6 4.4-4.3-4.4-4.4-14.6-14.6z"></path></svg></button>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
endif; ?>
