<?php
namespace Slince\ShipmentTracking\YanWen;

use GuzzleHttp\Client as HttpClient;
use Slince\ShipmentTracking\Exception\TrackException;
use Slince\ShipmentTracking\HttpAwareTracker;
use Slince\ShipmentTracking\Shipment;
use Slince\ShipmentTracking\ShipmentEvent;
use Slince\ShipmentTracking\Exception\RuntimeException;
use GuzzleHttp\Exception\GuzzleException;

class YanWenTracker extends HttpAwareTracker
{
    /**
     * @var string
     */
    const TRACKING_ENDPOINT = 'http://api.track.yw56.com.cn/v2/api/Tracking';

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $culture;

    protected $parameters = [
        'key' => 'none',
        'culture' => 'en',
    ];

    public function __construct($key = 'none', $culture =  'en', HttpClient $httpClient = null)
    {
        $this->key = $key;
        $this->culture = $culture;
        $httpClient && $this->setHttpClient($httpClient);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return YanWenTracker
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getCulture()
    {
        return $this->culture;
    }

    /**
     * @param string $culture
     * @return YanWenTracker
     */
    public function setCulture($culture)
    {
        $this->culture = $culture;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function track($trackingNumber)
    {
        $parameters = $this->parameters;
        $parameters['tracking_number'] = $trackingNumber;
        try {
            $response = $this->httpClient->get(static::TRACKING_ENDPOINT, [
                'query' => $parameters
            ]);
            $json = \GuzzleHttp\json_decode((string)$response->getBody(), true);
            if ($json['code'] != 0) {
                throw new RuntimeException(sprintf('Bad response with code "%d"', $json['code']));
            }
            return static::buildShipment($json);
        } catch (GuzzleException $exception) {
            throw new TrackException($exception->getMessage());
        }
    }

    protected static function buildShipment($json)
    {
        $shippingItems = array_merge($json['origin_items'], $json['destin_items']);
        $events = array_map(function($item) {
            return ShipmentEvent::fromArray([
                'location' => $item['location'],
                'description' => $item['message'],
                'date' => $item['timestamp'],
                'status' => null
            ]);
        }, $shippingItems);
        $shipment = new Shipment($events);
        $shipment->setIsDelivered($json['state'] == 40)
            ->setOrigin($json['origin_country'])
            ->setDestination($json['destin_country'])
            ->setDeliveredAt($json['receive_date']);
        return $shipment;
    }
}