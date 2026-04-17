<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    // GET /admin/settings
    public function index()
    {
        $groups = AppSetting::orderBy('group')->orderBy('id')->get()->groupBy('group');

        $systemSettings = [
            'app_name'       => config('app.name'),
            'app_url'        => config('app.url'),
            'timezone'       => config('app.timezone'),
            'environment'    => app()->environment(),
            'debug'          => config('app.debug'),
            'php_version'    => PHP_VERSION,
            'laravel_version'=> app()->version(),
            'cache_driver'   => config('cache.default'),
            'queue_driver'   => config('queue.default'),
        ];

        return view('admin.settings.index', compact('groups', 'systemSettings'));
    }

    // POST /admin/settings
    public function update(Request $request)
    {
        $settings = AppSetting::all();

        foreach ($settings as $setting) {
            if ($request->has($setting->key)) {
                $value = $setting->type === 'boolean'
                    ? ($request->boolean($setting->key) ? '1' : '0')
                    : $request->input($setting->key);

                $setting->update(['value' => $value]);
            } elseif ($setting->type === 'boolean') {
                // Unchecked checkboxes are not submitted — treat as false
                $setting->update(['value' => '0']);
            }
        }

        Cache::forget('app_settings_all');

        return back()->with('success', 'Settings saved successfully.');
    }

    // POST /admin/settings/clear-cache
    public function clearCache()
    {
        Cache::flush();
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        return back()->with('success', 'All caches cleared successfully.');
    }

    // GET /admin/settings/system-info
    public function systemInfo()
    {
        $info = [
            'php_version'      => PHP_VERSION,
            'laravel_version'  => app()->version(),
            'server_software'  => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_driver'  => config('database.default'),
            'cache_driver'     => config('cache.default'),
            'session_driver'   => config('session.driver'),
            'queue_driver'     => config('queue.default'),
            'mail_driver'      => config('mail.default'),
        ];

        return view('admin.settings.system-info', compact('info'));
    }
}