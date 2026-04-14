<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlayerStat;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    // GET /api/stats — paginated leaderboard
    public function index(Request $request)
    {
        $stats = PlayerStat::orderByRaw('CASE WHEN deaths = 0 THEN kills ELSE CAST(kills AS FLOAT) / deaths END DESC')
            ->orderBy('kills', 'desc')
            ->paginate(25);

        return response()->json($stats);
    }

    // POST /api/stats — receive batch from AMXX plugin
    public function store(Request $request)
    {
        $secret = config('app.stats_secret', 'cskillstreak_secret');
        if ($request->header('X-Stats-Secret') !== $secret) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $players = $request->input('players', []);

        foreach ($players as $player) {
            if (empty($player['ip'])) continue;

            PlayerStat::updateOrCreate(
                ['ip' => $player['ip']],
                [
                    'name'       => $player['name'] ?? 'Unknown',
                    'kills'      => \DB::raw('kills + ' . (int)($player['kills'] ?? 0)),
                    'deaths'     => \DB::raw('deaths + ' . (int)($player['deaths'] ?? 0)),
                    'headshots'  => \DB::raw('headshots + ' . (int)($player['headshots'] ?? 0)),
                    'playtime'   => \DB::raw('playtime + ' . (int)($player['playtime'] ?? 0)),
                    'last_seen'  => now(),
                ]
            );
        }

        return response()->json(['ok' => true]);
    }
}