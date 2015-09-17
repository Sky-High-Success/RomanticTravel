<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

add_action( 'admin_init', array( 'Scarcity_Samurai_Dialogs', 'add_ajax' ) );

class Scarcity_Samurai_Dialogs {

	public static function add_ajax() {
		add_action( 'wp_ajax_ss_create_new_page', array( __CLASS__, 'create_new_page' ) );
	}

	public static function create_new_page_link() {
		wp_enqueue_style( 'ss-dialogs', Scarcity_Samurai_Helper::url( 'stylesheets/css/dialogs.css' ) );
		wp_register_script( 'ss-backbone-utils', Scarcity_Samurai_Helper::url( 'scripts/js/backbone-utils.js' ), array( 'jquery', 'backbone' ) );
		wp_enqueue_script( 'ss-create-new-page-dialog', Scarcity_Samurai_Helper::url( 'scripts/js/dialogs/create-new-page.js' ), array( 'jquery', 'ss-backbone-utils', 'jquery-ui-dialog' ) );

		add_action( 'admin_footer', array( __CLASS__, 'admin_footer' ) );

		$nonce = wp_create_nonce( 'ss-create-new-page-request' );

		// Keep href here so that this behaves as a link in all browsers.
		// In chrome at least removing the href removes the hover underline and cursor.
		echo "<a href='' class='ss-create-new-page-link' data-nonce='$nonce'>Create New Page</a>";
	}

	public static function admin_footer() {
		global $scarcity_samurai_dir;

		Scarcity_Samurai_Helper::echo_html( $scarcity_samurai_dir . 'html/dialogs/create-new-page.php' );
	}

	public static function create_new_page() {
		extract( wp_parse_args( $_REQUEST, array(
			'post_type' => '',
			'post_title' => '',
			'nonce' => ''
		) ) );

		// check to see if the submitted nonce matches with the generated one we created earlier
		// and that the current user has sufficient permissions
		if ( ! wp_verify_nonce( $nonce, 'ss-create-new-page-request' )
			|| ! current_user_can( 'edit_posts' ) )
		{
			wp_send_json_error( "You don't have permission to do that." );
		}

		// hard coded values for new pages...
		$post_title = trim( $post_title );
		$post_status = 'draft';

		if ( $post_title === '' ) {
			wp_send_json_error( 'Page title cannot be empty.' );
		}

		// http://codex.wordpress.org/Function_Reference/wp_insert_post
		// the true value means the returned value will be a WP_Error in the case of something going wrong.
		$post_id = wp_insert_post( compact( 'post_type', 'post_title', 'post_status' ), true );

		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error( $post_id->get_error_message() );
		}

		Scarcity_Samurai_Helper::clear_posts_cache( $post_type );

		wp_send_json_success( array(
			'id' => $post_id,
			'page_select_options' => array(
				'all' => Scarcity_Samurai_Helper::page_select_options(),
				'not_in_campaign' => Scarcity_Samurai_Helper::page_select_options( array(
					'in_campaign' => false
				) )
			)
		) );
	}

}
