<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Role;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::whereNotNull('price')->orderBy('price', 'asc')->get();
        $user_roles = Auth::user()->role()->pluck('id')->toArray();

        return view('admin.subscriptions.index', compact(
            'roles',
            'user_roles'
        ));
    }

    public function update($id)
    {
        try {
            $payment = SubscriptionService::savePayment($id);
        } catch (\Stripe\Error\Base $e) {
            return redirect()->back()->withErrors('Stripe API Error: '.$e->getJsonBody()['error']['message']);
        }

        if ($payment) {
            $roleUntil = Carbon::now()->addMonth();
            Auth::user()->update([
                'role_id' => $id,
                'role_until' => $roleUntil
            ]);

        }

        return redirect()->route('admin.subscriptions.index');
    }

    public function destroy($id)
    {
        try {
            $subscription = SubscriptionService::cancelSub();
        } catch (\Stripe\Error\Base $e) {
            return redirect()->back()->withErrors('SubscriptionService: '.$e->getJsonBody()['error']['message']);
        }

        if ($subscription) {
            Auth::user()->update([
                'role_id' => 2,
                'role_until' => null
            ]);

        }

        return redirect()->route('admin.subscriptions.index');
    }
}
