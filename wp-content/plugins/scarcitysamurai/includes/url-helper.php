<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

class Scarcity_Samurai_Url_Helper
{
	/**
	 * The built in url_to_postid function doesn't pick up on custom post types
	 * so we need to do some crunching ourselves.
	 *
	 * loosely based on...
	 * http://betterwp.net/wordpress-tips/url_to_postid-for-custom-post-types/
	 */
	public static function url_to_postid( $url ) {
		global $wp, $wp_rewrite;

		$id = url_to_postid( $url );
		if ( $id ) {
			return $id;
		}

		$url = apply_filters( 'url_to_postid', $url );

		// check to see if there is a 'p=N' or 'page_id=N' to match against
		if ( preg_match( '/[?&](p|page_id|attachment_id)=(\d+)/', $url, $values ) ) {
			$id = absint( $values[2] );
			if ( $id ) {
				return $id;
			}
		}

		// check rewrite rules
		$rewrite = $wp_rewrite->wp_rewrite_rules();
		if ( empty( $rewrite ) ) {
			return 0;
		}

		// remove home path
		$home_path = parse_url( home_url(), PHP_URL_PATH );
		$url_path = parse_url( $url, PHP_URL_PATH );
		if ( $home_path && strpos( $url_path, $home_path ) === 0 ) {
			$url_path = substr( $url_path, strlen( $home_path ) );
		}

		// trim leading and trailing slashes
		$url_path = trim( $url_path, '/' );

		foreach( $rewrite as $match => $query ) {
			if ( preg_match("!^$match!", $url_path, $matches ) ) {
				$query = parse_url( $query, PHP_URL_QUERY );
				$query = addslashes( WP_MatchesMapRegex::apply( $query, $matches ) );
				parse_str( $query, $query_vars );

				// filter out non-public query vars
				$query = array();
				foreach ( $query_vars as $key => $value ) {
					if ( in_array( $key, $wp->public_query_vars ) ) {
						$query[ $key ] = $value;
					}
				}

				// from class-wp.php
				$post_type_query_vars = array();
				foreach ( $GLOBALS['wp_post_types'] as $post_type => $t ) {
					if ( $t->query_var ) {
						$post_type_query_vars[ $t->query_var ] = $post_type;
					}
				}

				foreach ( $wp->public_query_vars as $wp_var ) {
					if ( isset( $_REQUEST[ $wp_var ] ) ) {
						$query[ $wp_var ] = $_REQUEST[ $wp_var ];
					} else if ( isset ( $query_vars[ $wp_var ] ) ) {
						$query[ $wp_var ] = $query_vars[ $wp_var ];
					}

					if ( ! empty( $query[ $wp_var ] ) ) {
						if ( ! is_array( $query[ $wp_var ] ) ) {
							$query[ $wp_var ] = (string) $query[ $wp_var ];
						} else {
							foreach ( $query[ $wp_var ] as $vkey => $v ) {
								if ( ! is_object( $v ) ) {
									$query[ $wp_var ][ $vkey ] = (string) $v;
								}
							}
						}
						if ( isset( $post_type_query_vars[ $wp_var ] ) ) {
							$query['post_type'] = $post_type_query_vars[ $wp_var ];
							$query['name'] = $query[ $wp_var ];
						}
					}
				}

				$query['post_type'] = 'product';
				$query = new WP_Query( $query );
				if ( ! empty( $query->posts ) && $query->is_singular ) {
					return $query->post->ID;
				} else {
					return 0;
				}
			}
		}

		return 0;
	}
}
