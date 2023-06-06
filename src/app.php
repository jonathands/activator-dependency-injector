<?php
require 'injector.php';

abstract class Singleton
{
  private static $instance;

  protected function __construct()
  {
    //Don't
  }

  public static function get() {
    if (!self::$instance) {
      self::$instance = new static();
    }

    return self::$instance;
  }
}


final class App extends Singleton
{
  // All configurations
  private array $confs;
  private array $dependencies;

  // Dependency activators
  public function __construct(){
    $this->resolveConfs();
  }

  public function getDespendencies()
  {
    return $this->dependencies;
  }

  public function run()
  {
    $this->createDependencyActivators();
  }

  /**
   * gets dependencies as App attributes 
   * so $app->db returns the injected db 
   * otherwise tries to get a normal attribute
   */
  public function __get($attribute)
  {
    if (array_key_exists($attribute, $this->dependencies)) {
      $activatorClass = $this->dependencies[$attribute]['activator'];
      $configs = $this->dependencies[$attribute]['configs'];

      $activator = new $activatorClass(...array_values($configs));
      return $activator();
    }

    return $this->$attribute;
  }

  private function createDependencyActivators() {
    $injector = new Injector();
    
    $deps = array_filter($this->confs, function ($conf) {
      return (array_key_exists('class', $conf)) ? $conf : false;
    });

    $this->dependencies = [];
    foreach ($injector->getActivators($deps) as $dependencyActivator) {
      $activator = $dependencyActivator;
      $this->dependencies[$activator[0]] = $activator[1]; 
    }
  }

  private function resolveConfs()
  {
    $files = glob('../src/confs/*.php');
    foreach ($files as $file) {
      $this->confs = require $file;
    }
  }
};

return App::get();