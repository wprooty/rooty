<?php

namespace App\Providers;

use Rooty\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // dump(app('path.lang'));
    }

    public function boot()
    {
        // if ($this->app['wp.caps']->currentUserCan('manage_options')) {
        //     abort(
        //         code: 403,
        //         title: __('Not Allowed', 'rooty'),
        //         message: __('You are not allowed to access the Theme Editor.', 'rooty'),
        //         args: [
        //             'link_url'  => admin_url(),
        //             'link_text' => __('Dashboard', 'rooty'),
        //         ]
        //     );
        // }
    }
}
