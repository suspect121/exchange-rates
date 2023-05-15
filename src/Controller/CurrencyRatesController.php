<?php

namespace App\Controller;

use App\ExchangeRate\Exception\NoDataException;
use App\ExchangeRate\Exception\TodayNoDataException;
use App\ExchangeRate\ExchangeRate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use DateTimeImmutable;
use Exception;

class CurrencyRatesController extends AbstractController
{
    #[Route('/{date}', name: 'app_currency_rates', methods: ['GET'], requirements: ['date' => Requirement::DATE_YMD])]
    public function index(ExchangeRate $exchange_rate, string $date = null): Response
    {
        $currency_rates = [];
        $error = null;

        try {
            if($date !== null) {
                $date = new DateTimeImmutable($date);
            }
            $currency_rates = $exchange_rate->getExchangeRates($date);
        } catch (TodayNoDataException $exception) {
            $error = 'TODAY_NO_DATA';
        } catch (NoDataException $exception) {
            $error = 'NO_DATA';
        } catch (Exception $exception) {
            $error = 'UNKNOWN_ERROR';
        }

        return $this->render('currency_rates.html.twig', [
            'currency_rates' => $currency_rates,
            'selected_date' => $date,
            'error' => $error
        ]);
    }
}
