# hestia
This WordPress plugin introduces a number of shortcodes for listing related posts based on post hierarchy.

## Usage
The following shortcodes are introduced:

`[ancestors]`

Lists post ancestors using `get_post_ancestors()`. The `hestia-wrap`, `hestia-ancestor`, `post-{$ID}` and `has-post-thumbnail` CSS classes are available for custom styling.

`[attachments]`

Lists media that has been directly attached to the post. The `hestia-wrap`, `hestia-attachment` and `post-{$ID}` CSS classes are available for custom styling.

`[children]`

Lists child posts of the current post. The `hestia-wrap`, `hestia-child`, `post-{$ID}` and `has-post-thumbnail` CSS classes are available for custom styling.

`[siblings]`

Lists sibling posts (posts with the same parent) of the current post. The `hestia-wrap`, `hestia-sibling`, `post-{$ID}` and `has-post-thumbnail` CSS classes are available for custom styling.

`[sitemap]`

Lists the most recent posts of each public post type. The `hestia-wrap`, `hestia-sitemap` and `post-type-{$post_type}` CSS classes are available for custom styling. Note that before using `$post_type`, "_" are converted to "-".

## Notes and Considerations
* With the exception of `[ancestors]`, all of these shortcodes have a hard limit of 20 posts (for now).
* There is nothing in the output of any of the shortcode to indicate relative hierarchy.
