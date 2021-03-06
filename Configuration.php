<?php
namespace LanguageTag;

class Configuration {
    static $singleton;

    /**
     * TODO: dependency injection from external configuration
     */
    static $config = array(
        'LanguageTag\Modules\MediaWikiLocale',
        'LanguageTag\Modules\UnixLocale',
    );

    protected $modules;

    static function get() {
        if (!Configuration::$singleton) {
            Configuration::$singleton = new Configuration();
        }
        return Configuration::$singleton;
    }

    protected function __construct() {
        foreach (Configuration::$config as $moduleName) {
            $this->modules[] = new $moduleName;
        }
    }

    function runHook($hook, $args = array()) {
        foreach ($this->modules as $module) {
            $func = array($module, $hook);
            if (is_callable($func)) {
                call_user_func_array($func, $args);
            }
        }
    }
}
