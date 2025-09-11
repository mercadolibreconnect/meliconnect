<?php

namespace Meliconnect\Meliconnect\Modules;

interface ModuleInterface {
    public function init();
    public function registerSubmenus();
    public function registerModuleStyles($hook);
    public function registerModuleScripts($hook);
}