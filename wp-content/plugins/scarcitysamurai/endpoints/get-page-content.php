<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

if ( ! check_ajax_referer( 'evergreen-optin-wizard', 'security_token', false ) &&
     ! check_ajax_referer( 'fixed-date-multi-wizard', 'security_token', false ) &&
     ! check_ajax_referer( 'evergreen-pageload-wizard', 'security_token', false ) &&
     ! check_ajax_referer( 'fixed-date-single-wizard', 'security_token', false ) )
{
	exit;
}

extract( json_decode( Scarcity_Samurai_Helper::get_request('data'), true ) );

$content = Scarcity_Samurai_Helper::get_page_content_by_id( $page_id, false ); // Don't parse shortcodes

if ( $content === null ) {
	Scarcity_Samurai_Wizards::error( "Couldn't find the page." );
}

wp_send_json_success( array(
	'content' => $content
) );
