<?php

namespace App\Http\Middleware;

use App\Models\Log;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DataLogger
{
    private $start_time;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->start_time = microtime(true);
        return $next($request);
    }

    public function terminate(Request $request, $response): void
    {
        if (env('API_DATALOGGER', true)) {
            $attributes = [
                'time' => gmdate('Y-m-d H:i:s'),
                'duration' => number_format(microtime(true) - LARAVEL_START, 3),
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'input' => $request->getContent(),
            ];
            if (env('API_DATALOGGER_USE_DB', true)) {
                $log = new Log($attributes);
                $log->save();
            } else {
                $filename = 'api_datalogger_' . date('Y-m-d') . '.log';
                $data = [];
                foreach ($attributes as $key => $value) {
                    $data[] = "$key: $value";
                }
                $dataToLog = implode("\n", $data) . "\n" . str_repeat("-", 20) . "\n\n";
                Storage::append('logs' . DIRECTORY_SEPARATOR . $filename, $dataToLog);
            }
        }
    }
}
