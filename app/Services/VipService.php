<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class VipService
{
    protected string $vipsIni;
    protected string $serverIp;
    protected int    $serverPort;
    protected string $rconPassword;

    public function __construct()
    {
        $this->vipsIni      = env('CS_VIPS_INI_PATH', '/root/rehlds/cstrike/addons/amxmodx/configs/vips.ini');
        $this->serverIp     = env('CS_SERVER_IP', '38.210.227.192');
        $this->serverPort   = (int) env('CS_SERVER_PORT', 27015);
        $this->rconPassword = env('CS_SERVER_RCON', '');
    }

    public function activate(string $identifier, string $authMethod = 'steamid', ?string $password = null): void
    {
        Log::info("VIP activate: method={$authMethod} identifier={$identifier}");

        $this->removeFromFile($identifier);

        $expiry = now()->addMonth()->format('d.m.y');

        // Build vips.ini line based on auth method
        // Format: "identifier" "password" "flags" "auth_flag" "expiry"
        // Auth flags: a=steam, b=ip, c=name+password
        switch ($authMethod) {
            case 'ip':
                $line = "\"{$identifier}\" \"\" \"abcdefghijklmnopqrstuvwxyz\" \"b\" \"{$expiry}\"\n";
                break;
            case 'nick':
                $pwd  = $password ?? '';
                $line = "\"{$identifier}\" \"{$pwd}\" \"abcdefghijklmnopqrstuvwxyz\" \"c\" \"{$expiry}\"\n";
                break;
            case 'steamid':
            default:
                $line = "\"{$identifier}\" \"\" \"abcdefghijklmnopqrstuvwxyz\" \"a\" \"{$expiry}\"\n";
                break;
        }

        file_put_contents($this->vipsIni, $line, FILE_APPEND);
        Log::info('VIP written to vips.ini: ' . $line);

        $this->reloadVips();
    }

    public function deactivate(string $identifier, string $authMethod = 'steamid'): void
    {
        Log::info("VIP deactivate: method={$authMethod} identifier={$identifier}");
        $this->removeFromFile($identifier);
        $this->reloadVips();
    }

    protected function removeFromFile(string $identifier): void
    {
        if (!file_exists($this->vipsIni)) return;

        $lines    = file($this->vipsIni, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $filtered = array_filter($lines, fn($line) => !str_contains($line, $identifier));
        file_put_contents($this->vipsIni, implode("\n", $filtered) . "\n");
    }

    protected function reloadVips(): void
    {
        if (empty($this->rconPassword)) {
            Log::warning('RCON password not set - skipping amxx_reloadadmins');
            return;
        }

        try {
            $socket = fsockopen($this->serverIp, $this->serverPort, $errno, $errstr, 3);
            if (!$socket) {
                Log::error("RCON connect failed: {$errstr}");
                return;
            }

            $this->rconSend($socket, 3, $this->rconPassword);
            $this->rconSend($socket, 2, 'amxx_reloadadmins');

            fclose($socket);
            Log::info('RCON amxx_reloadadmins sent successfully');
        } catch (\Exception $e) {
            Log::error('RCON error: ' . $e->getMessage());
        }
    }

    private function rconSend($socket, int $type, string $body): void
    {
        $id     = rand(1, 999);
        $data   = pack('VV', $id, $type) . $body . "\x00\x00";
        $packet = pack('V', strlen($data)) . $data;
        fwrite($socket, $packet);
        fread($socket, 4096);
    }
}