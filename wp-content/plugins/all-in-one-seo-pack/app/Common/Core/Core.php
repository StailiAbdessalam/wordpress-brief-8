<?php
namespace AIOSEO\Plugin\Common\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Options;
use AIOSEO\Plugin\Common\Utils;

/**
 * Loads core classes.
 *
 * @since 4.1.9
 */
class Core {
	/**
	 * Class constructor.
	 *
	 * @since 4.1.9
	 */
	public function __construct() {
		$this->fs           = new Utils\Filesystem( $this );
		$this->assets       = new Utils\Assets( $this );
		$this->db           = new Utils\Database();
		$this->cache        = new Utils\Cache();
		$this->cachePrune   = new Utils\CachePrune();
		$this->optionsCache = new Options\Cache();
	}
}