<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CryptoApiService extends AbstractController
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $baseUrl = 'https://pro-api.coinmarketcap.com/'
    )
    {
    }

    public function getCryptos()
    {
        return  $this->httpClient->request('GET',  $this->baseUrl.'v1/cryptocurrency/listings/latest', [
            'query' => [
                'convert' => 'EUR'
            ]
        ])->toArray()['data'];
    }
    public function getCryptosFiltered(array $cryptosOwned )
    {
        $cryptosDownloaded =  $this->getCryptos();
        $amount = 0;
        $filteredCryptos = [];
        foreach ($cryptosOwned as $cryptoOwned){
            foreach ($cryptosDownloaded as $cryptoDownloaded){
                if($cryptoDownloaded['symbol'] === $cryptoOwned->getSymbol()){
                    $crypto['symbol'] = $cryptoOwned->getSymbol() ;
                    $crypto['title'] = $cryptoOwned->getTitle() ;
                    $crypto['logoLink'] = $cryptoOwned->getLogoLink() ;
                    $crypto['totalQuantity'] = $cryptoOwned->getTotalQuantity();
                    $crypto['unitValueNow'] = $cryptoDownloaded['quote']['EUR']['price'];
                    $totalValueNow = $cryptoOwned->getTotalQuantity() * $cryptoDownloaded['quote']['EUR']['price'];
                    $buyTotalValue = $cryptoOwned->getTotalSpent();
                    $crypto['totalValueSpent'] = $buyTotalValue;
                    $crypto['totalValueNow'] = $totalValueNow ;

                    $crypto['diff'] = $totalValueNow - $buyTotalValue;
                    $amount += $totalValueNow - $buyTotalValue;
                    $filteredCryptos['cryptos'][] = $crypto;
                }
            }
        }
        $filteredCryptos['amount'] = $amount;
        return $filteredCryptos;
    }

    public function getCryptosBySymbol($symbol)
    {

        return $this->httpClient->request('GET',  $this->baseUrl.'v2/cryptocurrency/quotes/latest', [
            'query' => [
                'symbol' => $symbol,
                'convert' => 'EUR'
            ]
        ])->toArray()['data'][$symbol][0];
    }
    public function getLogoBySymbol($symbol)
    {

        return $this->httpClient->request('GET',  $this->baseUrl.'v2/cryptocurrency/info', [
            'query' => [
                'symbol' => $symbol,
            ]
        ]);
    }
}
