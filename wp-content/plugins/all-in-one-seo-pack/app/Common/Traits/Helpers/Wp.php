<?php
namespace AIOSEO\Plugin\Common\Traits\Helpers;

use AIOSEO\Plugin\Common\Utils;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contains all WP related helper methods.
 *
 * @since 4.1.4
 */
trait Wp {
	/**
	 * Whether or not we have a local connection.
	 *
	 * @since 4.0.0
	 *
	 * @var bool
	 */
	private static $connection = false;

	/**
	 * Returns user roles in the current WP install.
	 *
	 * @since 4.0.0
	 *
	 * @return array An array of user roles.
	 */
	public function getUserRoles() {
		global $wp_roles;

		$wpRoles = $wp_roles;
		if ( ! is_object( $wpRoles ) ) {
			// Don't assign this to the global because otherwise WordPress won't override it.
			$wpRoles = new \WP_Roles();
		}

		$roleNames = $wpRoles->get_names();
		asort( $roleNames );

		return $roleNames;
	}

	/**
	 * Returns the custom roles in the current WP install.
	 *
	 * @since 4.1.3
	 *
	 * @return array An array of custom roles.
	 */
	public function getCustomRoles() {
		$allRoles = $this->getUserRoles();

		$toSkip = array_merge(
			// Default WordPress roles.
			[ 'superadmin', 'administrator', 'editor', 'author', 'contributor' ],
			// Default AIOSEO roles.
			[ 'aioseo_manager', 'aioseo_editor' ],
			// Filterable roles.
			apply_filters( 'aioseo_access_control_excluded_roles', array_merge( [
				'subscriber'
			], aioseo()->helpers->isWooCommerceActive() ? [ 'customer' ] : [] ) )
		);

		// Remove empty entries.
		$toSkip = array_filter( $toSkip );

		$customRoles = [];
		foreach ( $allRoles as $roleName => $role ) {
			// Skip specific roles.
			if ( in_array( $roleName, $toSkip, true ) ) {
				continue;
			}

			$customRoles[ $roleName ] = $role;
		}

		return $customRoles;
	}

	/**
	 * Returns an array of plugins with the active status.
	 *
	 * @since 4.0.0
	 *
	 * @return array An array of plugins with active status.
	 */
	public function getPluginData() {
		$pluginUpgrader   = new Utils\PluginUpgraderSilentAjax();
		$installedPlugins = array_keys( get_plugins() );

		$plugins = [];
		foreach ( $pluginUpgrader->pluginSlugs as $key => $slug ) {
			$plugins[ $key ] = [
				'basename'    => $slug,
				'installed'   => in_array( $slug, $installedPlugins, true ),
				'activated'   => is_plugin_active( $slug ),
				'adminUrl'    => admin_url( $pluginUpgrader->pluginAdminUrls[ $key ] ),
				'canInstall'  => aioseo()->addons->canInstall(),
				'canActivate' => aioseo()->addons->canActivate(),
				'canUpdate'   => aioseo()->addons->canUpdate(),
				'wpLink'      => ! empty( $pluginUpgrader->wpPluginLinks[ $key ] ) ? $pluginUpgrader->wpPluginLinks[ $key ] : null
			];
		}

		return $plugins;
	}

	/**
	 * Get all registered Post Statuses.
	 *
	 * @since 4.1.6
	 *
	 * @param  boolean $statusesOnly Whether or not to only return statuses.
	 * @return array              An array of post statuses.
	 */
	public function getPublicPostStatuses( $statusesOnly = false ) {
		$allStatuses = get_post_stati( [ 'show_in_admin_all_list' => true ], 'objects' );

		$postStatuses = [];
		foreach ( $allStatuses as $status => $data ) {
			if (
				! $data->public &&
				! $data->protected &&
				! $data->private
			) {
				continue;
			}

			if ( $statusesOnly ) {
				$postStatuses[] = $status;
				continue;
			}

			$postStatuses[] = [
				'label'  => $data->label,
				'status' => $status
			];
		}

		return $postStatuses;
	}

	/**
	 * Retrieve a list of public post types with slugs/icons.
	 *
	 * @since 4.0.0
	 *
	 * @param  boolean $namesOnly       Whether only the names should be returned.
	 * @param  boolean $hasArchivesOnly Whether or not to only include post types which have archives.
	 * @param  boolean $rewriteType     Whether or not to rewrite the type slugs.
	 * @return array                    An array of public post types.
	 */
	public function getPublicPostTypes( $namesOnly = false, $hasArchivesOnly = false, $rewriteType = false ) {
		$postTypes   = [];
		$postObjects = get_post_types( [ 'public' => true ], 'objects' );
		$woocommerce = class_exists( 'woocommerce' );
		foreach ( $postObjects as $postObject ) {
			if ( empty( $postObject->label ) ) {
				continue;
			}

			// We don't want to include archives for the WooCommerce shop page.
			if (
				$hasArchivesOnly &&
				(
					! $postObject->has_archive ||
					( 'product' === $postObject->name && $woocommerce )
				)
			) {
				continue;
			}

			if ( $namesOnly ) {
				$postTypes[] = $postObject->name;
				continue;
			}

			if ( 'attachment' === $postObject->name ) {
				$postObject->label = __( 'Attachments', 'all-in-one-seo-pack' );
			}

			if ( 'product' === $postObject->name && $woocommerce ) {
				$postObject->menu_icon = 'dashicons-products';
			}

			$name = $postObject->name;
			if ( 'type' === $postObject->name && $rewriteType ) {
				$name = '_aioseo_type';
			}

			$postTypes[] = [
				'name'         => $name,
				'label'        => ucwords( $postObject->label ),
				'singular'     => ucwords( $postObject->labels->singular_name ),
				'icon'         => $postObject->menu_icon,
				'hasExcerpt'   => post_type_supports( $postObject->name, 'excerpt' ),
				'hasArchive'   => $postObject->has_archive,
				'hierarchical' => $postObject->hierarchical,
				'taxonomies'   => get_object_taxonomies( $name ),
				'slug'         => isset( $postObject->rewrite['slug'] ) ? $postObject->rewrite['slug'] : $name
			];
		}

		return apply_filters( 'aioseo_public_post_types', $postTypes, $namesOnly, $hasArchivesOnly );
	}

	/**
	 * Retrieve a list of public taxonomies with slugs/icons.
	 *
	 * @since 4.0.0
	 *
	 * @param  boolean $namesOnly   Whether only the names should be returned.
	 * @param  boolean $rewriteType Whether or not to rewrite the type slugs.
	 * @return array                An array of public taxonomies.
	 */
	public function getPublicTaxonomies( $namesOnly = false, $rewriteType = false ) {
		$taxonomies = [];
		if ( count( $taxonomies ) ) {
			return $taxonomies;
		}

		$taxObjects = get_taxonomies( [ 'public' => true ], 'objects' );
		foreach ( $taxObjects as $taxObject ) {
			if ( empty( $taxObject->label ) ) {
				continue;
			}

			if ( in_array( $taxObject->name, [
				'product_shipping_class',
				'post_format'
			], true ) ) {
				continue;
			}

			// We need to exclude product attributes from this list as well.
			if (
				'pa_' === substr( $taxObject->name, 0, 3 ) &&
				'manage_product_terms' === $taxObject->cap->manage_terms &&
				! apply_filters( 'aioseo_woocommerce_product_attributes', false )
			) {
				continue;
			}

			if ( $namesOnly ) {
				$taxonomies[] = $taxObject->name;
				continue;
			}

			$name = $taxObject->name;
			if ( 'type' === $taxObject->name && $rewriteType ) {
				$name = '_aioseo_type';
			}

			$taxonomies[] = [
				'name'         => $name,
				'label'        => ucwords( $taxObject->label ),
				'singular'     => ucwords( $taxObject->labels->singular_name ),
				'icon'         => strpos( $taxObject->label, 'categor' ) !== false ? 'dashicons-category' : 'dashicons-tag',
				'hierarchical' => $taxObject->hierarchical,
				'slug'         => isset( $taxObject->rewrite['slug'] ) ? $taxObject->rewrite['slug'] : ''
			];
		}

		return apply_filters( 'aioseo_public_taxonomies', $taxonomies, $namesOnly );
	}

	/**
	 * Retrieve a list of users that match passed in roles.
	 *
	 * @since 4.0.0
	 *
	 * @return array An array of user data.
	 */
	public function getSiteUsers( $roles ) {
		static $users = [];

		if ( ! empty( $users ) ) {
			return $users;
		}

		$rolesWhere = [];
		foreach ( $roles as $role ) {
			$rolesWhere[] = '(um.meta_key = \'' . aioseo()->core->db->db->prefix . 'capabilities\' AND um.meta_value LIKE \'%\"' . $role . '\"%\')';
		}
		$dbUsers = aioseo()->core->db->start( 'users as u' )
			->select( 'u.ID, u.display_name, u.user_nicename, u.user_email' )
			->join( 'usermeta as um', 'u.ID = um.user_id' )
			->whereRaw( '(' . implode( ' OR ', $rolesWhere ) . ')' )
			->orderBy( 'u.user_nicename' )
			->run()
			->result();

		foreach ( $dbUsers as $dbUser ) {
			$users[] = [
				'id'          => (int) $dbUser->ID,
				'displayName' => $dbUser->display_name,
				'niceName'    => $dbUser->user_nicename,
				'email'       => $dbUser->user_email,
				'gravatar'    => get_avatar_url( $dbUser->user_email )
			];
		}

		return $users;
	}

	/**
	 * Retrieve a list of site authors.
	 *
	 * @since 4.1.8
	 *
	 * @return array An array of user data.
	 */
	public function getSiteAuthors() {
		$authors = aioseo()->core->cache->get( 'site_authors' );
		if ( null === $authors ) {
			// phpcs:disable WordPress.DB.SlowDBQuery, HM.Performance.SlowMetaQuery
			global $wpdb;
			$users = get_users(
				[
					'meta_key'     => $wpdb->prefix . 'user_level',
					'meta_value'   => '0',
					'meta_compare' => '!=',
					'blog_id'      => 0
				]
			);
			// phpcs:enable WordPress.DB.SlowDBQuery, HM.Performance.SlowMetaQuery

			$authors = [];
			foreach ( $users as $user ) {
				$authors[] = [
					'id'          => (int) $user->ID,
					'displayName' => $user->display_name,
					'niceName'    => $user->user_nicename,
					'email'       => $user->user_email,
					'gravatar'    => get_avatar_url( $user->user_email )
				];
			}
			aioseo()->core->cache->update( 'site_authors', $authors, 12 * HOUR_IN_SECONDS );
		}

		return $authors;
	}

	/**
	 * Returns the ID of the site logo if it exists.
	 *
	 * @since 4.0.0
	 *
	 * @return int
	 */
	public function getSiteLogoId() {
		if ( ! get_theme_support( 'custom-logo' ) ) {
			return false;
		}

		return get_theme_mod( 'custom_logo' );
	}

	/**
	 * Returns the URL of the site logo if it exists.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function getSiteLogoUrl() {
		$id = $this->getSiteLogoId();
		if ( ! $id ) {
			return false;
		}

		$image = wp_get_attachment_image_src( $id, 'full' );
		if ( empty( $image ) ) {
			return false;
		}

		return $image[0];
	}

	/**
	 * Returns noindexed post types.
	 *
	 * @since 4.0.0
	 *
	 * @return array A list of noindexed post types.
	 */
	public function getNoindexedPostTypes() {
		return $this->getNoindexedObjects( 'postTypes' );
	}

	/**
	 * Checks whether a given post type is noindexed.
	 *
	 * @since 4.0.0
	 *
	 * @param  string  $postType The post type.
	 * @return bool              Whether the post type is noindexed.
	 */
	public function isPostTypeNoindexed( $postType ) {
		$noindexedPostTypes = $this->getNoindexedPostTypes();

		return in_array( $postType, $noindexedPostTypes, true );
	}

	/**
	 * Returns noindexed taxonomies.
	 *
	 * @since 4.0.0
	 *
	 * @return array A list of noindexed taxonomies.
	 */
	public function getNoindexedTaxonomies() {
		return $this->getNoindexedObjects( 'taxonomies' );
	}

	/**
	 * Checks whether a given post type is noindexed.
	 *
	 * @since 4.0.0
	 *
	 * @param  string  $taxonomy The taxonomy.
	 * @return bool              Whether the taxonomy is noindexed.
	 */
	public function isTaxonomyNoindexed( $taxonomy ) {
		$noindexedTaxonomies = $this->getNoindexedTaxonomies();

		return in_array( $taxonomy, $noindexedTaxonomies, true );
	}

	/**
	 * Returns noindexed object types of a given parent type.
	 *
	 * @since 4.0.0
	 *
	 * @param  string $type The parent object type ("postTypes" or "taxonomies").
	 * @return array        A list of noindexed objects types.
	 */
	private function getNoindexedObjects( $type ) {
		$noindexed = [];
		foreach ( aioseo()->dynamicOptions->searchAppearance->$type->all() as $name => $object ) {
			if (
				! $object['show'] ||
				( $object['advanced']['robotsMeta'] && ! $object['advanced']['robotsMeta']['default'] && $object['advanced']['robotsMeta']['noindex'] )
			) {
				$noindexed[] = $name;
			}
		}

		return $noindexed;
	}

	/**
	 * Returns all categories for a post.
	 *
	 * @since 4.1.4
	 *
	 * @param  int   $postId The post ID.
	 * @return array $names  The category names.
	 */
	public function getAllCategories( $postId = 0 ) {
		$names      = [];
		$categories = get_the_category( $postId );
		if ( $categories && count( $categories ) ) {
			foreach ( $categories as $category ) {
				$names[] = aioseo()->helpers->internationalize( $category->cat_name );
			}
		}

		return $names;
	}

	/**
	 * Returns all tags for a post.
	 *
	 * @since 4.1.4
	 *
	 * @param  int   $postId The post ID.
	 * @return array $names  The tag names.
	 */
	public function getAllTags( $postId = 0 ) {
		$names = [];

		$tags = get_the_tags( $postId );
		if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) {
			foreach ( $tags as $tag ) {
				if ( ! empty( $tag->name ) ) {
					$names[] = aioseo()->helpers->internationalize( $tag->name );
				}
			}
		}

		return $names;
	}

	/**
	 * Loads the translations for a given domain.
	 *
	 * @since 4.1.4
	 *
	 * @return void
	 */
	public function loadTextDomain( $domain ) {
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Unload the domain in case WordPress has enqueued the translations for the site language instead of profile language.
		// Reloading the text domain will otherwise not override the existing loaded translations.
		unload_textdomain( $domain );

		$mofile = $domain . '-' . get_user_locale() . '.mo';
		load_textdomain( $domain, WP_LANG_DIR . '/plugins/' . $mofile );
	}

	/**
	 * Get the page builder the given Post ID was built with.
	 *
	 * @since 4.1.7
	 *
	 * @param  int         $postId The Post ID.
	 * @return bool|string         The page builder or false if not built with page builders.
	 */
	public function getPostPageBuilderName( $postId ) {
		foreach ( aioseo()->standalone->pageBuilderIntegrations as $integration => $pageBuilder ) {
			if ( $pageBuilder->isBuiltWith( $postId ) ) {
				return $integration;
			}
		}

		return false;
	}

	/**
	 * Checks if the current user can edit posts of the given post type.
	 *
	 * @since 4.1.9
	 *
	 * @param  string $postType The name of the post type.
	 * @return bool             Whether the user can edit posts of the given post type.
	 */
	public function canEditPostType( $postType ) {
		$capabilities = $this->getPostTypeCapabilities( $postType );

		return current_user_can( $capabilities['edit_posts'] );
	}

	/**
	 * Returns a list of capabilities for the given post type.
	 *
	 * @since 4.1.9
	 *
	 * @param  string $postType The name of the post type.
	 * @return array            The capabilities.
	 */
	public function getPostTypeCapabilities( $postType ) {
		static $capabilities = [];
		if ( isset( $capabilities[ $postType ] ) ) {
			return $capabilities[ $postType ];
		}

		$postTypeObject = get_post_type_object( $postType );
		if ( ! is_a( $postTypeObject, 'WP_Post_Type' ) ) {
			$capabilities[ $postType ] = [];

			return $capabilities[ $postType ];
		}

		if ( ! is_array( $postTypeObject->capability_type ) ) {
			$postTypeObject->capability_type = [
				$postTypeObject->capability_type,
				$postTypeObject->capability_type . 's'
			];
		}

		// Singular base for meta capabilities, plural base for primitive capabilities.
		list( $singularBase, $pluralBase ) = $postTypeObject->capability_type;

		$capabilities[ $postType ] = [
			'edit_post'          => 'edit_' . $singularBase,
			'read_post'          => 'read_' . $singularBase,
			'delete_post'        => 'delete_' . $singularBase,
			'edit_posts'         => 'edit_' . $pluralBase,
			'edit_others_posts'  => 'edit_others_' . $pluralBase,
			'delete_posts'       => 'delete_' . $pluralBase,
			'publish_posts'      => 'publish_' . $pluralBase,
			'read_private_posts' => 'read_private_' . $pluralBase,
		];

		return $capabilities[ $postType ];
	}

	/**
	 * Checks if the current user can edit terms of the given taxonomy.
	 *
	 * @since 4.1.9
	 *
	 * @param  string $taxonomy The name of the taxonomy.
	 * @return bool             Whether the user can edit posts of the given taxonomy.
	 */
	public function canEditTaxonomy( $taxonomy ) {
		$capabilities = $this->getTaxonomyCapabilities( $taxonomy );

		return current_user_can( $capabilities['edit_terms'] );
	}

	/**
	 * Returns a list of capabilities for the given taxonomy.
	 *
	 * @since 4.1.9
	 *
	 * @param  string $postType The name of the taxonomy.
	 * @return array            The capabilities.
	 */
	public function getTaxonomyCapabilities( $taxonomy ) {
		static $capabilities = [];
		if ( isset( $capabilities[ $taxonomy ] ) ) {
			return $capabilities[ $taxonomy ];
		}

		$taxonomyObject = get_taxonomy( $taxonomy );
		if ( ! is_a( $taxonomyObject, 'WP_Taxonomy' ) ) {
			$capabilities[ $taxonomy ] = [];

			return $capabilities[ $taxonomy ];
		}

		$capabilities[ $taxonomy ] = (array) $taxonomyObject->cap;

		return $capabilities[ $taxonomy ];
	}
}