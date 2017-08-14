<?php

namespace P3in\Middleware;

use Closure;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddDebug
{
    private $logged = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (env('APP_DEBUG')) {
            $this->enableLogging();

            // get response
            $response = $next($request);

            return $this->setOutput($response);
        } else {
            return $next($request);
        }
    }

    public function terminate()
    {
    }

    public function enableLogging()
    {
        DB::enableQueryLog();

        \Event::listen('illuminate.log', function ($level, $message, $context) {
            $this->setLogged('logged', [
                'level'   => $level,
                'message' => $message,
                'context' => $context,
            ], true);
        });
    }

    public function setOutput($response, Closure $callback = null)
    {
        if ($response instanceof JsonResponse) {
            $content = $response->getData(true);
            $rtn_method = 'setData';
        } else {
            $content = $response->getOriginalContent();
            $rtn_method = 'setContent';
        }

        $this->setLogged('queries', DB::getQueryLog());

        if ($callback) {
            $callback();
        }
        if (is_array($content)) {
            $content['debug'] = $this->getLogged();
        }

        return $response->$rtn_method($content);
    }

    public function setLogged($key, $val, $append = false)
    {
        if ($append) {
            $this->logged[$key][] = $val;
        } else {
            $this->logged[$key] = $val;
        }
    }

    public function getLogged()
    {
        return $this->logged;
    }
}
