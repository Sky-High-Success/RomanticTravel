<div class="wrap">
	<div id="ss-logo-small" class="icon32"></div>
	<h2>Campaigns</h2>

	<form id="ss-campaigns-form" method="get">
		<?php wp_nonce_field( 'campaigns', 'security_token' ); ?>

		<input type="hidden" name="page" value="scarcitysamurai/campaigns">
		<?php
			// wp_nonce_field() is called automatically by display()
    	$campaigns_table->display();
		?>
	</form>
</div>
