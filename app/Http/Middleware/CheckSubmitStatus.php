<?php

namespace App\Http\Middleware;

use App\Problem;
use Closure;

class CheckSubmitStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $problem_id = $request->get('problem_id');
        $problem = Problem::findOrFail($problem_id);
        $lesson = $problem->lesson;

        if($lesson->open_submit == 'false'){
            return response()->json(['msg' => 'submit timeout']);
        }

        return $next($request);
    }
}
