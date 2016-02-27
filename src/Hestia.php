<?php

namespace SSNepenthe\Hestia;

class Hestia {
	protected $name;
	protected $version;

	public function __construct( $name, $version ) {
		$this->name = $name;
		$this->version = $version;
	}

	public function init() {
		$ancestors = new Shortcodes\Ancestors;
		$ancestors->init();

		$attachments = new Shortcodes\Attachments;
		$attachments->init();

		$children = new Shortcodes\Children;
		$children->init();

		$family = new Shortcodes\Family;
		$family->init();

		$siblings = new Shortcodes\Siblings;
		$siblings->init();

		$sitemap = new Shortcodes\Sitemap;
		$sitemap->init();
	}
}
