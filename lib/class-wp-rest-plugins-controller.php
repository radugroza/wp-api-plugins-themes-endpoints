<?php

/**
 * Manage plugins for a WordPress site
 */
class WP_REST_Plugins_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'plugins';
	}

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),
			'schema' => array( $this, 'get_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<slug>[\w-]+)', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
			),
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_item' ),
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
			),
			'schema' => array( $this, 'get_item_schema' ),
		) );
	}

	/**
	 * Check if a given request has access to read /plugins.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {

		if ( ! current_user_can( 'manage_options' ) ) { // TODO: Something related to plugins. activate_plugin capability seems to not be available for multi-site superadmin (?)
			return new WP_Error( 'rest_forbidden', __( 'Sorry, you cannot view the list of plugins' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;

	}

	public function get_items( $request ) {

		$data = array();

		require_once ABSPATH . '/wp-admin/includes/plugin.php';
		foreach ( get_plugins() as $obj ) {
			$plugin = $this->prepare_item_for_response( $obj, $request );
			if ( is_wp_error( $plugin ) ) {
				continue;
			}

			$data[] = $this->prepare_response_for_collection( $plugin );
		}

		return rest_ensure_response( $data );
	}

	/**
	 * check if a given request has access to read /plugins/{plugin-name}
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {

		if ( ! current_user_can( 'manage_options' ) ) { // TODO: Something related to plugins. activate_plugin capability seems to not be available for multi-site superadmin (?)
			return new WP_Error( 'rest_forbidden', __( 'Sorry, you do not have access to this resource' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;

	}

	public function get_item( $request ) {
		$slug   = $request['slug'];
		$plugin = $this->get_plugin( $slug );

		if ( null === $plugin ) {
			return new WP_Error( 'rest_plugin_invalid_slug', sprintf( __( 'Plugin with slug %s not found.' ), $slug ), array( 'status' => 404 ) );
		}

		$data     = $this->prepare_item_for_response( $plugin, $request );
		$response = rest_ensure_response( $data );

		return $response;

	}

	/**
	 * check if a given request has access to delete a plugin
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|boolean
	 */
	public function delete_item_permissions_check( $request ) {

		if ( ! current_user_can( 'delete_plugins' ) ) {
			return new WP_Error( 'rest_forbidden', __( 'Sorry, you cannot delete plugins' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;

	}

	/**
	 * Delete a plugin.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public function delete_item( $request ) {

		$slug = $request['slug'];

		$plugin = $this->get_plugin( $slug );

		if ( null === $plugin ) {
			return new WP_Error( 'rest_plugin_invalid_slug', sprintf( __( 'Plugin with slug %s not found.' ), $slug ), array( 'status' => 404 ) );
		}

		/*
		 * TODO: we cannot directly use the delete_plugins() function because that has the possibility of terminating the script with a nice exit() call
		 *
		 * my initial stab at this would be:
		 *
		 * ob_start()
		 * request_filesystem_credentials() - if this is false => WP_Error (code =? message =?)
		 * if ( ! WP_filesyste( $credentials ) ) => WP_Error
		 *
		 * at this point, delete_plugins() can be called
		 *
		 */


	}

	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'plugin',
			'type'       => 'object',
			'properties' => array(
				'slug'        => array(
					'description' => __( 'Identifier for the plugin' ),
					'type'        => 'string',
				),
				'name'        => array(
					'description' => __( 'The title for the resource.' ),
					'type'        => 'string',
				),
				'plugin_uri'  => array(
					'description' => __( 'The title for the resource.' ),
					'type'        => 'string',
				),
				'version'     => array(
					'description' => __( 'The title for the resource.' ),
					'type'        => 'string',
				),
				'description' => array(
					'description' => __( 'The title for the resource.' ),
					'type'        => 'string',
				),
				'author'      => array(
					'description' => __( 'The title for the resource.' ),
					'type'        => 'string',
				),
				'author_uri'  => array(
					'description' => __( 'The title for the resource.' ),
					'type'        => 'string',
				),
				'text_domain' => array(
					'description' => __( 'The title for the resource.' ),
					'type'        => 'string',
				),
				'domain_path' => array(
					'description' => __( 'The title for the resource.' ),
					'type'        => 'string',
				),
				'network'     => array(
					'description' => __( 'The title for the resource.' ),
					'type'        => 'string',
				),
				'title'       => array(
					'description' => __( 'The title for the resource.' ),
					'type'        => 'string',
				),
				'author_name' => array(
					'description' => __( 'The title for the resource.' ),
					'type'        => 'string',
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	public function get_collection_params() {
		return array();
	}


	public function prepare_item_for_response( $plugin, $request ) {
		$data = array(
			'slug'        => $plugin['slug'],
			'name'        => $plugin['Name'],
			'plugin_uri'  => $plugin['PluginURI'],
			'version'     => $plugin['Version'],
			'description' => $plugin['Description'],
			'author'      => $plugin['Author'],
			'author_uri'  => $plugin['AuthorURI'],
			'text_domain' => $plugin['TextDomain'],
			'domain_path' => $plugin['DomainPath'],
			'network'     => $plugin['Network'],
			'title'       => $plugin['Title'],
			'author_name' => $plugin['AuthorName'],
		);

		return $data;
	}

	/**
	 * Find a plugin by slug
	 *
	 * @param string $slug
	 * @return array|null
	 */
	protected function get_plugin( $slug ) {

		require_once ABSPATH . '/wp-admin/includes/plugin.php';
		$plugins = get_plugins();

		foreach ( $plugins as $_path => $plugin ) {
			$sanitized_title = sanitize_title( $plugin['Name'] );
			if ( $slug === $sanitized_title ) {

				/* we need to store the path of the plugin */
				$plugin['_path'] = $_path;

				/* include the slug in the response, as this is the ID (for now - ?) for get / delete / update */
				$plugin['slug'] = $slug;

				return $plugin;
			}
		}

		return null;
	}
}
