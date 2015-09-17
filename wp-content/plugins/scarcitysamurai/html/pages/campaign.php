<div class="wrap <?php if ( $is_new_campaign ) echo 'ss-new-campaign'; ?>">
  <?php wp_nonce_field( 'campaign-edit', 'security_token' ); ?>

  <div id="ss-logo-small" class="icon32"></div>
  <h2>
    <?php echo $is_new_campaign ? 'Create New Campaign' : 'Edit Campaign'; ?>
  </h2>
  <br />

  <form method="post">
    <div id="titlediv">
      <?php if ( isset( $error ) ) { ?>
        <div class="ss-error"><?php esc_html_e( $error ); ?></div>
      <?php } ?>

      <?php if ( isset( $message ) ) { ?>
        <div class="ss-notice"><?php esc_html_e( $message ); ?></div>
      <?php } ?>

      <?php if ( ! $is_new_campaign ) { ?>
        <div class="ss-campaign-status-wrapper">
          <?php
            if ( Scarcity_Samurai_Campaign::has_unavailable_functionality( $campaign['id'] ) ) {
          ?>
              <span class='ss-inactive-campaign-message'>This campaign is inactive.</span>
              <a class="button-primary ss-upgrade-button"
                 href="<?php echo esc_attr( Scarcity_Samurai_Access::$f ); ?>"
                 target="_blank">
                Upgrade To Activate
              </a>
          <?php
            } else {
              $toggle_class = 'toggle-modern ss-toggle-' . ( $campaign['active'] ? 'on' : 'off' );
          ?>
              Is this campaign active?
              <div class="<?php echo $toggle_class; ?>" data-ss-campaign-id="<?php echo $campaign_id; ?>"></div>
          <?php
            }
          ?>
        </div>
      <?php } ?>

      <div class="ss-campaign-name-wrapper">
        Campaign Name:
        <div id="titlewrap">
          <label id="title-prompt-text" class="screen-reader-text" for="title">Enter campaign name here</label>
          <input id="title" type="text" name="campaign_name" value="<?php esc_attr_e( $name ); ?>">
        </div>
      </div>

      <?php if ( $is_new_campaign ) { ?>
        <input type="submit" name="save" class="button button-primary" value="Create" />
      <?php } else { ?>
        <input type="hidden" name="campaign_id" value="<?php esc_attr_e( $campaign_id ); ?>" />
        <input type="submit" name="save" class="button" value="Update" />
      <?php } ?>
    </div>
  </form>

  <?php if ( ! $is_new_campaign ) { ?>
    <?php if ( empty( $pages ) ) { ?>
      <p class="ss-no-pages-in-campaign-note">
        There are no pages in this campaign.
      </p>
    <?php } else { ?>
      <div class="ss-campaign-content">
        <div class="inside">
          <h2>Campaign Information</h2>
          <br />

          <!--
              Auto Responder information
          -->
          <?php if ( $auto_responder !== null ) { ?>
            <div id="ss-edit-campaign-auto-responder-information" class="ss-notice">
              <p>
                For your campaign to function properly you just need to configure
                <?php echo $auto_responder_short_name; ?> to work with
                Scarcity Samurai. This only takes a couple of minutes and you
                can see step-by-step instructions on how to do this here:
                <?php echo Scarcity_Samurai_Auto_Responder_Integrator::get_auto_responder_configuration_instructions_link( $auto_responder ); ?>
              </p>
            </div>
          <?php } ?>

          <!--
              Unpublished pages warning
          -->
          <?php if ( count( $unpublished_pages ) > 0 ) {
            $count = count( $unpublished_pages );
          ?>
            <div id="ss-edit-campaign-unpublished-pages-warning" class="ss-error">
              <p>
                The following <?php echo _n('page', 'pages', $count); ?> need to
                be published for this campaign to function correctly.
                &nbsp;
                <a id="ss-edit-campaign-publish-these-pages-now-button" class="button">
                  Publish <?php echo _n( 'This Page', 'These Pages', $count ); ?> Now
                </a>
                <ul class="ul-square">
                  <?php
                    foreach ( $unpublished_pages as $page ) {
                      echo '<li>' . Scarcity_Samurai_Helper::page_link( $page['id'] ) . '</li>';
                    }
                  ?>
                </ul>
              </p>
            </div>
          <?php } ?>

          <!--
              Cannot detect forms warning
          -->
          <?php if ( count( $auto_responder_not_set_pages ) > 0 ) {
            $count = count( $auto_responder_not_set_pages );
          ?>
            <div class="ss-error">
              <p>
                The following <?php echo _n('page is', 'pages are', $count); ?>
                marked as <?php echo _n('it contains', 'they contain', $count); ?>
                an opt-in form. Please select the auto responder for
                <?php echo _n('this page', 'these pages', $count); ?>.

                <ul class="ul-square">
                  <?php
                    foreach ( $auto_responder_not_set_pages as $page ) {
                      echo '<li>' . Scarcity_Samurai_Helper::page_link( $page['id'] ) . '</li>';
                    }
                  ?>
                </ul>
              </p>
            </div>
          <?php } ?>

          <!--
              Broken opt-in references warning
          -->
          <?php
            $contains_opt_in_form =
              Scarcity_Samurai_Campaign::contains_opt_in_form( $campaign_id );

            $has_opt_in_references =
              Scarcity_Samurai_Campaign::has_opt_in_references( $campaign_id );

            if ( $has_opt_in_references && ! $contains_opt_in_form ) {
          ?>
            <div class="ss-error">
              <p>
                This campaign has opt-in references, but doesn't have a page
                that contains an opt-in form.
              </p>
            </div>
          <?php } ?>

          <!--
              Page Links
          -->
          <?php if ( empty( $pages_that_require_a_token ) || ( $auto_responder !== null ) ) { ?>
            <h3>Page Links</h3>
            <?php if ( $campaign_contains_opt_in_form ) { ?>
              <p>
                To send users to pages in this campaign using
                <?php echo $auto_responder_short_name; ?>, use the following links:
              </p>
            <?php } else { ?>
              <p>
                To send users to pages in this campaign, use the following links:
              </p>
            <?php } ?>

            <?php
              foreach ( $pages as $page ) {
                $page_id = $page['id'];
                $page_title = Scarcity_Samurai_Helper::get_page_title_by_id( $page_id );
                $view_url = Scarcity_Samurai_Helper::get_page_url_by_id( $page_id );
                $edit_url = Scarcity_Samurai_Helper::get_edit_page_url_by_id( $page_id );
                $page_is_published = Scarcity_Samurai_Page::is_published( $page_id );
                $email_url = Scarcity_Samurai_Helper::get_page_url_by_id( $page_id, $auto_responder, true );
            ?>
                <div class="ss-sub ss-page-<?php echo $page_id . ( $page_is_published ? ' ss-page-status-publish' : '' ); ?>">
                  <ul class="ul-checklist">
                    <li class="incomplete">
                      <strong><?php echo esc_html( $page_title ); ?></strong> &mdash;
                      <a href="<?php echo esc_attr($view_url); ?>">View</a> |
                      <a href="<?php echo esc_attr($edit_url); ?>">Edit</a>

                      <p>
                        Link:
                        <input class="ss-page-url ss-text-input" type="text" readonly="readonly" value="<?php echo esc_attr($email_url) ; ?>" />
                        &nbsp;
                        <a class="button ss-edit-campaign-copy-to-clipboard-button">Copy To Clipboard</a>
                        &nbsp;
                        <span class="ss-copy-to-clipboard-confirmation-message">Copied</span>
                      </p>
                    </li>
                  </ul>
                </div>
          <?php
              }
            }
          ?>
        </div>
      </div>
      <div class="ss-campaign-left" id="poststuff">
        <div class="ss-campaign-entity-container">
          <?php foreach ( $pages as $page ) {
            $page_id = $page['id'];
            $view_url = Scarcity_Samurai_Helper::get_page_url_by_id( $page_id );
            $edit_url = Scarcity_Samurai_Helper::get_edit_page_url_by_id( $page_id );
          ?>
            <div class="ss-campaign-entity">
              <div class="ss-campaign-entity-item postbox">
                <h3>
                  <span>
                    <?php echo esc_html( $page['title'] ); ?>
                    <span class="postbox-title-action">
                      <a href="<?php echo esc_attr_e($edit_url); ?>#ss-campaign">Configure</a>
                    </span>
                  </span>
                </h3>
                <div class="inside ss-type-<?php esc_attr_e( $page['type'] ); ?>">
                  <?php if ( in_array( $page_id, $unpublished_page_ids ) ) { ?>
                    <div class="ss-draft"></div>
                  <?php } ?>
                  <div class="ss-page-actions">
                    <a href="<?php echo esc_attr_e($view_url); ?>">View</a> |
                    <a href="<?php echo esc_attr_e($edit_url); ?>">Edit</a>
                  </div>
                </div>
                <ul class="ss-page-options">
                  <li class="ss-page-lock" title="Configure Access Restriction">
                    <a href="<?php echo esc_attr_e($edit_url); ?>#ss-page-lock" <?php if ( $page['lock-active'] ) echo 'class="active"'; ?>>
                      <span>Access Restriction</span>
                    </a>
                  </li>
                  <li class="ss-page-timer" title="Configure Count Down Timer">
                    <a href="<?php echo esc_attr_e($edit_url); ?>#ss-page-timer" <?php if ( $page['timer-active'] ) echo 'class="active"'; ?>>
                      <span>Count Down Timer</span>
                    </a>
                  </li>
                  <li class="ss-page-banners" title="Configure Banners">
                    <a href="<?php echo esc_attr_e($edit_url); ?>#ss-page-banners" <?php if ( $page['banners-active'] ) echo 'class="active"'; ?>>
                      <span>Banners</span>
                    </a>
                  </li>
                </ul>
              </div>
              <div class="ss-campaign-entity-metabox postbox"><!-- TODO --></div>
            </div>
          <?php } ?>
          <div class="clear"></div>
        </div>
      </div>

      <div class="clear">
        <!--<a href="#" class="button">Add Page</a>-->
      </div>
    <?php } ?>
  <?php } ?>
</div>
