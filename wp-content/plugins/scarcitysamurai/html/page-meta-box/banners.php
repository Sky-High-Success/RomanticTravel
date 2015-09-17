<div id="ss-page-banners-tab-panel" class="wp-tab-panel ss-page-tab-panel">
  <h2>Banners</h2>
  <p>
    Banners provide instantly recognisable scarcity to a page by displaying a
    countdown timer.
  </p>
  <p>
    Here you can activate and customise the banners which will appear on this page.
  </p>

  <br />
  <div>
    <p>
      <label>
        <?php
          $checked = ($header_banner_attributes !== null) && $header_banner_attributes['enabled'];
        ?>
        <input class="ss-ui-toggle"
               type="checkbox"
               name="ss-page-header-banner-enable"
               data-ss-ui-toggle-element-class="ss-header-banner-configuration"
               <?php checked( $checked ); ?> />
        Enable Header Banner
      </label>
    </p>
    <div class="ss-header-banner-configuration ss-sub">
      <p>
        <strong>What would you like your banner to look like?</strong>
      </p>
      <p class="ss-sub">
        <?php
        Scarcity_Samurai_Banner_Editor::banner_select( array(
          'selected_banner_id' => $header_banner_attributes['id'],
          'page_id' => $page['id'],
          'name' => 'ss-page-header-banner-id'
        ) );
        ?>
      </p>
      <br />

      <p>
        <strong>When would you like your banner to appear?</strong>
      </p>
      <p class="ss-sub">
        <ul class="ul-radio">
          <li>
            <?php
              $show = $header_banner_attributes['data']['show'];
              $checked = ! isset( $show ) || ( $show['type'] === 'immediately' );
              $value = isset( $show['value'] ) ? $show['value'] : 0;
            ?>
            <input id="ss-page-header-banner-show-immediately" type="radio" name="ss-page-header-banner-show-type" value="immediately" <?php checked( $checked ); ?> />
            <label for="ss-page-header-banner-show-immediately">immediately</label>
          </li>
          <li>
            <?php
              $checked = isset( $show ) && ( $show['type'] === 'page_load' );
            ?>
            <input id="ss-page-header-banner-show-page-load" type="radio" name="ss-page-header-banner-show-type" value="page_load" <?php checked( $checked ); ?> />
            <input class="ss-show-value-input ss-text-input" type="number" name="ss-page-header-banner-show-value" min="0" value="<?php echo $value; ?>" />
            <label for="ss-page-header-banner-show-page-load">seconds after page load</label>
          </li>
        </ul>
      </p>
      <br />

      <p>
        <strong>What would you like to do when the banner is clicked?</strong>
      </p>
      <ul class="ul-radio">
        <li>
          <label>
            <?php
              $action = $header_banner_attributes['data']['action'];
              $checked = ($action === null) || array_key_exists('do_nothing', $action);
            ?>
            <input class="ul-radio" type="radio" name="ss-page-header-banner-action" value="do_nothing" <?php checked( $checked ); ?> />
            do nothing
          </label>
        </li>
        <li>
          <label>
            <?php
              $redirect_page_id = (($action !== null) &&
                                   array_key_exists('redirect', $action) &&
                                   array_key_exists('page_id', $action['redirect']) ?
                                   $action['redirect']['page_id'] :
                                   null);
              $checked = ($redirect_page_id !== null);
            ?>
            <input class="ul-radio" type="radio" name="ss-page-header-banner-action" value="redirect_to_page" <?php checked( $checked ); ?> />
            go to page
          </label>
          <div class="ss-opts">
            <p>
              <?php
                Scarcity_Samurai_Helper::page_select( array(
                  'name' => 'ss-page-header-banner-action-redirect-page-id',
                  'selected' => $redirect_page_id
                ) );

                Scarcity_Samurai_Dialogs::create_new_page_link();
              ?>
            </p>
          </div>
        </li>
        <li>
          <label>
            <?php
              $redirect_url = (($action !== null) &&
                               array_key_exists('redirect', $action) &&
                               array_key_exists('url', $action['redirect']) ?
                               $action['redirect']['url'] :
                               null);
              $checked = ($redirect_url !== null);
            ?>
            <input class="ul-radio" type="radio" name="ss-page-header-banner-action" value="redirect_to_url" <?php checked( $checked ); ?> />
            go to URL
          </label>
          <div class="ss-opts">
            <p>
              <input class="ss-text-input" type="text" size="40" name="ss-page-header-banner-action-redirect-url" value="<?php echo $redirect_url; ?>">
            </p>
          </div>
        </li>
      </ul>
    </div>
  </div>

  <br />
  <div>
    <p>
      <label>
        <?php
          $checked = ($footer_banner_attributes !== null) && $footer_banner_attributes['enabled'];
        ?>
        <input class="ss-ui-toggle" type="checkbox" name="ss-page-footer-banner-enable"
               data-ss-ui-toggle-element-class="ss-footer-banner-configuration"
               <?php checked( $checked ); ?> />
        Enable Footer Banner
      </label>
    </p>
    <div class="ss-footer-banner-configuration ss-sub">
      <p>
        <strong>What would you like your banner to look like?</strong>
      </p>
      <p class="ss-sub">
        <?php
        Scarcity_Samurai_Banner_Editor::banner_select( array(
          'selected_banner_id' => $footer_banner_attributes['id'],
          'page_id' => $page['id'],
          'name' => 'ss-page-footer-banner-id'
        ) );
        ?>
      </p>
      <br />

      <p>
        <strong>When would you like your banner to appear?</strong>
      </p>
      <p class="ss-sub">
        <ul class="ul-radio">
          <li>
            <?php
              $show = $footer_banner_attributes['data']['show'];
              $checked = ! isset( $show ) || ( $show['type'] === 'immediately' );
              $value = isset( $show['value'] ) ? $show['value'] : 0;
            ?>
            <input id="ss-page-footer-banner-show-immediately" type="radio" name="ss-page-footer-banner-show-type" value="immediately" <?php checked( $checked ); ?> />
            <label for="ss-page-footer-banner-show-immediately">immediately</label>
          </li>
          <li>
            <?php
              $checked = isset( $show ) && ( $show['type'] === 'page_load' );
            ?>
            <input id="ss-page-footer-banner-show-page-load" type="radio" name="ss-page-footer-banner-show-type" value="page_load" <?php checked( $checked ); ?> />
            <input class="ss-show-value-input ss-text-input" type="number" name="ss-page-footer-banner-show-value" min="0" value="<?php echo $value; ?>" />
            <label for="ss-page-footer-banner-show-page-load">seconds after page load</label>
          </li>
        </ul>
      </p>
      <br />

      <p>
        <strong>What would you like to do when the banner is clicked?</strong>
      </p>
      <ul class="ul-radio">
        <li>
          <label>
            <?php
              $action = $footer_banner_attributes['data']['action'];
              $checked = ($action === null) || array_key_exists('do_nothing', $action);
            ?>
            <input class="ul-radio" type="radio" name="ss-page-footer-banner-action" value="do_nothing" <?php checked( $checked ); ?> />
            do nothing
          </label>
        </li>
        <li>
          <label>
            <?php
              $redirect_page_id = (($action !== null) &&
                                   array_key_exists('redirect', $action) &&
                                   array_key_exists('page_id', $action['redirect']) ?
                                   $action['redirect']['page_id'] :
                                   null);
              $checked = ($redirect_page_id !== null);
            ?>
            <input class="ul-radio" type="radio" name="ss-page-footer-banner-action" value="redirect_to_page" <?php checked( $checked ); ?> />
            go to page
          </label>
          <div class="ss-opts">
            <p>
              <?php
                Scarcity_Samurai_Helper::page_select( array(
                  'name' => 'ss-page-footer-banner-action-redirect-page-id',
                  'selected' => $redirect_page_id
                ) );

                Scarcity_Samurai_Dialogs::create_new_page_link();
              ?>
            </p>
          </div>
        </li>
        <li>
          <label>
            <?php
              $redirect_url = (($action !== null) &&
                               array_key_exists('redirect', $action) &&
                               array_key_exists('url', $action['redirect']) ?
                               $action['redirect']['url'] :
                               null);
              $checked = ($redirect_url !== null);
            ?>
            <input class="ul-radio" type="radio" name="ss-page-footer-banner-action" value="redirect_to_url" <?php checked( $checked ); ?> />
            go to URL
          </label>
          <div class="ss-opts">
            <p>
              <input class="ss-text-input" type="text" size="40" name="ss-page-footer-banner-action-redirect-url" value="<?php echo $redirect_url; ?>">
            </p>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>
