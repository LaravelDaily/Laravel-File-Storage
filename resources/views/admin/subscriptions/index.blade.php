@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">Subscriptions</h3>

    @if(empty(env('PUB_STRIPE_API_KEY')) || empty(env('STRIPE_API_KEY')))
        <div class="alert alert-danger">
            <p>
                Please specify <strong>PUB_STRIPE_API_KEY</strong> and <strong>STRIPE_API_KEY</strong> in your <strong>.env</strong> file!
            </p>
        </div>
    @else
        @forelse($roles->chunk(3) as $chunk)
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="row">
                        @foreach ($chunk as $plan)
                            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
                                <div class="panel @if(in_array($plan->id, $user_roles)) panel-success @endif">
                                    <div class="panel-heading text-center">
                                        <h3 style="text-transform: uppercase;">{{ $plan->title }} plan</h3>
                                        @if(in_array($plan->id, $user_roles))
                                            Your current plan
                                        @endif
                                    </div>
                                    <div class="panel-body text-center">
                                        <p style="font-size:24px"><strong>${{ $plan->price }} / month</strong></p>
                                    </div>
                                    <div class="panel-footer text-center">
                                        @unless(in_array($plan->id, $user_roles))
                                            {{ Form::open(['route' => ['admin.subscriptions.update', $plan->id], 'method' => 'PUT', 'id' => 'role-' . $plan->id]) }}
                                            <script
                                                    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                                                    data-key="{{ env('PUB_STRIPE_API_KEY') }}"
                                                    data-amount="{{ $plan->price * 100 }}"
                                                    data-currency="usd"
                                                    data-name="{{ env('APP_NAME') }}"
                                                    data-label="Subscribe now!"
                                                    data-description="Subscription"
                                                    data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
                                                    data-locale="auto"
                                                    data-zip-code="false"
                                            >
                                            </script>
                                            {{ Form::close() }}
                                            @else
                                                {{ Form::open(['route' => ['admin.subscriptions.destroy', $plan->id], 'method' => 'DELETE', 'id' => 'role-' . $plan->id]) }}
                                                <button class="btn btn-warning">
                                                    <i class="glyphicon glyphicon-remove"></i>
                                                    Unsubscribe
                                                </button>
                                                {{ Form::close() }}
                                                @endunless
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-danger">
                <p>You haven't entered prices for plans, please enter them in <strong>Roles</strong></p>
            </div>
        @endforelse
    @endif
@stop
