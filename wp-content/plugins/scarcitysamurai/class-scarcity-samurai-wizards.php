<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

add_action( 'plugins_loaded', array( 'Scarcity_Samurai_Wizards', 'init' ) );

class Scarcity_Samurai_Wizards {

	static private $supported_wizards = array(
		'evergreen-optin',
		'fixed-date-multi',
		'evergreen-pageload',
		'fixed-date-single'
	);

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu_pages' ), 11 );
	}

	public static function load() {
		if ( ! isset( $_REQUEST['wizard'] )	||
		     ! in_array( $_REQUEST['wizard'], self::$supported_wizards ) ||
		     ! Scarcity_Samurai_Access::wizard_is_available( $_REQUEST['wizard'] ) )
		{
			wp_redirect( admin_url( 'admin.php?page=scarcitysamurai' ) );
		}

		// Remove the 'Add Media' button
		remove_action( 'media_buttons', 'media_buttons' );
	}

	public static function add_menu_pages() {
		$hook_suffix = add_submenu_page(
			null,                                       // Parent slug
			'Campaign Wizards',                         // Page title
			'Campaign Wizards',                         // Submenu item name
			'manage_options',                           // Capability
			'scarcitysamurai/campaign_wizards',         // Slug
			array( __CLASS__, 'campaign_wizards_page' ) // Function
		);

		add_action( "load-{$hook_suffix}", array( __CLASS__, 'load' ) );
	}

	public static function campaign_wizards_page() {
		global $scarcity_samurai_dir;

		wp_enqueue_style( 'ss-new-ui-core', Scarcity_Samurai_Helper::url( 'stylesheets/css/new-ui-core.css' ) );
		wp_enqueue_style( 'ss-wizards', Scarcity_Samurai_Helper::url( 'stylesheets/css/wizards.css' ) );
		wp_enqueue_style( 'ss-select2', Scarcity_Samurai_Helper::url( 'vendor/select2/select2.css' ) );

		if ( isset( $_REQUEST['wizard'] ) && in_array( $_REQUEST['wizard'], self::$supported_wizards ) ) {
			$js_data = array(
				'server_time' => time(),
				'ajax_action' => 'ss_wizard-' . $_REQUEST['wizard'],
				'campaigns' => array()
			);

			$campaigns = Scarcity_Samurai_Model::get( 'Campaign' )->all();

			foreach ( $campaigns as $campaign ) {
				$js_data['campaigns'][] = $campaign['name'];
			}

			wp_enqueue_script( 'ss-underscore', Scarcity_Samurai::$UNDERSCORE_URL );
			wp_enqueue_script( 'ss-zclip', Scarcity_Samurai_Helper::url( 'vendor/zclip/jquery.zclip.js' ) );
			wp_enqueue_script( 'ss-helper', Scarcity_Samurai_Helper::url( 'scripts/js/helper.js' ) );
			wp_enqueue_script( 'ss-select2', Scarcity_Samurai_Helper::url( 'vendor/select2/select2.min.js' ) );
			wp_enqueue_script( 'ss-wizard-' . $_REQUEST['wizard'], Scarcity_Samurai_Helper::url( 'scripts/js/wizards/wizard-' . $_REQUEST['wizard'] . '.js' ) );

			wp_localize_script( 'ss-wizard-' . $_REQUEST['wizard'], 'scarcitySamuraiData', $js_data );

			include( $scarcity_samurai_dir . 'html/wizards/wizard-' . $_REQUEST['wizard'] . '.php' );
		}
	}

	public static function error( $error_messages ) {
		if ( is_string( $error_messages ) ) {
			$error_messages = array( $error_messages );
		}

		wp_send_json_error( $error_messages );
	}

	public static function unexpected_error() {
		self::error( 'Error occurred. Please contact ' . SS_SUPPORT_EMAIL );
	}

}
