<?php
namespace AIOSEO\Plugin\Common\Standalone;

use AIOSEO\Plugin\Pro\Standalone as ProStandalone;

/**
 * Registers the standalone components.
 *
 * @since 4.2.0
 */
class Standalone {
	/**
	 * Class constructor.
	 *
	 * @since 4.2.0
	 */
	public function __construct() {
		$this->detailsColumn     = aioseo()->pro ? new ProStandalone\DetailsColumn : new DetailsColumn;
		$this->headlineAnalyzer  = new HeadlineAnalyzer;
		$this->flyoutMenu        = new FlyoutMenu;
		$this->setupWizard       = new SetupWizard;

		new PublishPanel;
		new LimitModifiedDate;
		new Notifications;

		$this->pageBuilderIntegrations = [
			'elementor' => new PageBuilders\Elementor,
			'divi'      => new PageBuilders\Divi,
			'seedprod'  => new PageBuilders\SeedProd
		];
	}
}