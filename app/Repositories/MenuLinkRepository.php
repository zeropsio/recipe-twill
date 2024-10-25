<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\Behaviors\HandleNesting;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\MenuLink;

class MenuLinkRepository extends ModuleRepository
{
    use HandleTranslations, HandleNesting;

    protected $relatedBrowsers = ['page'];

    public function __construct(MenuLink $model)
    {
        $this->model = $model;
    }
}
