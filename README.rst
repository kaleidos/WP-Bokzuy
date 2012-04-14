WP-Bokzuy
=========

Wp-Bokzuy is a plugin for wordpress that adds two widgets to display your received badges, to list your timeline or the timeline of some of your groups.

You need a user account from http://bokzuy.com before you can use it.

Instalation:
------------

WP-Bokzuy uses own bokzuy php library calls libbokzuy. This libray makes use of HttpRequest class that it's a PECL extension not included in PHP. At Arch Linux you can install it with::

	pacman -S php php-pear
	pecl install pecl_http

You can find more information about how to install it at http://www.php.net/manual/en/http.install.php.

Then copy the directory WP-Bokzuy to [YOUR_WORDPRESS_INSTANCE]/wp-content/plugins and activate it from the wordpres admin panel like all plugins.

And that's it, all your comments, questions and suggestions are welcome.

Enjoy Bokzuy.-
