<?php

namespace Filament\Widgets;

class FilamentInfoWidget extends Widget
{
    protected static ?int $sort = -2;

    protected static bool $isLazy = false;

    /**
     * @var view-string
     */
    protected string $view = 'filament::widgets.filament-info-widget';
}
