<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

class Scarcity_Samurai_Campaigns_Table extends WP_List_Table {

	function __construct() {
		// Set parent defaults
		parent::__construct(array(
			'singular' => 'campaign',  // Singular name of the listed records
			'plural'   => 'campaigns', // Plural name of the listed records
			'ajax'     => false        // Does this table support ajax?
		));
	}

	// If column_* isn't found, this function is called.
	function column_default($item, $column_name) {
		return $item[$column_name];
	}

	// Column 'name'
	function column_name( $campaign ) {
		// Build row actions
		$actions = array(
			'edit' => '<a href="?page=scarcitysamurai/campaigns&action=edit&id=' . $campaign['id'] . '">Edit</a>',
			'delete' => '<a href="?page=scarcitysamurai/campaigns&action=delete&campaign=' . $campaign['id'] . '&_wpnonce=' . wp_create_nonce('bulk-campaigns') . '">Delete</a>'
		);

		// Return cell contents
		return '<a href="' . admin_url('admin.php?page=scarcitysamurai/campaigns&action=edit&id=' . $campaign['id']) . '">' . esc_html($campaign['name']) . '</a>' . $this->row_actions($actions);
	}

	// Column 'active'
	function column_active( $campaign ) {
		if ( Scarcity_Samurai_Campaign::has_unavailable_functionality( $campaign['id'] ) ) {
			$escaped_upgrade_url = esc_attr( Scarcity_Samurai_Access::$f );
			$html = "<a class='button-primary ss-upgrade-button' href='$escaped_upgrade_url' target='_blank'>Upgrade To Activate</a>";
		} else {
			$class = 'toggle-modern ss-toggle-' . ( $campaign['active'] ? 'on' : 'off' );
			$campaign_id = $campaign['id'];
			$html = "<div class='$class' data-ss-campaign-id='$campaign_id'></div>";
		}

		return $html;
	}

	// REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	// is given special treatment when columns are processed. It ALWAYS needs to
	// have it's own method.
	function column_cb( $campaign ) {
		return '<input type="checkbox" ' .
		              'name="' . $this->_args['singular'] . '[]" ' .
		              'value="' . $campaign['id'] . '">';
	}

	// REQUIRED! This method dictates the table's columns and titles. This should
	// return an array where the key is the column slug (and class) and the value
	// is the column's title text. If you need a checkbox for bulk actions, refer
	// to the $columns array below.
	// The 'cb' column is treated differently than the rest. If including a checkbox
	// column in your table you must create a column_cb() method. If you don't need
	// bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	function get_columns() {
		return array(
			'cb'   => '<input type="checkbox">', // Render a checkbox instead of text
			'active' => 'Active?',
			'name' => 'Name'
		);
	}

	// Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
	// you will need to register it here. This should return an array where the
	// key is the column that needs to be sortable, and the value is db column to
	// sort by.
	// This method merely defines which columns should be sortable and makes them
	// clickable - it does not handle the actual sorting. You still need to detect
	// the ORDERBY and ORDER querystring variables within prepare_items() and sort
	// your data accordingly (usually by modifying your query).
	function get_sortable_columns() {
		return array(
			'active' => array('active', true),   // true means its already sorted
			'name' => array('name', true)        // true means its already sorted
		);
	}

	// Optional. If you need to include bulk actions in your list table, this is
	// the place to define them. Bulk actions are an associative array in the format
	// 'slug' => 'Visible Title'.
	// If this method returns an empty value, no bulk action will be rendered.
	// If you specify any bulk actions, the bulk actions box will be rendered with
	// the table automatically on display().
	// Also note that list tables are not automatically wrapped in <form> elements,
	// so you will need to create those manually in order for bulk actions to function.
	function get_bulk_actions() {
		return array(
			'activate' => 'Activate',
			'deactivate' => 'Deactivate',
			'delete' => 'Delete'
		);
	}

	// Optional. You can handle your bulk actions anywhere or anyhow you prefer.
	function process_bulk_action() {
		$action = $this->current_action();

		if ( empty( $action ) ) {
			return;
		}

		$campaign_ids = Scarcity_Samurai_Helper::get_request( 'campaign' );

		if ( is_string( $campaign_ids ) ) {
			$campaigns_ids = array( intval( $campaign_ids ) );
		} else {
			$campaigns_ids = array_map( 'intval', Scarcity_Samurai_Helper::get_request( 'campaign' ) );
		}

		switch ( $action ) {
			case 'activate':
			case 'deactivate':
				$update_data = array(
					'active' => ( $action === 'activate' )
				);

				$where = array(
					'id' => $campaigns_ids
				);

				if ( ! Scarcity_Samurai_Model::get( 'Campaign' )->update( $update_data, $where ) ) {
					Scarcity_Samurai_Helper::error( "Campaign" . ( count( $campaigns_ids ) === 1 ? ' ' : 's ' ) .
					                                ( $action === 'activate' ? 'activation' : 'deactivation' ) . " failed." );
				}

				break;
			case 'delete':
				Scarcity_Samurai_Model::get( 'Campaign' )->delete( array( 'id' => $campaigns_ids ) );
				break;
		}
	}

	// REQUIRED! This is where you prepare your data for display. This method will
	// usually be used to query the database, sort and filter the data, and generally
	// get it ready to be displayed. At a minimum, we should set $this->items and
	// $this->set_pagination_args(), although the following properties and methods
	// are frequently interacted with here:
	//   $this->_column_headers
	//   $this->items
	//   $this->get_columns()
	//   $this->get_sortable_columns()
	//   $this->get_pagenum()
	//   $this->set_pagination_args()
	function prepare_items() {
		// Number of records per page to show
		$per_page = 10;

		// REQUIRED. Now we need to define our column headers. This includes a complete
		// array of columns to be displayed (slugs & titles), a list of columns
		// to keep hidden, and a list of columns that are sortable. Each of these
		// can be defined in another method (as we've done here) before being
		// used to build the value for our _column_headers property.
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		// REQUIRED. Finally, we build an array to be used by the class for column
		// headers. The $this->_column_headers property takes an array which contains
		// 3 other arrays. One for all columns, one for hidden columns, and one
		// for sortable columns.
		$this->_column_headers = array($columns, $hidden, $sortable);

		// Optional. You can handle your bulk actions however you see fit.
		$this->process_bulk_action();

		// Set the default order
		$order_by = Scarcity_Samurai_Helper::get_request('orderby');
		$order_by = ($order_by === '') ? 'active' : $order_by;
		$order = Scarcity_Samurai_Helper::get_request('order');
		$order = ($order === '') ? 'desc' : $order;

		$order_by .= " $order";

		if ( $order_by !== 'name' ) {
			$order_by .= ', name asc';
		}

		// Get the data to display
		$data = Scarcity_Samurai_Model::get( 'Campaign' )->all($order_by);

		// REQUIRED for pagination. Let's figure out what page the user is currently
		// looking at. We'll need this later, so you should always include it in
		// your own package classes.
		$current_page = $this->get_pagenum();

		// REQUIRED for pagination. Let's check how many items are in our data array.
		// In real-world use, this would be the total number of items in your database,
		// without filtering. We'll need this later, so you should always include it
		// in your own package classes.
		$total_items = count($data);

		// The WP_List_Table class does not handle pagination for us, so we need
		// to ensure that the data is trimmed to only the current page.
		$data = array_slice($data, ($current_page - 1) * $per_page, $per_page);

		// REQUIRED. Now we can add our *sorted* data to the items property, where
		// it can be used by the rest of the class.
		$this->items = $data;

		// REQUIRED. We also have to register our pagination options & calculations.
		$this->set_pagination_args(array(
			'total_items' => $total_items,                  // WE have to calculate the total number of items
			'per_page'    => $per_page,                     // WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items / $per_page) // WE have to calculate the total number of pages
		));
	}

	// We override WP_List_Table's display() because we don't want to show
	// the bottom column headers and navigation panel.
	function display() {
		extract( $this->_args );

		$this->display_tablenav( 'top' );

?>
<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>" cellspacing="0">
	<thead>
	<tr>
		<?php $this->print_column_headers(); ?>
	</tr>
	</thead>

	<tbody id="the-list"<?php if ( $singular ) echo " class='list:$singular'"; ?>>
		<?php $this->display_rows_or_placeholder(); ?>
	</tbody>
</table>
<?php
	}

}
