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
    #[Route('/accueil', name: 'home')]
    public function index(Request $request, CryptoApiService $api, CryptoMoneyRepository $cryptoMoneyRepository,PaginatorInterface $paginator): Response
    {
        $data = $api->getCryptos();
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
    #[Route('/ajouter-une-crypto', name: 'add_view')]
    public function addView(CryptoApiService $api): Response
    {
        $data = $api->getCryptos();
        return $this->render('view/add.html.twig', [
            'controller_name' => 'HomeController',
            'cryptos' => $data,
        ]);
    }
    #[Route('/supprimer-une-crypto', name: 'delete_view')]
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


    #[Route('/add', name: 'add')]
    public function add(Request $request, CryptoApiService $api, CryptoMoneyRepository $cryptoMoneyRepository): Response
    {

        try{
            $currentCryptoSymbol = $request->request->get('crypto');
            $currentCryptoDetails = $api->getCryptosBySymbol($currentCryptoSymbol)->toArray()['data'][$currentCryptoSymbol][0];
            $choices = $request->request;

            $crypto = $cryptoMoneyRepository->findOneBy(['symbol'=> $currentCryptoSymbol]);

            if(!$crypto){
                $currentLogoLink = $api->getLogoBySymbol($currentCryptoSymbol)->toArray()['data'][$currentCryptoSymbol][0]['logo'];
                $crypto = new CryptoMoney();
                $crypto->setTitle($currentCryptoDetails['name'])
                    ->setSymbol($currentCryptoSymbol)
                    ->setLogoLink($currentLogoLink)
                    ->setTotalQuantity(0);
            }
            $transaction = new Transaction();
            $transaction->setQuantity($choices->get('quantity'))
                        ->setUnitPrice($currentCryptoDetails['quote']['EUR']['price'])
                        ->setCreatedAt(new \DateTimeImmutable())
                        ->setType('purchase');

            $crypto->addTransaction($transaction);
            $cryptoMoneyRepository->save($crypto,true);
            $this->addFlash('success', 'La crypto a bien été ajouté');
        } catch ( Exception $exception){
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_add_view');
    }
}
