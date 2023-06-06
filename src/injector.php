<?php 
/**
 * Injects dependencies creating anonymous activator classes
 * Activator classes will only be instanced when called
 */
class Injector {
  public function getActivators(array $dependencies) {
    foreach  ($dependencies as $objectName => $dependency) {

      $dependency['params'] ??= null;
      $dependency['setupFunction'] ??= null;

      [
        'class' => $class, 
        'params' => $params, 
        'setupFunction' => $setupFunction
      ] = $dependency;

      if (!array_key_exists('class', $dependency)) {
        continue ;
      }

      if (!class_exists($class)) {
        throw new Exception("Non instatiable class $class in injector configuration: verify you confs/");
      }

      yield [
        $objectName , [
          'activator' => $this->inject($class, $params, $setupFunction),
          'configs' => ['class' => $class, 'params'=> $params, 'setupFunction' => $setupFunction]
        ]
      ];
    }
  }

  public function inject(string $dependency, array $params, callable|null $setupFunction) {
    return new class($dependency, $params, $setupFunction) {
      private string $dependency;
      private array $params;
      private $setupFunction;

      private object|null $instance;

      public function __construct(string $dependency, array $params, callable|null $setupFunction) {
        $this->instance = null; 
        $this->dependency = $dependency;
        $this->params = $params;
        $this->setupFunction = $setupFunction;
      }

      // Whether the activator has an instance of the dependency;
      public function hasActiveInstane() {
        return is_object($this->instance);
      }

      /**
       * When you use the dependency $app->depname() it should create a new instance  
       * or return the existing one
       */
      public function __invoke() {
        return $this->resolve();
        if (!is_null($this->instance)) {
          return $this->instance;
        }

        $this->instance = new $this->dependency(...$this->params);
        
        if (is_callable($this->setupFunction)) { 
          ($this->setupFunction)($this->instance);
        }

        return $this->instance;
      }

      public function resolve() {

        if (isset($this->instance)) {
          return $this->instance;
        }
        
        $this->instance = new $this->dependency(...$this->params);
        
        if (is_callable($this->setupFunction)) { 
          ($this->setupFunction)($this->instance);
        }

        return $this->instance;
      }
    };
  }
}