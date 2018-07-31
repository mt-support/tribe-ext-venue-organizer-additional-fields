<?php
/**
 * Plugin Name:     Events Calendar Pro Extension: Venue and Organizer Custom Fields
 * Description:     Adds custom field support to venues and organizers
 * Version:         1.0.0
 * Extension Class: Tribe__Extension__Venue_Organizer_Custom_Fields
 * Author:          Modern Tribe, Inc.
 * Author URI:      http://m.tri.be/1971
 * License:         GPL version 3 or any later version
 * License URI:     https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:     tribe-ext-venue-organizer-custom-fields
 *
 *     This plugin is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     any later version.
 *
 *     This plugin is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *     GNU General Public License for more details.
 */

// Do not load unless Tribe Common is fully loaded and our class does not yet exist.
if (
	class_exists( 'Tribe__Extension' )
	&& ! class_exists( 'Tribe__Extension__Venue_Organizer_Custom_Fields' )
) {
	/**
	 * Extension main class, class begins loading on init() function.
	 */
	class Tribe__Extension__Venue_Organizer_Custom_Fields extends Tribe__Extension {

		/**
		 * Setup the Extension's properties.
		 *
		 * This always executes even if the required plugins are not present.
		 */
		public function construct() {
			$this->add_required_plugin( 'Tribe__Events__Pro__Main', '4.4.23' );

			// Register custom field support
			add_filter( 'tribe_events_register_organizer_type_args', array( $this, 'tribe_organizers_custom_field_support' ) );
			add_filter( 'tribe_events_register_venue_type_args', array( $this, 'tribe_venues_custom_field_support' ) );

			// Hooks the above function up so it runs in the single organizer and venue pages
			add_action( 'tribe_events_single_venue_before_upcoming_events', array( $this, 'tribe_show_venue_organizer_custom_fields' ) );
			add_action( 'tribe_events_single_organizer_before_upcoming_events', array( $this, 'tribe_show_venue_organizer_custom_fields' ) );
		}

		/**
		 * Extension initialization and hooks.
		 */
		public function init() {
			// Load plugin textdomain
			load_plugin_textdomain( 'tribe-ext-venue-organizer-custom-fields', false, basename( dirname( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Enable custom field support for organizer posts.
		 *
		 * @param  array $args
		 * @return array
		 */
		public function tribe_organizers_custom_field_support( $args ) {
			$args['supports'][] = 'custom-fields';
			return $args;
		}

		/**
		 * Enable custom field support for venue posts.
		 *
		 * @param  array $args
		 * @return array
		 */
		public function tribe_venues_custom_field_support( $args ) {
			$args['supports'][] = 'custom-fields';
			return $args;
		}

		/**
		 * Outputs all WP post meta fields (except those prefixed "_")
		 */
		public function tribe_show_venue_organizer_custom_fields() {
			$show_venue_fields = apply_filters( 'tribe_show_venue_custom_fields', $show = true );
			$show_organizer_fields = apply_filters( 'tribe_show_organizer_custom_fields', $show = true );
			$venue_organizer_ID = get_the_ID();
			$fields_to_echo = array();

			foreach ( get_post_meta( $venue_organizer_ID ) as $field => $value ) {
				$field = trim( $field );
				if ( is_array( $value ) ) $value = implode( ', ', $value );
				if ( 0 === strpos( $field, '_' ) ) continue; // Don't expose "private" fields
				$fields_to_echo[ $field ] = $value;
			}

			$fields_to_echo = apply_filters( 'tribe_show_venue_organizer_custom_fields', $fields_to_echo = $fields_to_echo, $venue_organizer_ID = $venue_organizer_ID );

			if ( tribe_is_venue( $venue_organizer_ID ) && $show_venue_fields && ! empty( $fields_to_echo ) ) {
				include apply_filters( 'tribe_venue_custom_fields_template', $template = plugin_dir_path( __FILE__ ) . '/src/views/custom-fields.php' ) ;
			} elseif ( tribe_is_organizer( $venue_organizer_ID ) && $show_organizer_fields && ! empty( $fields_to_echo ) ) {
				include apply_filters( 'tribe_organizer_custom_fields_template', $template = plugin_dir_path( __FILE__ ) . '/src/views/custom-fields.php' ) ;
			}
		}
	} // end class
} // end if class_exists check