<?php

namespace IngvarSoloma\FeeMaster\Utils;

require_once(__DIR__ . '/../bootstrap.php');

use GuzzleHttp\Client;
use IngvarSoloma\FeeMaster\Exceptions\CurrencyConverterException;

class CurrencyConverter implements CurrencyConverterInterface
{
    private static ?CurrencyConverterInterface $instance = null;
    private $rates;

    /**Singleton constructor*/
    private function __construct() {}

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \IngvarSoloma\FeeMaster\Exceptions\CurrencyConverterException
     */
    private function fetchRate(string $symbol): float
    {
        $client = new Client();
        $response = $client->request('GET', 'https://api.apilayer.com/exchangerates_data/latest', [
            'query' => [
                'base' => self::BASE_CURRENCY,
                'symbols' => $symbol,
            ],
            'headers' => [
                'apikey' => $_ENV['EXCHANGE_API_KEY']
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        if ($data['success']) {
            return $data['rates'][$symbol];
        }

        throw new CurrencyConverterException('Failed to fetch exchange rate for ' . $symbol);
    }

    public static function getInstance(): CurrencyConverter
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function convertToBaseCurrency(float $amount, string $currency): float
    {
        if ($currency === self::BASE_CURRENCY) {
            return $amount;
        }

        if (!isset($this->rates[$currency])) {
            $this->innitRate($currency);
        }

        return $amount / $this->rates[$currency];
    }

    public function convertFromBaseCurrency(float $amount, string $currency): float
    {
        if ($currency === self::BASE_CURRENCY) {
            return $amount;
        }

        if (!isset($this->rates[$currency])) {
            $this->innitRate($currency);
        }

        return $amount * $this->rates[$currency];
    }

    /**
     * @throws \IngvarSoloma\FeeMaster\Exceptions\CurrencyConverterException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function innitRate(string $currency): void
    {
        $this->rates[$currency] = $this->fetchRate($currency);
    }
}
