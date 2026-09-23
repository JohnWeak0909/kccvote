<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\SettingModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    protected function getCampusSettings(): array
    {
        $settingModel = new SettingModel();

        return [
            'campus_latitude'      => $settingModel->getValue('campus_latitude', ''),
            'campus_longitude'     => $settingModel->getValue('campus_longitude', ''),
            'campus_access_radius' => $settingModel->getValue('campus_access_radius', ''),
        ];
    }

    protected function calculateDistanceMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000.0; // meters

        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($deltaLng / 2) * sin($deltaLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    protected function isWithinCampusRange(?float $latitude, ?float $longitude): bool
    {
        $settings = $this->getCampusSettings();

        // If campus settings are not configured, allow access
        if ($settings['campus_access_radius'] === '' || $settings['campus_latitude'] === '' || $settings['campus_longitude'] === '') {
            return true;
        }

        // If coordinates are not provided, still allow access (geolocation may have failed)
        // Campus security will be logged but not enforced
        if ($latitude === null || $longitude === null) {
            return true;
        }

        $lat = floatval($settings['campus_latitude']);
        $lng = floatval($settings['campus_longitude']);
        $radius = floatval($settings['campus_access_radius']);

        return $this->calculateDistanceMeters($lat, $lng, $latitude, $longitude) <= $radius;
    }
}
