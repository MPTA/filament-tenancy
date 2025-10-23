<?php

namespace TomatoPHP\FilamentTenancy\Filament\Resources;

use TomatoPHP\FilamentTenancy\Filament\Resources\TenantResource\Pages;
use TomatoPHP\FilamentTenancy\Filament\Resources\TenantResource\RelationManagers;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use TomatoPHP\FilamentTenancy\Models\Tenant;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;
    //protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    public static function getNavigationGroup(): ?string
    {
        return trans('filament-tenancy::messages.group');
    }

    public static function getNavigationLabel(): string
    {
        return trans('filament-tenancy::messages.single');
    }

    public static function getPluralLabel(): ?string
    {
        return trans('filament-tenancy::messages.title');
    }

    public static function getLabel(): ?string
    {
        return trans('filament-tenancy::messages.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make([
                    Forms\Components\TextInput::make('name')
                        ->label(trans('filament-tenancy::messages.columns.name'))
                        ->required()
                        ->unique(table: 'tenants', ignoreRecord: true)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, $state) {
                            $set('id', Str::slug($state, '_'));
                            $set('domain', Str::slug($state));
                        }),
                    Forms\Components\TextInput::make('id')
                        ->label(trans('filament-tenancy::messages.columns.unique_id'))
                        ->required()
                        ->disabled(fn ($context) => $context !== 'create')
                        ->unique(table: 'tenants', ignoreRecord: true),
                    Forms\Components\TextInput::make('domain')
                        ->columnSpanFull()
                        ->label(trans('filament-tenancy::messages.columns.domain'))
                        ->required()
                        ->visible(fn ($context) => $context === 'create')
                        ->unique(table: 'domains', ignoreRecord: true)
                        ->prefix(request()->getScheme() . '://')
                        ->suffix('.' . request()->getHost()),
                    Forms\Components\TextInput::make('email')
                        ->label(trans('filament-tenancy::messages.columns.email'))
                        ->required()
                        ->email(),
                    Forms\Components\TextInput::make('phone')
                        ->label(trans('filament-tenancy::messages.columns.phone'))
                        ->tel(),
                    Forms\Components\TextInput::make('password')
                        ->label(trans('filament-tenancy::messages.columns.password'))
                        ->password()
                        ->revealable()
                        ->rules([Password::default()])
                        ->autocomplete('new-password')
                        ->dehydrated(fn ($state): bool => filled($state))
                        ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                        ->live(debounce: 500)
                        ->same('passwordConfirmation'),
                    Forms\Components\TextInput::make('passwordConfirmation')
                        ->label(trans('filament-tenancy::messages.columns.passwordConfirmation'))
                        ->password()
                        ->revealable()
                        ->dehydrated(false),
                    Forms\Components\Toggle::make('is_active')
                        ->label(trans('filament-tenancy::messages.columns.is_active'))
                        ->default(true),
                ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(trans('filament-tenancy::messages.columns.id'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(trans('filament-tenancy::messages.columns.name'))
                    ->description(function ($record) {
                        return request()->getScheme() . '://' . ($record->domains()->first()?->domain ?? '') . '.' . config('filament-tenancy.central_domain') . '/app';
                    }),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->sortable()
                    ->label(trans('filament-tenancy::messages.columns.is_active')),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(trans('filament-tenancy::messages.columns.is_active')),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('view')
                    ->label(trans('filament-tenancy::messages.actions.view'))
                    ->tooltip(trans('filament-tenancy::messages.actions.view'))
                    ->iconButton()
                    ->icon('heroicon-s-link')
                    ->url(fn ($record) => request()->getScheme() . '://' . ($record->domains()->first()?->domain ?? '') . '.' . config('filament-tenancy.central_domain') . '/' . filament()->getDefaultPanel()->getPath())
                    ->openUrlInNewTab(),
                Action::make('login')
                    ->label(trans('filament-tenancy::messages.actions.login'))
                    ->tooltip(trans('filament-tenancy::messages.actions.login'))
                    ->visible(config('filament-tenancy.allow_impersonate', false))
                    ->requiresConfirmation()
                    ->color('warning')
                    ->iconButton()
                    ->icon('heroicon-s-arrow-left-on-rectangle')
                    ->action(function ($record) {
                        $token = tenancy()->impersonate($record, 1, '/app', 'web');
                        return redirect()->to(request()->getScheme() . '://' . $record->domains[0]->domain . '.' . config('filament-tenancy.central_domain') . '/login/url?token=' . $token->token . '&email=' . urlencode($record->email));
                    }),
                Action::make('password')
                    ->label(trans('filament-tenancy::messages.actions.password'))
                    ->tooltip(trans('filament-tenancy::messages.actions.password'))
                    ->requiresConfirmation()
                    ->icon('heroicon-s-lock-closed')
                    ->iconButton()
                    ->color('danger')
                    ->form([
                        Forms\Components\TextInput::make('password')
                            ->label(trans('filament-tenancy::messages.columns.password'))
                            ->password()
                            ->revealable()
                            ->rules([Password::default()])
                            ->autocomplete('new-password')
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->live(debounce: 500)
                            ->same('passwordConfirmation'),
                        Forms\Components\TextInput::make('passwordConfirmation')
                            ->label(trans('filament-tenancy::messages.columns.passwordConfirmation'))
                            ->password()
                            ->revealable()
                            ->dehydrated(false),
                    ])
                    ->action(function (array $data, $record) {
                        $record->password = bcrypt($data['password']);
                        $record->save();
                        Notification::make()
                            ->title(trans('filament-tenancy::messages.actions.notificaitons.password.title'))
                            ->body(trans('filament-tenancy::messages.actions.notificaitons.password.body'))
                            ->success()
                            ->send();
                    }),
                EditAction::make()
                    ->label(trans('filament-tenancy::messages.actions.edit'))
                    ->tooltip(trans('filament-tenancy::messages.actions.edit'))
                    ->iconButton(),
                DeleteAction::make()
                    ->label(trans('filament-tenancy::messages.actions.delete'))
                    ->tooltip(trans('filament-tenancy::messages.actions.delete'))
                    ->iconButton()
                    ->before(function ($record) {
                        // For multi-schema, manually delete schema before tenant deletion
                        try {
                            $schemaName = $record->database()->getName();
                            
                            // Check if schema exists
                            $schemaExists = DB::connection('pgsql')->select("SELECT schema_name FROM information_schema.schemata WHERE schema_name = '{$schemaName}'");
                            
                            if (count($schemaExists) > 0) {
                                // Schema exists, delete it manually
                                DB::connection('pgsql')->statement("DROP SCHEMA \"{$schemaName}\" CASCADE");
                                Log::info("Schema {$schemaName} deleted manually");
                            } else {
                                Log::info("Schema {$schemaName} does not exist, skipping deletion");
                            }
                        } catch (\Exception $e) {
                            Log::info("Failed to delete schema manually: " . $e->getMessage());
                        }
                    })
                    ->after(function ($record) {
                        // Prevent TenantDeleted event from being triggered
                        // by manually handling the deletion process
                        try {
                            // Clear any cached data related to this tenant
                            Cache::forget("tenant_{$record->id}");
                            Log::info("Tenant {$record->name} deleted successfully without triggering TenantDeleted event");
                        } catch (\Exception $e) {
                            Log::info("Failed to clear cache for tenant {$record->id}: " . $e->getMessage());
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            // For multi-schema, manually delete schemas before tenant deletion
                            foreach ($records as $record) {
                                try {
                                    $schemaName = $record->database()->getName();
                                    
                                    // Check if schema exists
                                    $schemaExists = DB::connection('pgsql')->select("SELECT schema_name FROM information_schema.schemata WHERE schema_name = '{$schemaName}'");
                                    
                                    if (count($schemaExists) > 0) {
                                        // Schema exists, delete it manually
                                        DB::connection('pgsql')->statement("DROP SCHEMA \"{$schemaName}\" CASCADE");
                                        Log::info("Schema {$schemaName} deleted manually for tenant {$record->name}");
                                    } else {
                                        Log::info("Schema {$schemaName} does not exist for tenant {$record->name}, skipping deletion");
                                    }
                                } catch (\Exception $e) {
                                    Log::info("Failed to delete schema manually for tenant {$record->name}: " . $e->getMessage());
                                }
                            }
                        })
                        ->after(function ($records) {
                            // Prevent TenantDeleted event from being triggered
                            // by manually handling the deletion process
                            foreach ($records as $record) {
                                try {
                                    // Clear any cached data related to this tenant
                                    Cache::forget("tenant_{$record->id}");
                                    Log::info("Tenant {$record->name} deleted successfully without triggering TenantDeleted event");
                                } catch (\Exception $e) {
                                    Log::info("Failed to clear cache for tenant {$record->id}: " . $e->getMessage());
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DomainsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'view' => Pages\ViewTenant::route('/{record}'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
