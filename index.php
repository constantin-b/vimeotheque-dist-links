<?php
/*
 * Plugin Name: Vimeotheque PRO add-on
 * Plugin URI: https://vimeotheque.com
 * Description: Adds distribution links to responses from Vimeo API
 * Author: CodeFlavors
 * Version: 1.0
 * Author URI: https://codeflavors.com
 */
namespace Vimeotheque\DistributionLinks;

// No direct access
if( !defined('ABSPATH') ){
	die();
}

/**
 * Add 'files' field to API response
 *
 * @param array $fields
 *
 * @return array
 */
function add_dist_links( $fields ){
	$fields[] = 'files';
	return 	$fields;
}
add_filter( 'cvm_vimeo_api_request_extra_json_fields', __NAMESPACE__ . '\add_dist_links' );

/**
 * Register callback for additional Rest API fields
 */
function rest_api_init(){
	global $CVM_POST_TYPE;
	// must have Vimeotheque PRO activated
	if( !$CVM_POST_TYPE instanceof \CVM_Vimeo_Videos){
		return;
	}

	register_rest_field(
		array( $CVM_POST_TYPE->get_post_type(), 'post' ),
		'vimeo_dist_links',
		array(
			'get_callback' => __NAMESPACE__ . '\register_rest_api_field'
		)
	);

}
add_action( 'rest_api_init', __NAMESPACE__ . '\rest_api_init' );

/**
 * Register the field in REST API
 *
 * @param $object
 *
 * @return mixed
 */
function register_rest_api_field( $object ){
	global $CVM_POST_TYPE;
	$meta = $CVM_POST_TYPE->get_video_data( $object['id'] );
	if( is_array( $meta ) ){
		return $meta['files'];
	}
}