<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Dashboards\Main as Dashboard;
use PartridgeRocks\RecentMessages\RecentMessages;

class Main extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     */
    public function cards(): array
    {
        return [
            new RecentMessages(),
        ];
    }
}
