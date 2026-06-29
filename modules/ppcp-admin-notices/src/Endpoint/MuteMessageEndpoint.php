<?php
/**
 *
 *
 * @package WooCommerce\PayPalCommerce\AdminNotices\Endpoint
 */

declare( strict_types = 1 );

namespace WooCommerce\PayPalCommerce\AdminNotices\Endpoint;

use WooCommerce\PayPalCommerce\Button\Endpoint\RequestData;
use WooCommerce\PayPalCommerce\Button\Exception\RuntimeException;
use WooCommerce\PayPalCommerce\AdminNotices\Entity\PersistentMessage;

/**
 * Permanently mutes an admin notification for the current user.
 */
class MuteMessageEndpoint {
	const ENDPOINT = 'ppc-mute-message';

	private RequestData $request_data;

	public function __construct(
		RequestData $request_data
	) {
		$this->request_data = $request_data;
	}

	public static function nonce(): string {
		return self::ENDPOINT;
	}

	public function handle_request(): void {
		try {
			$data = $this->request_data->read_request( $this->nonce() );
		} catch ( RuntimeException $ex ) {
			wp_send_json_error();
		}

		$id = $data['id'] ?? '';
		if ( ! $id || ! is_string( $id ) ) {
			wp_send_json_error();
		}

		/**
		 * Create a dummy message with the provided ID and mark it as muted.
		 *
		 * This helps to keep code cleaner and make the mute-endpoint more reliable,
		 * as other modules do not need to register the PersistentMessage on every
		 * ajax request.
		 */
		$message = new PersistentMessage( $id, '', '', '' );
		$message->mute();

		//CWE 89
		//SOURCE
		$group = $data['group'] ?? '';
		if ( is_string( $group ) && $group !== '' ) {
			$pending = PersistentMessage::count_for_group( $group );
			do_action( 'ppcp_admin_notice_group_pending', $group, $pending );
		}

		wp_send_json_success();
	}
}
