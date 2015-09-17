<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

add_action( 'init', array( 'Scarcity_Samurai_Endpoints', 'init' ) );

class Scarcity_Samurai_Endpoints
{
	public static function init() {
		// Endpoints that aren't ajax...
		add_filter( 'query_vars', array( __CLASS__, 'add_endpoint_query_var' ) );
		add_action( 'template_redirect', array( __CLASS__, 'endpoint' ) );

		// AJAX hooks
		add_action( 'wp_ajax_ss_activate_deactivate_campaigns', array( __CLASS__, 'activate_deactivate_campaigns' ) );
		add_action( 'wp_ajax_ss_publish_pages', array( __CLASS__, 'publish_pages' ) );
		add_action( 'wp_ajax_ss_get_page_content', array( __CLASS__, 'get_page_content' ) );
		add_action( 'wp_ajax_ss_wizard-evergreen-optin', array( __CLASS__, 'wizard_evergreen_optin' ) );
		add_action( 'wp_ajax_ss_wizard-fixed-date-multi', array( __CLASS__, 'wizard_fixed_date_multi' ) );
		add_action( 'wp_ajax_ss_wizard-evergreen-pageload', array( __CLASS__, 'wizard_evergreen_pageload' ) );
		add_action( 'wp_ajax_ss_wizard-fixed-date-single', array( __CLASS__, 'wizard_fixed_date_single' ) );
	}

	// ---------------
	//    Endpoints
	// ---------------

	public static function add_endpoint_query_var( $vars ) {
		$vars[] = 'ss-endpoint';
		return $vars;
	}

	public static function endpoint() {
		$endpoint = get_query_var( 'ss-endpoint' );
		$endpoint_file = plugin_dir_path( __FILE__ ) . "endpoints/{$endpoint}.php";

		if ( $endpoint && file_exists( $endpoint_file ) ) {
			include( $endpoint_file );
			exit;
		}
	}

	// ------------------
	//    AJAX Actions
	// ------------------

	public static function activate_deactivate_campaigns() {
		include( plugin_dir_path( __FILE__ ) . 'endpoints/activate-deactivate-campaigns.php' );
	}

	public static function publish_pages() {
		include( plugin_dir_path( __FILE__ ) . 'endpoints/publish-pages.php' );
	}

	public static function get_page_content() {
		include( plugin_dir_path( __FILE__ ) . 'endpoints/get-page-content.php' );
	}

	public static function wizard_evergreen_optin() {
		include( plugin_dir_path( __FILE__ ) . 'endpoints/wizards/evergreen-optin.php' );
	}

	public static function wizard_fixed_date_multi() {
		include( plugin_dir_path( __FILE__ ) . 'endpoints/wizards/fixed-date-multi.php' );
	}

	public static function wizard_evergreen_pageload() {
		include( plugin_dir_path( __FILE__ ) . 'endpoints/wizards/evergreen-pageload.php' );
	}

	public static function wizard_fixed_date_single() {
		include( plugin_dir_path( __FILE__ ) . 'endpoints/wizards/fixed-date-single.php' );
	}
}
