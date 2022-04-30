<?php
namespace AIOSEO\Plugin\Common\Traits\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contains all third-party related helper methods.
 *
 * @since 4.1.4
 */
trait ThirdParty {
	/**
	 * Checks whether WooCommerce is active.
	 *
	 * @since 4.0.0
	 *
	 * @return boolean Whether WooCommerce is active.
	 */
	public function isWooCommerceActive() {
		return class_exists( 'woocommerce' );
	}

	/**
	 * Checks if the current page is a special WooCommerce page (Cart, Checkout, ...).
	 *
	 * @since 4.0.0
	 *
	 * @param  int         $postId The post ID.
	 * @return string|bool         The type of page or false if it isn't a WooCommerce page.
	 */
	public function isWooCommercePage( $postId = 0 ) {
		if ( ! $this->isWooCommerceActive() ) {
			return false;
		}

		$postId = $postId ? $postId : get_the_ID();

		static $cartPageId;
		if ( ! $cartPageId ) {
			$cartPageId = (int) get_option( 'woocommerce_cart_page_id' );
		}

		static $checkoutPageId;
		if ( ! $checkoutPageId ) {
			$checkoutPageId = (int) get_option( 'woocommerce_checkout_page_id' );
		}

		static $myAccountPageId;
		if ( ! $myAccountPageId ) {
			$myAccountPageId = (int) get_option( 'woocommerce_myaccount_page_id' );
		}

		static $termsPageId;
		if ( ! $termsPageId ) {
			$termsPageId = (int) get_option( 'woocommerce_terms_page_id' );
		}

		switch ( $postId ) {
			case $cartPageId:
				return 'cart';
			case $checkoutPageId:
				return 'checkout';
			case $myAccountPageId:
				return 'myAccount';
			case $termsPageId:
				return 'terms';
			default:
				return false;
		}
	}

	/**
	 * Checks whether the current page is a special WooCommerce page we shouldn't show our schema settings for.
	 *
	 * @since 4.1.6
	 *
	 * @param  int  $postId The post ID.
	 * @return bool         Whether the current page is a disallowed WooCommerce page.
	 */
	public function isWooCommercePageWithoutSchema( $postId = 0 ) {
		$page = $this->isWooCommercePage( $postId );
		if ( ! $page ) {
			return false;
		}

		$disallowedPages = [ 'cart', 'checkout', 'myAccount' ];

		return in_array( $page, $disallowedPages, true );
	}

	/**
	 * Checks whether the queried object is the WooCommerce shop page.
	 *
	 * @since 4.0.0
	 *
	 * @param  int  $id The post ID to check against (optional).
	 * @return bool     Whether the current page is the WooCommerce shop page.
	 */
	public function isWooCommerceShopPage( $id = 0 ) {
		if ( ! $this->isWooCommerceActive() ) {
			return false;
		}

		if ( ! is_admin() && ! aioseo()->helpers->isAjaxCronRestRequest() && function_exists( 'is_shop' ) ) {
			return is_shop();
		}

		$id = ! $id && ! empty( $_GET['post'] ) ? (int) wp_unslash( $_GET['post'] ) : (int) $id; // phpcs:ignore HM.Security.ValidatedSanitizedInput

		return $id && wc_get_page_id( 'shop' ) === $id;
	}

	/**
	 * Checks whether the queried object is the WooCommerce cart page.
	 *
	 * @since 4.1.3
	 *
	 * @param  int  $id The post ID to check against (optional).
	 * @return bool     Whether the current page is the WooCommerce cart page.
	 */
	public function isWooCommerceCartPage( $id = 0 ) {
		if ( ! $this->isWooCommerceActive() ) {
			return false;
		}

		if ( ! is_admin() && ! aioseo()->helpers->isAjaxCronRestRequest() && function_exists( 'is_cart' ) ) {
			return is_cart();
		}

		$id = ! $id && ! empty( $_GET['post'] ) ? (int) wp_unslash( $_GET['post'] ) : (int) $id; // phpcs:ignore HM.Security.ValidatedSanitizedInput

		return $id && wc_get_page_id( 'cart' ) === $id;
	}

	/**
	 * Checks whether the queried object is the WooCommerce checkout page.
	 *
	 * @since 4.1.3
	 *
	 * @param  int  $id The post ID to check against (optional).
	 * @return bool     Whether the current page is the WooCommerce checkout page.
	 */
	public function isWooCommerceCheckoutPage( $id = 0 ) {
		if ( ! $this->isWooCommerceActive() ) {
			return false;
		}

		if ( ! is_admin() && ! aioseo()->helpers->isAjaxCronRestRequest() && function_exists( 'is_checkout' ) ) {
			return is_checkout();
		}

		$id = ! $id && ! empty( $_GET['post'] ) ? (int) wp_unslash( $_GET['post'] ) : (int) $id; // phpcs:ignore HM.Security.ValidatedSanitizedInput

		return $id && wc_get_page_id( 'checkout' ) === $id;
	}

	/**
	 * Checks whether the queried object is the WooCommerce account page.
	 *
	 * @since 4.1.3
	 *
	 * @param  int  $id The post ID to check against (optional).
	 * @return bool     Whether the current page is the WooCommerce account page.
	 */
	public function isWooCommerceAccountPage( $id = 0 ) {
		if ( ! $this->isWooCommerceActive() ) {
			return false;
		}

		if ( ! is_admin() && ! aioseo()->helpers->isAjaxCronRestRequest() && function_exists( 'is_account_page' ) ) {
			return is_account_page();
		}

		$id = ! $id && ! empty( $_GET['post'] ) ? (int) wp_unslash( $_GET['post'] ) : (int) $id; // phpcs:ignore HM.Security.ValidatedSanitizedInput

		return $id && wc_get_page_id( 'myaccount' ) === $id;
	}

	/**
	 * Internationalize.
	 *
	 * @since 4.0.0
	 *
	 * @param $in
	 * @return mixed|void
	 */
	public function internationalize( $in ) {
		if ( function_exists( 'langswitch_filter_langs_with_message' ) ) {
			$in = langswitch_filter_langs_with_message( $in );
		}

		if ( function_exists( 'polyglot_filter' ) ) {
			$in = polyglot_filter( $in );
		}

		if ( function_exists( 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) ) {
			$in = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage( $in );
		} elseif ( function_exists( 'ppqtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) ) {
			$in = ppqtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage( $in );
		} elseif ( function_exists( 'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) ) {
			$in = qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage( $in );
		}

		return apply_filters( 'localization', $in );
	}

	/**
	 * Checks if WPML is active.
	 *
	 * @since 4.0.0
	 *
	 * @return bool True if it is, false if not.
	 */
	public function isWpmlActive() {
		return class_exists( 'SitePress' );
	}

	/**
	 * Localizes a given URL.
	 *
	 * This is required for compatibility with WPML.
	 *
	 * @since 4.0.0
	 *
	 * @param  string $path The relative path of the URL.
	 * @return string $url  The filtered URL.
	 */
	public function localizedUrl( $path ) {
		$url = apply_filters( 'wpml_home_url', home_url( '/' ) );

		// Remove URL parameters.
		preg_match_all( '/\?[\s\S]+/', $url, $matches );

		// Get the base URL.
		$url  = preg_replace( '/\?[\s\S]+/', '', $url );
		$url  = trailingslashit( $url );
		$url .= preg_replace( '/\//', '', $path, 1 );

		// Readd URL parameters.
		if ( $matches && $matches[0] ) {
			$url .= $matches[0][0];
		}

		return $url;
	}

	/**
	 * Checks whether BuddyPress is active.
	 *
	 * @since 4.0.0
	 *
	 * @return boolean
	 */
	public function isBuddyPressActive() {
		return class_exists( 'BuddyPress' );
	}

	/**
	 * Checks whether the queried object is a buddy press user page.
	 *
	 * @since 4.0.0
	 *
	 * @return boolean
	 */
	public function isBuddyPressUser() {
		return $this->isBuddyPressActive() && function_exists( 'bp_is_user' ) && bp_is_user();
	}

	/**
	 * Returns if the page is a BuddyPress page (Activity, Members, Groups).
	 *
	 * @since 4.0.0
	 *
	 * @param  int     $postId The post ID.
	 * @return boolean         If the page is a BuddyPress page or not.
	 */
	public function isBuddyPressPage( $postId = false ) {
		$bpPages = get_option( 'bp-pages' );

		if ( empty( $bpPages ) ) {
			return false;
		}

		foreach ( $bpPages as $page ) {
			if ( (int) $page === (int) $postId ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns ACF fields as an array of meta keys and values.
	 *
	 * @since 4.0.6
	 *
	 * @param  WP_Post|int $post         The post.
	 * @param  array       $allowedTypes A whitelist of ACF field types.
	 * @return array                     An array of meta keys and values.
	 */
	public function getAcfContent( $post = null, $types = [] ) {
		$post = ( $post && is_object( $post ) ) ? $post : $this->getPost( $post );

		if ( ! class_exists( 'ACF' ) || ! function_exists( 'get_field_objects' ) ) {
			return [];
		}

		if ( defined( 'ACF_VERSION' ) && version_compare( ACF_VERSION, '5.7.0', '<' ) ) {
			return [];
		}

		// Set defaults.
		$allowedTypes = [
			'text',
			'textarea',
			'email',
			'url',
			'wysiwyg',
			'image',
			'gallery',
			// 'link',
			// 'taxonomy',
		];

		$types     = wp_parse_args( $types, $allowedTypes );
		$acfFields = [];

		$fieldObjects = get_field_objects( $post->ID );
		if ( ! empty( $fieldObjects ) ) {
			foreach ( $fieldObjects as $field ) {
				if ( empty( $field['value'] ) ) {
					continue;
				}

				if ( ! in_array( $field['type'], $types, true ) ) {
					continue;
				}

				if ( 'url' === $field['type'] ) {
					// Url field
					$value = "<a href='{$field['value']}'>{$field['value']}</a>";
				} elseif ( 'image' === $field['type'] ) {
					// Image field
					$value = "<img src='{$field['value']['url']}'>";
				} elseif ( 'gallery' === $field['type'] ) {
					// Image field
					$value = "<img src='{$field['value'][0]['url']}'>";
				} else {
					// Other fields
					$value = $field['value'];
				}

				if ( $value ) {
					$acfFields[ $field['name'] ] = $value;
				}
			}
		}

		return $acfFields;
	}

	/**
	 * Checks whether the Smash Balloon Custom Facebook Feed plugin is active.
	 *
	 * @since 4.2.0
	 *
	 * @return bool Whether the SB CFF plugin is active.
	 */
	public function isSbCustomFacebookFeedActive() {
		static $isActive = null;
		if ( null !== $isActive ) {
			return $isActive;
		}

		$isActive = defined( 'CFFVER' ) || is_plugin_active( 'custom-facebook-feed/custom-facebook-feed.php' );

		return $isActive;
	}

	/**
	 * Returns the access token for Facebook from Smash Balloon if there is one.
	 *
	 * @since 4.2.0
	 *
	 * @return string|false The access token or false if there is none.
	 */
	public function getSbAccessToken() {
		static $accessToken = null;
		if ( null !== $accessToken ) {
			return $accessToken;
		}

		if ( ! $this->isSbCustomFacebookFeedActive() ) {
			$accessToken = false;

			return $accessToken;
		}

		$oembedTokenData = get_option( 'cff_oembed_token', [] );
		if ( ! $oembedTokenData || empty( $oembedTokenData['access_token'] ) ) {
			$accessToken = false;

			return $accessToken;
		}

		$sbFacebookDataEncryptionInstance = new \CustomFacebookFeed\SB_Facebook_Data_Encryption;
		$accessToken                      = $sbFacebookDataEncryptionInstance->maybe_decrypt( $oembedTokenData['access_token'] );

		return $accessToken;
	}
}