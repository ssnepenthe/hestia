<?php

class Bootstrap_Test extends WP_UnitTestCase {
	/** @test */
	function it_shares_a_single_plugin_instance() {
		$this->assertSame( _hestia_instance(), _hestia_instance() );
	}
}
