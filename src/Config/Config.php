<?php

declare(strict_types = 1);

namespace AlexSkrypnyk\Shellvar\Config;

/**
 * Class Config.
 *
 * Provides configuration for the application.
 */
class Config implements ConfigInterface {

  /**
   * The configuration values.
   *
   * @var array<string|array|mixed>
   */
  protected array $values = [];

  /**
   * Config constructor.
   *
   * @param array<string|array|mixed> $arguments
   *   Config arguments.
   */
  public function __construct(...$arguments) {
    $this->values = array_merge(...$arguments);
  }

  /**
   * Get config value.
   *
   * @param string $name
   *   Config name.
   * @param string|array|mixed $default
   *   Config value.
   */
  public function get($name, $default = NULL): mixed {
    return $this->values[$name] ?? $default;
  }

  /**
   * {@inheritdoc}
   */
  public function set($name, $value): ConfigInterface {
    $this->values[$name] = $value;

    return $this;
  }

  /**
   * Set values from all arguments.
   *
   * @param array<string|array|mixed> $arguments
   *   Arguments to set.
   *
   * @return void
   *   Returns nothing.
   */
  public function setAll(...$arguments) : void {
    $this->values = array_merge(...$arguments);
  }

}
