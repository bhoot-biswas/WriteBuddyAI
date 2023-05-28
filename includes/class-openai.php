<?php
namespace BengalStudio;

use Exception;
use WP_Error;

class OpenAI {
	private $api_key;
	private $api_endpoint;

	function __construct( $api_key ) {
		$this->api_key      = $api_key;
		$this->api_endpoint = 'https://api.openai.com/v1/';
	}

	function send_request( $path, $data ) {
		$url     = $this->api_endpoint . $path;
		$headers = array(
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $this->api_key,
		);

		$args = array(
			'headers' => $headers,
			'body'    => json_encode( $data ),
			'timeout' => 120,
		);

		try {
			$response = wp_remote_post( $url, $args );

			if( is_wp_error( $response ) ) {
				return $response;
			}

			$response_body = json_decode( wp_remote_retrieve_body( $response ) );
			if ( json_last_error() === JSON_ERROR_NONE ) {
				if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
					return $response_body;
				} else {
					return new WP_Error( 'error', $response_body->error->message );
				}
			} else {
				return new WP_Error( 'error', __( 'Something went wrong, please try again later!', 'writebuddyai' ) );
			}
		} catch ( Exception $e ) {
			return new WP_Error( 'error', $e->getMessage() );
		}
	}
}
