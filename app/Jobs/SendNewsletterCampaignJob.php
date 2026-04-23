<?php

namespace App\Jobs;

use App\Support\NewsletterCampaignSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNewsletterCampaignJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 900;

    public function __construct(
        public string $subject,
        public string $bodyHtmlRaw,
    ) {}

    public function handle(NewsletterCampaignSender $sender): void
    {
        $sender->sendToAllActive($this->subject, $this->bodyHtmlRaw);
    }
}
