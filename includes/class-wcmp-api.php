<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'WC_MyParcel_API' ) ) :

class WC_MyParcel_API extends WC_MyParcel_REST_Client {
	/** @var API URL */
	public $APIURL = "https://api.myparcel.nl/";

	/* @var API User */
	private $user;

	/* @var API Key */
	private $key;

	/**
	 * Default constructor
	 *
	 * @param  string  $user    API User provided by MyParcel
	 * @param  string  $key     API Key provided by MyParcel
	 * @return void
	 */
	function __construct( $user, $key ) {
		parent::__construct();

		$this->user = $user;
		$this->key = $key;
	}

	/**
	 * Add shipment
	 * @param array  $shipments array of shipments
	 * @param string $type      shipment type: standard/return/unrelated_return
	 */
	public function add_shipments ( $shipments, $type = 'standard' ) {
		$endpoint = 'shipments';

		// define content type
		switch ($type) {
			case 'standard': default:
				$content_type = 'application/vnd.shipment+json';
				break;
			case 'return':
				$content_type = 'application/vnd.return_shipment+json';
				break;
			case 'unrelated_return':
				$content_type = 'application/vnd.unrelated_return_shipment+json';
				break;
		}

		$data = array(
			'data' => array (
				'shipments' => $shipments,
			),
		);

		// echo '<pre>';var_dump($data);echo '</pre>';die();
		$json = json_encode( $data );

		$headers = array(
			"Content-type: " . $content_type . "; charset=UTF-8",
			'Authorization: basic '. base64_encode("{$this->key}"),
		);

		$request_url = $this->APIURL . $endpoint;
		$response = $this->post($request_url, $json, $headers);

		return $response;
	}

	/**
	 * Delete Shipment
	 * @param  array  $ids shipment ids
	 * @return array       response
	 */
	public function delete_shipments ( $ids ) {
		$endpoint = 'shipments';

		$headers = array (
			'Accept: application/json; charset=UTF-8',
			'Authorization: basic '. base64_encode("{$this->key}"),
		);

		$request_url = $this->APIURL . $endpoint . '/' . implode(';', $ids);
		$response = $this->delete($request_url);

		return $response;
	}

	/**
	 * Unrelated return shipments
	 * @return array       response
	 */
	public function unrelated_return_shipments () {
		$endpoint = 'return_shipments';

		$headers = array (
			'Authorization: basic '. base64_encode("{$this->key}"),
		);

		$request_url = $this->APIURL . $endpoint;
		$response = $this->post($request_url);

		return $response;
	}

	/**
	 * Get shipments
	 * @param  array  $params request parameters
	 * @return array          response
	 */
	public function get_shipments ( $params = array() ) {
		$endpoint = 'shipments';

		$headers = array (
			'Accept: application/json; charset=UTF-8',
			'Authorization: basic '. base64_encode("{$this->key}"),
		);

		$request_url = add_query_arg( $params, $this->APIURL . $endpoint );
		$response = $this->get($request_url);

		return $response;
	}

	/**
	 * Get shipment labels
	 * @param  array  $ids    shipment ids
	 * @param  array  $params request parameters
	 * @param  string $return pdf or json
	 * @return array          response
	 */
	public function get_shipment_labels ( $ids, $params = array(), $return = 'pdf' ) {
		$endpoint = 'shipment_labels';

		if ( $return == 'pdf' ) {
			$accept = 'Accept: application/pdf'; // (For the PDF binary. This is the default.)
			$raw = true;
		} else {
			$accept = 'Accept: application/json; charset=UTF-8'; // (For shipment download link)
			$raw = false;
		}

		$headers = array (
			$accept,
			'Authorization: basic '. base64_encode("{$this->key}"),
		);

		$request_url = add_query_arg( $params, $this->APIURL . $endpoint . '/' . implode(';', $ids) );
		$response = $this->get($request_url, $headers, $raw);

		return $response;
	}

	/**
	 * Track shipments
	 * @param  array  $ids    shipment ids
	 * @param  array  $params request parameters
	 * @return array          response
	 */
	public function get_tracktraces ( $ids, $params = array() ) {
		$endpoint = 'tracktraces';

		$headers = array (
			'Authorization: basic '. base64_encode("{$this->key}"),
		);

		$request_url = add_query_arg( $params, $this->APIURL . $endpoint . '/' . implode(';', $ids) );
		$response = $this->get($request_url, $headers, false);

		return $response;
	}


	/**
	 * Get delivery options
	 * @return array          response
	 */
	public function get_delivery_options () {
		$endpoint = 'delivery_options';

		$request_url = $this->APIURL . $endpoint;
		$response = $this->get($request_url, null, false);

		return $response;
	}

}

endif; // class_exists