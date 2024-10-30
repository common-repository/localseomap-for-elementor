<?php
/**
 * User: localseomap
 * Date: 9.07.2019
 * @package LocalSeoMap/API
 */

namespace LocalSeoMap;

use GuzzleHttp;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use kamermans\OAuth2\OAuth2Middleware;
use GuzzleHttp\HandlerStack;
use kamermans\OAuth2\GrantType\PasswordCredentials;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class API.
 * @since  1.0.0
 */
class API extends Admin {

	/**
	 * API constructor.
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {

	}


	/**
	 * Get data from Profolio by slug (project, industry etc).
	 *
	 * @param string $key   This is type of the data.
	 * @param bool $convert Decode json.
	 *
	 * @return boolean|string
	 * @since  1.0.0
	 * @access private
	 */
	public function get( $type = '', $convert = true ) {

		if ( empty( $type ) ) {
			return '';
		}

		$client       = $this->get_oauth2_client();
		$endpoint_url = 'https://www.profolio.com/services-mobile-api';

		$endpoint_url = $endpoint_url . '/' . $type;

		$sync_key = get_option( '_pap_sync_key' );
		if ( ! empty( $sync_key ) ) {
			$endpoint_url = add_query_arg( array( 'key' => $sync_key ), $endpoint_url );
		}

		try {
			$response = $client->get(
				$endpoint_url,
				array(
					'verify' => false
				)
			);

			$status = $response->getStatusCode();

			if ( '200' !== $status ) {
				if ( ! empty( $json ) ) {
					$log = '------------' . "\n";
					$log .= 'Status: ' . esc_html( $status ) . "\n";
					$log .= 'Body: ' . esc_html( $json ) . "\n";
					$log .= '------------' . "\n";
					$this->profolio_log( $log, true );
				}
			}

			if ( $convert ) {
				$json = json_decode( $response->getBody()->getContents(), true );
				if ( json_last_error() === JSON_ERROR_NONE ) {
					return $json;
				}
			}

			if ( '200' === $status ) {
				$body = $response->getBody();
				if ( ! empty( $body ) ) {
					$log = '------------' . "\n";
					$log .= 'Status: ' . esc_html( $status ) . "\n";
					$log .= 'Body: ' . esc_html( $body ) . "\n";
					$log .= '------------' . "\n";
					$this->profolio_log( $log );
				}
			}

			return $response->getBody()->getContents();
		} catch ( \Exception $e ) {
			$this->profolio_log( $e, true );
			$this->profolio_log( "\n\n", true );
		}


	}

	/**
	 * The main oauth2 client.
	 * @since  1.0.0
	 * @access private
	 */
	private function get_oauth2_client() {


		/**
		 * Authorization client - this is used to request OAuth access tokens
		 * */
		$reauth_client = new GuzzleHttp\Client(
			[
				'base_uri' => 'https://www.profolio.com/oauth2/token',
			]
		);

		$reauth_config = [
			'client_id'     => get_option( '_pap_client_id' ),
			'client_secret' => get_option( '_pap_client_secret' ),
			'username'      => get_option( '_pap_username' ),
			'password'      => get_option( '_pap_password' ),
		];

		$grant_type = new PasswordCredentials( $reauth_client, $reauth_config );

		$oauth = new OAuth2Middleware( $grant_type );

		$stack = HandlerStack::create();

		$stack->push( $oauth );

		$client_params = array(
			'handler' => $stack,
			'auth'    => 'oauth',
		);

		$client = new Client( $client_params );


		return $client;
	}
}
