<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class PelangganAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('pelanggan_id')) return redirect('/pelanggan/login');
        return $next($request);
    }
}
