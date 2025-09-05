<?php

namespace TomatoPHP\FilamentTenancy\Filament\Resources\TenantResource\Pages;

use TomatoPHP\FilamentTenancy\Filament\Resources\TenantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('open')
                ->label(trans('filament-tenancy::messages.actions.view'))
                ->icon('heroicon-s-link')
                ->url(fn($record) => request()->getScheme() . "://" . $record->domains()->first()?->domain . '.' . config('filament-tenancy.central_domain') . '/' . filament('filament-tenancy')->panel)
                ->openUrlInNewTab(),
            Actions\DeleteAction::make()
                ->icon('heroicon-s-trash')
                ->label(trans('filament-tenancy::messages.actions.delete'))
                ->before(function ($record) {
                    // For multi-schema, check if schema exists before deletion
                    try {
                        $schemaName = $record->database()->getName();
                        
                        // Check if schema exists
                        $schemaExists = DB::connection('pgsql')->select("SELECT schema_name FROM information_schema.schemata WHERE schema_name = '{$schemaName}'");
                        
                        if (count($schemaExists) > 0) {
                            // Schema exists, trigger deletion event
                            event(new \Stancl\Tenancy\Events\TenantDeleted($record));
                        } else {
                            Log::info("Schema {$schemaName} does not exist, skipping deletion event");
                        }
                    } catch (\Exception $e) {
                        Log::info("Failed to check schema or trigger tenant deletion event: " . $e->getMessage());
                    }
                })
                ->after(function ($record) {
                    // Additional cleanup if needed
                    try {
                        // Clear any cached data related to this tenant
                        Cache::forget("tenant_{$record->id}");
                    } catch (\Exception $e) {
                        Log::info("Failed to clear cache for tenant {$record->id}: " . $e->getMessage());
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (isset($data['password'])) {
            $updateData["password"] = $data['password'];
        }

        try {
            // For multi-schema, use dynamic connection which automatically switches to tenant schema
            DB::connection('dynamic')->getPdo();
        } catch (\Exception $e) {
            throw new \Exception("Failed to connect to tenant schema");
        }

        $user = DB::connection('dynamic')
            ->table('users')
            ->where('email', $record->email);

        if (config('filament-tenancy.single_database')) {
            $user = $user->where('tenant_id', $record->id);

            $updateData['tenant_id'] = $record->id;
        }

        $user->updateOrInsert(
            [
                'email' => $record->email,
            ],
            $updateData,
        );

        return $data;
    }
}

