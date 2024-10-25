<?php

namespace Database\Seeders;

use A17\Twill\Models\AppSetting;
use A17\Twill\Models\Block;
use A17\Twill\Models\RelatedItem;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Setup homepage

        AppSetting::create([
            'published' => 1,
            'name' => 'homepage'
        ]);

        $block = Block::create([
            'blockable_type' => 'A17\Twill\Models\AppSetting',
            'blockable_id' => 1,
            'position' => 1,
            'content' => ["browsers" => ["page" => [1]]],
            'type' => 'appSettings.homepage.homepage',
            'editor_name' => 'homepage'
        ]);

        RelatedItem::create([
            'subject_id' => $block->id,
            'subject_type' => 'blocks',
            'related_id' => 1,
            'related_type' => 'App\Models\Page',
            'browser_name' => 'page',
            'position' => '1',
        ]);
    }
}

