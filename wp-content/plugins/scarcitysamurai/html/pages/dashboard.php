<div class="wrap">
	<div class="welcome-panel ss-welcome-panel <?php echo 'ss-' . Scarcity_Samurai_Access::$d . '-access'; ?>">
		<img class="ss-logo" src="<?php echo Scarcity_Samurai_Helper::url( 'images/scarcity-samurai-logo.png' ); ?>" />
		<div class="welcome-panel-content">
			<div class="ss-welcome-message">
				<h3>Welcome to Scarcity Samurai!</h3>
				<p>
					You're just a few clicks away from supercharging your sales,
					your income, and your lifestyle with the most effective scarcity-based
					marketing system in the world...
				</p>
			</div>
			<?php if ( Scarcity_Samurai_Access::$d === 'trial_expired' ) { ?>
				<div class="ss-trial-banner">
					<div>
						<div>
							Your Trial Has Expired.
						</div>
						<div>
							<a class="button-primary ss-upgrade-now-button" href="<?php esc_attr_e( $f ); ?>" target="_blank">Upgrade Now</a>
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if ( Scarcity_Samurai_Access::$d === 'trial_not_expired' ) { ?>
				<div class="ss-trial-banner">
					<div>
						<div class="ss-trial-expires-in">
							Your Trial Expires In
						</div>
						<div class="ss-timer-dashboard ss-styled-timer">
							<div class="ss-timer-days">
								<span class="ss-timer-days-label ss-timer-label">days</span>
								<span class="ss-timer-value ss-days">00</span>
							</div>
							<div class="ss-timer-hours">
								<span class="ss-timer-hours-label ss-timer-label">hours</span>
								<span class="ss-timer-value ss-hours">00</span>
							</div>
							<div class="ss-timer-minutes">
								<span class="ss-timer-minutes-label ss-timer-label">mins</span>
								<span class="ss-timer-value ss-minutes">00</span>
							</div>
							<div class="ss-timer-seconds">
								<span class="ss-timer-seconds-label ss-timer-label">secs</span>
								<span class="ss-timer-value ss-seconds">00</span>
							</div>
						</div>
					</div>
					<div>
						<div class="ss-upgrade-ss-to-pro">
							Upgrade to Scarcity Samurai Pro before the<br />
							end of your trial and <strong>receive 50% OFF</strong>...
						</div>
						<div>
							<a class="button-primary ss-upgrade-now-button" href="<?php esc_attr_e( $f ); ?>" target="_blank">Upgrade Now</a>
						</div>
					</div>
				</div>
			<?php	}	?>
		</div>
		<?php if ( $update_url !== null ) { ?>
		<div class="welcome-panel-update-available">
			<div class="welcome-panel-update-background"></div>
			<p>There is a new version of Scarcity Samurai available. <a href="<?php echo esc_attr( $update_url ); ?>">Update now</a></p>
		</div>
		<?php } ?>
	</div>
	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder columns-2">
			<div class="postbox-container">
				<div id="postbox-container-1" class="meta-box-sortables">
					<?php
						$theme = wp_get_theme();
						if ( $theme->name === 'OptimizePress' && version_compare( $theme->version, '2', '>=' ) ) {
					?>
					<div class="error">
						<p><strong>NOTE:</strong> We’ve detected that you are using Optimize Press 2</p>
						<p>Here are some KB articles to integrate with our supported auto responder:<br />
							<a target="_blank" href="http://noblesamurai.zendesk.com/entries/39259174-Configuring-GetResponse-to-work-with-OptimizePress-2">GetResponse</a>,
							<a target="_blank" href="http://noblesamurai.zendesk.com/entries/38382320-Configuring-MailChimp-to-work-with-OptimizePress-2">Mailchimp</a>,
							<a target="_blank" href="http://noblesamurai.zendesk.com/entries/39723140-Configuring-AWeber-to-work-with-OptimizePress-2">AWeber</a>,
							<a target="_blank" href="http://noblesamurai.zendesk.com/entries/37919784-Configuring-SendPepper-to-work-with-OptimizePress-2">SendPepper</a>
						</p>
					</div>
					<?php } ?>
					<div class="postbox ss-wizards">
						<h3 class="hndle nodrag"><span>Campaign Wizards</span></h3>
						<ul>
							<li class="inside <?php echo $evergreen_optin_wizard_is_available ? '' : 'ss-unavailable'; ?>">
								<?php if ( $evergreen_optin_wizard_is_available ) { ?>
									<img class="ss-wizard-icon" src="<?php echo Scarcity_Samurai_Helper::url( 'images/wizards/evergreen-optin.png' ); ?>" />
									<a class="ss-wizard-button button-primary" href="<?php esc_attr_e( admin_url( 'admin.php?page=scarcitysamurai/campaign_wizards&wizard=evergreen-optin' ) ); ?>">Create &raquo;</a>
								<?php } else { ?>
									<img class="ss-wizard-icon" src="<?php echo Scarcity_Samurai_Helper::url( 'images/wizards/evergreen-optin-unavailable.png' ); ?>" />
									<a class="ss-wizard-button ss-upgrade-button button-primary" href="<?php esc_attr_e( $f ); ?>" target="_blank">Upgrade</a>
								<?php } ?>
								<strong class="ss-wizard-name">Evergreen Scarcity (Multi-page campaign)</strong>
								<span class="description ss-wizard-description">
									For promotions containing a sequence of pages and emails
									that unfold over a number of days.<br />
									This type of campaign is set into motion when a user signs up to your list.
									<em>
										(e.g. When you want to make your product launch last forever)
									</em>
								</span>
							</li>
							<li class="inside <?php echo $fixed_date_multi_wizard_is_available ? '' : 'ss-unavailable'; ?>">
								<?php if ( $fixed_date_multi_wizard_is_available ) { ?>
									<img class="ss-wizard-icon" src="<?php echo Scarcity_Samurai_Helper::url( 'images/wizards/fixed-date-multi.png' ); ?>" />
									<a class="ss-wizard-button button-primary" href="<?php esc_attr_e( admin_url( 'admin.php?page=scarcitysamurai/campaign_wizards&wizard=fixed-date-multi' ) ); ?>">Create &raquo;</a>
								<?php } else { ?>
									<img class="ss-wizard-icon" src="<?php echo Scarcity_Samurai_Helper::url( 'images/wizards/fixed-date-multi-unavailable.png' ); ?>" />
									<a class="ss-wizard-button ss-upgrade-button button-primary" href="<?php esc_attr_e( $f ); ?>" target="_blank">Upgrade</a>
								<?php } ?>
								<strong class="ss-wizard-name">Fixed Date Scarcity (Multi-page campaign)</strong>
								<span class="description ss-wizard-description">
									For promotions containing a sequence of pages and emails
									over a number of days.<br />
									This type of promotion has a specific lifespan.
									<em>
										(e.g. for a special launch promotion, such as a 50%
										discount in the first week)
									</em>
								</span>
							</li>
							<li class="inside <?php echo $evergreen_pageload_wizard_is_available ? '' : 'ss-unavailable'; ?>">
								<?php if ( $evergreen_pageload_wizard_is_available ) { ?>
									<img class="ss-wizard-icon" src="<?php echo Scarcity_Samurai_Helper::url( 'images/wizards/evergreen-pageload.png' ); ?>" />
									<a class="ss-wizard-button button-primary" href="<?php esc_attr_e( admin_url( 'admin.php?page=scarcitysamurai/campaign_wizards&wizard=evergreen-pageload' ) ); ?>">Create &raquo;</a>
								<?php } else { ?>
									<img class="ss-wizard-icon" src="<?php echo Scarcity_Samurai_Helper::url( 'images/wizards/evergreen-pageload-unavailable.png' ); ?>" />
									<a class="ss-wizard-button ss-upgrade-button button-primary" href="<?php esc_attr_e( $f ); ?>" target="_blank">Upgrade</a>
								<?php } ?>
								<strong class="ss-wizard-name">Evergreen Scarcity (Single page campaign)</strong>
								<span class="description ss-wizard-description">
									For promotions containing only one page.<br />
									This type of campaign is set into motion when a user visits your page.
									<em>
										(e.g. When someone visits your squeeze page, sales page,
										survey page, competition page etc)
									</em>
								</span>
							</li>
							<li class="inside <?php echo $fixed_date_single_wizard_is_available ? '' : 'ss-unavailable'; ?>">
								<?php if ( $fixed_date_single_wizard_is_available ) { ?>
									<img class="ss-wizard-icon" src="<?php echo Scarcity_Samurai_Helper::url( 'images/wizards/fixed-date-single.png' ); ?>" />
									<a class="ss-wizard-button button-primary" href="<?php esc_attr_e( admin_url( 'admin.php?page=scarcitysamurai/campaign_wizards&wizard=fixed-date-single' ) ); ?>">Create &raquo;</a>
								<?php } else { ?>
									<img class="ss-wizard-icon" src="<?php echo Scarcity_Samurai_Helper::url( 'images/wizards/fixed-date-single-unavailable.png' ); ?>" />
									<a class="ss-wizard-button ss-upgrade-button button-primary" href="<?php esc_attr_e( $f ); ?>" target="_blank">Upgrade</a>
								<?php } ?>
								<strong class="ss-wizard-name">Fixed Date Scarcity (Single page campaign)</strong>
								<span class="description ss-wizard-description">
									For promotions containing a single page.<br />
									This style of	scarcity can be used for limited-time offers which have a	fixed time period.
									<em>
										(e.g. Christmas specials, Black Friday deals - deals which
										must expire at a fixed time)
									</em>
								</span>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div id="postbox-container-2" class="postbox-container">
				<div class="meta-box-sortables">
					<div class="postbox">
						<h3 class="hndle nodrag"><span>Getting Started</span></h3>
						<div class="inside">
							<div class="ss-video">
								<iframe src="//www.youtube.com/embed/dGbcZERpT6o?wmode=opaque" frameborder="0"></iframe>
							</div>
							<br />
							<div class="ss-video">
								<iframe src="//www.youtube.com/embed/6lsNfYnNyZ4?wmode=opaque" frameborder="0"></iframe>
							</div>
							<br />
							<div class="ss-video">
								<iframe src="//www.youtube.com/embed/RTLwVQv-Qlo?wmode=opaque" frameborder="0"></iframe>
							</div>
							<br />
							<div class="ss-video">
								<iframe src="//www.youtube.com/embed/PxWye9TG1oA?wmode=opaque" frameborder="0"></iframe>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</div>
</div>
