<?php
declare(strict_types=1);

namespace App\Http\Controllers\Twitter;

use App\Http\Controllers\Controller;
use App\Twitter\Importer\Importer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;

class ReachController extends Controller
{
    /**
     * Listen to GET request submitted as XMLHttpRequest.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function compute(Request $request): JsonResponse
    {
        // Not an XMLHttpRequest GET request
        if (! $request->ajax() || ! $request->isMethod('GET')) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        try {
            $result = Importer::computeReach($request->input('tweet'));

            // Return metric
            return response()->json($result->toArray());
        } catch (\Exception $e) {
            report($e);

            return response()->json(['error' => $e->getMessage()], 500);
        } finally {
            Log::debug('XMLHttpRequest data', $request->all());
        }
    }
}
