<div id="ss-add-inline-banner-dialog"
     class="ss-dialog"
     title="Add Inline Banner"
     data-ss-campaign-id="<?php echo $campaign_id; ?>"
     data-ss-in-wizard="<?php echo $in_wizard; ?>">
	<p class="ss-dialog-notice">
		Note: You must assign this page to a campaign in order to see the banners.
	</p>

	<p>
		What would you like your banner to look like?
	</p>
	<p>
		<?php
			Scarcity_Samurai_Banner_Editor::banner_select( array(
				'page_id' => Scarcity_Samurai_Helper::current_page_id(),
				'name' => 'ss-inline-banner-id',
				'inline' => true,
				'selected_banner_id' => Scarcity_Samurai_Banner::default_inline_banner_id()
			) );
		?>
	</p>
	<br />

	<p>
		When would you like your banner to appear?
	</p>
	<ul class="ul-radio">
		<li>
			<input id="ss-inline-banner-show-immediately" type="radio" name="ss-inline-banner-show-type" value="immediately" checked="checked" />
			<label for="ss-inline-banner-show-immediately">immediately</label>
		</li>
		<li>
			<input id="ss-inline-banner-show-page-load" type="radio" name="ss-inline-banner-show-type" value="page_load" />
			<input class="ss-show-value-input ss-text-input" type="number" name="ss-inline-banner-show-value" min="0" value="0" />
			<label for="ss-inline-banner-show-page-load">seconds after page load</label>
		</li>
	</ul>

	<br />
	<p>
		What would you like to do when the banner is clicked?
	</p>
	<ul class="ul-radio">
		<li>
			<label>
				<input class="ul-radio" type="radio" name="ss-inline-banner-action" value="do_nothing" checked="checked" />
				do nothing
			</label>
		</li>
		<li>
			<label>
				<input class="ul-radio" type="radio" name="ss-inline-banner-action" value="redirect_to_page" />
				go to page
			</label>
			<div class="ss-opts">
				<p>
					<?php
						Scarcity_Samurai_Helper::page_select( array(
							'name' => 'ss-inline-banner-action-redirect-page-id'
						) );

						Scarcity_Samurai_Dialogs::create_new_page_link();
					?>
				</p>
			</div>
		</li>
		<li>
			<label>
				<input class="ul-radio" type="radio" name="ss-inline-banner-action" value="redirect_to_url" />
				go to URL
			</label>
			<div class="ss-opts">
				<p>
					<input class="ss-text-input" type="text" size="40" name="ss-inline-banner-action-redirect-url" />
				</p>
			</div>
		</li>
	</ul>
</div>