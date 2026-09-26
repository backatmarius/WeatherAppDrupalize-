<?php

declare(strict_types=1);

namespace Drupal\anytown\Controller;

use Drupal\anytown\ForecastClientInterface;
use Drupal\Core\Controller\ControllerBase;
// use Drupal\Core\DependencyInjection\AutowireTrait;
// use GuzzleHttp\ClientInterface;
// use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for anytown.weather_page route.
 */
class WeatherPage extends ControllerBase {

  //  use AutowireTrait;

    // protected $logger; */

    /**
     * The forecast client service.
     *
     * @var \Drupal\anytown\ForecastClientInterface
     *  Forecast API client service.
     */
    protected $forecastClient;


  /**
   * WeatherPage controller constructor.
   *
   * @param \Drupal\anytown\ForecastClientInterface $forecast_client
   *   Forecast API client service.
   */
   public function __construct(ForecastClientInterface $forecast_client) {
/*    $this->httpClient = $httpClient;
      $this->logger = $this->getLogger('anytown'); */
      $this->forecastClient = $forecast_client;
   }

    /**
      * {@inheritdoc}
      */
   public static function create(ContainerInterface $container) {
    return new static(
      $container->get('anytown.forecast_client')
    );
   }

  /**
   * Builds the response
   */
  public function build(string $style): array {
    // Style should be one of 'short', or 'extended'. And default to 'short'.
    $style = (in_array($style, ['short', 'extended'])) ? $style : 'short';


    $url = 'https://module-developer-guide-demo-site.ddev.site/modules/custom/anytown/data/weather_forecast.json';
    $forecast_data = $this->forecastClient->getForecastData($url);
/*
    $data = NULL;
    try {
      $response = $this->httpClient->get($url);
      $data = json_decode($response->getBody()->getContents(), TRUE);
    }
    catch (RequestException $e) {
      $this->logger->error('Error fetching weather data: @message', ['@message' => $e->getMessage()]);
    } */


    // if ($data) {
    //   $forecast = '<ul>';
    //   foreach ($data['list'] as $day) {
    //     $weekday = ucfirst($day['day']);
    //     $description = array_shift($day['weather'])['description'];
    //     // Convert units from Kelvin to Fahrenheit
    //     $high = round(($day['main']['temp_max'] - 273.15) * 9/5 + 32);
    //     $low = round(($day['main']['temp_min'] - 273.15) * 9/5 + 32);
    //     $forecast .= "<li>$weekday will be <em>$description</em> with a high of $high and a low of $low.</li>";
    //   }
    //   $forecast .= '</ul>';
    // }
    if ($forecast_data) {
      $forecast = '<ul>';


      $rows = [];

      foreach ($forecast_data as $item) {
        [
          'weekday' => $weekday,
          'description' => $description,
          'high' => $high,
          'low' => $low,
          'icon' => $icon,
        ] = $item;

        $row[] = [
          $weekday,
          [
            'data' => [
              '#markkup' => '<img src="https://openweathermap.org/img/wn/' . $icon . '@2x.png" alt="' . $description . '" title="' . $description . '" width="50" height="50"/>',
            ]
          ],
          [
            'data' => [
              '#markup' => "<li>$weekday will be <em>$description</em> with a high of $high and a low of $low.</li>"
            ]
          ]
        ];
        // $forecast .= "<li>$weekday will be <em>$description</em> with a high of $high and a low of $low.</li>";

      }
      $forecast .= '</ul>';
    }
    else {
      $forecast = '<p>Weather data is currently unavailable.</p>';
    }

    $output = "<p>Check out this weekend's forecast and come prepared:</p>$forecast";

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

    $build = [
      'weater_intro' => [
        '#type' => 'markup',
        '#markup' => '<p>The weather forecast for this week is sunny with a chance of meatballs.</p>'
      ],
      'weater_foreacts' => [
        '#type' => 'table',
      ],
      'weather_closure' => [],
    ];

    return [
      '#markup' => $output,
    ];
  }
}
