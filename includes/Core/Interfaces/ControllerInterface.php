<?php

namespace Meliconnect\Meliconnect\Core\Interfaces;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

interface ControllerInterface {
	public function getData();
}
