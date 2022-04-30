<?php
namespace AIOSEO\Plugin\Common\Schema;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds our schema.
 *
 * @since 4.0.0
 */
class Schema {
	/**
	 * The included graphs.
	 *
	 * @since 4.0.0
	 *
	 * @var array
	 */
	public $graphs = [];

	/**
	 * The context data.
	 *
	 * @since 4.0.0
	 *
	 * @var array
	 */
	public $context = [];


	/**
	 * All existing WebPage graphs.
	 *
	 * @since 4.0.0
	 *
	 * @var array
	 */
	protected $webPageGraphs = [
		'WebPage',
		'AboutPage',
		'CheckoutPage',
		'CollectionPage',
		'ContactPage',
		'FAQPage',
		'ItemPage',
		'MedicalWebPage',
		'ProfilePage',
		'RealEstateListing',
		'SearchResultsPage'
	];

	/**
	 * Fields that can be 0 or null, which shouldn't be stripped when cleaning the data.
	 *
	 * @since 4.1.2
	 *
	 * @var array
	 */
	public $nullableFields = [
		'price' // Needs to be 0 if free for Software Application.
	];

	/**
	 * Returns the JSON schema for the requested page.
	 *
	 * @since 4.0.0
	 *
	 * @return string The JSON schema.
	 */
	public function get() {
		// First, let's check if it's disabled.
		if (
			apply_filters( 'aioseo_schema_disable', false ) ||
			( in_array( 'enableSchemaMarkup', aioseo()->internalOptions->deprecatedOptions, true ) && ! aioseo()->options->deprecated->searchAppearance->global->schema->enableSchemaMarkup )
		) {
			return '';
		}

		$this->init();
		if ( ! $this->graphs ) {
			return '';
		}

		$schema = [
			'@context' => 'https://schema.org',
			'@graph'   => []
		];

		$graphs = apply_filters( 'aioseo_schema_graphs', array_unique( array_filter( $this->graphs ) ) );
		foreach ( $graphs as $graph ) {
			if ( class_exists( "\AIOSEO\Plugin\Common\Schema\Graphs\\$graph" ) ) {
				$namespace = "\AIOSEO\Plugin\Common\Schema\Graphs\\$graph";

				//if graph is actually a fully qualified class name
				if ( class_exists( $graph ) ) {
					$namespace = $graph;
				}

				$schema['@graph'][] = array_filter( ( new $namespace )->get() );
			}
		}

		$schema['@graph'] = apply_filters( 'aioseo_schema_output', $schema['@graph'] );
		$schema['@graph'] = array_values( $this->cleanData( $schema['@graph'] ) );

		return isset( $_GET['aioseo-dev'] ) ? wp_json_encode( $schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) : wp_json_encode( $schema );
	}

	/**
	 * Determines the context and graphs for the requested page.
	 *
	 * This can't run in the constructor since the queried object needs to be available first.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function init() {
		$context      = new Context();
		$this->graphs = [
			'WebSite',
			ucfirst( aioseo()->options->searchAppearance->global->schema->siteRepresents ),
			'BreadcrumbList'
		];

		if ( is_front_page() && 'posts' === get_option( 'show_on_front' ) ) {
			$this->graphs[] = 'CollectionPage';
			$this->context  = $context->home();

			return;
		}

		if ( is_home() || aioseo()->helpers->isWooCommerceShopPage() ) {
			$this->graphs[] = 'CollectionPage';
			$this->context  = $context->post();

			return;
		}

		if ( is_singular() ) {
			$this->context = $context->post();

			// Check if we're on a BuddyPress member page.
			if ( function_exists( 'bp_is_user' ) && bp_is_user() ) {
				array_push( $this->graphs, 'ProfilePage', 'PersonAuthor' );

				return;
			}

			$post = aioseo()->helpers->getPost();
			if ( $post && 'page' !== $post->post_type ) {
				$this->graphs[] = 'PersonAuthor';
			}

			$postGraphs = $this->getPostGraphs( $post );
			if ( is_array( $postGraphs ) ) {
				$this->graphs = array_merge( $this->graphs, $postGraphs );

				return;
			}
			$this->graphs[] = $postGraphs;
		}

		if ( is_category() || is_tag() || is_tax() ) {
			$this->graphs[] = 'CollectionPage';
			$this->context  = $context->term();

			return;
		}

		if ( is_author() ) {
			array_push( $this->graphs, 'CollectionPage', 'PersonAuthor' );
			$this->context = $context->author();

			return;
		}

		if ( is_post_type_archive() ) {
			$this->graphs[] = 'CollectionPage';
			$this->context  = $context->postArchive();

			return;
		}

		if ( is_date() ) {
			$this->graphs[] = 'CollectionPage';
			$this->context  = $context->date();

			return;
		}

		if ( is_search() ) {
			$this->graphs[] = 'SearchResultsPage';
			$this->context  = $context->search();

			return;
		}

		if ( is_404() ) {
			$this->context = $context->notFound();
		}
	}

	/**
	 * Returns the graph names that are set for the post.
	 *
	 * @since 4.0.0
	 *
	 * @param  WP_Post      The post object.
	 * @return string|array The graph name(s).
	 */
	public function getPostGraphs( $post = null ) {
		$post           = is_object( $post ) ? $post : aioseo()->helpers->getPost();
		$dynamicOptions = aioseo()->dynamicOptions->noConflict();

		if ( ! $dynamicOptions->searchAppearance->postTypes->has( $post->post_type ) ) {
			return 'WebPage';
		}

		$schemaType = $dynamicOptions->searchAppearance->postTypes->{$post->post_type}->schemaType;
		switch ( $schemaType ) {
			case 'WebPage':
				return ucfirst( $dynamicOptions->searchAppearance->postTypes->{$post->post_type}->webPageType );
			case 'Article':
				return [ 'WebPage', ucfirst( $dynamicOptions->searchAppearance->postTypes->{$post->post_type}->articleType ) ];
			case 'none':
				return '';
			default:
				// This fixes a bug from WPForms Form Pages.
				if ( 'default' === $schemaType ) {
					return 'WebPage';
				}

				// Check if the schema type isn't already WebPage or one of its child graphs.
				if ( in_array( $schemaType, $this->webPageGraphs, true ) ) {
					return ucfirst( $schemaType );
				}

				return [ 'WebPage', ucfirst( $schemaType ) ];
		}
	}

	/**
	 * Strips HTML and removes all blank properties in each of our graphs.
	 *
	 * @since 4.0.13
	 *
	 * @param  array $data The graph data.
	 * @return array       The cleaned graph data.
	 */
	protected function cleanData( $data ) {
		foreach ( $data as $k => &$v ) {
			if ( is_array( $v ) ) {
				$v = $this->cleanData( $v );
			} else {
				$v = is_int( $v ) ? $v : trim( wp_strip_all_tags( $v ) );
			}

			if ( empty( $v ) && ! in_array( $k, $this->nullableFields, true ) ) {
				unset( $data[ $k ] );
			} else {
				$data[ $k ] = $v;
			}
		}

		return $data;
	}
}