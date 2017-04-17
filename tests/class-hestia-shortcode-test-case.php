<?php

class Hestia_Shortcode_Test_Case extends WP_UnitTestCase {
	protected function assertShortcodeContent( $expected, $actual ) {
		$clean_actual = preg_replace(
			'/\s{2,}/',
			' ',
			trim( wp_strip_all_tags( $actual ) )
		);

		$this->assertEquals( $expected, $clean_actual );
	}
}
