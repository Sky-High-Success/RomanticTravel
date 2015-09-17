<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

add_action( 'init', array( 'Scarcity_Samurai_Page_Meta_Box', 'init' ) );

class Scarcity_Samurai_Page_Meta_Box {

	public static function init() {
		// AJAX hooks
		add_action( 'wp_ajax_ss_check_page_references', array( __CLASS__, 'check_page_references' ) );
		add_action( 'wp_ajax_ss_check_pages_with_opt_in_form', array( __CLASS__, 'check_pages_with_opt_in_form' ) );
		add_action( 'wp_ajax_ss_get_page_auto_responder', array( __CLASS__, 'get_page_auto_responder' ) );
	}

	public static function check_page_references() {
		global $scarcity_samurai_endpoints_dir;

		include( $scarcity_samurai_endpoints_dir . 'check-page-references.php' );
	}

	public static function check_pages_with_opt_in_form() {
		global $scarcity_samurai_endpoints_dir;

		include( $scarcity_samurai_endpoints_dir . 'check-pages-with-opt-in-form.php' );
	}

	public static function get_page_auto_responder() {
		global $scarcity_samurai_endpoints_dir;

		include( $scarcity_samurai_endpoints_dir . 'get-page-auto-responder.php' );
	}

	public static function save_page_meta_box_data( $page_id ) {
		// This function will be called when user creates a new page via the
		// 'Add New Page' link. In this case, we don't want to add Scarcity Samurai
		// page.
		if ( ! isset( $_POST['ss-page-campaign'] ) ) {
			return;
		}

		// Create the Scarcity Samurai page if it doesn't exist yet.
		$page = Scarcity_Samurai_Model::get( 'Page' )->find( $page_id );

		if ( $page === null ) {
			// If user enabled Scarcity Samurai, created a new page, and saved it,
			// we don't won't to create a Scarcity Samurai page (because this page
			// doesn't belong to a campaign yet).
			if ( $_POST['ss-page-campaign'] === '' ) {
				return;
			}

			$page = Scarcity_Samurai_Model::get( 'Page' )->insert( array(
				'id' => $page_id,
				'type_id' => Scarcity_Samurai_Page_Type::other_page_type_id(),
				'position' => Scarcity_Samurai_Campaign::pages_count( $_POST['ss-page-campaign'] )
			) );
		}

		// For all broken opt-in references, reset to 'any campaign page' option.
		self::fix_broken_opt_in_references( $page );

		// If user updated the page with 'No Campaign' selected, set 'campaign_id' to null.
		if ( $_POST['ss-page-campaign'] === '' ) {
			if ( ! Scarcity_Samurai_Model::get( 'Page' )->update( array( 'campaign_id' => null ), array( 'id' => $page_id ) ) ) {
				Scarcity_Samurai_Helper::error( 'Page update failed!' );
			}

			return;
		}

		$campaign_id = intval( $_POST['ss-page-campaign'] );
		$type_id = intval( $_POST['ss-page-type-id'] );

		// ----------------------------
		// ---| Access Restriction |---
		// ----------------------------

		$available_from = array(
			'enabled' => (array_key_exists('ss-page-lock-enable', $_POST) &&
			              ($_POST['ss-page-lock-enable'] === 'on')),
			'type' => $_POST['ss-page-lock-from']
		);

		// Visitors can access this page...
		switch ($_POST['ss-page-lock-from']) {
			case 'opt_in':
				if ($_POST['ss-page-lock-from-optin-page-id'] !== '') {
					$available_from['page_id'] = intval($_POST['ss-page-lock-from-optin-page-id']);
				}

				$available_from['value'] = Scarcity_Samurai_Helper::calculate_time_period(array(
					'days' => $_POST['ss-page-lock-from-optin-days'],
					'hours' => $_POST['ss-page-lock-from-optin-hours'],
					'minutes' => $_POST['ss-page-lock-from-optin-minutes'],
					'seconds' => $_POST['ss-page-lock-from-optin-seconds']
				));

				break;

			case 'fixed':
				$available_from['timezone'] = $_POST['ss-page-lock-from-fixed-timezone'];
				$available_from['value'] = Scarcity_Samurai_Helper::calculate_fixed_time(array(
					'year' => $_POST['ss-page-lock-from-fixed-year'],
					'month' => $_POST['ss-page-lock-from-fixed-month'],
					'day' => $_POST['ss-page-lock-from-fixed-day'],
					'hour' => $_POST['ss-page-lock-from-fixed-hour'],
					'minute' => $_POST['ss-page-lock-from-fixed-minute'],
					'timezone' => $_POST['ss-page-timer-from-fixed-timezone']
				));

				break;
		}

		// If a visitor attempts to access this page before it is available...
		switch ($_POST['ss-page-lock-early']) {
			case 'page_not_found':
				$available_from['too_early_action'] = array(
					'error' => 404
				);

				break;

			case 'redirect_to_page':
				if ($_POST['ss-page-lock-early-redirect-page-id'] === '') {
					$available_from['too_early_action'] = array(
						'error' => 404
					);
				} else {
					$available_from['too_early_action'] = array(
						'redirect' => array(
							'page_id' => intval($_POST['ss-page-lock-early-redirect-page-id'])
						)
					);
				}

				break;

			case 'redirect_to_url':
				$redirect_url = trim($_POST['ss-page-lock-early-redirect-url']);

				if ($redirect_url === '') {
					$available_from['too_early_action'] = array(
						'error' => 404
					);
				} else {
					$available_from['too_early_action'] = array(
						'redirect' => array(
							'url' => Scarcity_Samurai_Helper::add_http_to_url_if_required($redirect_url)
						)
					);
				}

				break;
		}

		// If a visitor has not opted-in...
		if ($_POST['ss-page-lock-from'] === 'opt_in') {
			switch ($_POST['ss-page-lock-not-opted-in']) {
				case 'page_not_found':
					$available_from['not_opted_in_action'] = array(
						'error' => 404
					);

					break;

				case 'redirect_to_page':
					if ($_POST['ss-page-lock-not-opted-in-redirect-page-id'] === '') {
						$available_from['not_opted_in_action'] = array(
							'error' => 404
						);
					} else {
						$available_from['not_opted_in_action'] = array(
							'redirect' => array(
								'page_id' => intval($_POST['ss-page-lock-not-opted-in-redirect-page-id'])
							)
						);
					}

					break;

				case 'redirect_to_url':
					$redirect_url = trim($_POST['ss-page-lock-not-opted-in-redirect-url']);

					if ($redirect_url === '') {
						$available_from['not_opted_in_action'] = array(
							'error' => 404
						);
					} else {
						$available_from['not_opted_in_action'] = array(
							'redirect' => array(
								'url' => Scarcity_Samurai_Helper::add_http_to_url_if_required($redirect_url)
							)
						);
					}

					break;
			}
		}

		// --------------------------
		// ---| Count Down Timer |---
		// --------------------------

		$available_until = array(
			'enabled' => (array_key_exists('ss-page-timer-enable', $_POST) &&
			              ($_POST['ss-page-timer-enable'] === 'on')),
			'type' => $_POST['ss-page-timer-from']
		);

		// Count down relative to...
		switch ($_POST['ss-page-timer-from']) {
			case 'page_load':
				$available_until['value'] = Scarcity_Samurai_Helper::calculate_time_period(array(
					'days' => $_POST['ss-page-timer-from-page-load-days'],
					'hours' => $_POST['ss-page-timer-from-page-load-hours'],
					'minutes' => $_POST['ss-page-timer-from-page-load-minutes'],
					'seconds' => $_POST['ss-page-timer-from-page-load-seconds']
				));

				break;

			case 'opt_in':
				if ($_POST['ss-page-timer-from-optin-page-id'] !== '') {
					$available_until['page_id'] = intval($_POST['ss-page-timer-from-optin-page-id']);
				}

				$available_until['value'] = Scarcity_Samurai_Helper::calculate_time_period(array(
					'days' => $_POST['ss-page-timer-from-optin-days'],
					'hours' => $_POST['ss-page-timer-from-optin-hours'],
					'minutes' => $_POST['ss-page-timer-from-optin-minutes'],
					'seconds' => $_POST['ss-page-timer-from-optin-seconds']
				));

				break;

			case 'fixed':
				$available_until['timezone'] = $_POST['ss-page-timer-from-fixed-timezone'];
				$available_until['value'] = Scarcity_Samurai_Helper::calculate_fixed_time(array(
					'year' => $_POST['ss-page-timer-from-fixed-year'],
					'month' => $_POST['ss-page-timer-from-fixed-month'],
					'day' => $_POST['ss-page-timer-from-fixed-day'],
					'hour' => $_POST['ss-page-timer-from-fixed-hour'],
					'minute' => $_POST['ss-page-timer-from-fixed-minute'],
					'timezone' => $_POST['ss-page-timer-from-fixed-timezone']
				));

				break;
		}

		// When the timer reaches zero...
		switch ($_POST['ss-page-timer-expired']) {
			case 'do_nothing':
				$available_until['too_late_action'] = array(
					'do_nothing' => true
				);

				break;

			case 'redirect_to_page':
				if ($_POST['ss-page-timer-expired-redirect-page-id'] === '') {
					$available_until['too_late_action'] = array(
						'do_nothing' => true
					);
				} else {
					$available_until['too_late_action'] = array(
						'redirect' => array(
							'page_id' => intval($_POST['ss-page-timer-expired-redirect-page-id'])
						)
					);
				}

				break;

			case 'redirect_to_url':
				$redirect_url = trim($_POST['ss-page-timer-expired-redirect-url']);

				if ($redirect_url === '') {
					$available_until['too_late_action'] = array(
						'do_nothing' => true
					);
				} else {
					$available_until['too_late_action'] = array(
						'redirect' => array(
							'url' => Scarcity_Samurai_Helper::add_http_to_url_if_required($redirect_url)
						)
					);
				}

				break;
		}

		// -----------------
		// ---| Banners |---
		// -----------------

		foreach ( array( 'header', 'footer' ) as $header_footer ) {
			if ( ! isset( $_POST["ss-page-${header_footer}-banner-id"] ) ||
			     $_POST["ss-page-${header_footer}-banner-id"] === '' )
			{
				continue;
			}

			$data = array(
				'enabled' => ( isset( $_POST["ss-page-${header_footer}-banner-enable"] ) &&
				               ( $_POST["ss-page-${header_footer}-banner-enable"] === 'on' ) ),
				'banner_id' => intval( $_POST["ss-page-${header_footer}-banner-id"] ),
				'data' => array(
					'show' => array(
						'type' => $_POST["ss-page-${header_footer}-banner-show-type"]
					)
				)
			);

			// Show time
			if ( $data['data']['show']['type'] === 'page_load' ) {
				$data['data']['show']['value'] = intval( $_POST["ss-page-${header_footer}-banner-show-value"] );
			}

			// Click action
			switch ( $_POST["ss-page-${header_footer}-banner-action"] ) {
				case 'do_nothing':
					$data['data']['action'] = array(
						'do_nothing' => true
					);

					break;

				case 'redirect_to_page':
					if ( $_POST["ss-page-${header_footer}-banner-action-redirect-page-id"] === '' ) {
						$data['data']['action'] = array(
							'do_nothing' => true
						);
					} else {
						$data['data']['action'] = array(
							'redirect' => array(
								'page_id' => intval( $_POST["ss-page-${header_footer}-banner-action-redirect-page-id"] )
							)
						);
					}

					break;

				case 'redirect_to_url':
					$redirect_url = trim( $_POST["ss-page-${header_footer}-banner-action-redirect-url"] );

					if ( $redirect_url === '' ) {
						$data['data']['action'] = array(
							'do_nothing' => true
						);
					} else {
						$data['data']['action'] = array(
							'redirect' => array(
								'url' => Scarcity_Samurai_Helper::add_http_to_url_if_required( $redirect_url )
							)
						);
					}

					break;
			}

			// Create or update the header/footer banner
			if ( $header_footer === 'header' ) {
				$banner_attributes = Scarcity_Samurai_Page::header_banner_attributes( $page_id );
				$position = 'fixed_top';
			} else {
				$banner_attributes = Scarcity_Samurai_Page::footer_banner_attributes( $page_id );
				$position = 'fixed_bottom';
			}

			if ( $banner_attributes === null ) {
				$data['page_id'] = $page_id;
				$data['position'] = $position;

				Scarcity_Samurai_Model::get( 'Pages_Banners' )->insert( $data );
			} else {
				$where = array(
					'page_id' => $page_id,
					'position' => $position
				);

				Scarcity_Samurai_Model::get( 'Pages_Banners' )->update( $data, $where );
			}
		}

		// ---------------
		// ---| Other |---
		// ---------------

		$data = $page['data'];
		$data['contains_opt_in_form'] =
			isset($_POST['ss-contains-opt-in-form']) && ($_POST['ss-contains-opt-in-form'] === 'on');

		if ( ! $data['contains_opt_in_form'] || ( $_POST['ss-auto-responder-select'] === '' ) ) {
			unset( $data['auto_responder'] );
		} else {
			$data['auto_responder'] = $_POST['ss-auto-responder-select'];
		}

		$update_data = compact( 'campaign_id', 'type_id', 'available_from', 'available_until', 'data' );

		if ( ! Scarcity_Samurai_Model::get( 'Page' )->update( $update_data, array( 'id' => $page_id ) ) ) {
			Scarcity_Samurai_Helper::error( 'Page update failed!' );
		}
	}

	// It may happen that user selects opt-in option relative to specific page,
	// while this page is not part of the campaign (e.g. user just set that
	// page's campaign to 'No Campaign' in another tab/browser).
	// In this case, we don't want to store such opt-in reference to that page,
	// as it won't work anyway. So, we remove that 'page_id', and the meaning
	// becomes opt-in on 'any campaign page'.
	private static function fix_broken_opt_in_references( $page ) {
		$current_campaign_pages = Scarcity_Samurai_Campaign::pages( $page['campaign_id'] );

		foreach ( $current_campaign_pages as $current_campaign_page ) {
			foreach ( array( 'from' => $current_campaign_page['available_from'],
			                 'until' => $current_campaign_page['available_until'] ) as $from_until => $available_from_until ) {
				if ( ( $current_campaign_page['id'] !== $page['id'] ) &&
				     isset( $available_from_until ) &&
				     ( $available_from_until['type'] === 'opt_in' ) &&
				     isset( $available_from_until['page_id'] ) &&
				     ( $available_from_until['page_id'] === $page['id'] ) )
				{
					unset( $available_from_until['page_id'] );
					$update_data = array( "available_{$from_until}" => $available_from_until );

					if ( ! Scarcity_Samurai_Model::get( 'Page' )->update( $update_data, array( 'id' => $current_campaign_page['id'] ) ) ) {
						Scarcity_Samurai_Helper::error( 'Page update failed!' );
					}
				}
			}
		}
	}

}
