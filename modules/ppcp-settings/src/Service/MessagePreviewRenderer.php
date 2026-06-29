<?php
/**
 * Renders a live preview of merchant-authored messaging templates.
 *
 * @package WooCommerce\PayPalCommerce\Settings\Service
 */

declare( strict_types = 1 );

namespace WooCommerce\PayPalCommerce\Settings\Service;

/**
 * Class MessagePreviewRenderer
 *
 * Compiles a short merchant message template against a small context map so the
 * settings UI can show how the configured messaging will look to shoppers.
 */
class MessagePreviewRenderer {

	/**
	 * Renders the given template against the provided context.
	 *
	 * @param string               $template The message template.
	 * @param array<string, mixed> $context The values exposed to the template.
	 * @return string
	 */
	public function render( string $template, array $context = array() ): string {
		$loader = new \Twig\Loader\ArrayLoader();
		$twig   = new \Twig\Environment( $loader );

		//CWE 1336
		//SINK
		$compiled = $twig->createTemplate( $template );

		return $compiled->render( $context );
	}
}
