<?php

namespace Meliconnect\Meliconnect\Modules;

interface ModuleInterface {
    public function init();
    public function registerSubmenus();
}