<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

add_action( 'plugins_loaded', array( 'Scarcity_Samurai_Campaigns', 'init' ) );

class Scarcity_Samurai_Campaigns
{
	private static $error = null;

	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'save_campaign' ) );
		add_action( 'admin_menu', array( __CLASS__, 'add_menu_items' ) );
	}

	public static function add_menu_items() {
		add_submenu_page(
			'scarcitysamurai',
			'Campaigns',
			'All Campaigns',
			'manage_options',
			'scarcitysamurai/campaigns',
			array( __CLASS__, 'campaigns_page' )
		);
	}

	public static function campaigns_page() {
		wp_enqueue_style( 'ss-new-ui-core', Scarcity_Samurai_Helper::url( 'stylesheets/css/new-ui-core.css' ) );

		$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';

		switch ( $action ) {
			case 'new':
			case 'edit':
				self::show_campaign_page();
				break;

			default:
				self::show_campaign_list_page();
				break;
		}
	}

	private static function show_campaign_list_page() {
		$campaigns_table = new Scarcity_Samurai_Campaigns_Table();
		$campaigns_table->prepare_items();
		include( dirname( __FILE__ ) . '/html/pages/campaigns.php' );
	}

	private static function show_campaign_page() {
		$campaign_id = intval( Scarcity_Samurai_Helper::get_request( 'id' ) );
		$campaign = Scarcity_Samurai_Model::get( 'Campaign' )->find( $campaign_id );

		$js_data = array(
			'campaign_id' => $campaign_id,
		);

		wp_enqueue_style( 'ss-new-ui-core', Scarcity_Samurai_Helper::url( 'stylesheets/css/new-ui-core.css' ) );
		wp_enqueue_style( 'ss-campaign', Scarcity_Samurai_Helper::url( 'stylesheets/css/campaign.css' ) );

		wp_enqueue_script( 'ss-zclip', Scarcity_Samurai_Helper::url( 'vendor/zclip/jquery.zclip.js' ) );
		wp_enqueue_script( 'ss-helper', Scarcity_Samurai_Helper::url( 'scripts/js/helper.js' ) );
		wp_enqueue_script( 'ss-campaign', Scarcity_Samurai_Helper::url( 'scripts/js/pages/campaign.js' ) );

		wp_localize_script( 'ss-campaign', 'scarcitySamuraiData', $js_data );

		if ( isset( $campaign ) ) {
			$name = $campaign['name'];
			$is_new_campaign = false;

			$pages = Scarcity_Samurai_Campaign::pages( $campaign_id );

			$unpublished_pages = array();
			$unpublished_page_ids = array();
			$auto_responder_not_set_pages = array();
			$pages_that_require_a_token = array();

			foreach ( $pages as $page ) {
				// Unpublished pages
				if ( ! Scarcity_Samurai_Page::is_published( $page['id'] ) ) {
					$unpublished_pages[] = $page;
					$unpublished_page_ids[] = $page['id'];
				}

				// Pages with 'Page contains an opt-in form' checked, but the
				// auto responder is not set.
				if ( Scarcity_Samurai_Page::contains_opt_in_form( $page ) &&
				     ( Scarcity_Samurai_Page::autoresponder( $page['id'] ) === null ) )
				{
					$auto_responder_not_set_pages[] = $page;
				}

				// Pages that require a token
				if ( Scarcity_Samurai_Page::url_requires_token( $page ) ) {
					$pages_that_require_a_token[] = $page;
				}
			}

			if ( ! empty( $pages ) ) {
				foreach ( $pages as &$page ) {
					$page['title'] = Scarcity_Samurai_Helper::get_page_title_by_id( $page['id'] );

					// set page options status
					$page['lock-active'] = isset( $page['available_from']['enabled'] ) && $page['available_from']['enabled'];
					$page['timer-active'] = isset( $page['available_until']['enabled'] ) && $page['available_until']['enabled'];

					// banners?
					$header_banner_attributes = Scarcity_Samurai_Page::header_banner_attributes( $page['id'] );
					$footer_banner_attributes = Scarcity_Samurai_Page::footer_banner_attributes( $page['id'] );
					$page['banners-active'] = $header_banner_attributes['enabled'] || $footer_banner_attributes['enabled'];

					// page type
					switch ( $page['type_id'] ) {
						case 1: $page['type'] = 'signup'; break;
						case 3: $page['type'] = 'offer'; break;
						default: $page['type'] = 'page'; break;
					}
					unset( $page );
				}
			}

			$campaign_contains_opt_in_form = Scarcity_Samurai_Campaign::contains_opt_in_form( $campaign_id );
			$auto_responder = ( $campaign_contains_opt_in_form ? Scarcity_Samurai_Campaign::autoresponder( $campaign_id ) : null );
			$auto_responder_short_name = Scarcity_Samurai_Auto_Responder_Integrator::get_auto_responder_short_name( $auto_responder );
			$squeeze_page = ( $campaign_contains_opt_in_form ? Scarcity_Samurai_Campaign::squeeze_page( $campaign_id ) : null );
		} else {
			$name = '';
			$is_new_campaign = true;
		}

		$error = self::$error;

		switch ( Scarcity_Samurai_Helper::get_request( 'message' ) ) {
			case 'created':
				$message = 'Campaign created.';
				break;

			case 'updated':
				$message = 'Campaign name updated.';
				break;

			case 'already_exists':
				$error = 'Campaign name already exists.';
				break;

			case 'unexpected_error':
				$error = 'Unexpected error occurred.';
				break;
		}

		include( dirname( __FILE__ ) . '/html/pages/campaign.php' );
	}

	public static function save_campaign() {
		$save_action = Scarcity_Samurai_Helper::get_request( 'save' );
		$campaign_id = intval( Scarcity_Samurai_Helper::get_request( 'campaign_id' ) );
		$campaign_name = trim( Scarcity_Samurai_Helper::get_request( 'campaign_name' ) );

		if ( $save_action !== '' && $campaign_name === '' ) {
			self::$error = 'Please enter a campaign name';
			return;
		}

		switch ( $save_action ) {
			case 'Create':
				if ( Scarcity_Samurai_Campaign::campaign_name_exists( $campaign_name ) ) {
					wp_redirect( add_query_arg( array( 'action' => 'new', 'message' => 'already_exists' ) ) );
				} else {
					$campaign_id = Scarcity_Samurai_Model::get( 'Campaign' )->insert( array(
						'name' => $campaign_name,
						'active' => true
					) );
					wp_redirect( add_query_arg( array( 'action' => 'edit', 'id' => $campaign_id, 'message' => 'created' ) ) );
				}

				break;

			case 'Update':
				$campaign = Scarcity_Samurai_Model::get( 'Campaign' )->find( $campaign_id );

				if ( $campaign === null ) {
					wp_redirect( add_query_arg( array( 'action' => 'edit', 'id' => $campaign_id, 'message' => 'unexpected_error' ) ) );
				} else if ( ( trim( $campaign_name ) !== $campaign[ 'name' ] ) &&
				            Scarcity_Samurai_Campaign::campaign_name_exists( $campaign_name ) )
				{
					wp_redirect( add_query_arg( array( 'action' => 'edit', 'id' => $campaign_id, 'message' => 'already_exists' ) ) );
				} else {
					Scarcity_Samurai_Model::get( 'Campaign' )->update(
						array( 'name' => $campaign_name ),
						array( 'id' => $campaign_id )
					);

					wp_redirect( add_query_arg( array( 'action' => 'edit', 'id' => $campaign_id, 'message' => 'updated' ) ) );
				}

				break;
		}
	}
}
