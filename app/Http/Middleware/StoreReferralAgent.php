<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StoreReferralAgent
{
    /**
     * Handle an incoming request.
     *
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('ref')) {
            $refAgentId = $request->get('ref');

            // Validasi singkat (opsional): Pastikan format AGXXX
            if (preg_match('/^AG\d{3,}$/', $refAgentId)) {
                session(['ref_agent' => $refAgentId]);
            }
        }

        return $next($request);
    }
}
