<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

class Scarcity_Samurai_User extends Scarcity_Samurai_Model {

	public $short_name, $table_name;
	protected $fields;

	public function __construct() {
		global $wpdb;

		$this->short_name = 'user';
		$this->table_name = $wpdb->prefix . 'scarcity_samurai_users';

		$this->fields = array(
			'id' => array(
				'type' => 'integer'
			),
			'email' => array(
				'type' => 'string'
			)
		);
	}

	public static function on_activate() {
		global $wpdb, $scarcity_samurai_charset_collate;

		self::$sql = "
			CREATE TABLE IF NOT EXISTS " . Scarcity_Samurai_Model::get( 'User' )->table_name . " (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				email VARCHAR(100) NOT NULL DEFAULT '',
				PRIMARY KEY (id),
				UNIQUE (email)
			) $scarcity_samurai_charset_collate";

		$wpdb->query(self::$sql);
	}

	protected function sanitize_before_write( $data ) {
		if ( array_key_exists( 'email', $data ) ) {
			$data['email'] = trim( $data['email'] );
		}

		return $data;
	}

	// Subscribes user with the specified email to a form in the specified page,
	// and adds the subscription token to this user.
	// If the user doesn't exist yet, creates it.
	// If the user already subscribed to a form on the specified page,
	// does nothing and returns true.
	// On success, returns true.
	// On failure (e.g. if the token is empty), returns false.
	public static function subscribe( $email, $token, $page_id ) {
		if ( $token === '' ) {
			return false;
		}

		$user = Scarcity_Samurai_Model::get( 'User' )->find_by( array(
			'email' => trim( $email )
		) );

		if ( $user === null ) {
			$user_id = Scarcity_Samurai_Model::get( 'User' )->insert( array(
				'email' => $email
			) );
		} else {
			$user_id = $user['id'];
		}

		Scarcity_Samurai_Model::get( 'Token' )->insert( array(
			'user_id' => $user_id,
			'token' => $token
		) );

		$campaign_id = Scarcity_Samurai_Page::campaign_id( $page_id );

		$record = Scarcity_Samurai_Model::get( 'Users_Subscriptions' )->find_by( array(
			'user_id' => $user_id,
			'page_id' => $page_id,
			'campaign_id' => $campaign_id
		) );

		if ( $record !== null ) {
			return true;
		}

		if ( Scarcity_Samurai_Model::get( 'Users_Subscriptions' )->insert( array(
			'user_id' => $user_id,
			'page_id' => $page_id,
			'campaign_id' => $campaign_id,
			'subscription_time' => time()   // Current Unix timestamp
		) ) === false ) {
			return false;
		} else {
			return true;
		}
	}

	// Returns:
	//                            true - if user is allowed to see the page
	//   array('redirect_to' => <URL>) - if user should be redirected
	//           array('error' => 404) - if user should see "Page Not Found"
	public static function allowed_to_see_page($page) {
		// Allow admin user to see the page
		if ( current_user_can( 'edit_post', $page['id'] ) ) {
			return true;
		}

		if ( empty( $page['campaign_id'] ) ) {
			return true;
		}

		if ( $page['available_from']['enabled'] &&
		     ( $page['available_from']['type'] === 'opt_in' ) ) {
			$subscription_time = self::subscription_time();

			if ( $subscription_time === null ) {
				return $page['available_from']['not_opted_in_action'];
			}
		}

		$now = time();

		if ( $page['available_from']['enabled'] ) {
			$start_time = Scarcity_Samurai_Page::start_time( $page );

			if ( $page['available_from']['enabled'] &&
			    ( $start_time !== null ) && ( $now < $start_time ) ) {
				return $page['available_from']['too_early_action'];
			}
		}

		if ( $page['available_until']['enabled'] ) {
			$end_time = Scarcity_Samurai_Page::end_time( $page );

			if ( ( $end_time !== null ) && ( $now > $end_time ) ) {
				return $page['available_until']['too_late_action'];
			}
		}

		return true;
	}

	// If current page's access restriction is set to 'Opt-in on specific page',
	// returns the time when the current user subscribed to this page.
	// If current page's access restriction is set to 'Opt-in on any campaign page',
	// returns the earliest time the current user subscribed to a page in the campaign.
	public static function subscription_time() {
		$page = Scarcity_Samurai_Helper::current_page();
		$user_id = Scarcity_Samurai_Helper::get_user_id();

		if ( $user_id === null ) {
			return null;
		}

		$campaign_id = $page['campaign_id'];

		if ( isset( $page['available_from']['page_id'] ) ) {
			$record = Scarcity_Samurai_Model::get( 'Users_Subscriptions' )->find_by( array(
				'user_id' => $user_id,
				'page_id' => $page['available_from']['page_id'],
				'campaign_id' => $campaign_id
			) );

			return $record['subscription_time'];
		}

		// Get all pages of the campaign which contains an opt-in form.
		$pages = array();

		foreach ( Scarcity_Samurai_Campaign::pages( $campaign_id ) as $p ) {
			if ( Scarcity_Samurai_Page::contains_opt_in_form( $p ) ) {
				$pages[] = $p;
			}
		}

		// Collect the subscription times of pages to which user subscribed.
		$subscription_times = array();

		foreach ( $pages as $p ) {
			$record = Scarcity_Samurai_Model::get( 'Users_Subscriptions' )->find_by( array(
				'user_id' => $user_id,
				'page_id' => $p['id'],
				'campaign_id' => $campaign_id
			) );

			if ( isset( $record['subscription_time'] ) ) {
				$subscription_times[] = $record['subscription_time'];
			}
		}

		if ( empty( $subscription_times ) ) {
			return null;
		}

		// If user subscribed to multiple pages in this campaign, pick the
		// earliest subscription time.
		return min( $subscription_times );
	}

}
