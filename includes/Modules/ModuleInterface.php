<?php

namespace Meliconnect\Meliconnect\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


interface ModuleInterface {
	public function init();
	public function registerSubmenus();
	public function registerModuleStyles( $hook );
	public function registerModuleScripts( $hook );
}
