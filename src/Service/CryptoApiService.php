<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Cette classe est un service qui permet de faire le lien avec l'API https://coinmarketcap.com
 */
class CryptoApiService extends AbstractController
{
    /**
     * L'url de base de l'api est definit
     * @param HttpClientInterface $httpClient
     * @param string $baseUrl
     */
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $baseUrl = 'https://pro-api.coinmarketcap.com/'
    )
    {
    }

    /**
     * Renvoie un tableau des cryptos obtenues par l'API
     * @return mixed
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getCryptos()
    {
        return  $this->httpClient->request('GET',  $this->baseUrl.'v1/cryptocurrency/listings/latest', [
            'query' => [
                'convert' => 'EUR'
            ]
        ])->toArray()['data'];
    }

    /**
     * Renvoie un tableau des cryptos obtenues par l'API, filtrés en fonction des cryptos données.
     * @param array $cryptosOwned
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
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

    /**
     * Renvoie les informations sur une crypto données (via son symbole)
     * @param $symbol
     * @return mixed
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getCryptosBySymbol($symbol)
    {

        return $this->httpClient->request('GET',  $this->baseUrl.'v2/cryptocurrency/quotes/latest', [
            'query' => [
                'symbol' => $symbol,
                'convert' => 'EUR'
            ]
        ])->toArray()['data'][$symbol][0];
    }

    /**
     * Renvoie le lien de l'image dune crypto données (via son symbole)
     * @param $symbol
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     */
    public function getLogoBySymbol($symbol)
    {

        return $this->httpClient->request('GET',  $this->baseUrl.'v2/cryptocurrency/info', [
            'query' => [
                'symbol' => $symbol,
            ]
        ]);
    }
}
