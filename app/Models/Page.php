<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasBlocks;
use A17\Twill\Models\Behaviors\HasTranslation;
use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasRevisions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use A17\Twill\Models\Model;

class Page extends Model
{
    use HasBlocks, HasTranslation, HasSlug, HasMedias, HasRevisions, HasFactory;

    protected $fillable = [
        'published',
        'title',
        'description',
    ];

    public $translatedAttributes = [
        'title',
        'description',
    ];

    public $slugAttributes = [
        'title',
    ];

    public $mediasParams = [
        'cover' => [
            'default' => [
                [
                    'name' => 'default',
                    'ratio' => 16 / 9,
                ],
            ],
        ],
    ];

}
