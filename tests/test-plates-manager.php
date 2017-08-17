<?php

use Hestia\Plates_Manager;

class Plates_Manager_Test extends WP_UnitTestCase {
	protected $hestia_manager;

	public function setUp() {
		parent::setUp();

		$this->hestia_manager = new Plates_Manager( [
			__DIR__, // No templates should exist here...
			__DIR__ . '/data',
		] );
	}

	public function tearDown() {
		parent::tearDown();

		$this->hestia_manager = null;
	}

	/** @test */
	function it_fetches_the_first_engine_where_template_exists() {
		$this->assertEquals(
			__DIR__ . '/data',
			$this->hestia_manager->make( 'template-for-tests' )->getDirectory()
		);
	}

	/** @test */
	function it_throws_when_no_template_is_found() {
		$this->expectException( LogicException::class );

		$this->hestia_manager->make( 'not-real' );
	}

	/** @test */
	function it_proxies_render_calls_to_first_engine_where_template_exists() {
		$this->assertEquals(
			'Test Template: Hello',
			$this->hestia_manager->render( 'template-for-tests', [ 'greeting' => 'Hello' ] )
		);
	}
}
