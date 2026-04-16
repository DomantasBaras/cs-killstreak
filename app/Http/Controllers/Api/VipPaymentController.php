<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VipService;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Illuminate\Support\Facades\Log;

class VipPaymentController extends Controller
{
    public function createCheckout(Request $request)
    {
        $request->validate([
            'auth_method' => 'required|in:ip,steamid,nick',
            'email'       => 'required|email',
            'steam_id'    => 'required_if:auth_method,steamid|nullable|string|max:50',
            'nickname'    => 'required_if:auth_method,nick|nullable|string|max:64',
            'password'    => 'required_if:auth_method,nick|nullable|string|max:64',
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Build metadata based on auth method
        $metadata = ['auth_method' => $request->auth_method];

        switch ($request->auth_method) {
            case 'ip':
                $metadata['identifier'] = $request->ip();
                break;
            case 'steamid':
                $metadata['identifier'] = $request->steam_id;
                break;
            case 'nick':
                $metadata['identifier'] = $request->nickname;
                $metadata['password']   = $request->password;
                break;
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode'                 => 'subscription',
            'line_items'           => [[
                'price'    => env('STRIPE_VIP_PRICE_ID'),
                'quantity' => 1,
            ]],
            'metadata'           => $metadata,
            'customer_email'     => $request->email,
            'success_url'        => env('APP_URL') . '/vip?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'         => env('APP_URL') . '/vip',
        ]);

        return response()->json(['url' => $session->url]);
    }

    public function webhook(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $payload   = $request->getContent();
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
            $session    = $event->data->object;
            $authMethod = $session->metadata->auth_method ?? 'steamid';
            $identifier = $session->metadata->identifier ?? null;
            $password   = $session->metadata->password ?? null;

            Log::info("VIP activation: method={$authMethod} identifier={$identifier}");

            if ($identifier) {
                app(VipService::class)->activate($identifier, $authMethod, $password);
            } else {
                Log::error('No identifier in metadata - activation skipped');
            }
        }

        if ($event->type === 'customer.subscription.deleted') {
            $subscription = $event->data->object;
            $identifier   = $subscription->metadata->identifier ?? null;
            $authMethod   = $subscription->metadata->auth_method ?? 'steamid';

            if ($identifier) {
                app(VipService::class)->deactivate($identifier, $authMethod);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}