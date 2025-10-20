<?php

namespace App\Traits;

trait HasTranslatableFallback
{
    /**
     * Get an attribute with fallback to first available translation.
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        
        // If this is a translatable field and value is empty/null, try fallback
        if (isset($this->translatable) && in_array($key, $this->translatable)) {
            $translations = $this->getTranslations($key);
            
            if (!empty($translations)) {
                $currentLocale = app()->getLocale();
                
                // If current locale exists and is not empty, return it
                if (isset($translations[$currentLocale]) && !empty($translations[$currentLocale])) {
                    return $translations[$currentLocale];
                }
                
                // Otherwise, return first available translation
                return reset($translations);
            }
        }
        
        return $value;
    }
}

