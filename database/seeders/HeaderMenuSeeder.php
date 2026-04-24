<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Database\Seeder;

class HeaderMenuSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['title' => 'Hikayemiz', 'slug' => 'hikayemiz'],
            ['title' => 'Baskanin Mesaji', 'slug' => 'baskanin-mesaji'],
            ['title' => 'Yonetim', 'slug' => 'yonetim'],
            ['title' => 'Resmi Belgiler', 'slug' => 'resmi-belgiler'],
            ['title' => 'Basin Kiti', 'slug' => 'basin-kiti'],
            ['title' => 'Kalkinma', 'slug' => 'kalkinma'],
            ['title' => 'Acil Yardim', 'slug' => 'acil-yardim'],
            ['title' => 'Egitim', 'slug' => 'egitim'],
            ['title' => 'Gida', 'slug' => 'gida'],
            ['title' => 'Saglik ve Hijyen', 'slug' => 'saglik-ve-hijyen'],
            ['title' => 'Faaliyet Raporlari', 'slug' => 'faaliyet-raporlari'],
            ['title' => 'Bolge Raporlari', 'slug' => 'bolge-raporlari'],
            ['title' => 'Medyada Biz', 'slug' => 'medyada-biz'],
        ];

        foreach ($pages as $page) {
            Page::query()->updateOrCreate(
                ['slug' => $page['slug']],
                [
                    'title' => $page['title'],
                    'content' => 'Bu sayfa icerigi admin panelden duzenlenecektir.',
                    'is_active' => true,
                ]
            );
        }

        MenuItem::query()
            ->whereIn('label', ['Projeler', 'Haberler', 'Bağış Hesapları', 'Hakkimizda'])
            ->update(['is_active' => false]);

        $kurumsal = MenuItem::query()->updateOrCreate(
            ['label' => 'Kurumsal', 'parent_id' => null],
            ['url' => '/sayfa/hikayemiz', 'open_in_new_tab' => false, 'is_active' => true, 'sort_order' => 20, 'footer_group' => null]
        );
        $faaliyetler = MenuItem::query()->updateOrCreate(
            ['label' => 'Faaliyetler', 'parent_id' => null],
            ['url' => '/sayfa/kalkinma', 'open_in_new_tab' => false, 'is_active' => true, 'sort_order' => 30, 'footer_group' => null]
        );
        $raporlar = MenuItem::query()->updateOrCreate(
            ['label' => 'Raporlar', 'parent_id' => null],
            ['url' => '/sayfa/faaliyet-raporlari', 'open_in_new_tab' => false, 'is_active' => true, 'sort_order' => 40, 'footer_group' => null]
        );

        MenuItem::query()->updateOrCreate(
            ['label' => 'Medyada Biz', 'parent_id' => null],
            ['url' => '/sayfa/medyada-biz', 'open_in_new_tab' => false, 'is_active' => true, 'sort_order' => 50, 'footer_group' => null]
        );

        $this->seedChildren($kurumsal->id, [
            ['Hikayemiz', '/sayfa/hikayemiz', 1],
            ['Baskanin Mesaji', '/sayfa/baskanin-mesaji', 2],
            ['Yonetim', '/sayfa/yonetim', 3],
            ['Resmi Belgiler', '/sayfa/resmi-belgiler', 4],
            ['Basin Kiti', '/sayfa/basin-kiti', 5],
        ]);

        $this->seedChildren($faaliyetler->id, [
            ['Kalkinma', '/sayfa/kalkinma', 1],
            ['Acil Yardim', '/sayfa/acil-yardim', 2],
            ['Egitim', '/sayfa/egitim', 3],
            ['Gida', '/sayfa/gida', 4],
            ['Saglik ve Hijyen', '/sayfa/saglik-ve-hijyen', 5],
        ]);

        $this->seedChildren($raporlar->id, [
            ['Faaliyet Raporlari', '/sayfa/faaliyet-raporlari', 1],
            ['Bolge Raporlari', '/sayfa/bolge-raporlari', 2],
        ]);
    }

    /**
     * @param  array<int, array{0:string,1:string,2:int}>  $items
     */
    private function seedChildren(int $parentId, array $items): void
    {
        foreach ($items as [$label, $url, $sortOrder]) {
            MenuItem::query()->updateOrCreate(
                ['label' => $label, 'parent_id' => $parentId],
                ['url' => $url, 'open_in_new_tab' => false, 'is_active' => true, 'sort_order' => $sortOrder, 'footer_group' => null]
            );
        }
    }
}
