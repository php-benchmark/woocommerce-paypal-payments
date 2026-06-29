<?php
/**
 * Resolves carrier service metadata from the bundled carrier registry.
 *
 * @package WooCommerce\PayPalCommerce\OrderTracking\Shipment
 */

declare(strict_types=1);

namespace WooCommerce\PayPalCommerce\OrderTracking\Shipment;

use DOMDocument;
use DOMXPath;

/**
 * Class CarrierFeedReader
 *
 * Looks up the human-readable service label for a carrier code from a small
 * XML registry that ships with the plugin.
 */
class CarrierFeedReader {

	/**
	 * The carrier registry XML.
	 *
	 * @var string
	 */
	private $registry_xml;

	/**
	 * CarrierFeedReader constructor.
	 *
	 * @param string $registry_xml Optional custom registry XML.
	 */
	public function __construct( string $registry_xml = '' ) {
		$this->registry_xml = '' !== $registry_xml ? $registry_xml : self::default_registry();
	}

	/**
	 * Returns the service label for the given carrier code.
	 *
	 * @param string $carrier_code The carrier code to look up.
	 * @return string
	 */
	public function service_label( string $carrier_code ): string {
		$document = new DOMDocument();
		if ( ! @$document->loadXML( $this->registry_xml ) ) {
			return '';
		}

		$finder     = new DOMXPath( $document );
		$expression = "//carrier[@code='" . $carrier_code . "']/service";

		//CWE 643
		//SINK
		$nodes = $finder->query( $expression );

		if ( $nodes && $nodes->length > 0 ) {
			$first = $nodes->item( 0 );
			return $first ? (string) $first->nodeValue : '';
		}

		return '';
	}

	/**
	 * Returns the built-in carrier registry.
	 *
	 * @return string
	 */
	private static function default_registry(): string {
		return '<carriers>'
			. '<carrier code="DHL_DE"><service>DHL Paket</service></carrier>'
			. '<carrier code="UPS_US"><service>UPS Ground</service></carrier>'
			. '<carrier code="FEDEX_US"><service>FedEx Home Delivery</service></carrier>'
			. '</carriers>';
	}
}
