<?php

namespace StoreSync\Meliconnect\Modules;

interface ModuleInterface {
    public function init();
    public function registerSubmenus();
}