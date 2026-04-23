<?php

namespace App\Jobs;

use App\Models\NewsletterSubscriber;
use App\Support\NewsletterCampaignSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNewsletterToSubscribersJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 900;

    /**
     * @param  list<int>  $subscriberIds
     */
    public function __construct(
        public string $subject,
        public string $bodyHtmlRaw,
        public array $subscriberIds,
    ) {}

    public function handle(NewsletterCampaignSender $sender): void
    {
        $subscribers = NewsletterSubscriber::query()
            ->whereIn('id', $this->subscriberIds)
            ->where('is_active', true)
            ->get();

        $sender->sendToSubscribers($this->subject, $this->bodyHtmlRaw, $subscribers);
    }
}
