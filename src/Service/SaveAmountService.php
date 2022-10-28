<?php

namespace App\Service;



use App\Entity\Result;
use App\Repository\CryptoMoneyRepository;
use App\Repository\ResultRepository;
use Doctrine\Persistence\ManagerRegistry;

class SaveAmountService
{
    private ResultRepository $resultRepository;
    private CryptoMoneyRepository $cryptoMoneyRepository;
    private CryptoApiService $api;

    public function __construct( ResultRepository $resultRepository,CryptoMoneyRepository $cryptoMoneyRepository,CryptoApiService $api )
    {
        $this->resultRepository = $resultRepository;
        $this->cryptoMoneyRepository = $cryptoMoneyRepository;
        $this->api = $api;
    }
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