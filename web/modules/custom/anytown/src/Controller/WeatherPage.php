<?php

declare(strict_types=1);

namespace Drupal\anytown\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\AutowireTrait;
use GuzzleHttp\ClientInterface;

/**
 * Controller for anytown.weather_page route.
 */
class WeatherPage extends ControllerBase {

   use AutowireTrait;

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

   public function __construct(ClientInterface $httpClient) {
      $this->httpClient = $httpClient;
      $this->logger = $this->getLogger('anytown');
   }

  /**
   * Builds the response
   */
  public function build(string $style): array {
    // Style should be one of 'short', or 'extended'. And default to 'short'.
    $style = (in_array($style, ['short', 'extended'])) ? $style : 'short';


    $url = 'https://module-developer-guide-demo-site.ddev.site/modules/custom/anytown/data/weather_forecast.json';

    try {
      $response = $this->httpClient->get($url);
      $data = json_decode($response->getBody()->getContents(), TRUE);
    }
    catch (RequestException $e) {
      $this->logger->error('Error fetching weather data: @message', ['@message' => $e->getMessage()]);
    }


    if($data) {
      $forecast = '<ul>';
      foreach($data->list as $day) {
        $weekday = ucifrst($day->day);
        $description = arraay_shift($day->weather)->description;
        // Convert units in Kelvin to Fanhrenheit
        $high = round($day->main->temp_max - 273.15) * 9/5 + 32;
        $low = round($day->main->temp_min - 273.15) * 9/5 + 32;
        $forecast .= "<li>$weekday will be <em>$description</em> wtih a high of $high and a low of $low.</li>";
      }
      $forecast .= '</ul>';
    } else  {
      $forecast = '<p>Weather data is currently unavailable.</p>';
    }

    $output = "<p>Check out this weekend's forecas and come prepared:</p>$forecast";

/*
    $build['content'] = [
      '#type' => 'markup',
      '#markup' => '<p>The weather forecast for this week is sunny with a chance of meatballs.</p>'
    ];

    if($style === 'extended') {
      $build['content'] = [
      '#type' => 'markup',
      '#markup' => '<p>Temperatures will range from 20 to 30 degrees Celsius.</p>',
      ];
    }
 */
    return [
      '#markup ' => $output,
    ];
  }
}
