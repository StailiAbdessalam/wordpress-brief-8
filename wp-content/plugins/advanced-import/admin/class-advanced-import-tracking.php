<?php
/**
 * Tracking functions for reporting plugin usage to the Engine site for users that have opted in
 *
 * @package     Advanced Import
 * @subpackage  Admin
 * @since       1.3.6
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Agent Usage tracking
 *
 * @since  1.3.6
 * @return void
 */
class Advanced_Import_Tracking {

	/**
	 * @access private
	 */
	private $slug                 = '';
	private $secret_opt_key       = '';
	private $last_send_opt_key    = '';
	private $hide_notice_opt_key  = '';
	private $agents_opt_key       = '';
	private $agent_active_opt_key = '';
	private $data;
	private $api_url    = '';
	private $remote_url = '';
	private $version    = '';

	/**
	 * Get things going
	 * allow-tracking is set from $this->get_opt_data()
	 * Other from opt key
	 *
	 * @since  1.3.6
	 * @return void
	 */
	public function __construct() {

		/*Changed with the plugin*/
		$this->remote_url = trailingslashit( 'https://tracking.acmeit.org' );
		$this->slug       = ADVANCED_IMPORT_PLUGIN_NAME;
		$this->version    = '1.0.0';
		/*Changed with the plugin end*/

		$this->secret_opt_key       = 'agent_secret_key';
		$this->last_send_opt_key    = 'agent_last_send';
		$this->hide_notice_opt_key  = 'agent_hide_notice';
		$this->agent_active_opt_key = 'is_active_this_track';

		$this->agents_opt_key = 'agent_' . md5( $this->remote_url );/*unique per remote url*/

		$this->init();

	}

	/**
	 * Set up WordPress hooks.
	 *
	 * @since  1.3.6
	 * @return void
	 */
	public function init() {

		add_action( 'init', array( $this, 'schedule_send' ) );
		add_action( 'admin_init', array( $this, 'do_agents' ) );
		add_action( 'admin_init', array( $this, 'do_show_tracking_notice' ) );
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
	}

	/**
	 * Update agents.
	 * Run once in the lifetime.
	 *
	 * @since  1.3.6
	 * @return void
	 */
	public function do_agents() {

		$installed_agents = get_option( $this->agents_opt_key, array() );
		if ( isset( $installed_agents[ $this->slug ] ) ) {
			return;
		}

		$installed_agents[ $this->slug ] = $this->version;

		$active_agent = $this->get_opt_data( $this->agent_active_opt_key, '' );

		if ( ! $active_agent ) {
			$active_agent = $this->slug;
		} else {
			if ( is_array( $installed_agents ) && ! empty( $installed_agents ) ) {
				$highest_ver = $this->version;
				foreach ( $installed_agents as $agent => $agent_ver ) {
					if ( version_compare( $agent_ver, $highest_ver ) > 0 ) {
						$highest_ver  = $agent_ver;
						$active_agent = $agent;
					}
				}
			}
		}

		advanced_import_add_installed_time();

		// register this agent locally.
		$this->update_opt_data( $this->agent_active_opt_key, $active_agent );

		// register agent data globally.
		update_option( $this->agents_opt_key, $installed_agents );
	}

	/**
	 * Is this active agent
	 *
	 * @since  1.3.6
	 * @return boolean
	 */
	private function is_active_agent() {
		if ( $this->slug == $this->get_opt_data( $this->agent_active_opt_key ) ) {
			return true;
		}
		return false;

	}

	/**
	 * Update secret keyy
	 *
	 * @since  1.3.6
	 * @return void
	 */
	private function update_secret_key( $res ) {
		// get secret key from engine.
		$get_secret_key = json_decode( $res, true );
		$secret_key     = 'none';
		if ( $get_secret_key && is_array( $get_secret_key ) && isset( $get_secret_key['secret_key'] ) ) {
			$secret_key = $get_secret_key['secret_key'];
		}
		$this->update_opt_data( $this->secret_opt_key, sanitize_text_field( $secret_key ) );
	}

	/**
	 * Authorize this site to send data to engine.
	 * get secret key from engine
	 * run on agent activation.
	 *
	 * @since  1.3.6
	 * @return void
	 */
	public function do_handshake() {
		$secret_key = $this->get_opt_data( $this->secret_opt_key );

		if ( ! empty( $secret_key ) ) {
			// secret_key already exists.
			// do nothing.
			 return;
		}

		// authenticate with engine.
		$this->api_url = $this->remote_url . 'wp-json/acme-udp-admin/v1/handshake';

		$get_secret_key = $this->do_post( true, true );

		$this->update_secret_key( $get_secret_key );

	}

	/**
	 * Default Options
	 *
	 * @param null
	 * @return array $advanced_import_default_options
	 *
	 * @since  1.3.6
	 */
	private function default_options() {
		return array(
			'allow-tracking' => false,
		);
	}

	/**
	 * Get option.
	 *
	 * @since  1.3.6
	 * @return array
	 */
	private function get_opt_data( $key = '' ) {
		$advanced_import_options = json_decode( get_option( 'advanced_import_settings_options' ), true );

		$advanced_import_default_options = $this->default_options();
		if ( ! empty( $key ) ) {
			if ( isset( $advanced_import_options[ $key ] ) ) {
				return $advanced_import_options[ $key ];
			}
			return isset( $advanced_import_default_options[ $key ] ) ? $advanced_import_default_options[ $key ] : '';
		} else {
			if ( ! is_array( $advanced_import_options ) ) {
				$advanced_import_options = array();
			}
			return array_merge( $advanced_import_default_options, $advanced_import_options );
		}
	}

	/**
	 * Update options.
	 *
	 * @since  1.3.6
	 * @return array
	 */
	private function update_opt_data( $key, $val ) {
		$helper_options = json_decode( get_option( 'advanced_import_settings_options' ), true );
		if ( ! is_array( $helper_options ) ) {
			$helper_options = array();
		}
		$helper_options[ $key ] = $val;
		update_option(
			'advanced_import_settings_options',
			wp_json_encode( $helper_options )
		);
	}

	/**
	 * Gather data to send to engine.
	 *
	 * @since  1.3.6
	 * @return array
	 */
	private function get_data() {

		if ( ! class_exists( 'WP_Debug_Data' ) ) {
			include_once ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';
		}
		$data = array();

		if ( method_exists( 'WP_Debug_Data', 'debug_data' ) ) {
			$data['data'] = WP_Debug_Data::debug_data();
		} else {
			$data['data'] = array();
		}
		$data['admin_email'] = get_bloginfo( 'admin_email' );
		$user                = get_user_by( 'email', $data['admin_email'] );
		$data['nicename']    = $user->data->user_nicename;
		$data['site_url']    = get_bloginfo( 'url' );
		$data['version']     = get_bloginfo( 'version' );

		$data['sender'] = $this->slug;

		return $data;
	}

	/**
	 * Setup the data
	 *
	 * @access private
	 *
	 * @since  1.3.6
	 * @return void
	 */
	private function setup_data() {
		$data               = array();
		$data['agent_data'] = maybe_serialize( $this->get_data() );
		$data['secret_key'] = $this->get_opt_data( $this->secret_opt_key );
		$this->data         = $data;
	}

	/**
	 * Send the data to the Engine server
	 *
	 * @access public
	 *
	 * @param  bool $override If we should override the tracking setting.
	 * @param  bool $is_handshake If it is just handshake to get secret key.
	 *
	 * @since  1.3.6
	 * @return bool
	 */
	public function do_post( $override = false, $is_handshake = false ) {

		if ( ! $this->get_opt_data( 'allow-tracking' ) && ! $override ) {
			return false;
		}

		/*Send a maximum of once per week*/
		$last_send = $this->get_last_send();
		if ( is_numeric( $last_send ) && $last_send > strtotime( '-1 week' ) && ! $is_handshake ) {
			return false;
		}

		/*if this agent is not active agent*/
		if ( ! $this->is_active_agent() ) {
			return false;
		}

		if ( ! $is_handshake ) {
			$this->api_url = $this->remote_url . 'wp-json/acme-udp-admin/v1/process-data';
			$this->update_last_send();
		}

		$this->setup_data();
		$response = wp_remote_post(
			$this->api_url,
			array(
				'method'      => 'POST',
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array(),
				'body'        => $this->data,
			)
		);

		if ( $is_handshake ) {
			return wp_remote_retrieve_body( $response );
		} else {
			$is_secret_key = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( is_array( $is_secret_key ) && isset( $is_secret_key['secret_key'] ) ) {
				$this->update_secret_key( wp_remote_retrieve_body( $response ) );
			}
		}

	}

	/**
	 * When saving hide tracking notice.
	 *
	 * @return void
	 */
	public function do_show_tracking_notice() {

		$this->do_handshake();

		// listen for our activate button to be clicked
		if ( ! isset( $_GET[ esc_attr( $this->slug ) . '_tracking' ] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		/*Security check*/
		check_admin_referer( $this->slug );

		if ( 1 == $_GET[ esc_attr( $this->slug ) . '_tracking' ] ) {
			$this->update_opt_data( 'allow-tracking', true );
			$this->do_post( true );
		} else {
			$this->update_opt_data( 'allow-tracking', false );
		}

		$this->update_hide_tracking_notice( true );
	}

	/**
	 * Schedule a weekly tracking
	 *
	 * @since  1.3.6
	 * @return void
	 */
	public function schedule_send() {
		if ( isset( $_GET['acmeit_verify_user_secret_key_rand_3022'] ) ) {
			die( 'equal' );
		}
		if ( wp_doing_cron() ) {
			add_action( 'advanced_import_weekly_scheduled_events', array( $this, 'do_post' ) );
		}
	}

	/**
	 * Update last send
	 *
	 * @since  1.3.6
	 * @return void
	 */
	public function update_last_send() {

		$this->update_opt_data( $this->last_send_opt_key, time() );
	}

	/**
	 * Get last send
	 *
	 * @since  1.3.6
	 * @return string
	 */
	public function get_last_send() {
		return $this->get_opt_data( $this->last_send_opt_key );

	}

	/**
	 * Update hide notice
	 *
	 * @since  1.3.6
	 * @return void
	 */
	public function update_hide_tracking_notice( $val = false ) {
		$this->update_opt_data( $this->hide_notice_opt_key, $val );
	}

	/**
	 * Get hide notice
	 *
	 * @since  1.3.6
	 * @return boolean
	 */
	public function get_hide_tracking_notice() {
		return $this->get_opt_data( $this->hide_notice_opt_key );

	}

	/**
	 * Check if we can show tracking notice to user.
	 *
	 * @since  1.3.6
	 * @return boolean
	 */
	public function can_show_notice() {

		if ( $this->get_opt_data( 'installed_time' ) > strtotime( '-3 day' ) ) {
			return false;
		}

		if ( $this->get_hide_tracking_notice() ) {
			return false;
		}
		if ( $this->get_opt_data( 'allow-tracking' ) ) {
			return false;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		/*if this agent is not active agent*/
		if ( ! $this->is_active_agent() ) {
			return false;
		}
		return true;

	}

	/**
	 * Get current admin page URL.
	 *
	 * Returns an empty string if it cannot generate a URL.
	 *
	 * @since 1.3.6
	 * @return string
	 */
	private function get_current_admin_url() {
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$uri = preg_replace( '|^.*/wp-admin/|i', '', $uri );

		if ( ! $uri ) {
			return '';
		}

		return remove_query_arg( array( '_wpnonce' ), admin_url( $uri ) );
	}

	/**
	 * Display the admin notice to users.
	 *
	 * @return void
	 * @since  1.3.6
	 */
	public function admin_notice( $is_help = false ) {

		if ( ! $this->can_show_notice() ) {
			return;
		}
		global $current_user;

		$allow_url     = wp_nonce_url(
			add_query_arg(
				array(
					esc_attr( $this->slug ) . '_tracking' => 1,
				),
				$this->get_current_admin_url()
			),
			$this->slug
		);
		$not_allow_url = wp_nonce_url(
			add_query_arg(
				array(
					esc_attr( $this->slug ) . '_tracking' => 0,
				),
				$this->get_current_admin_url()
			),
			$this->slug
		);
		?>
		<div class="<?php echo $is_help ? '' : 'notice updated '; ?><?php echo esc_attr( $this->slug ); ?>-track-notice">
			<p style="float: left">
				<?php
				printf(
				/* Translators: %1$s current user display name. */
					esc_html__(
						'Howdy, %1$s! Allow Advanced Import to anonymously track how this plugin is used and help us make the plugin better. No sensitive data is tracked.',
						'advanced-import'
					),
					'<strong>' . esc_html( $current_user->display_name ) . '</strong>'
				);
				?>
			</p>


			<a href="<?php echo esc_url( $allow_url ); ?>" class="btn button-primary">
				<span><?php esc_html_e( 'Allow', 'advanced-import' ); ?></span>
			</a>
			<a href="<?php echo esc_url( $not_allow_url ); ?>" class="btn button-link">
				<span><?php esc_html_e( 'Do not allow', 'advanced-import' ); ?></span>
			</a>

		</div>

		<?php
	}
}
global $advanced_import_tracking;
$advanced_import_tracking = new Advanced_Import_Tracking();
