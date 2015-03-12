<?php

namespace Tmv\WhatsApi\Service;

use Tmv\WhatsApi\Entity\Phone;
use InvalidArgumentException;
use Tmv\WhatsApi\Exception\RuntimeException;

class LocalizationService
{
    /**
     * @var string
     */
    protected $countriesPath;

    /**
     * @param  string $countriesPath
     * @return $this
     */
    public function setCountriesPath($countriesPath)
    {
        $this->countriesPath = $countriesPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountriesPath()
    {
        if (!$this->countriesPath) {
            $this->countriesPath = __DIR__.'/../../data/countries.csv';
        }

        return $this->countriesPath;
    }

    /**
     * Dissect country code from phone number.
     *
     * @param  Phone                    $phone
     * @return $this
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function injectPhoneProperties(Phone $phone)
    {
        if (!file_exists($this->getCountriesPath()) || !is_readable($this->getCountriesPath())) {
            throw new RuntimeException("File doesn't exists or isn't readable.");
        }
        if (($handle = fopen($this->getCountriesPath(), 'rb')) !== false) {
            while (($data = fgetcsv($handle, 1000)) !== false) {
                if (strpos($phone->getPhoneNumber(), $data[1]) === 0) {
                    // Return the first appearance.
                    fclose($handle);

                    $mcc = explode("|", $data[2]);
                    $mcc = $mcc[0];

                    //hook:
                    //fix country code for North America
                    if (substr($data[1], 0, 1) == "1") {
                        $data[1] = "1";
                    }

                    $phone->setCountry($data[0])
                        ->setCc($data[1])
                        ->setPhone(substr(
                                $phone->getPhoneNumber(),
                                strlen($data[1]),
                                strlen($phone->getPhoneNumber())
                            )
                        )
                        ->setMcc($mcc)
                        ->setIso3166(isset($data[3]) ? $data[3] : null)
                        ->setIso639(isset($data[4]) ? $data[4] : null)
                        ->setMnc(isset($data[5]) ? $data[5] : null);

                    return $this;
                }
            }
            fclose($handle);
        }

        throw new InvalidArgumentException("Phone number not recognized");
    }
}
