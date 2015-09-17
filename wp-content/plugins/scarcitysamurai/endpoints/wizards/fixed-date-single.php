<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

if ( ! check_ajax_referer( 'fixed-date-single-wizard', 'security_token', false ) ) {
	exit;
}

extract( json_decode( Scarcity_Samurai_Helper::get_request('data'), true ) );

// -----------------------------
// ---| Create the campaign |---
// -----------------------------

if ( $campaign_name === '' ) {
	Scarcity_Samurai_Wizards::error( 'Campaign name cannot be blank.' );
}

if ( Scarcity_Samurai_Campaign::campaign_name_exists( $campaign_name ) ) {
	Scarcity_Samurai_Wizards::error( 'Campaign name already exists.' );
}

$campaign_id = Scarcity_Samurai_Model::get( 'Campaign' )->insert( array(
	'name' => $campaign_name,
	'active' => true
) );

if ( $campaign_id === false ) {
	Scarcity_Samurai_Wizards::unexpected_error();
}

// --------------------------------------
// ---| Add the page to the campaign |---
// --------------------------------------

if ( $page_id === null ) {
	Scarcity_Samurai_Wizards::error( 'Page must be selected.' );
}

switch ( $page_expiry['too_late_action']['action'] ) {
	case 'do_nothing':
		$too_late_action = array(
			'do_nothing' => true
		);

		break;

	case 'redirect_to_page':
		$redirect_page_id = $page_expiry['too_late_action']['page_id'];

		if ( $redirect_page_id === null ) {
			Scarcity_Samurai_Wizards::error( 'Redirect page must be selected.' );
		}

		$too_late_action = array(
			'redirect' => array(
				'page_id' => $redirect_page_id
			)
		);

		break;

	case 'redirect_to_url':
		$redirect_url = $page_expiry['too_late_action']['url'];

		if ( $redirect_url === '' ) {
			Scarcity_Samurai_Wizards::error( 'Redirect URL must be specified.' );
		}

		if ( ! Scarcity_Samurai_Helper::isURLValid( $redirect_url ) ) {
			Scarcity_Samurai_Wizards::error( 'Redirect URL is invalid.' );
		}

		$too_late_action = array(
			'redirect' => array(
				'url' => Scarcity_Samurai_Helper::add_http_to_url_if_required( $redirect_url )
			)
		);

		break;
}

$available_until = array(
	'enabled' => true,
	'type' => 'fixed',
	'value' => Scarcity_Samurai_Helper::calculate_fixed_time(array(
		'year' => $page_expiry['time']['year'],
		'month' => $page_expiry['time']['month'],
		'day' => $page_expiry['time']['day'],
		'hour' => $page_expiry['time']['hour'],
		'minute' => $page_expiry['time']['minute'],
		'timezone' => $page_expiry['time']['timezone']
	)),
	'timezone' => $page_expiry['time']['timezone'],
	'too_late_action' => $too_late_action
);

$page = Scarcity_Samurai_Model::get( 'Page' )->find( $page_id );

$page_data = array(
	'campaign_id' => $campaign_id,
	'type_id' => Scarcity_Samurai_Page_Type::other_page_type_id(),
	'position' => 0,
	'available_until' => $available_until
);

if ( $page === null ) {
	$page_data['id'] = $page_id;

	$page_id = Scarcity_Samurai_Model::get( 'Page' )->insert( $page_data );

	if ( $page_id === false ) {
		Scarcity_Samurai_Wizards::unexpected_error();
	}
} else {
	if ( ! Scarcity_Samurai_Model::get( 'Page' )->update( $page_data, array( 'id' => $page_id ) ) ) {
		Scarcity_Samurai_Wizards::unexpected_error();
	}
}

// ---------------------
// ---| Add banners |---
// ---------------------

if ( $banner['position'] === 'content' ) { // Inline banners
	$page_id = wp_update_post( array(
		'ID' => $page_id,
		'post_content' => $content
	), true ); // true means return WP_Error object on failure

	if ( is_wp_error( $page_id ) ) {
		Scarcity_Samurai_Wizards::error( "Couldn't update page content. " . $page_id->get_error_message() );
	}
} else if ( in_array( $banner['position'], array( 'header', 'footer' ) ) ) { // Fixed banners
	$banner_id = $banner['banner_id'];

	if ( $banner_id === null ) {
		Scarcity_Samurai_Wizards::error( 'Banner must be selected.' );
	}

	$data = array(
		'enabled' => true,
		'banner_id' => $banner_id,
		'data' => array(
			'show' => $banner['show']
		)
	);

	switch ( $banner['click_action']['action'] ) {
		case 'do_nothing':
			$data['data']['action'] = array(
				'do_nothing' => true
			);

			break;

		case 'redirect_to_page':
			$redirect_page_id = $banner['click_action']['page_id'];

			if ( $redirect_page_id === null ) {
				Scarcity_Samurai_Wizards::error( "Banner's redirect page must be selected." );
			}

			$data['data']['action'] = array(
				'redirect' => array(
					'page_id' => $redirect_page_id
				)
			);

			break;

		case 'redirect_to_url':
			$data['data']['action'] = array(
				'redirect' => array(
					'url' => Scarcity_Samurai_Helper::add_http_to_url_if_required( $banner['click_action']['url'] )
				)
			);

			break;
	}

	// Create or update the header/footer banner
	if ( $banner['position'] === 'header' ) {
		$banner_attributes = Scarcity_Samurai_Page::header_banner_attributes( $page_id );
		$position = 'fixed_top';
	} else {
		$banner_attributes = Scarcity_Samurai_Page::footer_banner_attributes( $page_id );
		$position = 'fixed_bottom';
	}

	if ( $banner_attributes === null ) {
		$data['page_id'] = $page_id;
		$data['position'] = $position;

		$new_record_id = Scarcity_Samurai_Model::get( 'Pages_Banners' )->insert( $data );

		if ( $new_record_id === false ) {
			Scarcity_Samurai_Wizards::unexpected_error();
		}
	} else {
		$where = array(
			'page_id' => $page_id,
			'position' => $position
		);

		if ( ! Scarcity_Samurai_Model::get( 'Pages_Banners' )->update( $data, $where ) ) {
 			Scarcity_Samurai_Wizards::unexpected_error();
		}
	}
}

function page_details( $page_id ) {
	return array(
		'id' => $page_id,
		'title' => Scarcity_Samurai_Helper::get_page_title_by_id( $page_id ),
		'published' => Scarcity_Samurai_Page::is_published( $page_id ),
		'edit_url' => Scarcity_Samurai_Helper::get_edit_page_url_by_id( $page_id ),
		'view_url' => Scarcity_Samurai_Helper::get_page_url_by_id( $page_id ),
		'email_url' => Scarcity_Samurai_Helper::get_page_url_by_id( $page_id, null, true )
	);
}

wp_send_json_success(array(
	'campaign_id' => $campaign_id,
	'page' => page_details( $page_id )
));
