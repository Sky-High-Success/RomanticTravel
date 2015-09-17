<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

if ( ! check_ajax_referer( 'page-meta-box', 'security_token', false ) ) {
	exit;
}

extract( json_decode( Scarcity_Samurai_Helper::get_request( 'data' ), true ) );
// page_id - The page we are currently editing
// campaign_id - The campaign user selected in the campaigns select

$page = Scarcity_Samurai_Model::get( 'Page' )->find( $page_id );

if ( $page['campaign_id'] === $campaign_id ) {
	wp_send_json_success();
}

$campaign_pages = Scarcity_Samurai_Campaign::pages( $page['campaign_id'] );

foreach ( $campaign_pages as $campaign_page ) {
	foreach ( array( 'from' => $campaign_page['available_from'],
	                 'until' => $campaign_page['available_until'] ) as $from_until => $available_from_until ) {
		if ( isset( $available_from_until ) &&
		     $available_from_until['enabled'] &&
		     ( $available_from_until['type'] === 'opt_in' ) &&
		     isset( $available_from_until['page_id'] ) &&
		     ( $available_from_until['page_id'] === $page['id'] ) )
		{
			$page_title = Scarcity_Samurai_Helper::get_page_title_by_id( $campaign_page['id'] );
			$campaign = Scarcity_Samurai_Model::get( 'Campaign' )->find( $campaign_page['campaign_id'] );
			$tab_name = ( $from_until === 'from' ? 'Access Restriction' : 'Count Down Timer' );

			wp_send_json_error( "\"$page_title\" page has $tab_name which is relative to opt-in on this page.\n" .
			                    "This $tab_name won't work unless this page belongs to the \"{$campaign['name']}\" campaign." );
		}
	}
}

$contains_opt_in_form =
	Scarcity_Samurai_Campaign::contains_opt_in_form( $page['campaign_id'], array( $page_id ) ); // Exclude page_id

$has_opt_in_references =
	Scarcity_Samurai_Campaign::has_opt_in_references( $page['campaign_id'], array( $page_id ) ); // Exclude page_id

if ( $has_opt_in_references && ! $contains_opt_in_form ) {
	$campaign = Scarcity_Samurai_Model::get( 'Campaign' )->find( $page['campaign_id'] );

	wp_send_json_error( "This will break the \"{$campaign['name']}\" campaign, because it contains opt-in " .
	                    "references,\nand this page is the only one that contains an opt-in form." );
}

// All good
wp_send_json_success();
