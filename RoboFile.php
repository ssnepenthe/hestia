<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
	/**
	 * Update version across all relevant files.
	 *
	 * @param string The new plugin version.
	 */
	public function versionBump( $version ) {
		$this->taskReplaceInFile( __DIR__ . '/hestia.php' )
			->regex( '/Version:.*$/m' )
			->to( sprintf( 'Version: %s', $version ) )
			->run();

		$this->taskReplaceInFile( __DIR__ . '/hestia.php' )
			->regex( '/\$version =.*$/m' )
			->to( sprintf( '$version = \'%s\';', $version ) )
			->run();
	}
}
