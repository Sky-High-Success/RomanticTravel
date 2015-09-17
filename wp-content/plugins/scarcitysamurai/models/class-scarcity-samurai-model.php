<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

class Scarcity_Samurai_Model {

	public static $sql = null;
	private static $models = array();
	private $cache = array();

	public static function get( $model ) {
		if ( ! isset( self::$models[ $model ] ) ) {
			$class = 'Scarcity_Samurai_' . $model;
			if ( class_exists( $class ) ) {
				self::$models[ $model ] = new $class;
			} else {
				throw new Exception( "Class {$class} not found." );
			}
		}
		return self::$models[ $model ];
	}

	private function is_integer_field($key) {
		return ($this->fields[$key]['type'] === 'integer');
	}

	private function is_boolean_field($key) {
		return ($this->fields[$key]['type'] === 'boolean');
	}

	private function is_datetime_field($key) {
		return ($this->fields[$key]['type'] === 'datetime');
	}

	private function is_json_field($key) {
		return ($this->fields[$key]['type'] === 'json');
	}

	private function default_sort_order() {
		$column_names = array_keys($this->fields);
		return $column_names[0] . ' ASC';
	}

	protected function get_model( $id ) {
		return ( is_array( $id ) ? $id : $this->find( $id ) );
	}

	// May be overridden, for example, for trimming campaign's name.
	// return rather than using pass by reference
	protected function sanitize_before_write( $data ) {
		return $data;
	}

	protected function sanitize_after_read( $data ) {
		foreach ( array_keys( $this->fields ) as $key ) {
			if ( ! isset( $data[$key] ) ) {
				continue;
			}

			if ( $this->is_boolean_field( $key ) ) {
				$data[$key] = ( ( ord( $data[$key] ) == 1 ) || ( ord( $data[$key] ) == 49 ) );
			} else if ( $this->is_integer_field( $key ) ) {
				$data[$key] = intval( $data[$key] );
			} else if ( $this->is_datetime_field( $key ) ) {
				$data[$key] = strtotime( $data[$key] );
			} else if ( $this->is_json_field( $key ) ) {
				$data[$key] = json_decode( $data[$key], true );
			}
		}

		return apply_filters( 'scarcity_samurai_sanitize_after_read_' . $this->short_name, $data );
	}

	public function value_to_sql_query_string( $key, $value ) {
		global $wpdb;

		if ( $value === null ) {
			return 'NULL';
		} else if ( $this->is_boolean_field( $key ) ) {
			return $wpdb->prepare( '%b', $value );
		} else if ( $this->is_integer_field( $key ) ) {
			return $wpdb->prepare( '%d', $value );
		} else if ( $this->is_datetime_field( $key ) ) {
			return $wpdb->prepare( '%s', date( 'Y-m-d H:i:s', $value ) );
		} else if ( $this->is_json_field( $key ) ) {
			return $wpdb->prepare( '%s', json_encode( $value ) );
		} else {
			return $wpdb->prepare( '%s', $value );
		}
	}

	private function wrap_values( $values ) {
		return '(' . join( ', ', $values ) . ')';
	}

	private function keys_and_values($settings) {
		$keys = array();
		$values = array();

		foreach ($settings as $index => $setting) {
			$setting_values = array();

			foreach (array_keys($this->fields) as $key) {
				if (array_key_exists($key, $setting)) {
					if ($index == 0) {
						$keys[] = $key;
					}

					$setting_values[] = $this->value_to_sql_query_string($key, $setting[$key]);
				}
			}

			$values[] = $setting_values;
		}

		return array(
			$this->wrap_values($keys),
			join(', ', array_map( array( $this, 'wrap_values' ), $values))
		);
	}

	private function data_to_sql_query_string( $data, $glue ) {
		$result = array();

		foreach ( array_keys( $this->fields ) as $key ) {
			if ( array_key_exists( $key, $data ) ) {
				if ( ! $this->is_json_field( $key ) && is_array( $data[$key] ) ) {
					$separator = 'IN';
					$values = array();

					foreach ( $data[$key] as $d ) {
						$values[] = $this->value_to_sql_query_string( $key, $d );
					}

					$value = $this->wrap_values( $values );
				} else {
					$separator = '=';
					$value = $this->value_to_sql_query_string( $key, $data[$key] );
				}

				$result[] = "$key $separator $value";
			}
		}

		return join( $glue, $result );
	}

	public function find_by( $data = null, $only_first = true, $order_by = null ) {
		global $wpdb;

		self::$sql = 'SELECT * FROM ' . $this->table_name;

		if ( $data !== null ) {
			self::$sql .= ' WHERE ' . $this->data_to_sql_query_string( $data, ' AND ' );
		}

		if ( $only_first ) {
			$data = $wpdb->get_row( self::$sql, ARRAY_A );

			if ( $data !== null ) {
				$data = $this->sanitize_after_read( $data );
			}
		} else {
			if ( $order_by === null ) {
				$order_by = $this->default_sort_order();
			}

			if ( preg_match( '/^\w+\s+(ASC|DESC)(,\s+\w+\s+(ASC|DESC))*$/i', $order_by ) !== 1 ) {
				Scarcity_Samurai_Helper::error( "Illegal 'order_by' parameter: $order_by" );
			}

			self::$sql .= " ORDER BY $order_by";

			$data = $wpdb->get_results( self::$sql, ARRAY_A );

			foreach ( $data as &$d ) {
				$d = $this->sanitize_after_read( $d );
				unset( $d );
			}
		}

		return $data;
	}

	public function find( $id ) {
		if ( $id === null ) {
		  return null;
	  }

		if ( is_admin() || ! array_key_exists( $id, $this->cache ) ) {
			$this->cache[ $id ] = $this->find_by( array(
			  'id' => $id
			) );
		}

		return $this->cache[ $id ];
	}

	public function all( $order_by = null, $data = null ) {
		return $this->find_by( $data, false, $order_by );
	}

	public function insert( $data ) {
		global $wpdb;

		// If an empty array given, there is nothing to do.
		if ( count( $data ) === 0 ) {
			return array();
		}

		$single = ( ! isset( $data[0] ) );

		if ( $single ) {
			$data = array( $data );
		}

		foreach ( $data as &$d ) {
			$d = $this->sanitize_before_write( $d );
			unset( $d );
		}

		list( $keys, $values ) = $this->keys_and_values( $data );

		self::$sql = "
			INSERT INTO " . $this->table_name . "
			$keys
			VALUES $values
		";

		if ( $wpdb->query( self::$sql ) === false ) {
			return false;
		}

		if ( $single ) {
			if ( $wpdb->insert_id === 0 ) {
				return isset( $data[0]['id'] ) ? $data[0]['id'] : null;
			}

			return $wpdb->insert_id;
		}

		$return = array();

		foreach ( $data as $setting ) {
			$return[] = isset( $setting['id'] ) ? $setting['id'] : null;
		}

		return $return;
	}

	public function update( $data, $where = null ) {
		global $wpdb;

		$data = $this->sanitize_before_write( $data );

		self::$sql = "
			UPDATE " . $this->table_name . "
			SET " . $this->data_to_sql_query_string( $data, ', ' ) . "
		";

		if ( $where !== null ) {
			if ( is_array( $where ) ) {
				$where = $this->data_to_sql_query_string( $where, ' AND ' );
			}

			self::$sql .= "WHERE $where";
		}

		if ( $wpdb->query( self::$sql ) === false ) {
			return false;
		} else {
			$this->cache = array();
			return true;
		}
	}

	public function delete( $data ) {
		global $wpdb;

		self::$sql = "
			DELETE FROM " . $this->table_name . "
		";

		$where = $this->data_to_sql_query_string( $data, ' AND ' );

		if ( $where !== '' ) {
			self::$sql .= "WHERE $where";
		}

		if ( $wpdb->query( self::$sql ) === false ) {
			return false;
		} else {
			$this->cache = array();
			return true;
		}
	}

	public function count( $data ) {
		global $wpdb;

		self::$sql = "
			SELECT COUNT(*) FROM " . $this->table_name . "
		";

		$where = $this->data_to_sql_query_string( $data, ' AND ' );

		if ( $where !== '' ) {
			self::$sql .= "WHERE $where";
		}

		return intval( $wpdb->get_var( self::$sql ) );
	}

}
