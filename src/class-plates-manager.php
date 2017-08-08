<?php
/**
 * Plates_Manager class.
 *
 * @package hestia
 */

namespace Hestia;

use League\Plates\Engine;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Defines the plates manager class.
 */
class Plates_Manager {
	/**
	 * List of Engine instances.
	 *
	 * @var Engine[]
	 */
	protected $instances = [];

	/**
	 * Class constructor.
	 *
	 * @param array $dirs List of directories to look for templates in.
	 */
	public function __construct( array $dirs = [] ) {
		foreach ( $dirs as $dir ) {
			$this->add_dir( $dir );
		}
	}

	/**
	 * Add a directory to look in for templates.
	 *
	 * @param string $dir Template directory.
	 *
	 * @return void
	 */
	public function add_dir( $dir ) {
		$this->instances[] = new Engine( $dir );
	}

	/**
	 * Returns the first engine instance for which the template exists.
	 *
	 * @param  string $name Template name.
	 *
	 * @return Engine
	 *
	 * @throws \LogicException When template does not exist in any registered directory.
	 */
	public function make( $name ) {
		foreach ( $this->instances as $instance ) {
			if ( $instance->exists( $name ) ) {
				return $instance;
			}
		}

		throw new \LogicException(
			"The template [{$name}] could not be found in any registered location"
		);
	}

	/**
	 * Render a template using the first engine instance for which that template exists.
	 *
	 * @param  string $name Template name.
	 * @param  array  $data Template data.
	 *
	 * @return string
	 */
	public function render( $name, array $data ) {
		return $this->make( $name )->render( $name, $data );
	}
}
