<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VipService;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;

class VipPaymentController extends Controller
{
    public function createCheckout(Request $request)
    {
        $request->validate([
            'steam_id' => 'required|string|max:50',
            'email'    => 'required|email',
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode'                 => 'subscription',
            'line_items'           => [[
                'price'    => env('STRIPE_VIP_PRICE_ID'),
                'quantity' => 1,
            ]],
            'metadata' => [
                'steam_id' => $request->steam_id,
            ],
            'customer_email'   => $request->email,
            'success_url'      => env('APP_URL') . '/vip/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'       => env('APP_URL') . '/vip',
        ]);

        return response()->json(['url' => $session->url]);
    }

    public function webhook(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                env('STRIPE_WEBHOOK_SECRET')
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            
            \Log::info('RAW webhook metadata: ' . json_encode($session->metadata));
            \Log::info('RAW webhook customer email: ' . $session->customer_email);
            
            $fullSession = \Stripe\Checkout\Session::retrieve([
                'id' => $session->id,
                'expand' => ['metadata'],
            ]);
            
            $steamId = $fullSession->metadata->steam_id ?? null;
            
            \Log::info('VIP activation SteamID: ' . ($steamId ?? 'NULL'));
            
            if ($steamId) {
                app(VipService::class)->activate($steamId);
            } else {
                \Log::error('No steam_id in metadata - activation skipped');
            }
        }

        if ($event->type === 'customer.subscription.deleted') {
            $subscription = $event->data->object;
            $steamId = $subscription->metadata->steam_id ?? null;

            if ($steamId) {
                app(VipService::class)->deactivate($steamId);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}