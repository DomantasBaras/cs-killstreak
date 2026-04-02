<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class VipService
{
    protected string $usersIni;

    public function __construct()
    {
        $this->usersIni = env('CS_USERS_INI_PATH');
    }

    public function activate(string $steamId): void
    {
        Log::info('VIP activate called with SteamID: ' . $steamId);
        
        $this->removeFromFile($steamId);
        $line = "\"{$steamId}\" \"\" \"t\" \"ce\"\n";
        file_put_contents($this->usersIni, $line, FILE_APPEND);

        Log::info('VIP written to users.ini: ' . $line);

        $this->reloadAdmins();
    }

    public function deactivate(string $steamId): void
    {
        $this->removeFromFile($steamId);
        $this->reloadAdmins();
    }

    protected function removeFromFile(string $steamId): void
    {
        if (!file_exists($this->usersIni)) return;

        $lines = file($this->usersIni, FILE_IGNORE_NEW_LINES);
        $filtered = array_filter($lines, fn($line) =>
            !str_contains($line, $steamId)
        );

        file_put_contents($this->usersIni, implode("\n", $filtered) . "\n");
    }

    protected function reloadAdmins(): void
    {
        // When on VPS, this will RCON the server to reload admins
        // For now logs the action
        Log::info("VIP admins reload triggered");

        // Uncomment when on VPS with RCON configured:
        // $rcon = new \Rcon(env('CS_SERVER_IP'), env('CS_SERVER_PORT'), env('CS_SERVER_RCON'), 3);
        // $rcon->connect();
        // $rcon->sendCommand('amxx_reloadadmins');
    }
}