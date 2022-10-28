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
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/', name: 'app_')]
class CryptoController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, CryptoApiService $api, CryptoMoneyRepository $cryptoMoneyRepository, SaveAmountService $saveAmount): Response
    {
        $cryptoMoneys =  $api->getCryptosFiltered( $cryptoMoneyRepository->findAllWithTransactions() );
        $saveAmount->saveAmount();
        return $this->render('view/home.html.twig', [
            'controller_name' => 'CryptoController',
            'cryptos' =>  $cryptoMoneys['cryptos'] ,
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


    #[Route('/supprimer-une-crypto', name: 'delete')]
    public function deleteView(Request $request, CryptoApiService $api, CryptoMoneyRepository $cryptoMoneyRepository): Response
    {

        $cryptoMoneys  =  $api->getCryptosFiltered( $cryptoMoneyRepository->findAllWithTransactions() );
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
                return $this->redirectToRoute('app_delete',['cryptos' => $cryptoMoneys['cryptos']]);
            } catch ( Exception $e){
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('app_delete',['cryptos' => $cryptoMoneys['cryptos']]);
            }
        }

        return $this->render('view/delete.html.twig', [
            'controller_name' => 'CryptoController',
            'cryptos' => $cryptoMoneys['cryptos'],
        ]);
    }


    #[Route('/progression', name: 'graph')]
    public function graph(ResultRepository$resultRepository, ChartBuilderInterface $chartBuilder): Response
    {
        $dailyResults = $resultRepository->findAll();
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

        ]);
    }

}
