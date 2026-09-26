<?php

declare(strict_types=1);

namespace Drupal\anytown;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

/**
 * Forecast retrieval API client.
 */
class ForecastClient implements ForecastClientInterface {

   /**
    * The HTTP client service.
    *
    * @var \GuzzleHttp\ClientInterface
    */
   protected $httpClient;

    /**
      * The logger channel.
      *
      * @var \Psr\Log\LoggerInterface
      */
   protected $logger;

  /**
   * Construct a forecast API client.
   *
   * @param \GuzzleHttp\ClientInterface $httpClient
   *   Guzzle HTTP Client.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory service.
   */

  public function __construct(ClientInterface $httpclient, LoggerChannelFactoryInterface $logger_factory) {
    $this->httpClient = $httpclient;
    $this->logger = $logger_factory->get('anytown');

  }

  /**
   * {@inheritdoc }
   */
  public function getForecastData(string $url) : ?array {
    // TODO: Implement getForecastData() method.

    try {
      $response = $this->httpClient->get($url);
      $json = json_decode($response->getBody()->getContents());
    }
    catch (RequestException $e) {
      $this->logger->error('Error fetching weather data: @message', ['@message' => $e->getMessage()]);
      return NULL;
    }

    $forecast = [];

    foreach ($json->list as $day) {
      $forecast[$day->day] = [
        'weekday' => ucfirst($day->day),
        'description' => $day->weather[0]->description,
        'high' => $this->kelvinToFahrenheit($day->main->temp_max),
        'low' => $this->kelvinToFahrenheit($day->main->temp_min),
        'icon' => $day->weather[0]->icon,
      ];
    }

    return $forecast;

  }

  /**
   * Convert Kelvin to Fahrenheit.
   *
   * @param float $kelvin
   *   The temperature in Kelvin.
   *
   * @return float
   *   The temperature in Fahrenheit.
   */
  public static function kelvinToFahrenheit(float $kelvin): float {
    return round(($kelvin - 273.15) * 9/5 + 32);
  }
}
