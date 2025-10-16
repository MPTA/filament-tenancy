# Translation Files Structure

## Current Clean Structure

```
lang/
├── en/
│   ├── common-fields.php           # Shared field labels (43 keys)
│   ├── tenant-users.php            # TenantUsers resource
│   ├── tenant-accommodations.php   # TenantAccommodations resource
│   ├── tenant-attractions.php      # TenantAttractions resource
│   └── tenant-prices.php           # TenantPrices RelationManager
│
├── zh_CN/
│   └── [same files as en/]
│
└── vendor/
    └── filament-translation-component/
        └── en/
            └── messages.php        # Component labels (required)
```

---

## Translation Key Naming Convention

### Common Fields (common-fields.php)
```
Format: common-fields.{field_name}
Example: __('common-fields.name')
         __('common-fields.email_address')
```

**Usage:** For standard fields used across multiple resources

**Keys:** name, email, password, phone, country, city, price, etc.

---

### Resource-Specific (tenant-users.php, tenant-accommodations.php, etc.)
```
Format: {resource-file}.{category}.{key}
Example: __('tenant-users.resource_name')
         __('tenant-users.sections.essential_information.title')
         __('tenant-accommodations.filters.star_rating.5')
```

**Categories:**
- `resource_name`, `resource_name_plural`, `navigation_label`, `navigation_group`
- `sections.*` - Section titles and descriptions
- `filters.*` - Filter labels and options
- `actions.*` - Action button labels
- `messages.*` - Messages and notifications
- `placeholders.*` - Placeholder texts
- `validations.*` - Validation messages
- `notifications.*` - Notification titles and bodies
- `empty_state.*` - Empty state heading and description
- `modals.*` - Modal headings
- `columns.*` - Custom column labels (when different from common-fields)
- `repeater.*` - Repeater labels

---

## File Contents Structure

Each resource translation file contains:

```php
<?php

return [
    // Resource labels
    'resource_name' => 'Single name',
    'resource_name_plural' => 'Plural name',
    'navigation_label' => 'Menu label',
    'navigation_group' => 'Group name',
    
    // Sections (for forms and infolists)
    'sections' => [
        'section_key' => [
            'title' => 'Section Title',
            'description' => 'Section Description',
        ],
    ],
    
    // Custom columns (only if different from common-fields)
    'columns' => [
        'custom_column' => 'Column Label',
    ],
    
    // Filters
    'filters' => [
        'filter_key' => [
            'label' => 'Filter Label',
            'placeholder' => 'Placeholder',
            'options' => [...],
        ],
    ],
    
    // Actions
    'actions' => [
        'action_key' => 'Action Label',
    ],
    
    // Modals
    'modals' => [
        'modal_key' => 'Modal Heading',
    ],
    
    // Repeaters
    'repeater' => [
        'repeater_key' => 'Repeater Label',
    ],
    
    // Messages
    'messages' => [
        'message_key' => 'Message text',
    ],
    
    // Placeholders
    'placeholders' => [
        'placeholder_key' => 'Placeholder text',
    ],
    
    // Validations
    'validations' => [
        'validation_key' => 'Validation message',
    ],
    
    // Notifications
    'notifications' => [
        'notification_key' => 'Notification text',
    ],
    
    // Empty state
    'empty_state' => [
        'heading' => 'Empty State Heading',
        'description' => 'Empty State Description',
    ],
];
```

---

## Usage in Code

### Resource Class
```php
public static function getNavigationLabel(): string
{
    return __('tenant-users.navigation_label');
}

public static function getLabel(): ?string
{
    return __('tenant-users.resource_name');
}

public static function getPluralLabel(): ?string
{
    return __('tenant-users.resource_name_plural');
}

public static function getNavigationGroup(): ?string
{
    return __('tenant-users.navigation_group');
}
```

### Form Schema
```php
Section::make(__('tenant-users.sections.essential_information.title'))
    ->description(__('tenant-users.sections.essential_information.description'))
    ->schema([
        TextInput::make('name')
            ->label(__('common-fields.full_name'))
            ->helperText(__('tenant-users.messages.password_helper')),
    ])
```

### Table Schema
```php
->columns([
    TextColumn::make('name')
        ->label(__('common-fields.name'))
        ->copyMessage(__('tenant-users.messages.email_copied')),
])
->filters([
    TernaryFilter::make('verified')
        ->label(__('tenant-users.filters.email_verified.label'))
        ->placeholder(__('tenant-users.filters.email_verified.placeholder'))
        ->trueLabel(__('tenant-users.filters.email_verified.true_label'))
        ->falseLabel(__('tenant-users.filters.email_verified.false_label')),
])
->emptyStateHeading(__('tenant-users.empty_state.heading'))
->emptyStateDescription(__('tenant-users.empty_state.description'))
```

### RelationManager
```php
public static function getTitle(Model $ownerRecord, string $pageClass): string
{
    return __('tenant-prices.title');
}
```

---

## Translation Guidelines

### 1. Use Common Fields When Possible
If a field is standard (name, email, phone, etc.), use `common-fields.*`:
```php
// ✅ Good
->label(__('common-fields.email'))

// ❌ Avoid
->label(__('tenant-users.fields.email'))
```

### 2. Resource-Specific Only for Unique Content
Use resource-specific keys only for unique content:
```php
// ✅ Good - unique to this resource
->label(__('tenant-prices.columns.meals_included'))

// ❌ Avoid - this is common
->label(__('tenant-prices.fields.name'))  // Use common-fields.name instead
```

### 3. Professional Admin Panel Translations
Chinese translations should be appropriate for admin panel context:
```php
// ✅ Good - Admin panel context
'historical' => '历史'  // Short and professional

// ❌ Avoid - Too descriptive
'historical' => '历史性的景点'  // Too wordy
```

### 4. Consistent Terminology
Maintain consistent terminology across all resources:
- User → 用户 (not 使用者)
- Price → 价格 (not 费用)
- Local → 本地 (not 当地)

---

## Files Translated So Far

### Resources
1. ✅ **TenantUsers** (tenant-users.php)
   - Form, Table, Filters, Empty State
   
2. ✅ **TenantAccommodations** (tenant-accommodations.php)
   - Table, Infolist, Filters, Empty State
   
3. ✅ **TenantAttractions** (tenant-attractions.php)
   - Table, Infolist, Filters, Actions, Modals, Empty State

### RelationManagers
1. ✅ **TenantPrices** (tenant-prices.php)
   - Form, Infolist, Table, Filters, Actions, Validations, Notifications, Empty State

### Common
1. ✅ **Common Fields** (common-fields.php)
   - 43 standard field labels

---

## Statistics

- Total Translation Files: 10 (5 EN + 5 ZH_CN)
- Total Translation Keys: 280
- Resources Completed: 3
- RelationManagers Completed: 1
- Languages: 2 (English, Chinese Simplified)

---

## Next Steps

Continue translating remaining resources in Tenant Admin:
- VehicleTypes
- CompanionTypes
- ExchangeRates
- Experiences
- MealTypes
- TenantContacts

Then move to Base and App resources.

