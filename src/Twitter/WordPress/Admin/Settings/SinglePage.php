<?php
/*
The MIT License (MIT)

Copyright (c) 2015 Twitter Inc.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

namespace Twitter\WordPress\Admin\Settings;

/**
 * Combine Twitter settings into a single settings page
 *
 * @since 1.0.0
 */
class SinglePage
{
	use Template;

	/**
	 * Settings page identifier.
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const PAGE_SLUG = 'twitter';

	/**
	 * The hook suffix assigned by add_utility_page()
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	protected $hook_suffix;

	/**
	 * Class names containing single page settings components
	 *
	 * The addToSettingsPage method is called on each class to initialize its components
	 *
	 * @since 1.0.0
	 *
	 * @type array settings component fully qualified class names {
	 *   @type string fully qualified class name
	 * }
	 */
	protected static $SETTINGS_COMPONENTS = array( '\Twitter\WordPress\Admin\Settings\Theme', '\Twitter\WordPress\Admin\Settings\SiteAttribution', '\Twitter\WordPress\Admin\Settings\TweetButton' );

	/**
	 * Reference the feature by name
	 *
	 * @since 1.0.0
	 *
	 * @return string translated feature name
	 */
	public static function featureName()
	{
		return __( 'Twitter settings', 'twitter' );
	}

	/**
	 * Add a submenu item to WordPress admin.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null page hook or null if page capability requirements not met
	 */
	public static function menuItem()
	{
		$settings = new static();

		$hook_suffix = add_utility_page(
			static::featureName(), // page <title>
			'Twitter', // brand name. not translated
			'manage_options', // capability needed
			static::PAGE_SLUG, // unique menu slug
			array( &$settings, 'settingsPage' ), // pageload callback
			'dashicons-twitter' // to be replaced by dashicon
		);

		// hook_suffix may be false if current viewer does not have the manage_options capability
		if ( ! $hook_suffix ) {
			return;
		}
		$settings->hook_suffix = $hook_suffix;

		// add each settings component to the single page settings page
		foreach ( static::$SETTINGS_COMPONENTS as $settings_component ) {
			$settings_component::addToSettingsPage( $hook_suffix );
		}

		add_action(
			'load-' . $hook_suffix,
			array( &$settings, 'addContextualHelp' ),
			99,
			0
		);

		return $hook_suffix;
	}

	/**
	 * Add contextual help content to the settings screen
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function addContextualHelp()
	{
		$screen = get_current_screen();
		if ( ! $screen ) { // null if global not set
			return;
		}

		do_action( 'add-' . $this->hook_suffix . '-help-tab', $screen );
	}
}
