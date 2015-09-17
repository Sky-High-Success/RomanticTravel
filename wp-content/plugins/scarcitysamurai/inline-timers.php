<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

add_action( 'plugins_loaded', array( 'Scarcity_Samurai_Inline_Timers', 'init' ) );

class Scarcity_Samurai_Inline_Timers {

	public static $html_comment_regex = '/<!--\s*ss-timer\s+(.*?)\s*-->/';

	public static function init() {
		return;
		// Priority 12 means that the 'Add Inline Timer' button should appear AFTER
		// the 'Add Inline Banner' button.
		add_action( 'media_buttons', array( __CLASS__, 'add_media_buttons'), 12 );

		if ( get_user_option('rich_editing') ) { // Make sure that TinyMCE is used
			add_filter( 'mce_external_plugins', array( 'Scarcity_Samurai_Inline_Timers', 'add_tinymce_plugins' ) );
		}
	}

	public static function admin_footer() {
		global $scarcity_samurai_dir;

		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
		wp_enqueue_script( 'ss-helper', Scarcity_Samurai_Helper::url( 'scripts/js/helper.js' ) );
		wp_enqueue_script( 'ss-inline-timers', Scarcity_Samurai_Helper::url( 'scripts/js/add-inline-timer.js' ) );
	}

	public static function add_media_buttons() {
		global $scarcity_samurai_dir;

		add_action( 'admin_footer', array( __CLASS__, 'admin_footer' ) );

		echo "
			<a id='ss-add-inline-timer-button'
			   href='#'
			   class='button'
			   title='" . esc_attr( 'Add Inline Timer' ) . "'>" .
			  _( 'Add Inline Timer' ) . "
			</a>
			<img class='ss-inline-timers-question-mark' src='" . Scarcity_Samurai_Helper::url('images/question-mark.png') . "' />
		";
	}

	public static function add_tinymce_plugins( $plugins ) {
		$plugins['ss_inline_timer'] = Scarcity_Samurai_Helper::url( 'scripts/js/tinymce/inline_timer.js' );

		return $plugins;
	}

}
