<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class ShareMenuData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = Session::get('user_role');

        // Determine which menu file to load based on the user role
        switch ($role) {
            case 'admin':
                $menuFile = 'verticalMenu.json';
                break;
            case 'superAdmin':
                $menuFile = 'verticalMenuSuperAdmin.json';
                break;
            default:
                $menuFile = 'verticalMenuUser.json';
        }

        $verticalMenuJson = file_get_contents(base_path("resources/menu/{$menuFile}"));
        $verticalMenuData = json_decode($verticalMenuJson);

        // Share the menu data with all views
        View::share('menuData', [$verticalMenuData]);
        return $next($request);
    }
}
