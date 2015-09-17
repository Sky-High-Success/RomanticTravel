<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/
require_once( dirname( __FILE__ ) . '/includes/url-helper.php' );

class Scarcity_Samurai_Helper {

	private static $current_page_id = null, $current_user_id = null;
	private static $current_page = null;
	private static $posts_cache = array();

	public static $months = array(
		1 => 'Jan',
		2 => 'Feb',
		3 => 'Mar',
		4 => 'Apr',
		5 => 'May',
		6 => 'Jun',
		7 => 'Jul',
		8 => 'Aug',
		9 => 'Sep',
		10 => 'Oct',
		11 => 'Nov',
		12 => 'Dec'
	);

	public static function pluck( $array, $key ) {
		$return = array();

		foreach ( $array as $item ) {
			$return[] = $item[ $key ];
		}

		return $return;
	}

	// If the key doesn't exist, returns ''.
	public static function get_request( $key ) {
		if ( ! array_key_exists( $key, $_REQUEST ) ) {
			return '';
		}

		$value = $_REQUEST[$key];

		if ( gettype( $value ) === 'array' ) {
			return array_map( 'stripslashes_deep', $value );
		} else {
			return stripslashes( $value );
		}
	}

	public static function build_html_element( $tag, $attributes ) {
		libxml_use_internal_errors( true ); // Disables the standard libxml errors and enables user error handling

		$result = array();

		foreach ( $attributes as $key => $value ) {
			if ( $value !== null ) {
				$result[] = ( $key . '="' . esc_attr( $value ) . '"' );
			}
		}

		$result = join( ' ', $result );
		$result = "<$tag $result></$tag>";

		return $result;
	}

	public static function load_html( $filename, $vars = array() ) {
		extract( $vars );

		ob_start();
		include( $filename );
		$html = ob_get_clean();

		return $html;
	}

	public static function echo_html( $filename, $vars = array() ) {
		echo self::load_html( $filename, $vars );
	}

	// $attributes should be a string like: 'id="17" redirect-url="http://yahoo.com"'
	// On success, returns something like: array(
	//   'id' => '17'
	//   'redirect-url' => 'http://yahoo.com'
	// )
	// If it cannot parse $attributes properly, returns null.
	public static function parse_xml_attributes( $attributes ) {
		libxml_use_internal_errors( true ); // Disables the standard libxml errors and enables user error handling

		$result = array();

		// & is not allowed to appear in XML attribute value. For example, this is invalid:
		//   'id="17" redirect-url="http://yahoo.com?a=1&b=2"'
		// Therefore, we convert & to &amp;, parse the XML, and then convert it back to &.
		$attributes = preg_replace('/&/', '&amp;', $attributes);

		try {
			$xml = simplexml_load_string( "<element {$attributes} />" );
		} catch ( Exception $e ) {
			return null;
		}

		if ( $xml === false ) {
			return null;
		}

		foreach ( $xml->attributes() as $key => $value ) {
			$result[$key] = (string)$value;
			$result[$key] = preg_replace('/&amp;/', '&', $result[$key]);
		}

		return $result;
	}

	public static function current_page_id() {
		global $post;

		// Return the cached result, if exists.
		if ( self::$current_page_id !== null ) {
			return self::$current_page_id;
		}

		// if we are in the admin section then we want to return the page we are
		// editing rather than attempting to interpret the request uri.
		if ( is_admin() ) {
			self::$current_page_id = ( $post === null ? null : $post->ID );
			return self::$current_page_id;
		}

		$permalink = $_SERVER['REQUEST_URI'];
		$page_id = Scarcity_Samurai_Url_Helper::url_to_postid( $permalink );

		// don't attempt to override with $post->ID unless this is a singular
		// page.  ie. not a category or list of posts etc...
		if ( $page_id === 0 && is_singular() ) {
			$page_id = ( $post === null ? null : $post->ID );
		}

		// don't cache page id if we couldn't find it... it may still be found
		// later after more things have been initialised.
		if ( $page_id > 0 ) {
			self::$current_page_id = $page_id;
		}

		return $page_id;
	}

	public static function current_page() {
		// Return the cached result, if exists.
		if ( self::$current_page !== null ) {
			return self::$current_page;
		}

		$page = Scarcity_Samurai_Model::get( 'Page' )->find( self::current_page_id() );

		self::$current_page = $page;

		return $page;
	}

	public static function get_page_url_by_id( $page_id, $auto_responder = null, $is_published_check = false ) {
		$page_url = get_permalink( $page_id );

		if ( $is_published_check && ! Scarcity_Samurai_Page::is_published( $page_id ) ) {
			return 'Not available until published';
		}

		if ( ( $auto_responder === null ) ||
		     ( ! in_array( $auto_responder, Scarcity_Samurai_Auto_Responder_Integrator::supported_auto_responders() ) ) ||
		     ( ! Scarcity_Samurai_Page::url_requires_token( $page_id ) ) ) {
			return $page_url;
		}

		$token_placeholder = Scarcity_Samurai_Auto_Responder_Integrator::get_auto_responder_token_placeholder( $auto_responder, $page_id );

		return $page_url . ( strpos( $page_url, '?' ) === false ? '?' : '&' ) .
		       'token=' . $token_placeholder;
	}

	public static function get_edit_page_url_by_id($page_id) {
		return get_edit_post_link($page_id, '');
	}

	public static function get_page_title_by_id($page_id) {
		$post = get_post($page_id);
		$page_title = trim( $post->post_title );

		if ( empty( $page_title ) ) {
			$page_title = __( '(no title)' );
		}

		return $page_title;
	}

	public static function get_page_title_by_url( $url ) {
		$page_id = url_to_postid( $url );

		if ( $page_id === 0 ) {
			return null;
		}

		return self::get_page_title_by_id( $page_id );
	}

	// Returns page content with all shortcodes parsed.
	// To get the raw content, without parsing the shortcodes, pass false as
	// a second parameter.
	// If the page doesn't exist, returns null.
	public static function get_page_content_by_id( $page_id, $parse_shortcodes = true ) {
		// Get the page
		$page = get_page( $page_id );

		if ( $page === null ) {
			return null;
		}

		// Get the raw content
		$content = $page->post_content;

		if ( $parse_shortcodes ) {
			// HACK: see if we are running woocommerce and include their
			// include/wc-template-functions.php file so that things don't die with
			// fatal errors due to non existant functions
			$woocommerce_plugin = 'woocommerce/woocommerce.php';
			if ( is_plugin_active( $woocommerce_plugin ) ) {
				$file = ( is_plugin_active_for_network( $woocommerce_plugin ) ? WPMU_PLUGIN_DIR : WP_PLUGIN_DIR )
					. '/woocommerce/includes/wc-template-functions.php';
				if ( file_exists( $file ) ) require_once( $file );
			}

			// Parse all the shortcodes
			$content = do_shortcode( $content );
		}

		return $content;
	}

	public static function page_link($page_id) {
		$edit_page_url = Scarcity_Samurai_Helper::get_edit_page_url_by_id($page_id);
		$page_title = Scarcity_Samurai_Helper::get_page_title_by_id($page_id);

		return '<a href="' . esc_attr($edit_page_url) . '">' . $page_title . '</a>';
	}

	public static function add_token_to_url_if_required($url, $token) {
		$page_id = url_to_postid($url);

		if (Scarcity_Samurai_Page::url_requires_token($page_id)) {
			$url .= (strpos($url, '?') === false ? '?' : '&') . "token=$token";
		}

		return $url;
	}

	public static function add_http_to_url_if_required( $url ) {
		if ( preg_match( '/^https?:\/\//', $url ) === 0 ) {
			$url = "http://$url";
		}

		return $url;
	}

	private static function get_content_shortcodes($content, $tag, $attribute) {
		preg_match_all('/'. get_shortcode_regex() . '/s', $content, $matches);

		$count = count($matches[0]);

		$shortcodes = array();

		for ($i = 0; $i < $count; $i++) {
			$shortcode_tag = $matches[2][$i];
			$content = $matches[5][$i];

			// If there are no attributes, $attributes will be ''
			$attributes = shortcode_parse_atts($matches[3][$i]);

			if ((($tag === null) || ($tag === $shortcode_tag)) &&
			    (($attribute === null) || (!empty($attributes) && array_key_exists($attribute, $attributes)))) {
				$shortcode = array();

				$shortcode['tag'] = $shortcode_tag;
				$shortcode['content'] = $content;
				$shortcode['attributes'] = $attributes;

				$shortcodes[] = $shortcode;
			}

			$shortcodes += self::get_content_shortcodes($content, $tag, $attribute);
		}

		return $shortcodes;
	}

	// Returns an array of all shortcodes in the specified page.
	// If page id is not specified, the current page is assumed.
	// If the tag is provided, returns only the shortcodes with this tag.
	// If the attribute is provided, returns only the shortcodes with this attribute.
	// Item in the resulting array may look like:
	//   array(
	//     'tag' => 'ss-timer',
	//     'content' => '',
	//     'attributes' => array(
	//       'id' => 34
	//     )
	//   )
	public static function get_shortcodes( $page_id = null, $tag = null, $attribute = null ) {
		if ( $page_id === null ) {
			$page_id = self::current_page_id();
		}

		$page_content = self::get_page_content_by_id( $page_id, false ); // false means do not parse shortcodes

		return self::get_content_shortcodes( $page_content , $tag, $attribute );
	}

	public static function build_css( $css_data ) {
		$css_array = array();

		foreach ( $css_data as $key => $value ) {
			$css_array[] = "\t$key: $value;";
		}

		return join( "\n", $css_array );
	}

	// Returns user id by the specified token.
	// If the token is not specified:
	// - check if the URL has a token
	// - if not, check if the cookie has a token
	// If the token wasn't found, or the specified token doesn't exist
	// in the system, returns null.
	public static function get_user_id( $token = null ) {
		// Return the cached result, if exists.
		if ( self::$current_user_id !== null ) {
			return self::$current_user_id;
		}

		$user_id = null;

		if ( $token === null ) {
			$token = ( array_key_exists( 'token', $_REQUEST ) ?
			           $_REQUEST['token'] :
			           Scarcity_Samurai_Security::get_cookie( 'scarcity_samurai_token' ) );
		}

		if ( $token !== null ) {
			$token_record = Scarcity_Samurai_Model::get( 'Token' )->find_by( array(
				'token' => $token
			) );

			if ( $token_record !== null ) {
				$user_id = $token_record['user_id'];
			}
		}

		self::$current_user_id = apply_filters( 'scarcity_samurai_get_user_id', $user_id );

		return self::$current_user_id;
	}

	public static function redirect_to_404() {
		if ( did_action( 'template_redirect' ) === 0 ) {
			// Will be called if 'redirect_to_404()' is called before 'template_redirect'
			add_action( 'template_redirect', array( __CLASS__, '_redirect_404' ) );
		} else {
			// Will be called if 'redirect_to_404()' is called in 'template_redirect' or afterwards
			self::_redirect_404();
		}
	}

 	// This function must be public because it is called by 'template_redirect'.
	// To redirect to 404 use Scarcity_Samurai_Helper::redirect_to_404()
	public static function _redirect_404() {
		global $wp_query;
		$wp_query->set_404();
	}

	public static function parse_time_period( $seconds ) {
		$days = (int)( $seconds / 86400 );
		$seconds -= $days * 86400;

		$hours = (int)( $seconds / 3600 );
		$seconds -= $hours * 3600;

		$minutes = (int)( $seconds / 60 );
		$seconds -= $minutes * 60;

		return compact('days', 'hours', 'minutes', 'seconds');
	}

	public static function format_time_period( $seconds ) {
		extract( self::parse_time_period( $seconds ) );

		if ( $days > 0 ) {
			return "$days day" . ( $days === 1 ? '' : 's' );
		}

		if ( $hours > 0 ) {
			return "$hours hour" . ( $hours === 1 ? '' : 's' );
		}

		if ( $minutes > 0 ) {
			return "$minutes minute" . ( $minutes === 1 ? '' : 's' );
		}

		return "$seconds second" . ( $seconds === 1 ? '' : 's' );
	}

	public static function calculate_time_period( $data ) {
		extract( $data );

		return 86400 * intval( $days ) +
		        3600 * intval( $hours ) +
		          60 * intval( $minutes ) +
		               intval( $seconds );
	}

	public static function parse_fixed_time($value, $timezone) {
		$value += self::timezone_offset($timezone);

		$year = intval(date('Y', $value));
		$month = intval(date('m', $value));
		$day = intval(date('d', $value));
		$hour = intval(date('H', $value));
		$minute = intval(date('i', $value));

		return compact('year', 'month', 'day', 'hour', 'minute');
	}

	public static function timezone_offset($timezone) {
		$date_timezone = new DateTimeZone($timezone);

		return $date_timezone->getOffset(new DateTime());
	}

	public static function calculate_fixed_time($data) {
		extract($data);

		$unix_timestamp = gmmktime(intval($hour), intval($minute), 0, intval($month), intval($day), intval($year));

		return $unix_timestamp - self::timezone_offset($timezone);
	}

	// Usage:
	//   Scarcity_Samurai_Helper::timezone_select(array(
	//     'name' => 'ss-page-lock-from-fixed-timezone',
	//     'selected' => $timezone
	//   ));
	public static function timezone_select( $data ) {
		extract( $data );

		$select_options = wp_timezone_choice( $selected );

		$select_options = simple_html_1_11_str_get_html( $select_options );

		// Remove the 'Manual Offsets' section
		$manual_offsets = $select_options->find( 'optgroup[label="Manual Offsets"]' );

		if ( count( $manual_offsets ) > 0 ) {
			$manual_offsets[ 0 ]->outertext = '';
		}

		$name = isset( $name ) ? "name='$name'" : '';

		// <option></option> is needed for select2 'placeholder' to work.
		// See select2 docs: http://ivaynberg.github.io/select2/#documentation
		echo "
			<select class='ss-timezone-select' $name>
				<option></option>
				$select_options
			</select>
		";
	}

	public static function user_timezone() {
		$timezone = get_option('timezone_string');

		return ($timezone === '' ? 'UTC' : $timezone);
	}

	public static function dropdown_page_types() {
		$result = "<select class='ss-page-type'>";

		foreach ( Scarcity_Samurai::$supported_post_types as $post_type ) {
			$post_type_obj = get_post_type_object( $post_type );
			if ( $post_type_obj !== null ) {
				$result .= "<option value='{$post_type}'>{$post_type_obj->labels->singular_name}</option>";
			}
		}

		$result .= '</select>';

		echo $result;
	}

	public static function clear_posts_cache( $post_type ) {
		self::$posts_cache[$post_type] = null;
	}

	private static function get_posts( $data ) {
		extract( $data );

		$exclude_posts_that_belong_to_campaign =
			isset( $in_campaign ) && ( $in_campaign === false );

		$cache_key = json_encode( compact( 'post_type', 'exclude_posts_that_belong_to_campaign' ) );

		if ( isset( self::$posts_cache[$post_type][$cache_key] ) ) {
			return self::$posts_cache[$post_type][$cache_key];
		}

		global $wpdb;

		$exclude_post_ids = '';

		if ( $exclude_posts_that_belong_to_campaign ) {
			$post_ids_that_belong_to_campaign = Scarcity_Samurai_Page::page_ids_that_belong_to_campaign();

			if ( ! empty( $post_ids_that_belong_to_campaign ) ) {
				$post_ids_that_belong_to_campaign = join( ',', $post_ids_that_belong_to_campaign );
				$exclude_post_ids = "AND ID NOT IN ($post_ids_that_belong_to_campaign)";
			}
		}

		$query = "
			SELECT ID, post_title FROM {$wpdb->posts}
			WHERE post_type = '$post_type'
			AND post_status IN ('draft', 'future', 'pending', 'private', 'publish')
			$exclude_post_ids
			ORDER BY post_title ASC
		";

		$posts = $wpdb->get_results($query);

		self::$posts_cache[$post_type][$cache_key] = $posts;

		return $posts;
	}

	// Generates the <option>s for pages select.
	// Creating an <optgroup> for each supported post type.
	// Optional settings:
	//   * contains_opt_in_form - When true returns only those pages which
	//                            contain an opt-in form.
	//   * selected             - <option> value hat should be selected by default.
	//   * first_option         - The HTML of the first <option>. When not specified,
	//                            an empty <option></option> will be created to
	//                            support select2 placeholder.
	//   * campaign_id          - When specified, only pages with this campaign id
	//                            will be included.
	//   * in_campaign          - When false, only pages that do not belong to any
	//                            campaign will be included.
	//   * exclude_page_ids     - Page ids which won't be included.
	public static function page_select_options( $settings = array() ) {
		extract( $settings );

		$selected_page_id = ( isset( $selected ) ? $selected : null );
		$contains_opt_in_form = isset( $contains_opt_in_form ) && ( $contains_opt_in_form === true );
		$include_only_free_pages = isset( $in_campaign ) && ( $in_campaign === false );
		$exclude_page_ids = ( isset( $exclude_page_ids ) ? $exclude_page_ids : array() );

		if ( isset( $first_option ) ) {
			$result = $first_option;
		} else {
			// Needed for select2 'placeholder' to work.
			// See select2 docs: http://ivaynberg.github.io/select2/#documentation
			$result = "<option></option>";
		}

		foreach ( Scarcity_Samurai::$supported_post_types as $post_type ) {
			$post_type_obj = get_post_type_object( $post_type );

			if ( $post_type_obj === null ) {
				continue; // Post Type is not registered, ignore it.
			}

			$posts = self::get_posts( array(
				'post_type' => $post_type
			) );

			if ( ! empty( $posts ) ) {
				$result .= "<optgroup class='{$post_type}' label='{$post_type_obj->labels->name}'>";

				foreach ( $posts as $post ) {
					$page_id = $post->ID;
					$page = Scarcity_Samurai_Model::get( 'Page' )->find( $page_id );

					if ( ( isset( $campaign_id ) && ( $page['campaign_id'] !== $campaign_id ) ) ||
					     ( $include_only_free_pages && ( $page['campaign_id'] !== null ) ) ||
					     in_array( $page_id, $exclude_page_ids ) ||
					     ( $contains_opt_in_form && ! Scarcity_Samurai_Page::contains_opt_in_form( $page ) ) )
					{
						continue;
					}

					$selected = selected( $page_id, $selected_page_id, false );
					$result .= "<option value='{$page_id}' $selected>" . esc_html( $post->post_title ) . "</option>";
				}

				$result .= '</optgroup>';
			}
		}

		return $result;
	}

	// Usage:
	//   Scarcity_Samurai_Helper::page_select( array(
	//     'name' => 'ss-page-lock-early-redirect-page-id',
	//     'selected' => $redirect_page_id
	//   ) );
	//   Scarcity_Samurai_Helper::page_select( array(
	//     'name' => 'ss-wizard-evergreen-pageload-page-id',
	//     'in_campaign' => false   // Pages that do not belong to any campaign
	//   ) );
	public static function page_select( $settings = array() ) {
		extract( $settings );

		unset( $settings['name'] );

		$name = isset( $name ) ? "name='$name'" : '';
		$in_campaign_attr = ( isset( $in_campaign ) && ( $in_campaign === false ) ?
		                      'data-in-campaign="false"' :
		                      '' );

		$result = "<select class='ss-page-select' $name $in_campaign_attr>" .
		             self::page_select_options( $settings ) .
		          '</select>';

		echo $result;
	}

	public static function get_redirect_url( $redirect ) {
		if ( $redirect === null ) {
			return null;
		}

		if ( array_key_exists( 'url', $redirect ) ) {
			return $redirect['url'];
		}

		if ( array_key_exists( 'page_id', $redirect ) ) {
			return self::get_page_url_by_id( $redirect['page_id'] );
		}

		return null;
	}

	// Redirects to a specified location, and forwards any Google Analytics
	// URL parameters. Scarcity Samurai URL parameters shouldn't be forwarded.
	public static function redirect_with_args( $location, $status = 302 ) {
		parse_str( $_SERVER['QUERY_STRING'], $parameters );
		unset( $parameters['page_id'] );
		unset( $parameters['token'] );

		$query = ( empty( $parameters ) ? '' : '?' . http_build_query( $parameters ) );

		wp_redirect( $location . $query, $status );
	}

	public static function isURLValid( $url ) {
		// Needs to be implemented
		return true;
	}

	public static function error( $message ) {
		$email = SS_SUPPORT_EMAIL;
		$message = "Error occurred. Please contact <a href='mailto:$email'>$email</a><br />$message";

		wp_die( $message );
	}

	public static function url( $path ) {
		$url = plugins_url( $path, __FILE__ );

		if ( preg_match( '/\.php$/', $url ) === 0 ) {
			$url .= Scarcity_Samurai::$suffix;
		}

		return $url;
	}

	public static function page_editor() {
		$content = '';
		$settings = array(
			'editor_class' => 'ss-page-editor',
			'editor_height' => 300,
			'quicktags' => array(
				'id' => SS_DEFAULT_EDITOR_ID,
				'buttons' => 'strong,em'
			)
		);

		wp_editor( $content, SS_DEFAULT_EDITOR_ID, $settings );
	}

	// If $html has line breaks, some other plugin could run wpautop() and 
	// those would be converted to <br>s.
	// Thus, we remove all line breaks from $html to make sure <br>s are not
	// suddenly appear in our HTML.
	public static function clean_html( $html ) {
    return preg_replace( '/\s+/', ' ', $html );
	}

}
