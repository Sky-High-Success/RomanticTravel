<div class="wrap ss-wizard-page">
	<div id="ss-logo-small" class="icon32"></div>
	<h2>Evergreen Scarcity (Single page) Setup</h2>

	<br />
	<div class="menu-edit">
		<?php wp_nonce_field('evergreen-pageload-wizard', 'security_token'); ?>

		<!-- Progress Bar -->
		<div id="nav-menu-header">
			<ul class="scarcity-samurai-wizard-progress-bar">
				<li>Campaign Name</li>
				<li>Page Selection</li>
				<li>Page Expiry</li>
				<li>Visualize Scarcity</li>
			</ul>
			<div class="clear"></div>
		</div>

		<!-- Wizard pages -->
		<div id="post-body" class="scarcity-samurai-wizard-content">
			<!--
				Campaign Name
			-->
			<div>
				<h3>Campaign Name</h3>
				<div class="ss-error ss-wizard-validation-errors">
					<p>
						Please fix the following errors:
					</p>
					<ul class="ul-square"></ul>
				</div>
				<p>
					<strong>What should the campaign be called?</strong>
				</p>
				<p class="ss-sub">
					<input class="ss-wizard-campaign-name ss-text-input" type="text" name="ss-wizard-evergreen-pageload-campaign-name" />
				</p>
			</div>

			<!--
				Page Selection
			-->
			<div>
				<h3>Page Selection</h3>
				<div class="ss-error ss-wizard-validation-errors">
					<p>
						Please fix the following errors:
					</p>
					<ul class="ul-square"></ul>
				</div>
				<p>
					<strong>Select the page you would like to add scarcity to:</strong>
				</p>
				<p class="ss-sub">
					<?php
						Scarcity_Samurai_Helper::page_select( array(
							'name' => 'ss-wizard-evergreen-pageload-page-id',
							'in_campaign' => false
						) );

						Scarcity_Samurai_Dialogs::create_new_page_link();
					?>
				</p>
			</div>

			<!--
				Page Expiry
			-->
			<div>
				<h3>Set Page Expiry</h3>
				<div class="ss-error ss-wizard-validation-errors">
					<p>
						Please fix the following errors:
					</p>
					<ul class="ul-square"></ul>
				</div>
				<p>
					You must set an expiry time for Scarcity Samurai to close your offer automatically.
				</p>
				<br />
				<p>
					<strong>How long after a user visits your page should it expire?</strong>
				</p>
				<p class="ss-sub">
					<input class="ss-days-input ss-text-input" type="number" name="ss-wizard-evergreen-pageload-days" min="0" size="2" value="0" /> days,
					<input class="ss-hours-input ss-text-input" type="number" name="ss-wizard-evergreen-pageload-hours" min="0" max="23" size="2" value="0" /> hours,
					<input class="ss-minutes-input ss-text-input" type="number" name="ss-wizard-evergreen-pageload-minutes" min="0" max="59" size="2" value="0" /> mins,
					<input class="ss-seconds-input ss-text-input" type="number" name="ss-wizard-evergreen-pageload-seconds" min="0" max="59" size="2" value="0" /> secs
				</p>
				<br />
				<p>
					<strong>What should happen when your timer reaches zero?</strong>
				</p>
				<ul class="ul-radio">
					<li>
						<label>
							<input type="radio" class="ul-radio" name="ss-wizard-evergreen-pageload-timer-expired" value='do_nothing' checked="checked">
							do nothing
						</label>
					</li>
					<li>
						<label>
							<input type="radio" class="ul-radio" name="ss-wizard-evergreen-pageload-timer-expired" value='redirect_to_page'>
							redirect to page
						</label>
						<div class="ss-opts">
							<p>
								<?php
									Scarcity_Samurai_Helper::page_select( array(
										'name' => 'ss-wizard-evergreen-pageload-timer-expired-redirect-page-id'
									) );

									Scarcity_Samurai_Dialogs::create_new_page_link();
								?>
							</p>
						</div>
					</li>
					<li>
						<label>
							<input type="radio" class="ul-radio" name="ss-wizard-evergreen-pageload-timer-expired" value='redirect_to_url' />
							redirect to URL
						</label>
						<div class="ss-opts">
							<p>
								<input class="ss-text-input" type="text" size="40" name="ss-wizard-evergreen-pageload-timer-expired-redirect-url" />
							</p>
						</div>
					</li>
				</ul>
			</div>

			<!--
				Visualize Scarcity
			-->
			<div>
				<h3>Visualize Your Scarcity Banner</h3>
				<div class="ss-error ss-wizard-validation-errors">
					<p>
						Please fix the following errors:
					</p>
					<ul class="ul-square"></ul>
				</div>
				<p>
					Scarcity is MOST effective when it’s kept in your visitors field of
					view at all times. Here you can specify how you would like your
					scarcity banner to look on your page...
				</p>
				<br />
				<p>
					<strong>Where would you like to place your banner on your page?</strong>
				</p>
				<ul class="ul-radio">
					<li>
						<label>
							<input type="radio" class="ul-radio" name="ss-wizard-evergreen-pageload-banner-position" value="header" checked="checked" />
							in the header
						</label>
					</li>
					<li>
						<label>
							<input type="radio" class="ul-radio" name="ss-wizard-evergreen-pageload-banner-position" value="footer" />
							in the footer
						</label>
					</li>
					<li>
						<label>
							<input type="radio" class="ul-radio" name="ss-wizard-evergreen-pageload-banner-position" value="none" />
							no banner
						</label>
					</li>
				</ul>

				<div class="ss-banner-pos-opts header footer">
					<div>
						<br />
						<p>
							<strong>What would you like your banner to look like?</strong>
						</p>
						<p class="ss-sub">
							<?php
							Scarcity_Samurai_Banner_Editor::banner_select( array(
								'name' => 'ss-wizard-evergreen-pageload-banner-id',
								'selected_banner_id' => Scarcity_Samurai_Banner::default_fixed_banner_id()
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
									<input id="ss-wizard-evergreen-pageload-banner-show-immediately" type="radio" name="ss-wizard-evergreen-pageload-banner-show-type" value="immediately" checked="checked" />
									<label for="ss-wizard-evergreen-pageload-banner-show-immediately">immediately</label>
								</li>
								<li>
									<input id="ss-wizard-evergreen-pageload-banner-show-page-load" type="radio" name="ss-wizard-evergreen-pageload-banner-show-type" value="page_load" />
									<input class="ss-show-value-input ss-text-input" type="number" name="ss-wizard-evergreen-pageload-banner-show-value" min="0" value="0" />
									<label for="ss-wizard-evergreen-pageload-banner-show-page-load">seconds after page load</label>
								</li>
							</ul>
						</p>
					</div>

					<div class="ss-wizard-evergreen-pageload-banner-click-action-wrapper">
						<br />
						<p>
							<strong>
								What would you like to do when the banner is clicked?
							</strong>
						</p>
						<ul class="ul-radio">
							<li>
								<label>
									<input class="ul-radio" type="radio" name="ss-wizard-evergreen-pageload-banner-action" value="do_nothing" checked="checked" />
									do nothing
								</label>
							</li>
							<li>
								<label>
									<input class="ul-radio" type="radio" name="ss-wizard-evergreen-pageload-banner-action" value="redirect_to_page" />
									go to page
								</label>
								<div class="ss-opts">
									<p>
										<?php
											Scarcity_Samurai_Helper::page_select( array(
												'name' => 'ss-wizard-evergreen-pageload-banner-action-redirect-page-id'
											) );

											Scarcity_Samurai_Dialogs::create_new_page_link();
										?>
									</p>
								</div>
							</li>
							<li>
								<label>
									<input class="ul-radio" type="radio" name="ss-wizard-evergreen-pageload-banner-action" value="redirect_to_url" />
									go to URL
								</label>
								<div class="ss-opts">
									<p>
										<input class="ss-text-input" type="text" size="40" name="ss-wizard-evergreen-pageload-banner-action-redirect-url" />
									</p>
								</div>
							</li>
						</ul>
					</div>
				</div>

				<div class="ss-banner-pos-opts content">
					<div>
						<br />
						<p>
							<strong>
								Where in your page would you like to place your banner?
							</strong>
						</p>
						<p>
							To insert your banner, place your mouse cursor in your content
							area where you would like it to go, and then click the
							"Add Inline Banner" button.
						</p>
						<p class="description">
							Note: You can add (or remove) banners later on via the Edit Page
							screen as well.
						</p>
						<br />
						<?php Scarcity_Samurai_Helper::page_editor(); ?>
					</div>
				</div>
			</div>

			<!--
				Finished
			-->
			<div>
				<h3>Finished</h3>

				<p>
					Congratulations! You have now successfully set up your scarcity campaign.
				</p>

				<div class="ss-error ss-wizard-evergreen-pageload-unpublished-page-warning">
					The following page need to be published for this campaign to function correctly.
					&nbsp;
					<a class="button">Publish This Page Now</a>
					<ul class="ul-square"></ul>
				</div>

				<br />

				<div class="ss-wizard-evergreen-pageload-page-link">
					<h3>Page Link</h3>
					<p>
						To send users to your page, use the following link:
					</p>
					<div class="ss-sub">
						<ul class="ul-checklist">
							<li class="incomplete">
								<strong class="ss-wizard-evergreen-pageload-page-title"></strong> &mdash;
								<a class="ss-wizard-evergreen-pageload-page-edit-link">Edit</a> |
								<a class="ss-wizard-evergreen-pageload-page-view-link">View</a>
								<p>
									Link:
									<input class="ss-wizard-evergreen-pageload-page-email-url ss-page-url ss-text-input" type="text" readonly="readonly" />
									&nbsp;
									<a class="button ss-wizard-evergreen-pageload-copy-to-clipboard-button">Copy To Clipboard</a>
									&nbsp;
									<span class="ss-copy-to-clipboard-confirmation-message">Copied</span>
								</p>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<!-- Buttons -->
		<div id="nav-menu-footer" class="scarcity-samurai-wizard-buttons">
			<a class="back button left">&laquo; Back</a>
			<a class="continue button-primary right">Continue &raquo;</a>
			<a class="finish button-primary right">Finish</a>
			<a class="view_campaign button-primary right">View Campaign</a>
			<div class="clear"></div>
		</div>
	</div>
</div>
