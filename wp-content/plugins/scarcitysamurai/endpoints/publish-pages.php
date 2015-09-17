<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

if ( !check_ajax_referer( 'campaign-edit', 'security_token', false ) &&
     !check_ajax_referer( 'evergreen-optin-wizard', 'security_token', false ) &&
     !check_ajax_referer( 'fixed-date-multi-wizard', 'security_token', false ) &&
     !check_ajax_referer( 'evergreen-pageload-wizard', 'security_token', false ) &&
     !check_ajax_referer( 'fixed-date-single-wizard', 'security_token', false ) )
{
	exit;
}

$data = json_decode( Scarcity_Samurai_Helper::get_request('data'), true );
extract( $data );

if ( isset( $campaign_id ) ) {
	if ( Scarcity_Samurai_Campaign::publish_all_pages( $campaign_id ) ) {
		$email_urls = array();
		$campaign_contains_opt_in_form = Scarcity_Samurai_Campaign::contains_opt_in_form( $campaign_id );
		$auto_responder = ( $campaign_contains_opt_in_form ? Scarcity_Samurai_Campaign::autoresponder( $campaign_id ) : null );

		$pages = Scarcity_Samurai_Campaign::pages( $campaign_id );
		foreach ( $pages as $page ) {
			$email_urls[ $page['id'] ] = Scarcity_Samurai_Helper::get_page_url_by_id( $page['id'], $auto_responder );
		}
		wp_send_json_success( compact( 'email_urls' ) );
	} else {
		wp_send_json_error();
	}

	return;
}

if ( isset( $page_id ) ) {
	if ( Scarcity_Samurai_Page::publish( $page_id ) ) {
		wp_send_json_success();
	} else {
		wp_send_json_error();
	}

	return;
}

Scarcity_Samurai_Wizards::error( 'Page id or campaign id must be specified.' );
