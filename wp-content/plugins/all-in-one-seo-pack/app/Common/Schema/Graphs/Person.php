<?php
namespace AIOSEO\Plugin\Common\Schema\Graphs;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Person graph class.
 *
 * This is the main Person graph that can be set to represent the site.
 *
 * @since 4.0.0
 */
class Person extends Graph {
	/**
	 * Returns the graph data.
	 *
	 * @since 4.0.0
	 *
	 * @return array $data The graph data.
	 */
	public function get() {
		if ( 'person' !== aioseo()->options->searchAppearance->global->schema->siteRepresents ) {
			return [];
		}

		$person = aioseo()->options->searchAppearance->global->schema->person;
		if ( 'manual' === $person ) {
			return $this->manual();
		}

		$person = intval( $person );
		if ( empty( $person ) ) {
			return [];
		}

		$data = [
			'@type' => 'Person',
			'@id'   => trailingslashit( home_url() ) . '#person',
			'name'  => get_the_author_meta( 'display_name', $person )
		];

		$avatar = $this->avatar( $person, 'personImage' );
		if ( $avatar ) {
			$data['image'] = $avatar;
		}

		$socialUrls = $this->socialUrls( $person );
		if ( $socialUrls ) {
			$data['sameAs'] = $socialUrls;
		}

		return $data;
	}

	/**
	 * Returns the data for the person if it is set manually.
	 *
	 * @since 4.0.0
	 *
	 * @return array $data The graph data.
	 */
	private function manual() {
		$data = [
			'@type' => 'Person',
			'@id'   => trailingslashit( home_url() ) . '#person',
			'name'  => aioseo()->options->searchAppearance->global->schema->personName
		];

		$logo = aioseo()->options->searchAppearance->global->schema->personLogo;
		if ( $logo ) {
			$data['image'] = $logo;
		}

		$socialUrls = $this->socialUrls();
		if ( $socialUrls ) {
			$data['sameAs'] = $socialUrls;
		}

		return $data;
	}
}