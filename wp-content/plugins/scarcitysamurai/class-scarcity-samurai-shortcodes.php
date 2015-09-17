<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

class Scarcity_Samurai_Shortcodes {

	public static function timer( $atts, $content = '' ) {
		$supported_show_effects = array( 'slide', 'fade' );
		$supported_hide_effects = array( 'slide', 'fade' );

		$defaults = array(
			'expires_after'        => '',
			'show_elements'        => '',
			'show_effect'          => '',
			'show_effect_duration' => '',
			'hide_elements'        => '',
			'hide_effect'          => '',
			'hide_effect_duration' => ''
		);

		extract( shortcode_atts( $defaults, $atts ), EXTR_SKIP );

		if ( ! ctype_digit( $expires_after ) ) {
			wp_die( "'expires_after' value must be an integer: $expires_after" );
		}

		if ( ( $show_effect_duration !== '' ) && ! ctype_digit( $show_effect_duration ) ) {
			wp_die( "'show_effect_duration' value must be an integer: $show_effect_duration" );
		}

		if ( ( $hide_effect_duration !== '' ) && ! ctype_digit( $hide_effect_duration ) ) {
			wp_die( "'hide_effect_duration' value must be an integer: $hide_effect_duration" );
		}

		if ( $show_elements === '' ) {
			$show_elements_attr = '';
		} else {
			$show_elements_attr = "show_elements='$show_elements'";
		}

		if ( $show_effect === '' ) {
			$show_effect_attr = '';
		} else {
			if ( ! in_array($show_effect, $supported_show_effects ) ) {
				wp_die( "Unsupported show effect: $show_effect" );
			}

			$show_effect_attr = "show_effect='$show_effect'";
		}

		if ( $show_effect_duration === '' ) {
			$show_effect_duration_attr = '';
		} else {
			$show_effect_duration_attr = "show_effect_duration='$show_effect_duration'";
		}

		if ( $hide_elements === '' ) {
			$hide_elements_attr = '';
		} else {
			$hide_elements_attr = "hide_elements='$hide_elements'";
		}

		if ( $hide_effect === '' ) {
			$hide_effect_attr = '';
		} else {
			if ( ! in_array( $hide_effect, $supported_hide_effects ) ) {
				wp_die( "Unsupported hide effect: $hide_effect" );
			}

			$hide_effect_attr = "hide_effect='$hide_effect'";
		}

		if ( $hide_effect_duration === '' ) {
			$hide_effect_duration_attr = '';
		} else {
			$hide_effect_duration_attr = "hide_effect_duration='$hide_effect_duration'";
		}

		$timer_html = <<<ENDL
			<span class="ss-shortcode-timer-wrapper"
			      expires_after="$expires_after"
			      $show_elements_attr
			      $show_effect_attr
			      $show_effect_duration_attr
						$hide_elements_attr
			      $hide_effect_attr
			      $hide_effect_duration_attr>
			</span>
ENDL;

		$js_data = array(
			'now' => time()
		);

		wp_enqueue_script( 'ss-ss_timer', Scarcity_Samurai_Helper::url( 'scripts/js/shortcodes/ss_timer.js' ), array( 'jquery' ) );
		wp_localize_script( 'ss-ss_timer', 'scarcitySamuraiData', $js_data );

		return $timer_html;
	}

}
