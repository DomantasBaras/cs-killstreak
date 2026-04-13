<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use xPaw\SourceQuery\SourceQuery;

class ServerStatusController extends Controller
{
    public function index()
    {
        $ip   = env('CS_SERVER_IP', '38.210.227.192');
        $port = env('CS_SERVER_PORT', 27015);

        $query = new SourceQuery();

        try {
            $query->Connect($ip, $port, 2, SourceQuery::GOLDSOURCE);
            $info = $query->GetInfo();

            return response()->json([
                'online'     => true,
                'players'    => $info['Players'],
                'maxPlayers' => $info['MaxPlayers'],
                'map'        => $info['Map'],
                'name'       => $info['HostName'],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'online'     => false,
                'players'    => 0,
                'maxPlayers' => 0,
                'map'        => null,
                'name'       => null,
            ]);
        } finally {
            $query->Disconnect();
        }
    }
}