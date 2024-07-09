<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Log;


class StripeController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $products = $request->input('products');

            if (!is_array($products)) {
                throw new \Exception('Invalid products format');
            }

            $line_items = [];
            $totalAmount = 0;

            foreach ($products as $product) {
                if (!isset($product['name']) || !isset($product['amount']) || !isset($product['quantity'])) {
                    throw new \Exception('Invalid product data');
                }

                // Calcola il prezzo totale per questo prodotto
                $line_item_amount = $product['amount'] * $product['quantity'];
                $totalAmount += $line_item_amount;

                $line_items[] = [
                    'price_data' => [
                        'currency' => $request->input('currency'),
                        'product_data' => [
                            'name' => $product['name'],
                        ],
                        'unit_amount' => $product['amount'] * 100, // Trasformare in centesimi
                    ],
                    'quantity' => $product['quantity'],
                ];
            }

            // Creazione sessione di pagamento con Stripe

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => $request->input('success_url') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $request->input('cancel_url') . '?session_id={CHECKOUT_SESSION_ID}',
            ]);

            // Salvataggio dell'ordine nel database

            $order = new Order();
            $order->user_id = auth()->id();
            $order->session_id = $session->id;
            $order->amount = $totalAmount; // Totale dell'importo dell'ordine
            $order->currency = $request->input('currency');
            $order->status = 'pending'; // Stato iniziale dell'ordine
            $order->payment_method = 'card';
            $order->save();

            foreach ($products as $product) {
                $order->products()->attach($product['id'], ['quantity' => $product['quantity']]);
            }

            // ID della sessione per il redirect a Stripe Checkout
            return response()->json(['id' => $session->id]);
        } catch (\Exception $e) {

            return response()->json(['error' => 'Something went wrong. Please try again.' . $e], 500);
        }
    }

    public function success(Request $request)
    {
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Recupera l'ID della sessione dal parametro di query 'session_id'

            $sessionId = $request->query('session_id');

            $session = Session::retrieve($sessionId);

            if (!$session) {
                return response()->json(['error' => 'Session not found.'], 404);
            }

            $paymentIntentId = $session->payment_intent;
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            if (!$paymentIntent) {
                return response()->json(['error' => 'Payment intent not found.'], 404);
            }

            $paymentMethodId = $paymentIntent->payment_method;
            $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);

            if (!$paymentMethod) {
                return response()->json(['error' => 'Payment method not found.'], 404);
            }

            $cardBrand = $paymentMethod->card->brand;

            // Trova l'ordine corrispondente nel database

            $order = Order::where('session_id', $session->id)
                ->first();
            if (!$order) {
                return response()->json(['error' => 'Order not found.'], 404);
            }
            if ($order->status == 'pending') {
                // Verifica lo stato del pagamento

                $order->status = 'paid';

                $order->card_type = $cardBrand;
                $order->save();

                // Invia una risposta di successo

            }

            return response()->json(['message' => 'Order status: ' . $order->status]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function cancel(Request $request)
    {
        try {

            // Recupera l'ID della sessione dal parametro di query 'session_id'
            $sessionId = $request->query('session_id');

            // Trova l'ordine corrispondente nel database
            $order = Order::where('session_id', $sessionId)
                ->where('status', 'pending')
                ->first();


            if (!$order) {
                return response()->json(['error' => 'Order not found.'], 404);
            }


            if (!$order) {
                throw new \Exception('Order not found');
            }

            // Aggiorna lo stato dell'ordine a 'completed'
            $order->status = 'cancelled';
            $order->save();

            // Invia una risposta di successo
            return response()->json(['message' => 'Order cancelled.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function webhook()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // This is your Stripe CLI webhook secret for testing your endpoint locally.
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload', ['error' => $e->getMessage()]);
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid signature', ['error' => $e->getMessage()]);
            http_response_code(400);
            exit();
        }

        // Handle the event
        try {
            switch ($event->type) {
                case 'checkout.session.async_payment_succeeded':
                case 'checkout.session.completed':
                    $session = $event->data->object;


                    // Trova l'ordine corrispondente nel database
                    $order = Order::where('session_id', $session->id)->first();

                    if ($order && $order->status == 'pending') {

                        // Recupera il Payment Intent dalla sessione
                        $paymentIntentId = $session->payment_intent;
                        $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

                        if ($paymentIntent) {

                            // Recupera il metodo di pagamento dal Payment Intent
                            $paymentMethodId = $paymentIntent->payment_method;
                            $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);

                            if ($paymentMethod) {
                                // Estrai le informazioni sulla carta
                                $cardBrand = $paymentMethod->card->brand;
                                $order->card_type = $cardBrand; // Imposta il tipo di carta utilizzata
                            }
                        }

                        // Aggiorna lo stato dell'ordine a 'paid'
                        $order->status = 'paid';
                        $order->save();
                    } else {
                    }

                    break;

                case 'checkout.session.expired':
                case 'checkout.session.async_payment_failed':
                    $session = $event->data->object;
                    $order = Order::where('session_id', $session->id)->first();

                    if ($order && $order->status == 'pending') {
                        $order->status = 'cancelled';
                        $order->save();
                    }

                    break;

                default:
                    echo 'Received unknown event type ' . $event->type;
            }
        } catch (\Exception $e) {
            http_response_code(500);
            exit();
        }

        http_response_code(200);
    }
}
