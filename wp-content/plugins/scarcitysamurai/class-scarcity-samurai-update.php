<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

add_action( 'init', array( 'Scarcity_Samurai_Update', 'init_autoupdates' ) );

class Scarcity_Samurai_Update {

	private static $sql = null;

	private static function get_foreign_key_constraint_name($table_name, $column_name) {
		global $wpdb;

		self::$sql = $wpdb->prepare("
			SELECT i.TABLE_NAME, i.CONSTRAINT_TYPE, i.CONSTRAINT_NAME, k.COLUMN_NAME
			FROM information_schema.TABLE_CONSTRAINTS i
			LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME
			WHERE i.CONSTRAINT_TYPE = 'FOREIGN KEY'
			AND i.TABLE_NAME = %s
			AND k.COLUMN_NAME = %s
		", $table_name, $column_name);

		$record = $wpdb->get_results(self::$sql, ARRAY_A);

		if (is_array($record) && (count($record) === 1)) {
			return $record[0]['CONSTRAINT_NAME'];
		} else {
			return null;
		}
	}

	private static function add_primary_key( $table_name, $key ) {
		global $wpdb;
		if ( is_array( $key ) ) {
			$key = join( ', ', $key );
		}
		if ( ! self::table_primary_key_exists( $table_name ) ) {
			self::$sql = "ALTER TABLE $table_name ADD PRIMARY KEY ($key)";

			if ( $wpdb->query( self::$sql ) === false ) {
				return false;
			}
		}

		return true;
	}

	private static function add_column( $table_name, $column, $column_definition ) {
		global $wpdb;
		if ( ! self::table_column_exists( $table_name, $column ) ) {
			self::$sql = "ALTER TABLE $table_name ADD $column $column_definition";
			if ( $wpdb->query( self::$sql ) === false ) {
				return false;
			}
		}

		return true;
	}

	private static function remove_foreign_key($table_name, $column_name) {
		global $wpdb;

		$constraint_name = self::get_foreign_key_constraint_name($table_name, $column_name);

		if ($constraint_name === null) {
			return true;
		}

		self::$sql = "
			ALTER TABLE $table_name DROP FOREIGN KEY $constraint_name
		";

		if ($wpdb->query(self::$sql) === false) {
			return false;
		}

		return true;
	}

	private static function remove_column($table_name, $column_name) {
		global $wpdb;

		// don't try dropping things if it doesn't exist in the first place
		if ( ! self::table_column_exists( $table_name, $column_name ) ) {
			return true;
		}

		if (self::remove_foreign_key($table_name, $column_name) === false) {
			return false;
		}

		self::$sql = "
			ALTER TABLE $table_name DROP $column_name
		";

		if ($wpdb->query(self::$sql) === false) {
			return false;
		}

		return true;
	}

	public static function update( $from_version, $to_version ) {
		if ( $from_version >= $to_version ) {
			return;
		}

		$update_func = "update_from_${from_version}_to_" . ( $from_version + 1 );

		if ( ! method_exists( __CLASS__, $update_func ) ) {
			Scarcity_Samurai_Helper::error( "<strong>$update_func</strong> doesn't exist." );
		}

		$result = self::$update_func();

		if ( $result === true ) {
			if ( $from_version + 1 == $to_version ) {
				update_site_option('scarcity_samurai_db_version', $to_version);
			}

			self::update( $from_version + 1, $to_version );
		} else {
			Scarcity_Samurai_Helper::error(
				"Scarcity Samurai <strong>$update_func</strong> failed.<br>" .
				'Last SQL command:<br><strong>' . self::$sql . '</strong>'
			);
		}
	}

	private static function update_model_by_id( $model_name, $model_id, $update_data ) {
		return Scarcity_Samurai_Model::get( $model_name )->update( $update_data, array( 'id' => $model_id ) );
	}

	private static function update_all_model_records( $model_name, $update_data ) {
		global $wpdb;

		return Scarcity_Samurai_Model::get( $model_name )->update( $update_data );
	}

	private static function table_primary_key_exists( $table ) {
		global $wpdb;
		self::$sql = $wpdb->prepare( "
				SELECT COUNT(*) FROM information_schema.columns
				WHERE table_schema = %s AND table_name = %s AND column_key = 'PRI'
			",
			DB_NAME, $table );
		return $wpdb->get_var( self::$sql ) !== '0';
	}

	private static function table_column_exists( $table, $column ) {
		global $wpdb;
		self::$sql = $wpdb->prepare( "
				SELECT COUNT(*) FROM information_schema.columns
				WHERE table_schema = %s AND table_name = %s AND column_name = %s
			",
			DB_NAME, $table, $column );
		return $wpdb->get_var( self::$sql ) !== '0';
	}

	private static function update_from_7_to_8() {
		global $wpdb;

		$users_subscriptions = Scarcity_Samurai_Model::get( 'Users_Subscriptions' );
		if ( self::add_primary_key( $users_subscriptions->table_name, array( 'user_id', 'page_id' ) ) === false ) {
			return false;
		}

		$pages_banners = Scarcity_Samurai_Model::get( 'Pages_Banners' );
		if ( self::add_primary_key( $pages_banners->table_name, array( 'page_id', 'banner_id', 'position' ) ) === false ) {
			return false;
		}

		return true;
	}

	private static function update_from_8_to_9() {
		global $wpdb;

		// Remove the 'name' column from User
		if ( self::remove_column( Scarcity_Samurai_Model::get( 'User' )->table_name, 'name' ) === false ) {
			return false;
		}

		// Remove the 'thank_you_page' from Page's data
		$pages = Scarcity_Samurai_Model::get( 'Page' )->all();

		foreach ( $pages as $page ) {
			unset( $page['data']['thank_you_page'] );

			if ( ! self::update_model_by_id( 'Page', $page['id'], array( 'data' => $page['data'] ) ) ) {
				return false;
			}
		}

		return true;
	}

	private static function update_from_9_to_10() {
		global $wpdb;

		// Add 'data' to Banner
		$banner = Scarcity_Samurai_Model::get( 'Banner' );
		if ( self::add_column( $banner->table_name, 'data', 'LONGTEXT') === false ) {
			return false;
		}

		// For all existing banners, set: data = array( 'inline' => false )
		$default_banner_data = array(
			'inline' => false
		);

		if ( ! self::update_all_model_records( 'Banner', array( 'data' => $default_banner_data ) ) ) {
			return false;
		}

		return true;
	}

	private static function update_from_10_to_11() {
		// Add default inline banners
		if ( Scarcity_Samurai::add_default_banners( 1 ) === false ) { // Skip the first default fixed banner
			return false;
		}

		return true;
	}

	private static function update_from_11_to_12() {
		global $wpdb;

		$pages_banners = Scarcity_Samurai_Model::get( 'Pages_Banners' );

		// if we do not have a 'data' column already then add it.
		if ( self::add_column( $pages_banners->table_name, 'data', 'LONGTEXT') === false ) {
			return false;
		}

		// if we have an action column then move everything in it to the data column before removing it
		if ( ! self::table_column_exists( $pages_banners->table_name, 'action' ) ) {
			return true;
		}

		// For all existing header/footer banners, move 'action' to 'data', and set
		// the default 'show'.
		$records = $pages_banners->all();

		foreach ( $records as $record ) {
			$data = array(
				'data' => array(
					// We must json_decode() here because there is no 'action' in Pages_Banners
					// fields anymore. Previously it was there, and it was defined with
					// type 'json'. Therefore, sanitize_after_read() was automatically
					// json_decoding it. But, now, we have to do this manually.
					'action' => json_decode( $record['action'], true ),
					'show' => array(
						'type' => 'immediately'
					)
				)
			);

			$where = array(
				'page_id' => $record['page_id'],
				'position' => $record['position']
			);

			if ( ! $pages_banners->update( $data, $where ) ) {
				return false;
			}
		}

		// Finally, remove the old 'action' column.
		if ( self::remove_column( $pages_banners->table_name, 'action' ) === false ) {
			return false;
		}

		return true;
	}

	private static function update_from_12_to_13() {
		global $wpdb;

		// Remove Counters and Counters_Access tables
		$wpdb->query( 'DROP TABLE ' . $wpdb->prefix . 'scarcity_samurai_counters_access' );
		$wpdb->query( 'DROP TABLE ' . $wpdb->prefix . 'scarcity_samurai_counters' );

		// Unset data['counter'] from all pages
		$pages = Scarcity_Samurai_Model::get( 'Page' )->all();

		foreach ( $pages as $page ) {
			unset( $page['data']['counter'] );

			if ( ! self::update_model_by_id( 'Page', $page['id'], array( 'data' => $page['data'] ) ) ) {
				return false;
			}
		}

		return true;
	}

	private static function update_from_13_to_14() {
		global $wpdb;

		$users_subscriptions = Scarcity_Samurai_Model::get( 'Users_Subscriptions' );

		// Add 'campaign_id' to Users_Subscriptions if it doesn't exist, and update the primary key.
		if ( ! self::table_column_exists( $users_subscriptions->table_name, 'campaign_id' ) ) {
			self::$sql = "
				ALTER TABLE {$users_subscriptions->table_name}
				ADD campaign_id BIGINT(20) UNSIGNED,
				DROP PRIMARY KEY,
				ADD PRIMARY KEY(user_id, page_id, campaign_id)
			";

			if ( $wpdb->query( self::$sql ) === false ) {
				return false;
			}
		}

		return true;
	}

	private static function update_from_14_to_15() {
		global $wpdb;

		$campaign = Scarcity_Samurai_Model::get( 'Campaign' );

		// Add 'active' to Campaigns
		if ( self::add_column( $campaign->table_name, 'active', 'BIT NOT NULL DEFAULT 1') === false ) {
			return false;
		}

		return true;
	}

	// Examples here: https://github.com/noblesamurai/scarcitysamurai/blob/dc3f961bb0d7d9f8e2a6608eb4483c2c9f567361/class-scarcity-samurai-update.php#L106

	//------------------------------------------------------------------------
	// Automatic Update API
	//------------------------------------------------------------------------

	/**
	 * The current plugin version
	 * @var string
	 */
	private static $current_version;

	/**
	 * The update path for the plugin
	 * @var string
	 */
	private static $update_path;

	/**
	 * Plugin Slug (plugin_directory/plugin_file.php)
	 * @var string
	 */
	private static $plugin_slug;

	/**
	 * Plugin name (plugin_file)
	 * @var string
	 */
	private static $slug = 'scarcitysamurai';

	/**
	 * Setup update process
	 */
	public static function init_autoupdates() {
		$plugin_data = get_plugin_data( SS_PLUGIN_FILE, false );

		self::$current_version = $plugin_data['Version'];
		self::$update_path = SS_PLUGIN_UPDATE_PATH;
		self::$plugin_slug = plugin_basename( SS_PLUGIN_FILE );

		add_filter( 'pre_set_site_transient_update_plugins', array( __CLASS__, 'check_update' ) );
		add_filter( 'plugins_api', array( __CLASS__, 'check_info' ), 10, 3 );
	}

	/**
	 * Get the update link if an update is available.
	 */
	public static function get_update_url() {
		if ( current_user_can( 'update_plugins' ) ) {
			$plugins = get_site_transient( 'update_plugins' );

			if ( isset( $plugins->response[ self::$plugin_slug ] ) ) {
				return wp_nonce_url( self_admin_url('update.php?action=upgrade-plugin&plugin=') . self::$plugin_slug,
				                     'upgrade-plugin_' . self::$plugin_slug );
			}
		}

		return null;
	}

	/**
	 * Add our self-hosted autoupdate plugin to the filter transient
	 *
	 * @param $transient
	 * @return object $transient
	 */
	public static function check_update( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		// Get the remote update information
		$request = wp_remote_post( self::$update_path, array(
			'body' => array(
				'action' => 'update',
				'slug' => self::$slug
			)
		) );

		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) !== 200 ) {
			return $transient;
		}

		$update_info = json_decode( $request['body'] );

		// If a newer version is available, add the update
		if ( version_compare( self::$current_version, $update_info->new_version, '<' ) ) {
			$transient->response[ self::$plugin_slug ] = $update_info;
		}

		return $transient;
	}

	/**
	 * Add our self-hosted description to the filter
	 *
	 * @param boolean $false
	 * @param array $action
	 * @param object $args
	 * @return boolean|object
	 */
	public static function check_info( $false, $action, $args ) {
		// Check if this plugins API is about this plugin
		if ( ! isset( $args->slug ) || ( $args->slug !== self::$slug ) ) {
			return false;
		}

		$request = wp_remote_post( self::$update_path, array(
			'body' => array(
				'action' => 'info',
				'slug' => self::$slug
			)
		) );

		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) !== 200 ) {
			return false;
		}

		$info = json_decode( $request['body'], false );

		// $info needs to be a stdObject, so we json_decode with the false argument.
		// $info->sections needs to be an array so if it is an object we need to convert it
		// json_decode with false will convert all arrays to objects with the exception
		// of empty arrays which will remain arrays.
		if ( isset( $info->sections ) && is_object( $info->sections ) ) {
			$info->sections = get_object_vars( $info->sections );
			$info->sections['changelog'] = self::format_changelog( $info->sections['changelog'] );
		}

		return $info;
	}

	private static function format_changelog( $changelog ) {
		$html = '';

		foreach ( $changelog as $log ) {
			$log = get_object_vars( $log );

			$html .= '<h4>' . $log['version'] . ' (' . $log['release_time'] . ')</h4>';

			if ( ! empty( $log['changes'] ) ) {
				$html .= '<ul>';

				foreach ( $log['changes'] as $change ) {
					if ( is_string( $change ) ) {
						$html .= '<li>' . $change . '</li>';
					} else if ( is_object( $change ) ) {
						$change = get_object_vars( $change );

						if ( isset ( $change['type'] ) ) {
							$html .= '<li><strong>' . $change['type'] . '</strong>: ' . $change['content'] . '</li>';
						} else {
							$html .= '<li>' . $change['content'] . '</li>';
						}
					}
				}

				$html .= '</ul>';
			}
		}

		return $html;
	}

}
