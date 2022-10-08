<?php

namespace App\Providers;

use ChargeBee\ChargeBee\Environment;
use Illuminate\Support\ServiceProvider;

class ChargeBeeServiceProvider extends ServiceProvider {

    public function boot()
    {
        Environment::configure(config('chargebee.site'), config('chargebee.key'));
    }

}
