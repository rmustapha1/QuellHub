<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use App\Models\ApiAuth;

class ApiAuthentication extends Middleware
{
    /**
     * authenticate request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle($request, Closure $next, ...$guards){
        $user_id = $request->header('User-Id');
        $token = $request->header('Token');

        $valid = ApiAuth::where('user_id', $user_id)->get()->count();

        if($valid !== 1){
            return json_encode(['success' => 'false', 'status' => 'Error', 'message' => 'Authentication Error']);
           // throw new Exception("Authentication Error");
        } else {
            return $next($request);
        }

    }

}
