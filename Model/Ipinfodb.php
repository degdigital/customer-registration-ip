<?php
namespace DEG\CustomerRegIp\Model;

class Ipinfodb
{
    /**
     * See http://www.ipinfodb.com/ip_location_api.php for more api details
     *
     * @var string
     */
    protected $_apiUrl = 'http://api.ipinfodb.com/v3/ip-city/';

    protected $_format = 'xml';

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * See http://www.ipinfodb.com/ip_location_api.php for more api details
     *
     * @param string $ipAddr
     * @return Varien_Object
     */
    public function lookupIp($ipAddr)
    {
        $url = $this->_getApiQueryUrl($ipAddr);
        $info = new \Magento\Framework\Object();
        if ($result = @file_get_contents($url)) {
            /*
             * Example positive lookup result:

             <Response>
                <statusCode>OK</statusCode>
                <statusMessage/>
                <ipAddress>74.125.77.147</ipAddress>
                <countryCode>US</countryCode>
                <countryName>UNITED STATES</countryName>
                <regionName>CALIFORNIA</regionName>
                <cityName>MOUNTAIN VIEW</cityName>
                <zipCode>94043</zipCode>
                <latitude>37.3956</latitude>
                <longitude>-122.076</longitude>
                <timeZone>-08:00</timeZone>
             </Response>

             *
             * Example failed lookup result:
             *

             <Response>
                <statusCode>OK</statusCode>
                <statusMessage/>
                <ipAddress>xs</ipAddress>
                <countryCode/>
                <countryName/>
                <regionName/>
                <cityName/>
                <zipCode/>
                <latitude/>
                <longitude/>
                <timeZone/>
             </Response>

             */

            $xml = simplexml_load_string($result);
            if ($xml->statusCode != 'OK') {
                throw new \Magento\Framework\Model\Exception($xml->statusMessage);
            }
            $info->setData(array(
                'ip' => $xml->ipAddress,
                'country' => $xml->countryCode,
                'country_name' => $xml->countryName,
                'region' => $xml->regionName,
                'city' => $xml->cityName,
                'postcode' => $xml->zipCode,
                'latitude' => $xml->latitude,
                'longitude' => $xml->longitude,
                'timezone' => $xml->timeZone,
            ));
        }

        return $info;
    }

    /**
     * Build lookup query url
     *
     * http://api.ipinfodb.com/v3/ip-city/?key=<API-KEY>&ip=74.125.77.147&format=xml
     *
     * @param string $ipAddr
     * @return string
     */
    protected function _getApiQueryUrl($ipAddr)
    {
        $key = $this->_scopeConfig->getValue(
            'customerregip/general/ipinfodb_api_key'
        );
        $params = array(
            'key' => $key,
            'ip' => (string)$ipAddr,
            'format' => $this->_format,
        );
        $url = '';
        foreach ($params as $key => $value) {
            $url .= $url ? '&' : '';
            $url .= $key . '=' . $value;
        }
        $url = $this->_apiUrl . '?' . $url;
        return $url;
    }

}
