<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Toplu bülteni kuyrukta çalıştır
    |--------------------------------------------------------------------------
    |
    | false: Admin panelden gönderim anında çalışır (queue:work gerekmez).
    | true:  SendNewsletterCampaignJob kuyruğa düşer; sunucuda mutlaka
    |        `php artisan queue:work` çalışmalıdır.
    |
    */

    'async_campaign' => env('NEWSLETTER_ASYNC_CAMPAIGN', false),

];
