<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

if ( ! check_ajax_referer('fixed-date-multi-wizard', 'security_token', false ) ) {
	exit;
}

extract( json_decode( Scarcity_Samurai_Helper::get_request( 'data' ), true ) );

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

// --------------------------------------------------------------------------
// ---| Add the squeeze page to the campaign, and set its auto responder |---
// --------------------------------------------------------------------------

if ( $squeeze_page_id === null ) {
	Scarcity_Samurai_Wizards::error( 'Squeeze page must be selected.' );
}

if ( $auto_responder === null ) {
	Scarcity_Samurai_Wizards::error( 'Auto responder must be selected.' );
}

$squeeze_page = Scarcity_Samurai_Model::get( 'Page' )->find( $squeeze_page_id );

$page_data = array(
	'campaign_id' => $campaign_id,
	'type_id' => Scarcity_Samurai_Page_Type::squeeze_page_type_id(),
	'position' => 0,
	'available_from' => null
);

if ( $squeeze_page === null ) {
	$page_data['id'] = $squeeze_page_id;
	$page_data['data'] = array(
		'contains_opt_in_form' => true,
		'auto_responder' => $auto_responder
	);

	$page_id = Scarcity_Samurai_Model::get( 'Page' )->insert( $page_data );

	if ( $page_id === false ) {
		Scarcity_Samurai_Wizards::unexpected_error();
	}
} else {
	$page_data['data'] = $squeeze_page['data'];
	$page_data['data']['contains_opt_in_form'] = true;
	$page_data['data']['auto_responder'] = $auto_responder;

	if ( ! Scarcity_Samurai_Model::get( 'Page' )->update( $page_data, array( 'id' => $squeeze_page_id ) ) ) {
		Scarcity_Samurai_Wizards::unexpected_error();
	}
}

// -----------------------------------------------
// ---| Add the content pages to the campaign |---
// -----------------------------------------------

if ( in_array( $squeeze_page_id, $content_page_ids ) ) {
	Scarcity_Samurai_Wizards::error( 'Content pages must be different from the squeeze page.' );
}

if ( count( $content_page_ids ) !== count( array_unique( $content_page_ids ) ) ) {
	Scarcity_Samurai_Wizards::error( 'Content pages must be different.' );
}

$available_from = array(
	'enabled' => true,
	'type' => 'opt_in',
	'page_id' => $squeeze_page_id,
	'value' => 0,
	'too_early_action' => array(
		'error' => 404
	),
	'not_opted_in_action' => array(
		'error' => 404
	)
);

foreach ( $content_page_ids as $index => $content_page_id ) {
	$content_page_position = $index + 1;
	$content_page = Scarcity_Samurai_Model::get( 'Page' )->find( $content_page_id );

	$page_data = array(
		'campaign_id' => $campaign_id,
		'type_id' => Scarcity_Samurai_Page_Type::content_page_type_id(),
		'position' => $content_page_position,
		'available_from' => $available_from
	);

	if ( $content_page === null ) {
		$page_data['id'] = $content_page_id;

		$page_id = Scarcity_Samurai_Model::get( 'Page' )->insert( $page_data );

		if ( $page_id === false ) {
			Scarcity_Samurai_Wizards::unexpected_error();
		}
	} else {
		if ( ! Scarcity_Samurai_Model::get( 'Page' )->update( $page_data, array( 'id' => $content_page_id ) ) ) {
			Scarcity_Samurai_Wizards::unexpected_error();
		}
	}
}

// --------------------------------------------
// ---| Add the offer page to the campaign |---
// --------------------------------------------

if ( $offer_page_id === null ) {
	Scarcity_Samurai_Wizards::error( 'Offer page must be selected.' );
}

if ( $offer_page_id === $squeeze_page_id ) {
	Scarcity_Samurai_Wizards::error( 'Offer page must be different from the squeeze page.' );
}

if ( in_array( $offer_page_id, $content_page_ids ) ) {
	Scarcity_Samurai_Wizards::error( 'Content pages must be different from the offer page.' );
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
	'value' => Scarcity_Samurai_Helper::calculate_fixed_time( array(
		'year' => $page_expiry['time']['year'],
		'month' => $page_expiry['time']['month'],
		'day' => $page_expiry['time']['day'],
		'hour' => $page_expiry['time']['hour'],
		'minute' => $page_expiry['time']['minute'],
		'timezone' => $page_expiry['time']['timezone']
	) ),
	'timezone' => $page_expiry['time']['timezone'],
	'too_late_action' => $too_late_action
);

$offer_page_position = count( $content_page_ids ) + 1;
$offer_page = Scarcity_Samurai_Model::get( 'Page' )->find( $offer_page_id );

$page_data = array(
	'campaign_id' => $campaign_id,
	'type_id' => Scarcity_Samurai_Page_Type::offer_page_type_id(),
	'position' => $offer_page_position,
	'available_from' => $available_from,
	'available_until' => $available_until
);

if ( $offer_page === null ) {
	$page_data['id'] = $offer_page_id;

	$page_id = Scarcity_Samurai_Model::get( 'Page' )->insert( $page_data );

	if ( $page_id === false ) {
		Scarcity_Samurai_Wizards::unexpected_error();
	}
} else {
	if ( ! Scarcity_Samurai_Model::get( 'Page' )->update( $page_data, array( 'id' => $offer_page_id ) ) ) {
		Scarcity_Samurai_Wizards::unexpected_error();
	}
}

// ---------------------
// ---| Add banners |---
// ---------------------

if ( $banner['position'] === 'content' ) { // Inline banners
	$offer_page_id = wp_update_post( array(
		'ID' => $offer_page_id,
		'post_content' => $content
	), true ); // true means return WP_Error object on failure

	if ( is_wp_error( $offer_page_id ) ) {
		Scarcity_Samurai_Wizards::error( "Couldn't update page content. " . $offer_page_id->get_error_message() );
	}
} else if ( in_array( $banner['position'], array( 'header', 'footer' ) ) ) { // Fixed banners
	$banner_id = $banner['banner_id'];

	if ( $banner_id === 0 ) {
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

			if ( $redirect_page_id === 0 ) {
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
		$banner_attributes = Scarcity_Samurai_Page::header_banner_attributes( $offer_page_id );
		$position = 'fixed_top';
	} else {
		$banner_attributes = Scarcity_Samurai_Page::footer_banner_attributes( $offer_page_id );
		$position = 'fixed_bottom';
	}

	if ( $banner_attributes === null ) {
		$data['page_id'] = $offer_page_id;
		$data['position'] = $position;

		$new_record_id = Scarcity_Samurai_Model::get( 'Pages_Banners' )->insert( $data );

		if ( $new_record_id === false ) {
			Scarcity_Samurai_Wizards::unexpected_error();
		}
	} else {
		$where = array(
			'page_id' => $offer_page_id,
			'position' => $position
		);

		if ( ! Scarcity_Samurai_Model::get( 'Pages_Banners' )->update( $data, $where ) ) {
 			Scarcity_Samurai_Wizards::unexpected_error();
		}
	}
}

function page_details( $page_id, $auto_responder = null ) {
	return array(
		'id' => $page_id,
		'title' => Scarcity_Samurai_Helper::get_page_title_by_id( $page_id ),
		'published' => Scarcity_Samurai_Page::is_published( $page_id ),
		'edit_url' => Scarcity_Samurai_Helper::get_edit_page_url_by_id( $page_id ),
		'view_url' => Scarcity_Samurai_Helper::get_page_url_by_id( $page_id ),
		'email_url' => Scarcity_Samurai_Helper::get_page_url_by_id( $page_id, $auto_responder, true )
	);
}

$content_pages = array();

foreach ( $content_page_ids as $content_page_id ) {
	$content_pages[] = page_details( $content_page_id, $auto_responder );
}

$response = array(
	'campaign_id' => $campaign_id,
	'auto_responder' => $auto_responder,
	'auto_responder_short_name' => Scarcity_Samurai_Auto_Responder_Integrator::get_auto_responder_short_name( $auto_responder ),
	'squeeze_page' => page_details( $squeeze_page_id ),
	'content_pages' => $content_pages,
	'offer_page' => page_details( $offer_page_id, $auto_responder )
);

wp_send_json_success( $response );
