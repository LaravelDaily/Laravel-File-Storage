<?php

namespace App\Services;

use App\Payment;
use App\Role;
use Auth;
use Illuminate\Http\Request;
use Stripe\Customer;
use Stripe\Invoice;
use Stripe\Plan;
use Stripe\Stripe;
use Stripe\Subscription;

class SubscriptionService
{
    public static function savePayment($id)
    {
        Stripe::setApiKey(env('STRIPE_API_KEY'));
        $user = Auth::user();
        $role = Role::findOrFail($id);

        if ($user->stripe_customer_id) {
            $cu = Customer::retrieve($user->stripe_customer_id);

            if ($cu->subscriptions->total_count) {
                $subscription = $cu->subscriptions->data[0];

                $itemID = $subscription->items->data[0]->id;
                Subscription::update($subscription->id, [
                    "prorate" => true,
                    "items" => [
                        [
                            "id" => $itemID,
                            "plan" => $role->stripe_plan_id
                        ]
                    ]
                ]);

                $cu->email = request('stripeEmail');
                $cu->save();

                $invoice = Invoice::create([
                    "customer" => $cu->id
                ]);
                $invoice->pay();

                $payment = Payment::create([
                    'user_id'                => $user->id,
                    'role_id'                => $role->id,
                    'payment_amount'         => $invoice->total/100,
                ]);
            } else {
                self::createSub($cu, $role);

                $payment = Payment::create([
                    'user_id'                => $user->id,
                    'role_id'                => $role->id,
                    'payment_amount'         => $role->price,
                ]);
            }
        } else {
            $cu = Customer::create([
                "email" => request('stripeEmail'),
                "metadata" => [
                    "user_id" => $user->id
                ]
            ]);

            $subscription = self::createSub($cu, $role);

            $user->update([
                'stripe_customer_id' => $cu->id
            ]);

            $payment = Payment::create([
                'user_id'                => $user->id,
                'role_id'                => $role->id,
                'payment_amount'         => $role->price,
            ]);
        }

        if (! $payment) {
            return false;
        }

        return true;
    }

    public static function cancelSub()
    {
        Stripe::setApiKey(env('STRIPE_API_KEY'));

        $cu = Customer::retrieve(\Illuminate\Support\Facades\Auth::user()->stripe_customer_id);
        $subscription = $cu->subscriptions->data[0];
        $subscription->cancel();

        if (! $subscription) {
            return false;
        }

        return true;
    }

    private static function createSub(Customer $cu, Role $role)
    {
        $subscription = Subscription::create([
            "customer" => $cu->id,
            "items" => [
                [
                    "plan" => $role->stripe_plan_id,
                ],
            ],
            "source" => request('stripeToken')
        ]);

        return $subscription;
    }
}
