<?php

declare(strict_types=1);

namespace Drupal\anytown;

/**
 * Forecast retrieval API client.
 */
interface ForecastClientInterface {

  /**
   * Get the current forecast
   *
   * @param string $url
   *  The URL to retrieve the forecast data.
   *
   * @return array|null
   *  An array containing the formatted data for the forecast, or null.
   */

  public function getForecastData(string $url) : ?array;

}
