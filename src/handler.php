<?php

use Endroid\QrCode\QrCode;
use Stripe\Error\ApiConnection;
use Stripe\Error\Authentication;
use Stripe\Error\Base;
use Stripe\Error\Card;
use Stripe\Error\InvalidRequest;
use Stripe\Error\RateLimit;
use Stripe\Source;

require_once "bootstrap.php";

$action = input("action");

switch($action) {
    default:
        error("Action is required", "Parameter :action is required");

    case "qr":

        $qrCode = new QrCode(input("address", null));

        header(sprintf('Content-Type: %s', $qrCode->getContentType()));
        echo $qrCode->writeString();
        exit;

    case "init_payment":

        try {
            $amount = input("amount", 5);
            
            $source = Source::create([
                "type" => "bitcoin",
                "amount" => intval($amount),
                "currency" => "usd",
                "owner" => array(
                    "email" => "jenny.rosen@example.com"
                )
            ]);

            /** @var \Stripe\ApiResponse $response */
            $response = $source->getLastResponse();
            
            success([
                "address" => $response->json["bitcoin"]["address"],
                "qrcode" => sprintf("handler.php?action=qr&address=%s", $response->json["bitcoin"]["uri"]),
                "amount" => [
                    "usd" => $amount,
                    "satoshi" => ($response->json["bitcoin"]["amount"] / 100000000)
                ]
            ]);
        } catch(Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err  = $body['error'];

            print('Status is:' . $e->getHttpStatus() . "\n");
            print('Type is:' . $err['type'] . "\n");
            print('Code is:' . $err['code'] . "\n");
            // param is '' in this case
            print('Param is:' . $err['param'] . "\n");
            print('Message is:' . $err['message'] . "\n");
        } catch (RateLimit $e) {
            // Too many requests made to the API too quickly
        } catch (InvalidRequest $e) {
            // Invalid parameters were supplied to Stripe's API
        } catch (Authentication $e) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
        } catch (ApiConnection $e) {
            // Network communication with Stripe failed
        } catch (Base $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
        } catch (Exception $e) {
            // Something else happened, completely unrelated to Stripe
        }

        break;
}