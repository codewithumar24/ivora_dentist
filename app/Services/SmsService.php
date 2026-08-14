<?php

namespace App\Services;

use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Two drivers: `log` for local work (writes storage/logs/sms.log and the
 * sms_logs table so the console still shows a full outbox), and `twilio`
 * for production. Every send is recorded either way.
 */
class SmsService
{
    public function send(string $to, string $body, string $purpose = 'general', ?User $user = null): SmsLog
    {
        $driver = config('clinic.sms.driver', 'log');

        $log = SmsLog::create([
            'user_id' => $user?->id,
            'to' => $to,
            'body' => $body,
            'purpose' => $purpose,
            'driver' => $driver,
            'status' => 'queued',
        ]);

        try {
            $providerId = $driver === 'twilio'
                ? $this->viaTwilio($to, $body)
                : $this->viaLog($to, $body, $purpose);

            $log->update(['status' => 'sent', 'provider_id' => $providerId, 'sent_at' => now()]);
        } catch (Throwable $e) {
            $log->update(['status' => 'failed', 'error' => $e->getMessage()]);
            Log::channel('sms')->error("SMS to {$to} failed: {$e->getMessage()}");
        }

        return $log;
    }

    protected function viaLog(string $to, string $body, string $purpose): string
    {
        Log::channel('sms')->info("[{$purpose}] → {$to} :: {$body}");

        return 'log-'.uniqid();
    }

    protected function viaTwilio(string $to, string $body): string
    {
        $client = new \Twilio\Rest\Client(config('services.twilio.sid'), config('services.twilio.token'));

        $message = $client->messages->create($to, [
            'from' => config('services.twilio.from'),
            'body' => $body,
        ]);

        return $message->sid;
    }
}
