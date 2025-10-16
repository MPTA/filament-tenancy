<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use TomatoPHP\FilamentTranslations\Models\Translation;

class TranslationsSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:sync
                            {--locale= : Sync only specific locale}
                            {--group= : Sync only specific group}
                            {--force : Force update existing translations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync translation files from lang/ directory to database (Smart Import)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Smart Translation Sync Started...');
        $this->newLine();
        
        $locales = config('filament-translations.locals', []);
        $localeCodes = array_keys($locales);
        
        // Filter by option
        if ($locale = $this->option('locale')) {
            if (!in_array($locale, $localeCodes)) {
                $this->error("Locale '{$locale}' is not configured!");
                return 1;
            }
            $localeCodes = [$locale];
        }
        
        $new = 0;
        $updated = 0;
        $skipped = 0;
        
        // Loop through each locale
        foreach ($localeCodes as $locale) {
            $langPath = base_path("lang/{$locale}");
            
            if (!File::exists($langPath)) {
                $this->warn("⚠️  Path not found: {$langPath}");
                continue;
            }
            
            $this->line("📂 Scanning {$locale}...");
            
            $files = File::allFiles($langPath);
            
            foreach ($files as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }
                
                $group = $file->getFilenameWithoutExtension();
                
                // Filter by group option
                if ($groupOption = $this->option('group')) {
                    if ($group !== $groupOption) {
                        continue;
                    }
                }
                
                $translations = include $file->getPathname();
                
                if (!is_array($translations)) {
                    continue;
                }
                
                $result = $this->importTranslations($group, $translations, $localeCodes);
                $new += $result['new'];
                $updated += $result['updated'];
                $skipped += $result['skipped'];
                
                if ($result['new'] + $result['updated'] > 0) {
                    $this->line("  ✓ {$group}.php: +{$result['new']} new, ~{$result['updated']} updated, -{$result['skipped']} skipped");
                }
            }
        }
        
        $this->newLine();
        $this->info("✅ Sync Complete!");
        $this->table(
            ['Status', 'Count'],
            [
                ['New keys added', $new],
                ['Languages added to existing keys', $updated],
                ['Existing keys preserved', $skipped],
            ]
        );
        
        return 0;
    }
    
    private function importTranslations(string $group, array $translations, array $locales, string $prefix = ''): array
    {
        $stats = ['new' => 0, 'updated' => 0, 'skipped' => 0];
        
        foreach ($translations as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;
            
            // Handle nested arrays
            if (is_array($value)) {
                $nested = $this->importTranslations($group, $value, $locales, $fullKey);
                $stats['new'] += $nested['new'];
                $stats['updated'] += $nested['updated'];
                $stats['skipped'] += $nested['skipped'];
                continue;
            }
            
            // Build text array for all locales
            $text = [];
            foreach ($locales as $locale) {
                $localePath = base_path("lang/{$locale}/{$group}.php");
                
                if (File::exists($localePath)) {
                    $localeTranslations = include $localePath;
                    $translationValue = data_get($localeTranslations, $fullKey, '');
                    if (!empty($translationValue)) {
                        $text[$locale] = $translationValue;
                    }
                }
            }
            
            // Smart import: only add new keys or new languages
            $existing = Translation::where('namespace', '*')
                ->where('group', $group)
                ->where('key', $fullKey)
                ->first();
            
            if ($existing) {
                // Key exists - only add missing languages (unless --force)
                $existingText = is_array($existing->text) ? $existing->text : json_decode($existing->text, true);
                $hasNewLanguage = false;
                
                foreach ($text as $locale => $translation) {
                    if ($this->option('force')) {
                        // Force mode: update all
                        $existingText[$locale] = $translation;
                        $hasNewLanguage = true;
                    } elseif (!isset($existingText[$locale]) && !empty($translation)) {
                        // Normal mode: only add missing languages
                        $existingText[$locale] = $translation;
                        $hasNewLanguage = true;
                    }
                }
                
                if ($hasNewLanguage) {
                    $existing->update(['text' => $existingText]);
                    $stats['updated']++;
                } else {
                    $stats['skipped']++;
                }
            } else {
                // New key - create with all translations
                if (!empty(array_filter($text))) {
                    Translation::create([
                        'namespace' => '*',
                        'group' => $group,
                        'key' => $fullKey,
                        'text' => $text,
                    ]);
                    
                    $stats['new']++;
                }
            }
        }
        
        return $stats;
    }
}
