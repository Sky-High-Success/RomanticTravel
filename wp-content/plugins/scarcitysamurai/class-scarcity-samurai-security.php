<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

class Scarcity_Samurai_Security {

	public static function generate_guid() {
		return md5( uniqid( rand(), true ) );
	}

	public static function user_ip() {
		return $_SERVER['REMOTE_ADDR'];
	}

	// Returns true if the cookie exists.
	// Otherwise, returns false.
	public static function cookie_exists( $key ) {
		return ( self::get_cookie( $key ) !== null );
	}

	// Sets the specified cookie
	public static function set_cookie( $key, $value ) {
		setcookie( $key, $value, strtotime( '+1 year' ), '/', '', '', true );
	}

	// Sets the specified cookie if it doesn't exist
	public static function set_cookie_if_doesnt_exist( $key, $value ) {
		if ( ! self::cookie_exists( $key ) ) {
			self::set_cookie( $key, $value );
		}
	}

	// Returns the cookie value, or null if the cookie doesn't exist.
	public static function get_cookie( $key ) {
		return ( isset( $_COOKIE[$key] ) ? $_COOKIE[$key] : null );
	}

	// This cookie stores a timestamp of the first page load.
	// Since the same page can belong to multiple campaigns, it's not enough to
	// have only the page id (like we do in 'page_load_legacy_cookie_name()'.
	// This is why we have the campaign id as well.
	public static function page_load_cookie_name( $page ) {
		return 'scarcity_samurai_page_load_' . $page['id'] . '_' . $page['campaign_id'];
	}

	// We still need the old cookies (that don't have the campaign id), so that
	// users won't have the timers reset.
	public static function page_load_legacy_cookie_name( $page ) {
		return 'scarcity_samurai_page_load_' . $page['id'];
	}

}
