<?php
class Gfb_Admin extends Genesis_Admin_Boxes {
	
	function __construct() {
		
		$page_id  = 'genesis-footer-builder';
		
		$menu_ops = array(
			'submenu' => array(
				'parent_slug' => __( 'genesis', 'genesis' ),
				'page_title' => __( 'Genesis Footer Builder', GFB_PLUGIN_DOMAIN ),
				'menu_title' => __( 'Footer Builder', GFB_PLUGIN_DOMAIN ) 
			) 
		);
		
		$page_ops = array(
			'screen_icon' => 'themes',
			'save_button_text'  => __( 'Save Settings', 'genesis' ),
			'reset_button_text' => __( 'Reset Settings', 'genesis' ),
		);
		
		$settings_field = GFB_SETTINGS_FIELD;
		
		$default_settings = gfb_defaults();
				
		$this->create( $page_id, $menu_ops, $page_ops, $settings_field, $default_settings );
		
		//add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		
		add_action( 'genesis_settings_sanitizer_init', array( $this, 'sanitizer_filters' ) );
		
		add_action( 'admin_print_styles', array( $this, 'styles' ) );
		
	}
	
	function help() {
		 
		$screen = get_current_screen();
		if ( $screen->id != $this->pagehook )
			return;
		
		$screen->add_help_tab( array(
		
			'id'		=> 'gfb-ht-overview',
			'title' 	=> 'Overview',
			'content'	=> '<h3>Genesis Footer Builder</h3><p>'. __( 'Genesis Footer Builder allows you to customize the site footer just as you want. You can configure the options and go with the plugin default copyright message or you can completely customize the copyright text.', GFB_PLUGIN_DOMAIN ) .'</p><p>'. __( 'Genesis Footer Builder allows you to:', GFB_PLUGIN_DOMAIN ) .'</p><ol><li>'. __( 'Specify custom brand name for use in the footer credits, which otherwise defaults to the site title.', GFB_PLUGIN_DOMAIN ) .'</li><li>'. __( 'Specify the copyright year or duration to be included in the copyright notice. Defaults to current year.', GFB_PLUGIN_DOMAIN ) .'</li><li>'. __( 'Select and set <strong>Privacy Policy</strong> and <strong>Disclaimer</strong> pages from the dropdown for use in the footer information.', GFB_PLUGIN_DOMAIN ) .'</li><li>'. __( 'Set-up and display Genesis affiliate link in the footer credits text.', GFB_PLUGIN_DOMAIN ) .'</li><li>'. __( 'Customize the footer credits text completely (in case the plugin\'s default credits text doesn\'t work for you).', GFB_PLUGIN_DOMAIN ) .'</li><li>'. __( 'Set-up and display a footer menu on the site.', GFB_PLUGIN_DOMAIN ) .'</li></ol>',
			
		) );
		
		$screen->add_help_tab( array(
		
			'id'		=> 'gfb-ht-shortcodes',
			'title'		=> 'Shortcode Reference',
			'content'	=> '<h3>'. __( 'Genesis Footer Builder &mdash; Shortcode Reference', GFB_PLUGIN_DOMAIN ) .'</h3><p>'. __( 'Genesis Footer Builder offers several shortcodes for customizing footer copyrights and credits. Here\'s the list of shortcodes available for use in <strong>Custom Footer Copyrights</strong> textarea:', GFB_PLUGIN_DOMAIN ) .'</p><dl><dt>'. __( '[gfb-brand]', GFB_PLUGIN_DOMAIN ) .'</dt><dd>'. __( 'Displays the value of <em>Brand Name</em> setting.', GFB_PLUGIN_DOMAIN ) .'</dd><dt>'. __( '[gfb-date]', GFB_PLUGIN_DOMAIN ) .'</dt><dd>'. __( 'Displays the value of <em>Copyright Duration</em> setting.', GFB_PLUGIN_DOMAIN ) .'</dd><dt>'. __( '[gfb-privacy-policy]', GFB_PLUGIN_DOMAIN ) .'</dt><dd>'. __( 'Displays the selected page as set in <em>Privacy Policy page</em> option.', GFB_PLUGIN_DOMAIN ) .'</dd><dt>'. __( '[gfb-disclaimer]', GFB_PLUGIN_DOMAIN ) .'</dt><dd>'. __( 'Displays the selected page as set in <em>Disclaimer page</em> option.', GFB_PLUGIN_DOMAIN ) .'</dd><dt>'. __( '[gfb-affiliate-link]', GFB_PLUGIN_DOMAIN ) .'</dt><dd>'. __( 'Displays the affiliate link for Genesis as set in <em>Genesis Affiliate Link</em> option.', GFB_PLUGIN_DOMAIN ) .'</dd></dl>',
			
		) );
		
		$screen->set_help_sidebar( '<h4>'. __( 'Additional Information', GFB_PLUGIN_DOMAIN ) .'</h3><p><a href="https://wordpress.org/support/plugin/genesis-footer-builder">'. __( 'WordPress.Org Forums', GFB_PLUGIN_DOMAIN ) .'</a></p><p><a href="https://www.binaryturf.com/forum/genesis-footer-builder">'. __( 'Support Forums', GFB_PLUGIN_DOMAIN ) .'</a></p><p><a href="https://www.binaryturf.com/about/contact">'. __( 'Contact the Developer', GFB_PLUGIN_DOMAIN ) .'</a></p>' );
	}
	
	/**
	 *	Safe load plugin textdomain, during init for translations
	 *	Let's make the plugin look in the WordPress languages directory for translations
	 *	@Refer http://geertdedeckere.be/article/loading-wordpress-language-files-the-right-way
	 */
	public function load_plugin_textdomain() {
			
		$domain = GFB_PLUGIN_DOMAIN;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR .'/genesis-footer-builder/'. $domain .'-'. $locale .'.mo' );
		
		load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) .'/languages/' );
	
	}
	
	/** Registering a metabox to hold all the plugin settings **/
	function metaboxes() {
		
		add_meta_box( 'gfb-footer-creds', __( 'Footer Copyright Customizer', GFB_PLUGIN_DOMAIN ), array( $this, 'gfb_customizer_box' ), $this->pagehook, 'main' );
		
		add_meta_box( 'gfb-support', __( 'Help and Support', GFB_PLUGIN_DOMAIN ), array( $this, 'gfb_help_support' ), $this->pagehook, 'column2' );
		
	}
	
	/** Sanitizing the plugin options **/
	function sanitizer_filters() {
		
		genesis_add_option_filter( 'one_zero', $this->settings_field, array(
			'gfb_footer_menu',
			'gfb_date_format'
		));
		
		genesis_add_option_filter( 'no_html', $this->settings_field, array(
			'gfb_brand'
		));
		
		genesis_add_option_filter( 'absint', $this->settings_field, array(
			'gfb_privacy',
			'gfb_disclaimer'
		));
		
		genesis_add_option_filter( 'safe_html', $this->settings_field, array(
			'gfb_output'
		));
		
		genesis_add_option_filter( 'url', $this->settings_field, array(
			'gfb_affiliate_link'
		));
		
	}
	
	/** Enqueue plugin styles **/
	function styles() {
		
		wp_enqueue_style( 'gfb-styles', GFB_PLUGIN_URL . '/styles/gfb-styles.css' );
	
	}
	
	/** Load parent scripts as well as Genesis admin scripts **/
	function scripts() {

		parent::scripts();
		genesis_load_admin_js();
		
	}
	
	/** Sanitize the input for date field to accept only 4 digit numbers and starting with 19 or 20. **/	
	function save( $newsettings, $oldsettings ) {
		
		$newsettings['gfb_date'] 		= $this->validate_date( $newsettings['gfb_date'], $oldsettings['gfb_date'] );
		
		$newsettings['gfb_date_start'] 	= $this->validate_date( $newsettings['gfb_date_start'], $oldsettings['gfb_date_start'] );
		
		$newsettings['gfb_date_end'] 	= $this->validate_date( $newsettings['gfb_date_end'], $oldsettings['gfb_date_end'] );
		
		return $newsettings;
		
	}
	
	/** A helper function for date input validation **/
	function validate_date( $old_val, $new_val ) {
		
		if( preg_match( '/^(19|20)\d{2}$/', $old_val ) )
			return $old_val;
	
		return $new_val;
		
	}
	
	
	/** Generate the output for the metabox **/
	
	function gfb_customizer_box() {
		
		?>
		<div class="gfb-outer">
		<div class="gfb-inner gfb-brand">
			<table class="gfb-layout">
			<tr>
				<td colspan="3">
				<h4><?php _e( 'Brand Name', GFB_PLUGIN_DOMAIN ) ?></h4>
				<p><span class="gfb-desc"><?php _e( 'Enter brand name that will show up in footer credits. Site title is used by default.', GFB_PLUGIN_DOMAIN ); ?></span></p>
				</td>
			</tr>
			<tr>
				<td class="field-label">
				<p><label for="<?php $this->field_id( 'gfb_brand' ); ?>"><?php _e( 'Brand Name: ', GFB_PLUGIN_DOMAIN ); ?></label></p>
				</td>
				<td>
				<p><input type="text" name="<?php $this->field_name( 'gfb_brand' ); ?>" id="<?php $this->field_id( 'gfb_brand' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'gfb_brand' ) ); ?>" /></p>
				</td>
			</tr>
			</table>
		</div>
		
		<div class="gfb-inner gfb-duration">
			<table class="gfb-layout">
			<tr>
				<td colspan="3">
				<h4><?php _e( 'Copyright Duration ', GFB_PLUGIN_DOMAIN ) ?></h4>
				<p class="gfb-desc"><?php _e( 'Please select a custom year for copyright notice. Current year is used by default. You can also specify the from - to years by checking the from - to box below. Years valid from 19** to 20**.', GFB_PLUGIN_DOMAIN ); ?></p>
				</td>
			</tr>
			<tr>
				<td class="field-label">
				<p><label for="<?php $this->field_id( 'gfb_current_date' ); ?>"><?php _e( 'Use current year?', GFB_PLUGIN_DOMAIN ); ?></label></p>
				</td>
				<td>
				<p><input type="checkbox" name="<?php $this->field_name( 'gfb_current_date' ); ?>" id="<?php $this->field_id( 'gfb_current_date' ); ?>" value="1"<?php checked( $this->get_field_value( 'gfb_current_date' ) ); ?> /></p>
				</td>
				</tr>
			</table>
			<div id="gfb-custom-date">
				<div id="gfb-date-format-unset">
					<table class="gfb-layout">
					<tr>
						<td class="field-label">
						<p><label for="<?php $this->field_id( 'gfb_date' ); ?>"><?php _e( 'Enter the year: ', GFB_PLUGIN_DOMAIN ); ?></label></p>
						</td>
						<td>
						<p><input type="number"  name="<?php $this->field_name( 'gfb_date' ); ?>" id="<?php $this->field_id( 'gfb_date' ); ?>" maxlength="4" size="4" value="<?php echo esc_attr( $this->get_field_value( 'gfb_date' ) ); ?>" /></p>
						</td>
					</tr>
					</table>
				</div>
			
				<div id="gfb-date-format-set">
					<table class="gfb-layout">
					<tr>
						<td class="field-label">
						<p><label for="<?php $this->field_id( 'gfb_date' ); ?>"><?php _e( 'Enter the year(s): ', GFB_PLUGIN_DOMAIN ); ?></label></p>
						</td>
						<td>
						<p><input type="number" name="<?php $this->field_name( 'gfb_date_start' ); ?>" id="<?php $this->field_id( 'gfb_date_start' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'gfb_date_start' ) ); ?>" />
						<?php _e( ' &mdash; ' ) ?>
						<input type="number" name="<?php $this->field_name( 'gfb_date_end' ); ?>" id="<?php $this->field_id( 'gfb_date_end' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'gfb_date_end' ) ); ?>" /></p>
						</td>
					</tr>
					</table>
				</div>
				
				<table class="gfb-layout">
				<tr>
					<td class="field-label">
					<p><label for="<?php $this->field_id( 'gfb_date_format' ); ?>"><?php _e( 'Use <em>from - to</em> format? ', GFB_PLUGIN_DOMAIN ); ?></label></p>
					</td>
					<td>
					<p><input type="checkbox" name="<?php $this->field_name( 'gfb_date_format' ); ?>" id="<?php $this->field_id( 'gfb_date_format' ); ?>" value="1"<?php checked( $this->get_field_value( 'gfb_date_format' ) ); ?> /></p>
					</td>
				</tr>
				</table>
			</div>
		</div>
		
		<div class="gfb-inner gfb-privacy">
			<table class="gfb-layout">
			<tr>
				<td colspan="3">
				<h4><?php _e( 'Privacy Policy Page', GFB_PLUGIN_DOMAIN ) ?></h4>
				<p class="gfb-desc"><?php _e( 'Select a page below to be used as the Privacy Policy page in the footer information.', GFB_PLUGIN_DOMAIN ); ?></p>
				</td>
			</tr>
			<tr>
				<?php 
					$valid_pg = get_pages( array( 'post_status' => 'trash, draft, pending' ) );
				?>
				<td class="field-label">
				<p><label for="<?php $this->field_id( 'gfb_privacy' ); ?>"><?php _e( 'Select a page: ', GFB_PLUGIN_DOMAIN ); ?></label></p>
				</td>
				<td>
				<p><?php wp_dropdown_pages( array( 'selected' => $this->get_field_value( 'gfb_privacy' ), 'name' => $this->get_field_name( 'gfb_privacy' ), 'exclude' => $valid_pg, 'show_option_none' => __( '&mdash; Select &mdash;', GFB_PLUGIN_DOMAIN ) ) ); ?></p>
				</td>
			</tr>
			</table>
		</div>
		
		<div class="gfb-inner gfb-disclaimer">
			<table class="gfb-layout">
			<tr>
				<td colspan="3">
				<h4><?php _e( 'Disclaimer Page', GFB_PLUGIN_DOMAIN ) ?></h4>
				<p class="gfb-desc"><?php _e( 'Select a page below to be used as the Disclaimer page in the footer information.', GFB_PLUGIN_DOMAIN ); ?></p>
				</td>
			</tr>
			<tr>
				<td class="field-label">
				<p><label for="<?php $this->field_id( 'gfb_disclaimer' ); ?>"><?php _e( 'Select a page: ', GFB_PLUGIN_DOMAIN ); ?></label></p>
				</td>
				<td>
				<p><?php wp_dropdown_pages( array( 'selected' => $this->get_field_value( 'gfb_disclaimer' ), 'name' => $this->get_field_name( 'gfb_disclaimer' ), 'exclude' => $valid_pg, 'show_option_none' => __( '&mdash; Select &mdash;', GFB_PLUGIN_DOMAIN ) ) ); ?></p>
				</td>
			</tr>
			</table>
		</div>
		
		<div class="gfb-inner gfb-fmenu">
			<table class="gfb-layout">
			<tr>
				<td colspan="3">
				<h4><?php _e( 'Footer Menu', GFB_PLUGIN_DOMAIN ) ?></h4>
				<p class="gfb-desc"><?php printf( __( 'With this option enabled, Genesis Footer Builder allows you to set-up a footer menu and output it in the footer. This can come handy if you want to insert some useful links like Home, About Us, Contact Us, Sitemap etc. Enable this option and save the settings. Then you can go to <a href="%1$s">Menus page</a>, create a new menu or select an existing menu and assign %2$s location.', GFB_PLUGIN_DOMAIN ), esc_url( admin_url('nav-menus.php') ), genesis_code( 'Genesis Footer Builder Menu' ) ); ?></p>
				<p class="gfb-desc"><?php _e( '*Note: Only the first level menu-items will be displayed.', GFB_PLUGIN_DOMAIN ); ?></p>
				</td>
			</tr>
			<tr>
				<td class="field-label">
				<p><label for="<?php $this->field_id( 'gfb_footer_menu' ); ?>"><?php _e( 'Register and insert Footer Menu? ', GFB_PLUGIN_DOMAIN ); ?></label></p>
				</td>
				<td>
				<p><input type="checkbox" name="<?php $this->field_name( 'gfb_footer_menu' ); ?>" id="<?php $this->field_id( 'gfb_footer_menu' ); ?>" value="1"<?php checked( $this->get_field_value( 'gfb_footer_menu' ) ); ?> /></p>
				</td>
			</tr>
			</table>
		</div>
		
		<div class="gfb-inner gfb-fmenu">
			<table class="gfb-layout">
			<tr>
				<td colspan="3">
				<h4><?php _e( 'Genesis Affiliate Link', GFB_PLUGIN_DOMAIN ) ?></h4>
				<p class="gfb-desc"><?php _e( 'Use this option to set your own Genesis affiliate link in the  footer credits text.', GFB_PLUGIN_DOMAIN ); ?></p>
				</td>
			</tr>
			<tr>
				<td class="field-label">
				<p><label for="<?php $this->field_id( 'gfb_affiliate_link' ) ?>"><?php _e( 'Enter the Genesis affiliate link:', GFB_PLUGIN_DOMAIN ); ?></label></p>
				</td>
				<td>
				<input type="text" name="<?php $this->field_name( 'gfb_affiliate_link' ); ?>" id="<?php $this->field_id( 'gfb_affiliate_link' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'gfb_affiliate_link' ) ); ?>" />
				</td>
			</tr>
			</table>
		</div>
		
		<div class="gfb-inner gfb-custom-copy">
			<?php
				$default_copyright = gfb_customized_footer( $this->get_field_value( 'gfb_output' ) );
			?>
			<table class="gfb-layout">
			<tr>
				<td colspan="3">
				<h4><?php _e( 'Custom Footer Copyrights', GFB_PLUGIN_DOMAIN ) ?></h4>
				<p class="gfb-desc"><?php printf( __( 'You can build your own custom credits text below. This field allows you to use HTML tags, entities, Genesis footer shortcodes or any other shortcodes. Additionally, the plugin provides the following shortcodes to customize the output:<dl class="gfb-def-list"><dt>%1$s:</dt><dd>Displays the value of <em>Brand Name</em> option as set above.</dd><br /><dt>%2$s:</dt><dd>Displays the value of <em>Copyright Duration</em> option as set above.</dd><br /><dt>%3$s:</dt><dd>Displays the selected page as set in <em>Privacy Policy page</em> option above.</dd><br /><dt>%4$s:</dt><dd>Displays the selected page as set in <em>Disclaimer page</em> option above.</dd><br /><dt>%5$s:</dt><dd>Displays the Genesis affiliate link as set in the <em>Genesis Affiliate Link</em> option above.</dd><br /></dl>', GFB_PLUGIN_DOMAIN ), genesis_code( '[gfb-brand]' ), genesis_code( '[gfb-date]' ), genesis_code( '[gfb-privacy-policy]' ), genesis_code( '[gfb-disclaimer]' ), genesis_code( '[gfb-affiliate-link]' ) ); ?></p>
				</td>
			</tr>
			<tr>
				<td colspan="3">
				<p><textarea name="<?php $this->field_name( 'gfb_output' ) ?>" id="<?php $this->field_id( 'gfb_output' ); ?>" rows="3" style="width: 100%;"><?php echo esc_textarea( $this->get_field_value( 'gfb_output' ) ); ?></textarea></p>
				</td>
			</tr>
			<?php
				$default_output = gfb_defaults();
				$default_output = $default_output['gfb_output'];
				
				$footer_output = gfb_customized_footer( $this->get_field_value( 'gfb_output' ) );
				
				if( !gfb_get_option( 'gfb_output' ) ) {
			?>
					<tr>
						<td colspan="3">
						<div class="gfb-highlight gfb-example"><?php printf( __( '<strong>Usage Example:</strong><div class="gfb-example">%s</div>', GFB_PLUGIN_DOMAIN ), htmlentities( $default_output )); ?></div>
						</td>
					</tr>
			<?php
				}
				else {
			?>
					<tr>
						<td colspan="3">
						<div class="gfb-highlight"><?php printf( __( '<strong>Preview:</strong><br /> %s', GFB_PLUGIN_DOMAIN ), do_shortcode( $footer_output ) ); ?></div>
						</td>
					</tr>
			<?php
				}
			?>
			</table>
		</div>
		</div>
		<?php
	}
	
	function gfb_help_support() {
		
		?>
		<div class="gfb-help-sidebar">
			<p><?php _e( 'For Genesis Footer Builder support, see:', GFB_PLUGIN_DOMAIN ) ?></p>
			<!-- Yet to add link to WordPress.Org forum for our plugin -->
			<p><?php printf( __( '<a href="https://wordpress.org/support/plugin/genesis-footer-builder">WordPress.Org Forums</a>', GFB_PLUGIN_DOMAIN ) ); ?></p>
			<p><?php printf( __( '<a href="%1$s">Support Forums</a>', GFB_PLUGIN_DOMAIN ), 'https://www.binaryturf.com/forum/genesis-footer-builder' ); ?></p>
			<p><?php printf( __( '<a href="%1$s">Contact the Developer</a>', GFB_PLUGIN_DOMAIN ), 'https://www.binaryturf.com/about/contact' ); ?></p>
			<p class="gfb-sb-creds gfb-desc"><?php printf( __( 'Genesis plugin by <a title="Binary Turf" target="_blank" href="%s">BinaryTurf.Com</a>', GFB_PLUGIN_DOMAIN ), 'https://www.binaryturf.com/genesis-developer' ); ?></p>
		</div>
		<?php
		
	}

}