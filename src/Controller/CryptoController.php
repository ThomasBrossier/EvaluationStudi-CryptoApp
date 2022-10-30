<?php

namespace App\Controller;

use App\Entity\CryptoMoney;
use App\Entity\Result;
use App\Entity\Transaction;
use App\Repository\CryptoMoneyRepository;
use App\Repository\ResultRepository;
use App\Service\CryptoApiService;
use App\Service\SaveAmountService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;


/**
 * Cette classe représente le controller principal de l'application. c'est lui qui définit les différentes routes.
 *
 **/


#[Route('/', name: 'app_')]
class CryptoController extends AbstractController
{

    /**
     * La route principale de l'application. Si l'utilisateur n'est pas connecté, il sera redirigé sur la route de connexion.("/login")
     * Si des cryptos existent dans la base de données, elles sont affichées sinon on renvoie un tableau vide
     **/
    #[Route('/', name: 'home')]
    public function index(Request $request, CryptoApiService $api, CryptoMoneyRepository $cryptoMoneyRepository, SaveAmountService $saveAmount): Response
    {
        $cryptosOwned = $cryptoMoneyRepository->findAllWithTransactions();
        foreach ($cryptosOwned as $key => $cryptoOwned ) {
            if($cryptoOwned->getTotalQuantity() === 0.0 ){
                unset($cryptosOwned[$key]);
            }
        }
        if($cryptosOwned){
            $cryptoMoneys =  $api->getCryptosFiltered( $cryptosOwned );
            $amount = $cryptoMoneys['amount'];
            $cryptoMoneys = $cryptoMoneys['cryptos'];
        }else{
            $cryptoMoneys = [] ;
            $amount = "" ;
        }

        return $this->render('view/home.html.twig', [
            'controller_name' => 'CryptoController',
            'cryptos' =>  $cryptoMoneys,
            'amount' => $amount
        ]);
    }


    /**
     * La route qui permet d'acceder à la page d'ajout de l'application. Si l'utilisateur n'est pas connecté, il sera redirigé sur la route de connexion.("/login")
     * Nous faisons un appel à une API pour récupérer les crypto et leurs valeurs actuelles. Ces cryptos seront envoyé à la vue.
     * Si nous détectons une validation d'ajout dans la requête, nous créons la transaction d'achat demandée par l'utilisateur. Dans le cas ou la crypto n'est pas en base de données,
     * nous là créons également.
     * Un message de succés ou d'echec est envoyé via "addFlash".
     **/
    #[Route('/ajouter-une-crypto', name: 'add')]
    public function addView(Request $request, CryptoApiService $api, CryptoMoneyRepository $cryptoMoneyRepository): Response
    {
        $data = $api->getCryptos();
        if($request->request->get('crypto')){

            try{
                $choices = $request->request;
                $quantity = $choices->get('quantity');
                $symbol = $choices->get('crypto');
                $currentCryptoDetails = $api->getCryptosBySymbol($symbol);
                $crypto = $cryptoMoneyRepository->findOneBy(['symbol'=> $symbol]);
                if(!is_numeric($quantity)){
                    throw new \InvalidArgumentException( "La quantité doit être un chiffre");
                }
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
            'controller_name' => 'CryptoController',
            'cryptos' => $data,
        ]);
    }

    /**
     * La route qui permet d'acceder à la page de suppression. Si l'utilisateur n'est pas connecté, il sera redirigé sur la route de connexion.("/login")
     * Si nous avons des cryptos, alors elles sont transmises a la vue avec différentes valeurs.(quantité, valeur d'achat ...)
     * Si nous détectons une suppression d'ajout dans la requête, nous créons la transaction de vente demandée par l'utilisateur.
     **/
    #[Route('/supprimer-une-crypto', name: 'delete')]
    public function deleteView(Request $request, CryptoApiService $api, CryptoMoneyRepository $cryptoMoneyRepository): Response
    {

        $cryptosOwned = $cryptoMoneyRepository->findAllWithTransactions();
        if($cryptosOwned){
            $cryptoMoneys =  $api->getCryptosFiltered( $cryptosOwned )['cryptos'];
            if($request->request->get('crypto')){
                try{
                    $choices = $request->request;
                    $quantity = $choices->get('quantity');
                    $unitPrice = $choices->get('unitPrice');
                    if(!is_numeric($quantity)){
                        throw new \InvalidArgumentException( "La quantité doit être un chiffre");
                    }
                    if( !is_numeric($unitPrice) || !is_numeric($unitPrice) ){
                        throw new \InvalidArgumentException("La valeur doit être un chiffre");
                    }
                    $symbol = $choices->get('crypto');
                    $crypto = $cryptoMoneyRepository->findOneBy(['symbol'=> $symbol ]);
                    $crypto->getTotalQuantity();
                    $transaction = new Transaction();
                    $transaction->setQuantity( $quantity )
                        ->setUnitPrice($unitPrice)
                        ->setCreatedAt(new \DateTimeImmutable())
                        ->setType('sale');
                    $crypto->addTransaction($transaction);
                    $cryptoMoneyRepository->save($crypto,true);
                    $this->addFlash('success', 'La vente a bien été effectuée');
                    return $this->redirectToRoute('app_delete',['cryptos' => $cryptoMoneys]);
                } catch ( Exception $e){
                    $this->addFlash('error', $e->getMessage());
                    return $this->redirectToRoute('app_delete',['cryptos' => $cryptoMoneys]);
                }
            }
        }else{
            $cryptoMoneys = [];
        }


        return $this->render('view/delete.html.twig', [
            'controller_name' => 'CryptoController',
            'cryptos' => $cryptoMoneys,
        ]);
    }



    /**
     * La route qui permet d'accéder à la page de representation graphique. Si l'utilisateur n'est pas connecté, il sera redirigé sur la route de connexion.("/login")
     * Si nous avons des cryptos, alors un graph est crée puis transmit à la vue. Sinon nous indiquons à la vue de ne rien afficher.
     *
     **/
    #[Route('/progression', name: 'graph')]
    public function graph(ResultRepository$resultRepository, ChartBuilderInterface $chartBuilder): Response
    {
        $dailyResults = $resultRepository->findAll();
        if(!$dailyResults){
            $dontShow = true;
        }else{
            $dontShow = false;
        }
        $labels = [];
        $data = [];
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        /*dd($dailyResults);*/
        foreach ($dailyResults as $dailyResult) {
            $labels[] = $dailyResult->getCreatedAt()->format('d/m');
            $data[] = $dailyResult->getAmount();
        }
        $chart->setData([
            'labels' => $labels ,
            'datasets' => [
                [
                    'label' => 'Résultat des investissements par jour',
                    'backgroundColor' => 'rgb(239, 239, 239)',
                    'borderColor' => 'rgb(31, 195, 108)',
                    'data' => $data,
                ],
            ],
        ]);

        $chart->setOptions([/* ... */]);

        return $this->render('view/graph.html.twig', [
            'controller_name' => 'CryptoController',
            'chart' => $chart,
            'dontShow' => $dontShow
        ]);
    }

}
