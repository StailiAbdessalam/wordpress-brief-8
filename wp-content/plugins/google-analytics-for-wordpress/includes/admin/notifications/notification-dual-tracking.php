<?php


class MonsterInsights_Notification_Dual_Tracking extends MonsterInsights_Notification_Event
{
    public $notification_id = 'monsterinsights_notification_dual_tracking';
    public $notification_interval = 30; // in days
    public $notification_type = array( 'basic', 'lite', 'master', 'plus', 'pro' );
    public $notification_category = 'insight';
    public $notification_priority = 1;

    /**
     * Build Notification
     *
     * @return array $notification notification is ready to add
     *
     * @since 7.12.3
     */
    public function prepare_notification_data( $notification ) {

        $ua = MonsterInsights()->auth->get_ua();
        $v4 = MonsterInsights()->auth->get_v4_id();

        if ( $ua && !$v4 ) {
            $is_em = defined( 'EXACTMETRICS_VERSION' );
            $learn_more_url = $is_em
                ? 'https://www.exactmetrics.com/adding-google-analytics-4-to-wordpress-step-by-step-guide/'
                : 'https://www.monsterinsights.com/what-is-google-analytics-4-should-you-use-it/';

            $notification['title'] = __('Prepare for Google Analytics 4', 'google-analytics-for-wordpress');
            $notification['content'] = __( 'Prepare for the future of analytics by setting up Google Analytics 4. Enable "Dual Tracking" today.', 'google-analytics-for-wordpress' );
            $notification['btns']    = array(
                'setup_now' => array(
                    'url'           => $this->get_view_url( 'monsterinsights-dual-tracking-id', 'monsterinsights_settings' ),
                    'text'          => __( 'Setup now', 'google-analytics-for-wordpress' ),
                ),
                'learn_more'  => array(
                    'url'           => $this->build_external_link( $learn_more_url ),
                    'text'          => __( 'Learn More', 'google-analytics-for-wordpress' ),
                    'is_external'   => true,
                ),
            );
            return $notification;
        }

        return false;
    }
}

new MonsterInsights_Notification_Dual_Tracking();