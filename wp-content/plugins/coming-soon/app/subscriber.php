<?php
/**
 * Backend funtions for Subscribers functionality.
 */

/**
 * Get Datatable Info for the Subscribers page.
 *
 * @return JSON object.
 */
function seedprod_lite_subscribers_datatable() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_subscriber_capability', 'list_users' ) ) ) {
			wp_send_json_error();
		}
		$data         = array( '' );
		$current_page = 1;
		if ( ! empty( absint( $_GET['current_page'] ) ) ) {
			$current_page = absint( $_GET['current_page'] );
		}
		$per_page = 100;

		$filter = null;
		if ( ! empty( $_GET['filter'] ) ) {
			$filter = sanitize_text_field( wp_unslash( $_GET['filter'] ) );
			if ( 'all' === $filter ) {
				$filter = null;
			}
		}

		if ( ! empty( $_GET['s'] ) ) {
			$filter = null;
		}

		$results = array();

		$data = array();
		foreach ( $results as $v ) {

				// Format Date
			$created_at = gmdate( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $v->created ) );

			// Load Data
			$data[] = array(
				'id'         => $v->id,
				'email'      => $v->email,
				'name'       => $v->fname . ' ' . $v->lname,
				'created_at' => $created_at,
				'page_uuid'  => $v->page_uuid,
			);
		}

		$totalitems = 0;
		$views      = array();

		// Get recent subscriber data
		$chart_timeframe = 7;
		if ( ! empty( $_GET['interval'] ) ) {
			$chart_timeframe = absint( $_GET['interval'] );
		}

		$recent_subscribers = array();

		$now      = new \DateTime( "$chart_timeframe days ago", new \DateTimeZone( 'America/New_York' ) );
		$interval = new \DateInterval( 'P1D' ); // 1 Day interval
		$period   = new \DatePeriod( $now, $interval, $chart_timeframe ); // 7 Days

		$recent_subscribers_data = array(
			array( 'Year', 'Subscribers' ),
		);
		foreach ( $period as $day ) {
			$key         = $day->format( 'Y-m-d' );
			$display_key = $day->format( 'M j' );
			$no_val      = true;
			foreach ( $recent_subscribers as $v ) {
				if ( $key == $v->created ) {
					$recent_subscribers_data[] = array( $display_key, absint( $v->count ) );
					$no_val                    = false;
				}
			}
			if ( $no_val ) {
				$recent_subscribers_data[] = array( $display_key, 0 );
			}
		}

		$response = array(
			'recent_subscribers' => $recent_subscribers_data,
			'rows'               => $data,
			'lpage_name'         => '',
			'totalitems'         => $totalitems,
			'totalpages'         => ceil( $totalitems / $per_page ),
			'currentpage'        => $current_page,
			'views'              => $views,
		);

		wp_send_json( $response );
	}
}


