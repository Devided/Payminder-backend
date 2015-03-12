<?php

namespace Tmv\WhatsApi\Service;

use Tmv\WhatsApi\Client;
use Tmv\WhatsApi\Entity\Identity;
use RuntimeException;

class IdentityService
{
    /**
     * @var string
     */
    protected $networkInfoPath;

    /**
     * @return string
     */
    public function getNetworkInfoPath()
    {
        if (!$this->networkInfoPath) {
            $this->networkInfoPath = __DIR__ . '/../../data/networkinfo.csv';
        }

        return $this->networkInfoPath;
    }

    /**
     * @param  string $networkInfoPath
     * @return $this
     */
    public function setNetworkInfoPath($networkInfoPath)
    {
        $this->networkInfoPath = $networkInfoPath;

        return $this;
    }

    /**
     * Request a registration code from WhatsApp.
     *
     * @param Identity $identity
     * @param string   $method      Accepts only 'sms' or 'voice' as a value.
     * @param string   $carrier     Carrier name
     * @param string   $countryCode ISO Country Code, 2 Digit.
     * @param string   $langCode    ISO 639-1 Language Code: two-letter codes.
     *
     * @return object
     *                An object with server response.
     *                - status: Status of the request (sent/fail).
     *                - length: Registration code lenght.
     *                - method: Used method.
     *                - reason: Reason of the status (e.g. too_recent/missing_param/bad_param).
     *                - param: The missing_param/bad_param.
     *                - retry_after: Waiting time before requesting a new code.
     *
     * @throws RuntimeException
     */
    public function codeRequest(Identity $identity, $method = 'sms', $carrier = "T-Mobile5", $countryCode = null, $langCode = null)
    {
        $phone = $identity->getPhone();
        if ($countryCode == null && $phone->getIso3166() != '') {
            $countryCode = $phone->getIso3166();
        }
        if ($countryCode == null) {
            $countryCode = 'US';
        }
        if ($langCode == null && $phone->getIso639() != '') {
            $langCode = $phone->getIso639();
        }
        if ($langCode == null) {
            $langCode = 'en';
        }

        if (null !== $carrier) {
            $mnc = $this->detectMnc(strtolower($countryCode), $carrier);
        } else {
            $mnc = $phone->getMcc();
        }

        // Build the token.
        $token = $this->generateRequestToken($phone->getPhone());

        // Build the url.
        $host = 'https://'.Client::WHATSAPP_REQUEST_HOST;
        $query = [
            'in' => $phone->getPhone(),
            'cc' => $phone->getCc(),
            'id' => $identity->getIdentityToken(),
            'lg' => $langCode,
            'lc' => $countryCode,
            'sim_mcc' => $phone->getMcc(),
            'sim_mnc' => $mnc,
            'method' => $method,
            'token' => $token,
        ];

        $response = $this->getResponse($host, $query);

        if ($response['status'] != 'sent' && $response['status'] != 'ok') {
            if (isset($response['reason']) && $response['reason'] == "too_recent") {
                $minutes = round($response['retry_after'] / 60);
                throw new RuntimeException("Code already sent. Retry after $minutes minutes.");
            } else {
                throw new RuntimeException('There was a problem trying to request the code.');
            }
        }

        return $response;
    }

    /**
     * Register account on WhatsApp using the provided code.
     *
     * @param Identity $identity
     * @param integer  $code     Numeric code value provided on requestCode().
     *
     * @return object
     *                An object with server response.
     *                - status: Account status.
     *                - login: Phone number with country code.
     *                - pw: Account password.
     *                - type: Type of account.
     *                - expiration: Expiration date in UNIX TimeStamp.
     *                - kind: Kind of account.
     *                - price: Formatted price of account.
     *                - cost: Decimal amount of account.
     *                - currency: Currency price of account.
     *                - price_expiration: Price expiration in UNIX TimeStamp.
     *
     * @throws RuntimeException
     */
    public function codeRegister(Identity $identity, $code)
    {
        // Build the url.
        $host = 'https://'.Client::WHATSAPP_REGISTER_HOST;

        $query = [
            'cc' => $identity->getPhone()->getCc(),
            'in' => $identity->getPhone()->getPhone(),
            'id' => $identity->getIdentityString(),
            'code' => $code,
            'lg' => $identity->getPhone()->getIso639() ?: 'en',
            'lc' => $identity->getPhone()->getIso3166() ?: 'US',
        ];

        $response = $this->getResponse($host, $query);

        if ($response['status'] != 'ok') {
            $message = 'An error occurred registering the registration code from WhatsApp. '.$response['reason'];
            throw new RuntimeException($message);
        }

        return $response;
    }

    /**
     * Check if account credentials are valid.
     *
     * WARNING: WhatsApp now changes your password everytime you use this.
     * Make sure you update your config file if the output informs about
     * a password change.
     *
     * @param  Identity $identity
     * @return array
     *                           An object with server response.
     *                           - status: Account status.
     *                           - login: Phone number with country code.
     *                           - pw: Account password.
     *                           - type: Type of account.
     *                           - expiration: Expiration date in UNIX TimeStamp.
     *                           - kind: Kind of account.
     *                           - price: Formatted price of account.
     *                           - cost: Decimal amount of account.
     *                           - currency: Currency price of account.
     *                           - price_expiration: Price expiration in UNIX TimeStamp.
     *
     * @throws \RuntimeException
     */
    public function checkCredentials(Identity $identity)
    {
        $host = 'https://'.Client::WHATSAPP_CHECK_HOST;
        $query = [
            'cc' => $identity->getPhone()->getCc(),
            'in' => $identity->getPhone()->getPhoneNumber(),
            'id' => $identity->getIdentityString(),
            'lg' => $identity->getPhone()->getIso639() ?: 'en',
            'lc' => $identity->getPhone()->getIso3166() ?: 'US',
            'network_radio_type' => "1"
        ];

        $response = $this->getResponse($host, $query);

        if ($response['status'] != 'ok') {
            $message = 'There was a problem trying to request the code. '.$response['reason'];
            throw new \RuntimeException($message);
        }

        return $response;
    }

    /**
     * Get a decoded JSON response from Whatsapp server
     *
     * @param  string $host  The host URL
     * @param  array  $query A associative array of keys and values to send to server.
     * @return object NULL is returned if the json cannot be decoded or if the encoded data is deeper than the recursion limit
     */
    protected function getResponse($host, array $query)
    {
        // Build the url.
        $url = $host.'?';
        foreach ($query as $key => $value) {
            $url .= $key.'='.$value.'&';
        }
        $url = rtrim($url, '&');

        // Open connection.
        $ch = curl_init();

        // Configure the connection.
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, Client::WHATSAPP_USER_AGENT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: text/json']);
        // This makes CURL accept any peer!
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Get the response.
        $response = curl_exec($ch);

        // Close the connection.
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * @param  string $phone
     * @return string
     */
    protected function generateRequestToken($phone)
    {
        return $token = md5("PdA2DJyKoUrwLw1Bg6EIhzh502dF9noR9uFCllGk1419900749520" . $phone);
        /*
        $signature = "MIIDMjCCAvCgAwIBAgIETCU2pDALBgcqhkjOOAQDBQAwfDELMAkGA1UEBhMCVVMxEzARBgNVBAgTCkNhbGlmb3JuaWExFDASBgNVBAcTC1NhbnRhIENsYXJhMRYwFAYDVQQKEw1XaGF0c0FwcCBJbmMuMRQwEgYDVQQLEwtFbmdpbmVlcmluZzEUMBIGA1UEAxMLQnJpYW4gQWN0b24wHhcNMTAwNjI1MjMwNzE2WhcNNDQwMjE1MjMwNzE2WjB8MQswCQYDVQQGEwJVUzETMBEGA1UECBMKQ2FsaWZvcm5pYTEUMBIGA1UEBxMLU2FudGEgQ2xhcmExFjAUBgNVBAoTDVdoYXRzQXBwIEluYy4xFDASBgNVBAsTC0VuZ2luZWVyaW5nMRQwEgYDVQQDEwtCcmlhbiBBY3RvbjCCAbgwggEsBgcqhkjOOAQBMIIBHwKBgQD9f1OBHXUSKVLfSpwu7OTn9hG3UjzvRADDHj+AtlEmaUVdQCJR+1k9jVj6v8X1ujD2y5tVbNeBO4AdNG/yZmC3a5lQpaSfn+gEexAiwk+7qdf+t8Yb+DtX58aophUPBPuD9tPFHsMCNVQTWhaRMvZ1864rYdcq7/IiAxmd0UgBxwIVAJdgUI8VIwvMspK5gqLrhAvwWBz1AoGBAPfhoIXWmz3ey7yrXDa4V7l5lK+7+jrqgvlXTAs9B4JnUVlXjrrUWU/mcQcQgYC0SRZxI+hMKBYTt88JMozIpuE8FnqLVHyNKOCjrh4rs6Z1kW6jfwv6ITVi8ftiegEkO8yk8b6oUZCJqIPf4VrlnwaSi2ZegHtVJWQBTDv+z0kqA4GFAAKBgQDRGYtLgWh7zyRtQainJfCpiaUbzjJuhMgo4fVWZIvXHaSHBU1t5w//S0lDK2hiqkj8KpMWGywVov9eZxZy37V26dEqr/c2m5qZ0E+ynSu7sqUD7kGx/zeIcGT0H+KAVgkGNQCo5Uc0koLRWYHNtYoIvt5R3X6YZylbPftF/8ayWTALBgcqhkjOOAQDBQADLwAwLAIUAKYCp0d6z4QQdyN74JDfQ2WCyi8CFDUM4CaNB+ceVXdKtOrNTQcc0e+t";
        $classesMd5 = "oCtjlSonS+4H16h9HW6nNA=="; // 2.11.378 [*]

        $key2 = base64_decode("/UIGKU1FVQa+ATM2A0za7G2KI9S/CwPYjgAbc67v7ep42eO/WeTLx1lb1cHwxpsEgF4+PmYpLd2YpGUdX/A2JQitsHzDwgcdBpUf7psX1BU=");
        $data = base64_decode($signature).base64_decode($classesMd5).$phone;

        $opad = str_repeat(chr(0x5C), 64);
        $ipad = str_repeat(chr(0x36), 64);
        for ($i = 0; $i < 64; $i++) {
            $opad[$i] = $opad[$i] ^ $key2[$i];
            $ipad[$i] = $ipad[$i] ^ $key2[$i];
        }

        $output = hash("sha1", $opad.hash("sha1", $ipad.$data, true), true);

        return base64_encode($output);
        */
    }

    /**
     * @param  string      $lc          LangCode
     * @param  string      $carrierName Name of the carrier
     * @return null|string
     */
    protected function detectMnc($lc, $carrierName)
    {
        $fp = fopen($this->getNetworkInfoPath(), 'r');
        $mnc = null;

        while ($data = fgetcsv($fp, 0, ',')) {
            if (($data[4] === $lc) && ($data[7] === $carrierName)) {
                $mnc = $data[2];
                break;
            }
        }

        if ($mnc == null) {
            $mnc = '000';
        }

        fclose($fp);

        return $mnc;
    }

    /**
     * @return string
     */
    public static function generateIdentity()
    {
        $bytes = strtolower(openssl_random_pseudo_bytes(20));

        return $bytes;
    }
}
