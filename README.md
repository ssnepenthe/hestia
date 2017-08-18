# hestia
This WordPress plugin introduces a number of shortcodes for listing related posts based on post hierarchy.

## Requirements
PHP 5.6 or greater and Composer.

## Installation
Install using Composer:

```
$ composer require ssnepenthe/hestia
```

## Usage
Once the plugin has been activated you will have access to the following shortcodes:

### ancestors
Lists all post ancestors of the current post.

Accepts the following attributes:

* `id`: ID of the post for which you would like to display ancestors. Defaults to the return value of `get_the_ID()`.
* `order`: one of `ASC` or `DESC`. Sets the sort order of found posts. Defaults to `ASC`.
* `thumbnails`: `true` or `false`. Whether or not to include featured images in post list. Defaults to `false`.

**Example:** `[ancestors order="DESC" thumbnails="true"]`

### attachments
Lists all media that has been directly attached to the current post.

Accepts the following attributes:

* `id`: ID of the post for which you would like to display attachments. Defaults to the return value of `get_the_ID()`.
* `link`: one of `PAGE` or `FILE`. Sets whether to link to the attachment page or the actual attachment file. Defaults to `PAGE`.
* `max`: integer between 1 and 100. Sets the maximum number of attachments to display. Defaults to `20`.
* `order`: one of `ASC` or `DESC`. Sets the sort order of found posts. Defaults to `ASC`.
* `thumbnails`: `true` or `false`. Whether or not to include thumbnails in list. Defaults to `false`.

**Example:** `[attachments link="FILE" max="50" order="DESC" thumbnails="true"]`

### children
Lists all child posts of the current post.

Accepts the following attributes:

* `id`: ID of the post for which you would like to display children. Defaults to the return value of `get_the_ID()`.
* `max`: integer between 1 and 100. Sets the maximum number of children to display. Defaults to `20`.
* `order`: one of `ASC` or `DESC`. Sets the sort order of found posts. Defaults to `ASC`.
* `thumbnails`: `true` or `false`. Whether or not to include featured images in post list. Defaults to `false`.

**Example:** `[children max="35" order="DESC" thumbnails="true"]`

### siblings
Lists sibling posts of the current post.

Accepts the following attributes:

* `id`: ID of the post for which you would like to display siblings. Defaults to the return value of `get_the_ID()`.
* `max`: integer between 1 and 100. Sets the maximum number of siblings to display. Defaults to `20`.
* `order`: one of `ASC` or `DESC`. Sets the sort order of found posts. Defaults to `ASC`.
* `thumbnails`: `true` or `false`. Whether or not to include featured images in post list. Defaults to `false`.

**Example:** `[siblings max="65" order="DESC" thumbnails="true"]`

### sitemap
Lists the most recent posts of each public post type.

Accepts the following attributes:

* `max`: integer between 1 and 100. Sets the maximum number of posts to display per post type. Defaults to `20`.
* `order`: one of `ASC` or `DESC`. Sets the sort order of found posts. Defaults to `ASC`.
* `thumbnails`: `true` or `false`. Whether or not to include featured images in post list. Defaults to `false`.

**Example:** `[sitemap max="100" order="DESC" thumbnails="true"]`

## Custom Output
Shortcode output can be overridden within a theme or child theme. To do so, create the following PHP files either in your theme root or in a templates subdirectory:

```
hestia-ancestors.php
hestia-attachments.php
hestia-children.php
hestia-siblings.php
hestia-sitemap.php
```

These templates do not work the same as a standard WordPress loop - They are rendered using the [Plates template system](http://platesphp.com/) with an array of `WP_Post` objects.

View the existing plugin templates to get an idea of what data will be available in a given file.

### A Note About Custom Templates And Post Meta
By default, all queries are performed **WITHOUT** updating the meta cache unless thumbnails are enabled. It is done this way to minimize the number of database queries performed.

However - if you need to create a custom shortcode template that does access post meta, the result will be an extra database query **PER POST!**

This is obviously not desirable, so the following filters are provided to force a meta cache update:

```
hestia_ancestors_preload_meta
hestia_attachments_preload_meta
hestia_children_preload_meta
hestia_siblings_preload_meta
hestia_sitemap_preload_meta
```

Return `true` to any of these and all post meta will be loaded in a single query up front rather than a query per post.

## Caching
No caching is done by this plugin.

For the most part, these shortcodes run very basic queries and you should not notice any performance impact.

The possible exception is the sitemap shortcode which runs **AT LEAST** two queries per public post type.

If you have a large number of post types I do not recommend using the sitemap shortcode without a solid caching strategy in place.
