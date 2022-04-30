<?php

namespace BolbyBuilder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

/*
 *  Elementor widget for Contact Info
 *  @since 1.0
 */ 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Contact extends Widget_Base {
	
	public function get_name() {
		return 'bolby-contact';
	}
	
	public function get_title() {
		return __( 'Contact Info', 'bolby' );
	}
	
	public function get_icon() {
		return 'eicon-posts-ticker';
	}
	
	public function get_categories() {
		return ['bolby-elements'];
	}
	
	protected function _register_controls() {
		$this->start_controls_section(
			'content',
			[
				'label' => __( 'Content', 'bolby' ),
			]
		);
		
		$this->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'bolby' ),
				'type' => Controls_Manager::ICON,
				'include' => [
					'icon-user', 
					'icon-people', 
					'icon-user-female', 
					'icon-user-follow', 
					'icon-user-following', 
					'icon-user-unfollow', 
					'icon-login', 
					'icon-logout', 
					'icon-emotsmile', 
					'icon-phone', 
					'icon-call-end', 
					'icon-call-in', 
					'icon-call-out', 
					'icon-map', 
					'icon-location-pin', 
					'icon-direction', 
					'icon-directions', 
					'icon-compass', 
					'icon-layers', 
					'icon-menu', 
					'icon-list', 
					'icon-options-vertical', 
					'icon-options', 
					'icon-arrow-down', 
					'icon-arrow-left', 
					'icon-arrow-right', 
					'icon-arrow-up', 
					'icon-arrow-up-circle', 
					'icon-arrow-left-circle', 
					'icon-arrow-right-circle', 
					'icon-arrow-down-circle', 
					'icon-check', 
					'icon-clock', 
					'icon-plus', 
					'icon-minus', 
					'icon-close', 
					'icon-event', 
					'icon-exclamation', 
					'icon-organization', 
					'icon-trophy', 
					'icon-screen-smartphone', 
					'icon-screen-desktop', 
					'icon-plane', 
					'icon-notebook', 
					'icon-mustache', 
					'icon-mouse', 
					'icon-magnet', 
					'icon-energy', 
					'icon-disc', 
					'icon-cursor', 
					'icon-cursor-move', 
					'icon-crop', 
					'icon-chemistry', 
					'icon-speedometer', 
					'icon-shield', 
					'icon-screen-tablet', 
					'icon-magic-wand', 
					'icon-hourglass', 
					'icon-graduation', 
					'icon-ghost', 
					'icon-game-controller', 
					'icon-fire', 
					'icon-eyeglass', 
					'icon-envelope-open', 
					'icon-envelope-letter', 
					'icon-bell', 
					'icon-badge', 
					'icon-anchor', 
					'icon-wallet', 
					'icon-vector', 
					'icon-speech', 
					'icon-puzzle', 
					'icon-printer', 
					'icon-present', 
					'icon-playlist', 
					'icon-pin', 
					'icon-picture', 
					'icon-handbag', 
					'icon-globe-alt', 
					'icon-globe', 
					'icon-folder-alt', 
					'icon-folder', 
					'icon-film', 
					'icon-feed', 
					'icon-drop', 
					'icon-drawer', 
					'icon-docs', 
					'icon-doc', 
					'icon-diamond', 
					'icon-cup', 
					'icon-calculator', 
					'icon-bubbles', 
					'icon-briefcase', 
					'icon-book-open', 
					'icon-basket-loaded', 
					'icon-basket', 
					'icon-bag', 
					'icon-action-undo', 
					'icon-action-redo', 
					'icon-wrench', 
					'icon-umbrella', 
					'icon-trash', 
					'icon-tag', 
					'icon-support', 
					'icon-frame', 
					'icon-size-fullscreen', 
					'icon-size-actual', 
					'icon-shuffle', 
					'icon-share-alt', 
					'icon-share', 
					'icon-rocket', 
					'icon-question', 
					'icon-pie-chart', 
					'icon-pencil', 
					'icon-note', 
					'icon-loop', 
					'icon-home', 
					'icon-grid', 
					'icon-graph', 
					'icon-microphone', 
					'icon-music-tone-alt', 
					'icon-music-tone', 
					'icon-earphones-alt', 
					'icon-earphones', 
					'icon-equalizer', 
					'icon-like', 
					'icon-dislike', 
					'icon-control-start', 
					'icon-control-rewind', 
					'icon-control-play', 
					'icon-control-pause', 
					'icon-control-forward', 
					'icon-control-end', 
					'icon-volume-1', 
					'icon-volume-2', 
					'icon-volume-off', 
					'icon-calendar', 
					'icon-bulb', 
					'icon-chart', 
					'icon-ban', 
					'icon-bubble', 
					'icon-camrecorder', 
					'icon-camera', 
					'icon-cloud-download', 
					'icon-cloud-upload', 
					'icon-envelope', 
					'icon-eye', 
					'icon-flag', 
					'icon-heart', 
					'icon-info', 
					'icon-key', 
					'icon-link', 
					'icon-lock', 
					'icon-lock-open', 
					'icon-magnifier', 
					'icon-magnifier-add', 
					'icon-magnifier-remove', 
					'icon-paper-clip', 
					'icon-paper-plane', 
					'icon-power', 
					'icon-refresh', 
					'icon-reload', 
					'icon-settings', 
					'icon-star', 
					'icon-symbol-female', 
					'icon-symbol-male', 
					'icon-target', 
					'icon-credit-card', 
					'icon-paypal', 
					'icon-social-tumblr', 
					'icon-social-twitter', 
					'icon-social-facebook', 
					'icon-social-instagram', 
					'icon-social-linkedin', 
					'icon-social-pinterest', 
					'icon-social-github', 
					'icon-social-google', 
					'icon-social-reddit', 
					'icon-social-skype', 
					'icon-social-dribbble', 
					'icon-social-behance', 
					'icon-social-foursqare', 
					'icon-social-soundcloud', 
					'icon-social-spotify', 
					'icon-social-stumbleupon', 
					'icon-social-youtube', 
					'icon-social-dropbox', 
					'icon-social-vkontakte', 
					'icon-social-steam'
				],
				'default' => 'icon-phone',
			]
		);
		
		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'bolby' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Phone', 'bolby' ),
				'placeholder' => __( 'Write your title', 'bolby' )
			]
		);
		
		$this->add_control(
			'text',
			[
				'label' => __( 'Text', 'bolby' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( '+44 1632 960428', 'bolby' ),
				'placeholder' => __( 'Write your text', 'bolby' )
			]
		);
		

		$this->end_controls_section();
	}
	
	protected function render() {
		$settings = $this->get_settings();
		if( $settings['title'] || $settings['text'] ) {
		?>
		<div class="contact-info">
			<?php if( $settings['icon'] ) { ?>
			<i class="<?php echo esc_attr($settings['icon']); ?>"></i>
			<?php } ?>
			<div class="details">
				<?php if( $settings['title'] ) { ?>
				<h5><?php echo esc_attr($settings['title']); ?></h5>
				<?php } ?>
				<?php if( $settings['text'] ) { ?>
				<span><?php echo esc_attr($settings['text']); ?></span>
				<?php } ?>
			</div>
		</div>
		<?php
		}
	}
	
	protected function _content_template() {
		?>
		<# if( settings.title || settings.text ) { #>
		<div class="contact-info">
			<# if( settings.icon ) { #>
			<i class="{{ settings.icon }}"></i>
			<# } #>
			<div class="details">
				<# if( settings.title ) { #>
				<h5>{{{ settings.title }}}</h5>
				<# } #>
				<# if( settings.text ) { #>
				<span>{{{ settings.text }}}</span>
				<# } #>
			</div>
		</div>
		<# } #>
		<?php
	}
}
?>