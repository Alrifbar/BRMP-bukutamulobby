<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah admin sudah login dan admin ID valid
        if (!session('admin_logged_in') || !session('admin_id')) {
            return redirect()->route('admin.login');
        }
        
        // Validasi bahwa admin ID masih ada di database
        $admin = \App\Models\Admin::find(session('admin_id'));
        if (!$admin) {
            // Hapus session yang tidak valid
            session()->forget(['admin_logged_in', 'admin_login_time', 'admin_id']);
            return redirect()->route('admin.login');
        }
        
        return $next($request);
    }
}
