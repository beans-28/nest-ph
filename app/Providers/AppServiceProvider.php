<?php

namespace App\Providers;

use App\Models\DormitoryProfile;
use App\Rules\HasUppercase;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Password policy for every password a person chooses (reset, change,
        // register, owner setup): 8+ characters with at least one number, one
        // symbol and one uppercase letter. Anything that calls
        // Password::defaults() picks this up automatically.
        Password::defaults(fn () => Password::min(8)->numbers()->symbols()->rules([new HasUppercase]));

        // Dual branding (Data Privacy Act: the dorm is the PIC, NEST.PH is
        // the PIP). Every view gets the dorm's name and logo so pages can
        // show the dorm as the main identity with "Powered by NEST.PH" beside it.
        // Loaded once per request, and never breaks a page if the DB is down.
        View::composer('*', function ($view) {
            static $brand = null;
            if ($brand === null) {
                try {
                    $profile = DormitoryProfile::current();
                    $brand = [
                        'brandDormName' => $profile->dorm_name ?: 'NEST.PH',
                        'brandLogoUrl' => $profile->brandLogoUrl(),
                        // PDFs can't load web links, so they get the file on disk.
                        'brandLogoFile' => $profile->brand_logo_path && is_file(storage_path('app/public/'.$profile->brand_logo_path))
                            ? storage_path('app/public/'.$profile->brand_logo_path)
                            : null,
                    ];
                } catch (\Throwable $e) {
                    $brand = ['brandDormName' => 'NEST.PH', 'brandLogoUrl' => null, 'brandLogoFile' => null];
                }
                // nestph.png is white (for green bars); light surfaces such as
                // cards and the browser tab need the green version instead.
                $brand['brandLogoOnLightUrl'] = $brand['brandLogoUrl'] ?? asset('images/nestphgreen.png');
                $brand['brandFaviconUrl'] = $brand['brandLogoOnLightUrl'];
            }
            $view->with($brand);
        });
    }
}
