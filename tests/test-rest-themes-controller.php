<?php

class WP_Test_REST_Themes_Controller extends WP_Test_REST_Controller_TestCase {

	public function test_register_routes() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( '/wp/v2/themes', $routes );
		$this->assertArrayHasKey( '/wp/v2/themes/(?P<id>[\d]+)', $routes );
	}

	public function test_context_param() {

	}

	public function test_get_items() {

	}

	public function test_get_item() {

	}

	public function test_create_item() {

	}

	public function test_update_item() {

	}

	public function test_delete_item() {

	}

	public function test_prepare_item() {

	}

	public function test_get_item_schema() {

	}

}
