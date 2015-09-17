<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

add_action( 'plugins_loaded', array( 'Scarcity_Samurai_Inline_Banners', 'init' ) );

class Scarcity_Samurai_Inline_Banners {

	private static $current_page = null;
	private static $campaign_id = null;
	private static $has_inline_banners = null;
	private static $has_inline_timers = null;
	public static $html_comment_regex = '/<!--\s*ss-banner\s+(.*?)\s*-->/';

	public static function init() {
		add_action( 'admin_head', array( __CLASS__, 'admin_head' ) );
		// Priority 11 means that the 'Add Inline Banner' button should appear AFTER
		// the 'Add Media' button.
		//add_action( 'media_buttons', array( __CLASS__, 'add_media_buttons'), 11 );
		// Priority 13 means that the warnings should appear AFTER
		// the 'Add Inline Timer' button.
		add_action( 'media_buttons', array( __CLASS__, 'add_warnings'), 13 );
		return;

		if ( get_user_option('rich_editing') ) { // Make sure that TinyMCE is used
			add_filter( 'mce_external_plugins', array( 'Scarcity_Samurai_Inline_Banners', 'add_tinymce_plugins' ) );
			add_filter( 'mce_buttons', array( __CLASS__, 'add_tinymce_buttons' ), 10, 2 );
			add_filter( 'mce_buttons_2', array( __CLASS__, 'add_tinymce_buttons_2' ), 10, 2 );
		}
	}

	public static function admin_head() {
		self::$current_page = Scarcity_Samurai_Helper::current_page();
		self::$campaign_id = self::$current_page['campaign_id'];
	}

	public static function admin_footer() {
		global $scarcity_samurai_dir;

		self::$has_inline_banners = Scarcity_Samurai_Page::has_inline_banners();
		self::$has_inline_timers = Scarcity_Samurai_Page::has_inline_timers();

		wp_enqueue_style( 'ss-jquery-ui', Scarcity_Samurai::$JQUERY_UI_CSS_URL );
		wp_enqueue_script( 'ss-helper', Scarcity_Samurai_Helper::url( 'scripts/js/helper.js' ) );
		wp_enqueue_script( 'ss-inline-banners', Scarcity_Samurai_Helper::url( 'scripts/js/dialogs/add-inline-banner.js' ) );
		wp_localize_script( 'ss-inline-banners', 'scarcitySamuraiPageData', array(
			'has_inline_banners' => self::$has_inline_banners,
			'has_inline_timers' => self::$has_inline_timers
		) );

		$in_wizard = ( Scarcity_Samurai_Helper::get_request('page') === 'scarcitysamurai/campaign_wizards' );

		Scarcity_Samurai_Helper::echo_html( $scarcity_samurai_dir . 'html/dialogs/add-inline-banner.php', array(
			'campaign_id' => self::$campaign_id,
			'in_wizard' => $in_wizard
		) );
	}

	public static function add_media_buttons() {
		global $scarcity_samurai_dir;

		add_action( 'admin_footer', array( __CLASS__, 'admin_footer' ) );

		echo "
			<a id='ss-add-inline-banner-button'
			   href='#'
			   class='button'
			   title='" . esc_attr( 'Add Inline Banner' ) . "'>" .
			  _( 'Add Inline Banner' ) . "
			</a>
		";
	}

	public static function add_warnings() {
		global $scarcity_samurai_dir;

		$display_no_campaign_note = false;
		$display_no_timer_note = false;

		if ( self::$has_inline_banners || self::$has_inline_timers ) {
			$display_no_campaign_note = ( self::$campaign_id === null );

			$available_until = self::$current_page['available_until'];
			$display_no_timer_note = ! $display_no_campaign_note && ( ! isset( $available_until['enabled'] ) || ( $available_until['enabled'] === false ) );
		}

		echo "
			<span class='ss-no-campaign-warning ss-warning-message'" .
			      ( $display_no_campaign_note ? '' : ' style="display:none"' ) . ">" .
			  _( 'Note: You must assign this page to a campaign in order to see inline banners/timers.' ) . "
			</span>
			<span class='ss-no-timer-warning ss-warning-message'" .
			      ( $display_no_timer_note ? '' : ' style="display:none"' ) . ">" .
			  _( 'Note: You must enable the count down timer in order to see inline banners/timers.' ) . "
			</span>
		";
	}

	public static function add_tinymce_plugins( $plugins ) {
		$plugins['ss_inline_banner'] = Scarcity_Samurai_Helper::url( 'scripts/js/tinymce/inline_banner.js' );

		return $plugins;
	}

	// Row 1 buttons
	public static function add_tinymce_buttons( $buttons, $editor_id ) {
		switch ( $editor_id ) {
			case SS_DEFAULT_EDITOR_ID:
				return array( 'justifyleft', 'justifycenter', 'justifyright', 'undo', 'redo', 'ssbanner' );

			default:
				return $buttons;
		}
	}

	// Row 2 buttons
	public static function add_tinymce_buttons_2( $buttons, $editor_id ) {
		switch ( $editor_id ) {
			case SS_DEFAULT_EDITOR_ID:
				return array();

			default:
				return $buttons;
		}
	}

}
