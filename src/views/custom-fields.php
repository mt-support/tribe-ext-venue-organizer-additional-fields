<?php
/**
 * Single Venue/Organizer Meta (Additional Fields) Template
 */

if ( ! isset( $fields_to_echo ) || empty( $fields_to_echo ) || ! is_array( $fields_to_echo ) ) {
	return;
}

?>

<div class="tribe-events-meta-group">
	<h2 class="tribe-events-single-section-title"> <?php esc_html_e( 'Other', 'tribe-events-calendar-pro' ) ?> </h2>
	<dl>
		<?php foreach ( $fields_to_echo as $name => $value ): ?>
			<dt> <?php echo esc_html( $name );  ?> </dt>
			<dd class="tribe-meta-value">
				<?php
				// This can hold HTML. The values are cleansed upstream
				echo $value;
				?>
			</dd>
		<?php endforeach ?>
	</dl>
</div>
