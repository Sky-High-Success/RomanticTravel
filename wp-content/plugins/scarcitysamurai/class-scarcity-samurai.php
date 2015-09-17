<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

$scarcity_samurai_dir = dirname(__FILE__) . '/';
$scarcity_samurai_tables_dir = $scarcity_samurai_dir . 'tables/';
$scarcity_samurai_helpers_dir = $scarcity_samurai_dir . 'helpers/';
$scarcity_samurai_vendor_dir = $scarcity_samurai_dir . 'vendor/';
$scarcity_samurai_models_dir = $scarcity_samurai_dir . 'models/';
$scarcity_samurai_endpoints_dir = $scarcity_samurai_dir . 'endpoints/';

define( 'SS_SUPPORT_EMAIL', 'support@noblesamurai.com' );
define( 'SS_DEFAULT_EDITOR_ID', 'sspageeditor' ); // May only contain lower-case letters. See: http://codex.wordpress.org/Function_Reference/wp_editor#Parameters
define( 'SS_FIXED_BANNERS_HTML_DIR', "{$scarcity_samurai_dir}html/banners/fixed/" );
define( 'SS_INLINE_BANNERS_HTML_DIR', "{$scarcity_samurai_dir}html/banners/inline/" );
define( 'SS_TIMERS_HTML_DIR', "{$scarcity_samurai_dir}html/timers/" );
define( 'SS_FIXED_BANNERS_CSS_DIR', "{$scarcity_samurai_dir}stylesheets/css/banners/fixed/" );
define( 'SS_INLINE_BANNERS_CSS_DIR', "{$scarcity_samurai_dir}stylesheets/css/banners/inline/" );

require_once($scarcity_samurai_models_dir . 'class-scarcity-samurai-model.php');
require_once($scarcity_samurai_models_dir . 'class-scarcity-samurai-user.php');
require_once($scarcity_samurai_models_dir . 'class-scarcity-samurai-token.php');
require_once($scarcity_samurai_models_dir . 'class-scarcity-samurai-campaign.php');
require_once($scarcity_samurai_models_dir . 'class-scarcity-samurai-page-type.php');
require_once($scarcity_samurai_models_dir . 'class-scarcity-samurai-page.php');
require_once($scarcity_samurai_models_dir . 'class-scarcity-samurai-users-subscriptions.php');
require_once($scarcity_samurai_models_dir . 'class-scarcity-samurai-settings.php');
require_once($scarcity_samurai_models_dir . 'class-scarcity-samurai-banner.php');
require_once($scarcity_samurai_models_dir . 'class-scarcity-samurai-banner-element.php');
require_once($scarcity_samurai_models_dir . 'class-scarcity-samurai-pages-banners.php');

require_once($scarcity_samurai_dir . 'class-scarcity-samurai-auto-responder-integrator.php');
require_once($scarcity_samurai_dir . 'class-scarcity-samurai-shortcodes.php');
require_once($scarcity_samurai_dir . 'class-scarcity-samurai-access.php');
require_once($scarcity_samurai_dir . 'class-scarcity-samurai-security.php');
require_once($scarcity_samurai_dir . 'class-scarcity-samurai-helper.php');
require_once($scarcity_samurai_dir . 'class-scarcity-samurai-update.php');
require_once($scarcity_samurai_dir . 'class-scarcity-samurai-wizards.php');
require_once($scarcity_samurai_dir . 'inline-banners.php');
require_once($scarcity_samurai_dir . 'inline-timers.php');
require_once($scarcity_samurai_dir . 'dialogs.php');
require_once($scarcity_samurai_dir . 'page-meta-box.php');
require_once($scarcity_samurai_dir . 'campaigns.php');
require_once($scarcity_samurai_dir . 'endpoints.php');
require_once($scarcity_samurai_dir . 'class-scarcity-samurai-css-parser.php');

require_once($scarcity_samurai_tables_dir . 'class-scarcity-samurai-campaigns-table.php');

require_once($scarcity_samurai_vendor_dir . 'simple-html-dom/simple-html-dom.php');

require_once($scarcity_samurai_dir . 'banner-editor.php');

class Scarcity_Samurai {

	const DATABASE_VERSION = 15;
	const DEVELOPMENT_MODE = false;
	const ONLINE = true;

	static $JQUERY_UI_URL;
	static $JQUERY_UI_CSS_URL;
	static $UNDERSCORE_URL;
	static $suffix;
	static $supported_post_types = array(
		'page', 'post', 'landing_page', 'product','travel_package',
	);
	private static $fixed_banners = null;

	function __construct() {
		global $wpdb;

		if ( self::ONLINE ) {
			self::$JQUERY_UI_URL = 'http://code.jquery.com/jquery-1.9.1.js';
			self::$JQUERY_UI_CSS_URL = 'http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css';
		} else {
			self::$JQUERY_UI_URL = Scarcity_Samurai_Helper::url( 'vendor/jquery-ui/jquery-ui.js' );
			self::$JQUERY_UI_CSS_URL = Scarcity_Samurai_Helper::url( 'vendor/jquery-ui/jquery-ui.css' );
		}

		self::$suffix = '?scarcity_samurai_version=' . SCARCITY_SAMURAI_VERSION;

		add_action( 'send_headers', array( $this, 'send_headers' ) );
		add_action( 'wp_head', array( $this, 'add_selectivizr' ) );

		add_action( 'admin_menu', array( $this, 'add_menu_items' ), 9 ); // needs to be less than 10 to work with jetpack + hostme theme
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'template_redirect', array( $this, 'template_redirect' ) );

		// We need 'the_content' section to insert inline banners
		add_filter( 'the_content', array( $this, 'update_content' ), 1000 );
		// As some themes or custom post types don't necessarily call
		// "the_content" we now push all banner info (excluding inline banners) in
		// above the footer.
		add_action( 'wp_footer', array( $this, 'footer_content' ), -10 );

		add_filter( 'nocache_headers', array( $this, 'nocache_headers' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		add_action( 'pre_post_update', array( $this, 'pre_post_update' ) );
		add_action( 'save_post', array( $this, 'on_page_save' ), 10, 2 );
		add_action( 'delete_post', array( $this, 'on_page_delete' ) );

		// Register shortcodes
		add_shortcode( 'ss-timer', array( 'Scarcity_Samurai_Shortcodes', 'timer' ) );

		load_plugin_textdomain( 'scarcitysamurai', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	function add_selectivizr() {
		// See: http://selectivizr.com
		$selectivizr_path = Scarcity_Samurai_Helper::url( 'vendor/selectivizr/selectivizr-min.js' );

		echo <<<EOL
			<!--[if (gte IE 6)&(lte IE 8)]>
  			<script type="text/javascript" src="$selectivizr_path"></script>
			<![endif]-->
EOL;
	}

	static function add_default_settings() {
		if ( Scarcity_Samurai_Model::get( 'Settings' )->insert(array(
			array(
				'name' => 'salt',
				'value' => Scarcity_Samurai_Security::generate_guid()
			)
		) ) === false ) {
			return false;
		}

		return true;
	}

	static function add_default_page_types() {
		if ( Scarcity_Samurai_Model::get( 'Page_Type' )->insert( Scarcity_Samurai_Model::get( 'Page_Type' )->defaults ) === false ) {
			return false;
		}

		return true;
	}

	// When upgrading, $offset won't be 0.
	static function add_default_banners( $offset = 0 ) {
		$banner_model = Scarcity_Samurai_Model::get( 'Banner' );
		$banner_element_model = Scarcity_Samurai_Model::get( 'Banner_Element' );

		$banners = array_slice( $banner_model->defaults, $offset );
		foreach ( $banners as $banner ) {
			if ( Scarcity_Samurai_Banner::banner_name_exists( $banner['name'] ) ) {
				continue;
			}

			$banner_elements = $banner['banner_elements'];
			unset( $banner['banner_elements'] );

			$banner_id = $banner_model->insert( $banner );

			if ( $banner_id === false ) {
				return false;
			}

			foreach ( $banner_elements as $banner_element ) {
				$banner_element['banner_id'] = $banner_id;

				if ( $banner_element_model->insert( $banner_element ) === false ) {
					return false;
				}
			}
		}

		return true;
	}

	static function uninstall() {
		global $wpdb;

		// If uninstall not called from WordPress, exit.
		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) && ! self::DEVELOPMENT_MODE ) {
			return;
		}

		// Remove all plugin tables.
		// The order is important because of foreign key constraints.
		$wpdb->query( 'DROP TABLE ' . Scarcity_Samurai_Model::get( 'Users_Subscriptions' )->table_name );
		$wpdb->query( 'DROP TABLE ' . Scarcity_Samurai_Model::get( 'Token' )->table_name );
		$wpdb->query( 'DROP TABLE ' . Scarcity_Samurai_Model::get( 'User' )->table_name );
		$wpdb->query( 'DROP TABLE ' . Scarcity_Samurai_Model::get( 'Pages_Banners' )->table_name );
		$wpdb->query( 'DROP TABLE ' . Scarcity_Samurai_Model::get( 'Page' )->table_name );
		$wpdb->query( 'DROP TABLE ' . Scarcity_Samurai_Model::get( 'Page_Type' )->table_name );
		$wpdb->query( 'DROP TABLE ' . Scarcity_Samurai_Model::get( 'Campaign' )->table_name );
		$wpdb->query( 'DROP TABLE ' . Scarcity_Samurai_Model::get( 'Settings' )->table_name );
		$wpdb->query( 'DROP TABLE ' . Scarcity_Samurai_Model::get( 'Banner_Element' )->table_name );
		$wpdb->query( 'DROP TABLE ' . Scarcity_Samurai_Model::get( 'Banner' )->table_name );

		// Remove data from WordPress tables
		delete_site_option( 'scarcity_samurai_db_version' );
		delete_site_option( 'scarcity_samurai_version' );
		delete_site_option( 'scarcity_samurai_activated' );
	}

	// Parse <!-- ss-timer ... -->, and return the corresponding inline timer HTML.
	// $matches should be an array with second element being a string of
	// attributes, e.g.: array(
	//   <doesn't matter>,
	//   'timestamp="1373007600"'
	// )
	private function get_inline_timer_html( $matches ) {
		$attributes = Scarcity_Samurai_Helper::parse_xml_attributes( $matches[1] );

		$timestamp = isset( $attributes['timestamp'] ) ? $attributes['timestamp'] : '';
		$format = isset( $attributes['format'] ) ? $attributes['format'] : 'text';

		return "
			<span class='ss-inline-timer'
			      data-ss-timer-timestamp='$timestamp'
			      data-ss-timer-format='$format'></span>
		";
	}

	// Parse <!-- ss-banner ... -->, and return the corresponding inline banner HTML.
	// $matches should be an array with second element being a string of
	// attributes, e.g.: array(
	//   <doesn't matter>,
	//   'id="17" redirect-url="http://yahoo.com"'
	// )
	private function get_inline_banner_html( $matches ) {
		$attributes = Scarcity_Samurai_Helper::parse_xml_attributes( $matches[1] );

		if ( ! isset( $attributes['id'] ) ) {
			return '';
		}

		$banner_id = (int)( $attributes['id'] );
		$page_id = Scarcity_Samurai_Helper::current_page_id();
		$args = array(
			'data' => array(
				'show' => array(
					'type' => $attributes['show-type']
				)
			)
		);

		if ( isset( $attributes['show-value'] ) ) {
			$args['data']['show']['value'] = (int)( $attributes['show-value'] );
		}

		if ( isset( $attributes['align'] ) && in_array( $attributes['align'], array( 'left', 'right', 'center' ) ) ) {
			$args['align'] = $attributes['align'];
		}

		if ( isset( $attributes['redirect-page-id'] ) ) {
			$args['data']['action'] = array(
				'redirect' => array(
					'page_id' => (int)( $attributes['redirect-page-id'] )
				)
			);
		} else if ( isset( $attributes['redirect-url'] ) ) {
			$args['data']['action'] = array(
				'redirect' => array(
					'url' => Scarcity_Samurai_Helper::add_http_to_url_if_required( $attributes['redirect-url'] )
				)
			);
		}

		return Scarcity_Samurai_Banner::get_html( $banner_id, $page_id, $args );
	}


	function use_scarcity_content() {
		if ( current_user_can( 'edit_pages' ) && isset( $_REQUEST['ss-no-scarcity'] ) ) {
			return false;
		}

		// show scarcity only on individual posts or pages (not category listings).
		if ( ! is_singular() ) {
			return false;
		}

		$current_page = Scarcity_Samurai_Helper::current_page();
		$campaign_id = $current_page['campaign_id'];

		if ( ( $campaign_id === null ) ||
		     ( ! Scarcity_Samurai_Campaign::is_active( $campaign_id ) ) )
		{
			return false;
		}

		return true;
	}

	function update_content( $content ) {
		if ( ! $this->use_scarcity_content() ) return $content;

		// Replace all inline timer HTML comments with actual timers HTML
		$content = preg_replace_callback( Scarcity_Samurai_Inline_Timers::$html_comment_regex,
		                                  array( $this, 'get_inline_timer_html' ),
		                                  $content );

		// Replace all inline banner HTML comments with actual banners HTML
		$content = preg_replace_callback( Scarcity_Samurai_Inline_Banners::$html_comment_regex,
		                                  array( $this, 'get_inline_banner_html' ),
		                                  $content );

		if ( $content === '' ) {
			return $content;
		}

		// Hide all 'show_elements'
		$shortcodes = Scarcity_Samurai_Helper::get_shortcodes( null, 'ss-timer', 'expires_after' );

		foreach ( $shortcodes as $shortcode ) {
			$html = simple_html_1_11_str_get_html( $content );
			$elements = $html->find( $shortcode['attributes']['show_elements'] );

			foreach ( $elements as $element ) {
				$style = isset( $element->style ) ? "$element->style; " : '';
				$style .= 'display: none;';
				$element->style = $style;
			}

			$content = $html;
		}

		return $content;
	}

	function footer_content() {
		if ( ! $this->use_scarcity_content() ) return;

		$current_page = Scarcity_Samurai_Helper::current_page();

		// Append all fixed banners HTML to page's content
		echo Scarcity_Samurai_Helper::clean_html( $this->get_fixed_banners_html() );

		$js_data = $this->get_banners_js();

		$js_data['now'] = time();

		if ( $current_page['available_until']['enabled'] === true ) {
			$page_end_time = Scarcity_Samurai_Page::end_time( $current_page );

			if ( $page_end_time !== null ) {
				$js_data['end_time_timer'] = array(
					'deadline' => $page_end_time
				);

				if ( isset( $current_page['available_until']['too_late_action'] ) &&
				     array_key_exists( 'redirect', $current_page['available_until']['too_late_action'] ) ) {
					$redirect = $current_page['available_until']['too_late_action']['redirect'];
					$js_data['end_time_timer']['redirect_url'] = Scarcity_Samurai_Helper::get_redirect_url( $redirect );
				}
			}
		}

		// Disable W3 Total Cache for this page.
		// NOTE: a better solution in the future would be to get the server
		// "now" value in an ajax request (which wouldn't be cached due to it
		// being in the wp-admin dir).  The rest of the page could then be
		// cached without too much trouble (until any other values are changed
		// anyway).
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}

		wp_enqueue_script( 'ss-banner', Scarcity_Samurai_Helper::url( 'scripts/js/banner.js' ), array( 'jquery' ) );
		wp_enqueue_script( 'ss-timer', Scarcity_Samurai_Helper::url( 'scripts/js/timer.js' ), array( 'jquery' ) );
		wp_localize_script( 'ss-timer', 'scarcitySamuraiData', $js_data );

		if ( Scarcity_Samurai_Page::contains_opt_in_form( $current_page ) ) {
			$theme = wp_get_theme();
			$optimizepress2 = $theme->name === 'OptimizePress' && version_compare( $theme->version, '2', '>=' )
				&& 'Y' === get_post_meta( $current_page['id'], '_optimizepress_pagebuilder', true );

			wp_enqueue_script( 'ss-auto-responders', Scarcity_Samurai_Helper::url( 'scripts/js/auto-responders.js' ), array( 'jquery', 'underscore' ) );
			wp_localize_script( 'ss-auto-responders', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
			wp_localize_script( 'ss-auto-responders', 'scarcitySamuraiAutoRespondersData', array(
				'page_id' => $current_page['id'],
				'auto_responder' => Scarcity_Samurai_Page::autoresponder( $current_page['id'] ),
				'optimizepress2' => $optimizepress2,
				'token' => Scarcity_Samurai_Security::generate_guid(),
				'nonce' => wp_create_nonce( 'ss-subscribe-user' )
			) );
		}
	}

	function add_menu_items() {
		$capability = 'manage_options';

		$menu_title = 'Scarcity Samurai';
		if ( Scarcity_Samurai_Update::get_update_url() !== null ) {
			$menu_title .= ' <em class="update">Update Available</em>';
		}

		add_menu_page(
			'Scarcity Samurai',                                  // Page title
			$menu_title,                                         // Menu item name
			$capability,                                         // Capability
			'scarcitysamurai',                                   // Slug
			array($this, 'dashboard_page'),                      // Function
			'div'                                                // Menu icon in CSS
		);

		// Dashboard page
		add_submenu_page(
			'scarcitysamurai',             // Parent slug
			'Scarcity Samurai',            // Page title
			'Dashboard',                   // Submenu item name
			$capability,                   // Capability
			'scarcitysamurai',             // Slug
			array($this, 'dashboard_page') // Function
		);
	}

	/**
	 * If unregistered this sets up the correct registration page and redirects
	 * any requests for the normal pages to the registration.  Note that if not
	 * registered those pages won't exist anyway.  This just stops you from
	 * getting an error if you try to access them.
	 */
	static function register_init() {
		if ( preg_match( '/(?<!register_)scarcitysamurai/', Scarcity_Samurai_Helper::get_request( 'page' ) ) ) {
			wp_redirect( admin_url( 'admin.php?page=register_scarcitysamurai' ) );
			exit;
		}

		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ), 9 ); // needs to be less than 10 to work with jetpack + hostme theme
	}

	/**
	 * Setup registration menu / page.  Also adds the check action that will
	 * do the actual registration when the registration form is submitted.
	 */
	static function register_menu() {
		$capability = 'manage_options';

		wp_enqueue_style('ss-admin', Scarcity_Samurai_Helper::url('stylesheets/css/admin.css'));

		add_menu_page(
			'Scarcity Samurai',                                  // Page title
			'Scarcity Samurai',                                  // Menu item name
			$capability,                                         // Capability
			'register_scarcitysamurai',                          // Slug
			array(__CLASS__, 'register_page'),                   // Function
			'div'                                                // Menu icon in CSS
		);

		// Register page
		add_submenu_page(
			'register_scarcitysamurai',        // Parent slug
			'Scarcity Samurai',                // Page title
			'Registration',                    // Submenu item name
			$capability,                       // Capability
			'register_scarcitysamurai',        // Slug
			array(__CLASS__, 'register_page')  // Function
		);

		add_action( 'admin_init', array( __CLASS__, 'check_register_action' ) );
	}

	function admin_init() {
		wp_enqueue_style( 'ss-admin', Scarcity_Samurai_Helper::url( 'stylesheets/css/admin.css' ) );
		wp_enqueue_style( 'ss-banner-styles', Scarcity_Samurai_Helper::url( 'stylesheets/css/styles.css' ) );

		switch ( Scarcity_Samurai_Helper::get_request( 'page' ) ) {
			case 'scarcitysamurai':
				scarcity_samurai_check();

				wp_enqueue_style( 'ss-dashboard', Scarcity_Samurai_Helper::url( 'stylesheets/css/dashboard.css' ) );

				if ( Scarcity_Samurai_Access::$d === 'trial_not_expired' ) {
					wp_enqueue_script( 'ss-timer', Scarcity_Samurai_Helper::url( 'scripts/js/timer.js' ) );
					wp_localize_script( 'ss-timer', 'scarcitySamuraiData', array(
						'now' => time(),
						'timer' => array(
							'dashboard' => array(
								'deadline' => Scarcity_Samurai_Access::$e,
								'redirect_url' => '?page=scarcitysamurai'   // Reload the current page
							)
						)
					) );
				}

				break;

			case 'scarcitysamurai/campaigns':
				scarcity_samurai_check();

				wp_enqueue_style( 'ss-jquery-ui', self::$JQUERY_UI_CSS_URL );
				wp_enqueue_style( 'ss-jquery-toggles', Scarcity_Samurai_Helper::url( 'vendor/jquery-toggles/toggles.css' ) );
				wp_enqueue_style( 'ss-jquery-toggles-modern', Scarcity_Samurai_Helper::url( 'vendor/jquery-toggles/toggles-modern.css' ) );
				wp_enqueue_style( 'ss-campaigns', Scarcity_Samurai_Helper::url( 'stylesheets/css/campaigns.css' ) );

				wp_enqueue_script( 'ss-jquery-ui', self::$JQUERY_UI_URL );
				wp_enqueue_script( 'ss-jquery-toggles', Scarcity_Samurai_Helper::url( 'vendor/jquery-toggles/toggles.min.js' ) );
				wp_enqueue_script( 'ss-core', Scarcity_Samurai_Helper::url( 'scripts/js/core.js' ) );
				wp_enqueue_script( 'ss-helper', Scarcity_Samurai_Helper::url( 'scripts/js/helper.js' ) );
				wp_enqueue_script( 'ss-campaigns', Scarcity_Samurai_Helper::url( 'scripts/js/pages/campaigns.js' ), array( 'underscore' ) );

				$this->campaigns_page_init();
				break;
		}
	}

	private static function update_plugin() {
		// Update the plugin version, if required
		$current_version = get_site_option( 'scarcity_samurai_version' ); // Will be false on the first activation

		if ( $current_version !== SCARCITY_SAMURAI_VERSION ) {
			update_site_option( 'scarcity_samurai_version', SCARCITY_SAMURAI_VERSION );
		}

		// Update the database, if required
		$current_db_version = get_site_option( 'scarcity_samurai_db_version' );

		if ( $current_db_version === false ) {
			Scarcity_Samurai_Helper::error( "Current DB version doesn't exist." );
		}

		$current_db_version = intval( $current_db_version );

		if ( self::DATABASE_VERSION > $current_db_version ) {
			Scarcity_Samurai_Update::update( $current_db_version, self::DATABASE_VERSION );
		}
	}

	function send_headers() {
		$current_page = Scarcity_Samurai_Helper::current_page();

		if ( Scarcity_Samurai_Page::has_redirect( $current_page ) ) {
			nocache_headers();
		}
	}

	function nocache_headers( $headers ) {
		$current_page = Scarcity_Samurai_Helper::current_page();

		if ( Scarcity_Samurai_Page::has_redirect( $current_page ) ) {
			$headers['Cache-Control'] .= ', no-store';
		}

		return $headers;
	}

	function init() {
		self::update_plugin();

		// Deactivate all campaigns with unavailable functionality
		foreach ( Scarcity_Samurai_Model::get( 'Campaign' )->all() as $campaign ) {
			if ( Scarcity_Samurai_Campaign::has_unavailable_functionality( $campaign['id'] ) ) {
				Scarcity_Samurai_Campaign::toggle_activation( $campaign['id'], false ); // Deactivate
			}
		}
	}

	function template_redirect() {
		// If it is a dashboard page, do nothing.
		if ( is_admin() ) {
			return;
		}

		$current_page = Scarcity_Samurai_Helper::current_page();

		// If one of the endpoints is called via AJAX, exit.
		if ( $current_page === null ) {
			return;
		}

		if ( ! Scarcity_Samurai_Campaign::is_active( $current_page['campaign_id'] ) ) {
			return;
		}

		// Set cookie, if required and not set yet
		if ( Scarcity_Samurai_Page::requires_cookie( $current_page ) ) {
			$cookie_name = Scarcity_Samurai_Security::page_load_cookie_name( $current_page );
			Scarcity_Samurai_Security::set_cookie_if_doesnt_exist( $cookie_name, time() );
		}

		$allowed = Scarcity_Samurai_User::allowed_to_see_page( $current_page );

		if ( is_array( $allowed ) ) {
			if ( array_key_exists( 'redirect', $allowed ) ) {
				$redirect_url = Scarcity_Samurai_Helper::get_redirect_url( $allowed['redirect'] );
				Scarcity_Samurai_Helper::redirect_with_args( $redirect_url );
				exit;
			}

			if ( array_key_exists( 'error', $allowed ) ) {
				if ( $allowed['error'] === 404 ) {
					Scarcity_Samurai_Helper::redirect_to_404();
					return;
				}

				wp_die( 'Unknown error code: ' . $allowed['error'] );
			}
		}

		// Suppose user visits a page that requires a token (i.e. it has opt-in
		// references) by clicking the link in his email. He'll be able to access
		// this page. But, if this page has links to other pages in this campaign,
		// user won't be able to access them because the links don't have token in
		// them. To solve this problem, we set the token cookie, so that other pages
		// could know who we are.
		if ( Scarcity_Samurai_Page::url_requires_token( $current_page ) ) {
			$token = Scarcity_Samurai_Helper::get_request( 'token' );

			if ( $token !== '' ) {
				Scarcity_Samurai_Security::set_cookie( 'scarcity_samurai_token', $token );
			}
		}

		$this->add_banners_css();
	}

	private function add_banners_css() {
		$banners = Scarcity_Samurai_Page::banners();

		if ( empty( $banners ) ) {
			return;
		}

		// Add generic CSS
		wp_enqueue_style( 'ss-banner-styles', Scarcity_Samurai_Helper::url( 'stylesheets/css/styles.css' ) );

		// Add banners CSS
		foreach ( $banners as $banner ) {
			$banner_id = $banner['banner']['id'];
			$banner_position = $banner['position'];
			$page_id = Scarcity_Samurai_Helper::current_page_id();

			if ( ! Scarcity_Samurai_Banner::is_enabled( $banner_id, $page_id, $banner_position ) ) {
				continue;
			}

			// Should this banner be visible on this page?
			if ( ! Scarcity_Samurai_Banner::is_visible( $banner_id, $page_id, $banner_position ) ) {
				continue;
			}

			$banner_css = Scarcity_Samurai_Banner::get_css( $banner_id );

			if ( $banner_css !== '' ) {
				wp_add_inline_style( 'ss-banner-styles', $banner_css );
			}
		}
	}

	function add_banner_elements_css( $element_type, $banner_id, $banner_replacements, &$memo ) {
		global $scarcity_samurai_dir;

		$elements = Scarcity_Samurai_Banner::get_elements( $banner_id, $element_type );

		foreach ( $elements as $element ) {
			$element_id = $element['id'];

			if ( ( $element['style'] !== null ) && array_key_exists( 'id', $element['style'] ) ) {
				$element_style_id = $element['style']['id'];
				unset( $element['style']['id'] );
			} else {
				$element_style_id = null;
			}

			// Don't add the same element style twice
			if ( ( $element_style_id !== null ) && empty( $element['style'] ) &&
			     array_key_exists( $element_style_id, $memo ) )
			{
				continue;
			}

			$element_style = ( $element['style'] === null ? array() : $element['style'] );

			if ( $element_style_id === null ) {
				$element_css = ".ss-${element_type}-${element_id} {\n" . Scarcity_Samurai_Helper::build_css( $element_style ) . "\n}";
			} else {
				$element_css_prefix = ( empty( $element['style'] ) ? '' : ".ss-${element_type}-${element_id} " );
				$element_css_filename = $scarcity_samurai_dir . "stylesheets/css/${element_type}s/${element_type}_${element_style_id}.css";
				$parser = new Scarcity_Samurai_CSS_Parser( $element_css_filename );
				$element_css = $parser->get_css( $element_css_prefix, array_merge( $element_style, $banner_replacements ) );
			}

			wp_add_inline_style( 'ss-banner-styles', $element_css );

			if ( $element_style_id !== null ) {
				$memo[$element_style_id] = true;
			}
		}
	}

	function get_fixed_banners_html() {
		$page = Scarcity_Samurai_Helper::current_page();

		if ( ( $page === null ) || ( $page['campaign_id'] === null ) ) {
			self::$fixed_banners = array();
			return '';
		}

		$page_id = $page['id'];

		// Not including inline banners
		$page_banners = Scarcity_Samurai_Model::get( 'Pages_Banners' )->all( null, array(
			'page_id' => $page_id,
			'enabled' => true
		) );

		if ( ! current_user_can( 'edit_post', $page_id ) ) {
			// If page's end time is relative to opt-in, but we cannot identify the user,
			// or the we cannot calculate the subscription time, filter out all banners
			// which contain timers.
			// We can't calculate the time to show in the timers in this case.
			$user_id = Scarcity_Samurai_Helper::get_user_id();
			$subscription_time = Scarcity_Samurai_User::subscription_time();

			if ( ( $page['available_until'] !== null ) && $page['available_until']['enabled'] &&
				   ( $page['available_until']['type'] === 'opt_in' ) &&
				   ( ( $user_id === null ) || ( $subscription_time === null ) ) )
			{
				foreach ( $page_banners as &$banner ) {
					if ( Scarcity_Samurai_Banner::contains_timer( $banner['banner_id'] ) ) {
						$banner = false;
					}

					unset( $banner );
				}

				$page_banners = array_filter( $page_banners );
			}
		}

		// remove false values
		$page_banners = array_filter( $page_banners );

		self::$fixed_banners = $page_banners;

		$html = '';

		foreach ( $page_banners as $page_banner ) {
			$html .= Scarcity_Samurai_Banner::get_html( $page_banner['banner_id'], $page_id, array(
				'enabled' => $page_banner['enabled'],
				'position' => $page_banner['position'],
				'data' => $page_banner['data']
			) );
		}

		return $html;
	}

	function get_banners_js() {
		$page_id = Scarcity_Samurai_Helper::current_page_id();

		$js_data = array(
			'page_id' => $page_id
		);

		$banners = Scarcity_Samurai_Page::banners();

		foreach ( $banners as $banner ) {
			if ( ! Scarcity_Samurai_Banner::is_enabled( $banner['banner']['id'], $page_id, $banner['position'] ) ) {
				continue;
			}

			$banner_js_data = Scarcity_Samurai_Banner::get_js_data( $banner['banner'], $banner['position'] );

			if ( ! array_key_exists( 'timer', $banner_js_data ) ) {
				continue;
			}

			if ( ! array_key_exists( 'timer', $js_data ) ) {
				$js_data['timer'] = array();
			}

			$js_data['timer'] += $banner_js_data['timer'];
		}

		return $js_data;
	}

	function dashboard_page() {
		global $scarcity_samurai_dir;

		$update_url = Scarcity_Samurai_Update::get_update_url();

		$evergreen_optin_wizard_is_available = Scarcity_Samurai_Access::wizard_is_available( 'evergreen-optin' );
		$fixed_date_multi_wizard_is_available = Scarcity_Samurai_Access::wizard_is_available( 'fixed-date-multi' );
		$evergreen_pageload_wizard_is_available = Scarcity_Samurai_Access::wizard_is_available( 'evergreen-pageload' );
		$fixed_date_single_wizard_is_available = Scarcity_Samurai_Access::wizard_is_available( 'fixed-date-single' );

		$f = Scarcity_Samurai_Access::$f;

		include( $scarcity_samurai_dir . 'html/pages/dashboard.php' );
	}

	static function register_page() {
		global $scarcity_samurai_dir;

		$email = get_site_option( 'scarcity_samurai_email' );

		switch( Scarcity_Samurai_Helper::get_request( 'error' ) ) {
			case 'error': $error = 'Failed to contact the registration server. Please try again.'; break;
			case 'eula': $error = 'You need to accept the End User Licence Agreement to continue.'; break;
			case 'invalid': $error = 'Incorrect user details.'; break;
			case 'limit_reached': $error = 'Activation limit reached.'; break;
		}

		wp_enqueue_style( 'ss-dashboard', Scarcity_Samurai_Helper::url( 'stylesheets/css/dashboard.css' ) );

		include( $scarcity_samurai_dir . 'html/pages/register.php' );
	}

	static function check_register_action() {
		if ( ( Scarcity_Samurai_Helper::get_request( 'page' ) === 'register_scarcitysamurai' ) &&
		     ( wp_verify_nonce( Scarcity_Samurai_Helper::get_request( 'ss_register_nonce' ), 'register_scarcity_samurai' ) ) )
		{
			if ( Scarcity_Samurai_Helper::get_request( 'ss_eula' ) !== 'i_agree' ) {
				wp_redirect( add_query_arg( array( 'error' => 'eula' ) ) );
				exit;
			}

			$email = trim( Scarcity_Samurai_Helper::get_request( 'ss_email' ) );
			$samid = trim( Scarcity_Samurai_Helper::get_request( 'ss_samid' ) );
			$check = scarcity_samurai_check( $samid, $email );

			if ( $check === true ) {
				wp_redirect( admin_url( 'admin.php?page=scarcitysamurai&registered=yes' ) );
				exit;
			} else {
				update_site_option( 'scarcity_samurai_email', $email );
				wp_redirect( add_query_arg( array( 'error' => $check ) ) );
				exit;
			}
		}
	}

	function campaigns_page() {
		global $scarcity_samurai_dir;

		$campaigns_table = new Scarcity_Samurai_Campaigns_Table();
		$campaigns_table->prepare_items();

		include( $scarcity_samurai_dir . 'html/pages/campaigns.php' );
	}

	function campaigns_page_init() {
		if ( Scarcity_Samurai_Helper::get_request( 'security_token' ) === '' ) {
			return;
		}

		switch ( Scarcity_Samurai_Helper::get_request( 'action' ) ) {
			case 'delete':
				// Don't change 'bulk-campaigns' to something else.
				// WP_List_Table uses exactly this value when calling wp_nonce_field().
				check_admin_referer( 'bulk-campaigns', '_wpnonce' );
				$campaign_id = intval( Scarcity_Samurai_Helper::get_request( 'id' ) );
				Scarcity_Samurai_Model::get( 'Campaign' )->delete( array( 'id' => $campaign_id ) );
				break;
		}
	}

	// ----------------
	//    Meta boxes
	// ----------------
	function add_meta_boxes() {
		foreach ( self::$supported_post_types as $post_type ) {
			add_meta_box( 'ss-page-meta-box', 'Scarcity Samurai', array( $this, 'add_page_meta_box' ), $post_type, 'normal' );
		}
	}

	function add_page_meta_box() {
		global $scarcity_samurai_dir;

		wp_enqueue_style( 'ss-new-ui-core', Scarcity_Samurai_Helper::url( 'stylesheets/css/new-ui-core.css' ) );
		wp_enqueue_style( 'ss-select2', Scarcity_Samurai_Helper::url( 'vendor/select2/select2.css' ) );
		wp_enqueue_style( 'ss-page-meta-box', Scarcity_Samurai_Helper::url( 'stylesheets/css/page-meta-box.css' ) );

		wp_enqueue_script( 'ss-select2', Scarcity_Samurai_Helper::url( 'vendor/select2/select2.min.js' ) );
		wp_enqueue_script( 'ss-helper', Scarcity_Samurai_Helper::url( 'scripts/js/helper.js' ) );
		wp_enqueue_script( 'ss-page-meta-box', Scarcity_Samurai_Helper::url( 'scripts/js/page-meta-box.js' ) );

		$page = Scarcity_Samurai_Helper::current_page();

		$js_data = array(
			'page_id' => $page['id'],
			'auto_responders' => array()
		);

		foreach ( Scarcity_Samurai_Auto_Responder_Integrator::supported_auto_responders() as $auto_responder ) {
			$js_data['auto_responders'][$auto_responder] = array(
				'short_name' => Scarcity_Samurai_Auto_Responder_Integrator::get_auto_responder_short_name( $auto_responder ),
				'configuration_instructions_link' => Scarcity_Samurai_Auto_Responder_Integrator::get_auto_responder_configuration_instructions_link( $auto_responder )
			);
		}

		$campaigns = Scarcity_Samurai_Model::get( 'Campaign' )->all( 'name ASC' );

		$auto_responder = Scarcity_Samurai_Page::autoresponder( $page['id'] );
		$auto_responder_short_name =
			Scarcity_Samurai_Auto_Responder_Integrator::get_auto_responder_short_name( $auto_responder );

		$header_banner_attributes = Scarcity_Samurai_Page::header_banner_attributes( $page['id'] );
		$footer_banner_attributes = Scarcity_Samurai_Page::footer_banner_attributes( $page['id'] );

		$trial_has_expired = ( Scarcity_Samurai_Access::$d === 'trial_expired' );
		$f = Scarcity_Samurai_Access::$f;

		wp_localize_script( 'ss-page-meta-box', 'scarcitySamuraiData', $js_data );

		include( $scarcity_samurai_dir . 'html/page-meta-box/page-meta-box.php' );
	}

	// It is possible that some plugin/theme (e.g. OptimizePress) saved the page
	// with a custom page template, say 'my_template' (see '_wp_page_template' key
	// in the 'wp_postmeta' table with the correcponding 'post_id').
	// Then, if we change the theme to 'Headway Themes', for example, and try to
	// update the page, the update will fail because WordPress checks whether
	// page's template is available in the current theme.
	// Therefore, we reset page's template to 'default' just before the update
	// is done, so that the update will succeed.
	function pre_post_update( $post_id ) {
		$page_templates = wp_get_theme()->get_page_templates();
		$page_template = get_post_meta( $post_id, '_wp_page_template', true );

		if ( ( $page_template !== 'default' ) && ! isset( $page_templates[ $page_template ] ) ) {
			// Unhook this function so it doesn't loop infinitely
			remove_action( 'pre_post_update', array( $this, 'pre_post_update' ) );

			// Update the post, which calls 'save_post' then 'pre_post_update'
			$page_id = wp_update_post( array(
				'ID' => $post_id,
				'page_template' => 'default'
			) );

			// Re-hook this function
			add_action( 'pre_post_update', array( $this, 'pre_post_update' ) );
		}
	}

	function on_page_save( $post_id, $post ) {
		if ( wp_is_post_autosave( $post ) || wp_is_post_revision( $post ) ) {
			return;
		}

		Scarcity_Samurai_Page_Meta_Box::save_page_meta_box_data( $post_id );
	}

	function on_page_delete( $page_id ) {
		if ( wp_is_post_revision( $page_id ) ) {
			return;
		}

		Scarcity_Samurai_Model::get( 'Page' )->delete( array( 'id' => $page_id ) );
	}

}
