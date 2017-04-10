# hestia
This WordPress plugin introduces a number of shortcodes for listing related posts based on post hierarchy.

## Requirements
WordPress 4.4 or greater, PHP 7.0 or greater and Composer.

## Installation
Install using Composer:

```
$ composer require ssnepenthe/hestia
```

OR

```
$ cd /path/to/project/wp-content/plugins
$ git clone git@github.com:ssnepenthe/hestia.git
$ cd hestia
$ composer install
```

## Usage
Once the plugin has been activated you will have access to the following shortcodes:

### ancestors
Lists all post ancestors of the current post.

Accepts the following attributes:

* `order`: one of `ASC` or `DESC`. Sets the sort order of found posts. Defaults to `ASC`.
* `thumbnails`: `true` or `false`. Whether or not to include featured images in post list. Defaults to `false`.

**Example:** `[ancestors order="DESC" thumbnails="true"]`

### attachments
Lists all media that has been directly attached to the current post.

Accepts the following attributes:

* `link`: one of `PAGE` or `FILE`. Sets whether to link to the attachment page or the actual attachment file. Defaults to `PAGE`.
* `max`: integer between 1 and 100. Sets the maximum number of attachments to display. Defaults to `20`.
* `order`: one of `ASC` or `DESC`. Sets the sort order of found posts. Defaults to `ASC`.

**Example:** `[attachments link="FILE" max="50" order="DESC"]`

### children
Lists all child posts of the current post.

Accepts the following attributes:

* `max`: integer between 1 and 100. Sets the maximum number of children to display. Defaults to `20`.
* `order`: one of `ASC` or `DESC`. Sets the sort order of found posts. Defaults to `ASC`.
* `thumbnails`: `true` or `false`. Whether or not to include featured images in post list. Defaults to `false`.

**Example:** `[children max="35" order="DESC" thumbnails="true"]`

### siblings
Lists sibling posts of the current post.

Accepts the following attributes:

* `max`: integer between 1 and 100. Sets the maximum number of siblings to display. Defaults to `20`.
* `order`: one of `ASC` or `DESC`. Sets the sort order of found posts. Defaults to `ASC`.
* `thumbnails`: `true` or `false`. Whether or not to include featured images in post list. Defaults to `false`.

**Example:** `[siblings max="65" order="DESC" thumbnails="true"]`

### sitemap
Lists the most recent posts of each public post type.

Accepts the following attributes:

* `max`: integer between 1 and 100. Sets the maximum number of posts to display per post type. Defaults to `20`.
* `order`: one of `ASC` or `DESC`. Sets the sort order of found posts. Defaults to `ASC`.

**Example:** `[sitemap max="100" order="DESC"]`

## Caching
I have done my best to optimize the queries performed by each shortcode, but depending on how they are used it is still possible for them to be pretty resource intensive.

With that in mind, shortcode output is cached in the transient cache on a per post/per attributes basis. The default lifetime is 600 seconds (10 minutes) and is filterable per-shortcode using the `hestia_{$shortcode}_cache_lifetime` filter.

For example, to cache sitemap output for 24 hours:

```PHP
add_filter( 'hestia_sitemap_cache_lifetime', function( $lifetime ) {
    // Lifetime is in seconds.
    return 60 * 60 * 24;
} );
```

## Custom Output
Shortcode output can be overridden within a theme. To do so, create the following PHP files either in your theme root or in a templates subdirectory:

```
hestia-ancestors.php
hestia-attachments.php
hestia-children.php
hestia-siblings.php
hestia-sitemap.php
```

View the existing plugin templates for an idea of what data is available to each.
