<?php

namespace App\Console\Commands;

use App\Support\Crm\PosterTemplateInstaller;
use Illuminate\Console\Command;

class InstallPosterTemplatesCommand extends Command
{
    protected $signature = 'crm:install-poster-templates {--force : Mevcut PNG şablonları yeniden üretir ve alanları sıfırlar}';

    protected $description = 'Bağış ve teşekkür afişi için boş PNG şablonları ve varsayılan alanları kurar';

    public function handle(PosterTemplateInstaller $installer): int
    {
        $force = (bool) $this->option('force');

        if ($force && ! $this->confirm('Mevcut şablon görselleri ve alan konumları sıfırlanacak. Devam edilsin mi?')) {
            return self::FAILURE;
        }

        $templates = $installer->install($force);

        foreach ($templates as $type => $template) {
            $size = $template->canvasSize();
            $this->line(sprintf(
                '- %s: %s (%dx%d px, %d alan)',
                $type,
                $template->name,
                $size['width'],
                $size['height'],
                $template->fields->count(),
            ));
        }

        $this->info('Afiş şablonları hazır. CRM → Afiş Şablonları → Düzenleyici ile ince ayar yapın.');

        return self::SUCCESS;
    }
}
