<?php

namespace App\Service;



use App\Entity\Result;
use App\Repository\CryptoMoneyRepository;
use App\Repository\ResultRepository;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Cette classe est un service qui permet de sauvegarder le montant des investissements actuels.
 */
class SaveAmountService
{
    private ResultRepository $resultRepository;
    private CryptoMoneyRepository $cryptoMoneyRepository;
    private CryptoApiService $api;

    /**
     * @param ResultRepository $resultRepository
     * @param CryptoMoneyRepository $cryptoMoneyRepository
     * @param CryptoApiService $api
     */
    public function __construct( ResultRepository $resultRepository,CryptoMoneyRepository $cryptoMoneyRepository,CryptoApiService $api )
    {
        $this->resultRepository = $resultRepository;
        $this->cryptoMoneyRepository = $cryptoMoneyRepository;
        $this->api = $api;
    }

    /**
     * Obtient la valeur de l'investissement à l'instant T
     * Retourne "OK" si cela a fonctionné sinon retourne "fail"
     * @return string
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function saveAmount() : string {
        $result = new Result();
        $amount = $this->api->getCryptosFiltered($this->cryptoMoneyRepository->findAllWithTransactions())["amount"];
        if($amount){
            $result->setCreatedAt(new \DateTimeImmutable())
                ->setAmount($amount);
            $this->resultRepository->save($result, true);
            return "OK";
        }else{
            return "fail";
        }

    }
}