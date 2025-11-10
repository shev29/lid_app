<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Gd\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Attach a view composer to all views
        View::composer('*', function ($view) {
            $view->with('layout', 'layouts.app');
        });

        // Add avatar URL to all views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $employees = DB::table('vw_master_employee_active')
                                ->select('employee_name', 'avatar')
                                ->where('employee_id', $user->employee_id)
                                ->first();

                $avatar = $employees->avatar ?? $this->createAvatar(['employeeName' => $employees->employee_name, 'employeeId' => $user->employee_id]);
                $avatarUrl = asset('storage/avatars/' . $avatar);
                $view->with('avatarUrl', $avatarUrl);
            }
        });
    }

    private function createAvatar($params) {
        $employeeName = $params['employeeName'];
        $employeeId = $params['employeeId'];
        $manager = $image = new ImageManager(new Driver());

        $width = 60;
        $height = 60;
        $backgroundColor = '#115fad';
        $textColor = '#ffffff';
        $words = explode(' ', trim($employeeName));
        $initials = strtoupper(Str::substr($words[0], 0, 1));
        if (count($words) > 1) {
            $initials .= strtoupper(Str::substr($words[1], 0, 1));
        }

        $image = $manager->create($width, $height);
        $image->fill($backgroundColor);

        $fontSize = 24;
        $fontPath = public_path('css/app/fonts/Inter/Inter-Bold.ttf');

        $image->text($initials, $width / 2, $height / 2, function ($font) use ($fontPath, $fontSize, $textColor) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->color($textColor);
            $font->align('center');
            $font->valign('middle');
        });

        $filename = $employeeId.'_'.uniqid().'.webp';
        $path = "public/avatars/{$filename}";
        Storage::put($path, (string) $image->encode(new WebpEncoder(quality: 100)));

        DB::table('master_employees_avatar')->insert(
            ['employee_id' => $employeeId, 'avatar' => $filename, 'is_active' => '1']
        );

        return $filename;
    }
}
