<?php

namespace App\Controller;

use App\Entity\CryptoMoney;
use App\Entity\Transaction;
use App\Repository\CryptoMoneyRepository;
use App\Service\CryptoApiService;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/', name: 'app_')]
class CryptoController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, CryptoApiService $api, CryptoMoneyRepository $cryptoMoneyRepository,PaginatorInterface $paginator): Response
    {
        $cryptoMoneys =  $api->getCryptosFiltered( $cryptoMoneyRepository->findAllWithTransactions() );
        /*$cryptoMoneys = $paginator->paginate(
            $cryptoMoneyQuery,
            $request->query->getInt('page', 1),
            10 /*limit per page*//*
            ['options' => ['button'=> 'Filtrer']]
        );
        $cryptoMoneys->setCustomParameters([
            'align' => 'center',
            'rounded' => true,
        ]);*/
        return $this->render('view/home.html.twig', [
            'controller_name' => 'HomeController',
            'cryptos' =>  $cryptoMoneys['cryptos'],
            'amount' => $cryptoMoneys['amount']
        ]);
    }
    #[Route('/ajouter-une-crypto', name: 'add')]
    public function addView(Request $request, CryptoApiService $api, CryptoMoneyRepository $cryptoMoneyRepository): Response
    {
        $data = $api->getCryptos();
        if($request->request->get('crypto')){

            try{
                $choices = $request->request;
                $quantity = $choices->get('quantity');
                $symbol = $choices->get('symbol');
                $currentCryptoDetails = $api->getCryptosBySymbol($symbol);
                $crypto = $cryptoMoneyRepository->findOneBy(['symbol'=> $symbol]);

                if(!$crypto){
                    $currentLogoLink = $api->getLogoBySymbol($symbol)->toArray()['data'][$symbol][0]['logo'];
                    $crypto = new CryptoMoney();
                    $crypto->setTitle($currentCryptoDetails['name'])
                        ->setSymbol($symbol)
                        ->setLogoLink($currentLogoLink);
                }
                $transaction = new Transaction();
                $transaction->setQuantity( $quantity )
                    ->setUnitPrice($currentCryptoDetails['quote']['EUR']['price'])
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setType('purchase');

                $crypto->addTransaction($transaction);
                $cryptoMoneyRepository->save($crypto,true);
                $this->addFlash('success', 'La crypto a bien été ajouté');
            } catch ( Exception $exception){
                $this->addFlash('error', $exception->getMessage());
            }
        }


        return $this->render('view/add.html.twig', [
            'controller_name' => 'HomeController',
            'cryptos' => $data,
        ]);
    }


    #[Route('/supprimer-une-crypto', name: 'delete')]
    public function deleteView(): Response
    {
        return $this->render('view/delete.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/progression', name: 'graph')]
    public function graph(): Response
    {
        return $this->render('view/graph.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

}
