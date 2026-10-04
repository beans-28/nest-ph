<?php

namespace App\Providers;

use App\Models\DormitoryProfile;
use App\Rules\HasUppercase;
use Illuminate\Validation\Rules\Password;
use App\Mail\Transport\SplitTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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
        // "split" mailer: real inbox (Gmail) for listed addresses, Mailtrap for
        // everyone else. Configured in config/mail.php.
        Mail::extend('split', fn (array $config) => new SplitTransport(
            Mail::mailer($config['real'])->getSymfonyTransport(),
            Mail::mailer($config['sandbox'])->getSymfonyTransport(),
            array_values(array_filter(array_map(
                fn ($email) => strtolower(trim($email)),
                explode(',', (string) $config['real_recipients'])
            ))),
        ));

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
                    ];
                } catch (\Throwable $e) {
                    $brand = ['brandDormName' => 'NEST.PH', 'brandLogoUrl' => null];
                }
                // nestph.png is white (for green bars); light surfaces such as
                // cards and the browser tab need the green version instead.
                $brand['brandLogoOnLightUrl'] = $brand['brandLogoUrl'] ?? asset('images/nestphgreen.png');
                $brand['brandFaviconUrl'] = $brand['brandLogoOnLightUrl'];
            }
            $view->with($brand);
        });

        // PDFs can't load web links, so they get the logo embedded as a data
        // URI. Only PDF views pay for reading the file from storage.
        View::composer('pdfs.*', function ($view) {
            static $logo = false;
            if ($logo === false) {
                $logo = null;
                try {
                    $path = DormitoryProfile::current()->brand_logo_path;
                    $disk = Storage::disk('public');
                    if ($path && $disk->exists($path)) {
                        $logo = 'data:'.($disk->mimeType($path) ?: 'image/png').';base64,'.base64_encode($disk->get($path));
                    }
                } catch (\Throwable $e) {
                    $logo = null;
                }
            }
            $view->with('brandLogoFile', $logo);
        });
    }
}
