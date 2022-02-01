<?php


namespace App\Helpers;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class CloudinaryHelper {
    public $cloudinary;

    /**
     * CloudinaryHelper constructor.
     * @param $cloudinary
     */
    public function __construct() {
        $config = Configuration::instance();
        $config->cloud->cloudName = config('custom.cl_cloudname');
        $config->cloud->apiKey = config('custom.cl_apikey');
        $config->cloud->apiSecret = config('custom.cl_apisecret');
        $config->url->secure = config('custom.cl_secure');
        $this->cloudinary = new Cloudinary($config);
    }

    public function destroy($public_id) {
        $this->cloudinary->uploadApi()->destroy($public_id);
    }

}
