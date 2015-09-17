<?php

try {
	require_once('class-scarcity-samurai.php');

	Scarcity_Samurai::uninstall();
} catch ( Exception $e ) {
}
