<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);


        try {

            // open config file for writing
            $fp = fopen(base_path() . '/config/permissions.php', 'w');
            fwrite($fp, '<?php return ');

            foreach (Permission::get('name') as $permission) {
                //edit config for current runtime
                config(['permissions.' . trim(strtolower($permission->name)) => trim(strtolower($permission->name))]);
            }

            // write updated runtime config to file
            fwrite($fp, var_export(config('permissions'), true));
            fwrite($fp, ';');
            // close the file
            fclose($fp);

            // clear config cache
//            Artisan::call('cache:clear');




            // open config file for writing
            $fp = fopen(base_path() . '/config/roles.php', 'w');
            fwrite($fp, '<?php return ');

            foreach (Role::get() as $role) {
                //edit config for current runtime
                config(['roles.' . trim(strtolower($role->name)) => trim(strtolower($role->name))]);
            }

            // write updated runtime config to file
            fwrite($fp, var_export(config('roles'), true));
            fwrite($fp, ';');
            // close the file
            fclose($fp);

        } catch (\Exception $e) {
            //ignored
            Log::error($e);
            Log::info('Loading System Config and Error Messages');
        }
    }
}
