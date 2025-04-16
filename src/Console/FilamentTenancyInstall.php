<?php

namespace TomatoPHP\FilamentTenancy\Console;

use Illuminate\Console\Command;
use TomatoPHP\ConsoleHelpers\Traits\HandleFiles;
use TomatoPHP\ConsoleHelpers\Traits\RunCommand;

use function Laravel\Prompts\select;

class FilamentTenancyInstall extends Command
{
    use RunCommand;
    use HandleFiles;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filament-tenancy:install {--multiple} {--single}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'install package and publish assets';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! $this->option('multiple') && ! $this->option('single')) {
            $connectionType = select(
                label: 'Which database connection type will you use?',
                options: [
                    'multiple' => 'Multiple Databases',
                    'single' => 'Single Database',
                ],
                default: 'multiple',
            );
        } else {
            $connectionType = $this->option('multiple') ? 'multiple' : 'single';
        }

        $this->info('Publish Vendor Assets');

        $this->callSilent('optimize:clear');

        $this->artisanCommand(["migrate"]);

        $this->copyFile(
            $connectionType == 'multiple' ? __DIR__ .'/../../publish/config/tenancy.php' : __DIR__ .'/../../publish/config/single.tenancy.php',
            config_path('tenancy.php')
        );

        $this->copyFile(
            __DIR__ .'/../../publish/routes/tenant.php',
            base_path('routes/tenant.php')
        );

        $this->copyFile(
            __DIR__ .'/../../publish/resources/views/components',
            resource_path('views/components'),
            'folder'
        );

        $this->copyFile(
            __DIR__ .'/../../publish/database/migrations/tenant',
            database_path('migrations/tenant'),
            'folder'
        );

        $this->artisanCommand(["optimize"]);
        $this->info('Filament Tenancy installed successfully.');
    }
}
