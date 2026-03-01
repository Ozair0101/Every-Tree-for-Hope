# Multi-Language Translation Implementation

## Overview

The website now supports three languages with **full RTL support** for Dari and Pashto:

- **English** (en) - LTR direction
- **Dari/Farsi** (fa) - RTL direction
- **Pashto** (ps) - RTL direction

## Files Created/Modified

### 1. Language Files (90+ translation keys each)

- `resources/lang/en/messages.php` - English translations
- `resources/lang/fa/messages.php` - Dari translations
- `resources/lang/ps/messages.php` - Pashto translations

### 2. Middleware

- `app/Http/Middleware/LanguageMiddleware.php` - Handles language detection, switching, and RTL support

### 3. Configuration

- `bootstrap/app.php` - Registered the LanguageMiddleware for web routes

### 4. All Pages Updated with Translations

- `resources/views/layouts/layout.blade.php` - Language switcher + RTL support
- `resources/views/pages/home.blade.php` - Full translation support
- `resources/views/pages/about.blade.php` - Full translation support
- `resources/views/pages/contact.blade.php` - Full translation support
- `resources/views/pages/works.blade.php` - Full translation support
- `resources/views/pages/report.blade.php` - Full translation support
- `resources/views/pages/gallery.blade.php` - Full translation support
- `resources/views/pages/event-details.blade.php` - Full translation support
- `resources/views/pages/donators.blade.php` - Full translation support

## Translation Categories

All translations are organized into these categories:

- **Navigation** - Menu items (Home, About, Works, Gallery, Donators, Report, Contact)
- **Hero Section** - Main banner content
- **Statistics** - Trees planted, CO2 offset counters
- **Media Stories** - Video gallery section
- **Mission & Vision** - Core purpose statements
- **About Section** - Organization description
- **Donation Section** - Payment and contribution info
- **Contact Page** - Form fields and contact information
- **Report Page** - Statistics and transparency data
- **Gallery Page** - Events and media display
- **Event Details** - Individual event information
- **Donators Page** - Supporters and sponsors
- **Common** - Shared phrases (Read More, Learn More, etc.)

## How to Use

### Switching Languages

Users can switch languages using:

1. **Desktop**: Language dropdown in the top navigation bar (shows current language)
2. **Mobile**: Language buttons (EN, FA, PS) in the mobile menu

### URL Parameter Method

Add `?lang=fa` or `?lang=ps` to any URL to switch language:

- `http://127.0.0.1:8000/?lang=fa` - Dari
- `http://127.0.0.1:8000/?lang=ps` - Pashto
- `http://127.0.0.1:8000/?lang=en` - English

### Language Persistence

- Selected language is stored in session
- Browser language preference is detected automatically
- Language choice persists across page navigation

## RTL (Right-to-Left) Support

### Automatic Direction

The page automatically sets `dir="rtl"` on the HTML element when Dari or Pashto is selected.

### RTL-Aware Components

- Navigation menu items align to the right in RTL mode
- Text alignment adjusts automatically
- Icons and arrows flip direction appropriately
- Form inputs support RTL text entry

### Manual RTL Styling

The middleware provides `$is_rtl` variable to all views. Use it for RTL-specific CSS classes:

```blade
<p class="{{ $is_rtl ? 'pr-16' : 'pl-16' }}">Content</p>
```

## Adding New Translations

To add a new translation:

1. Add the key and translation to all three `messages.php` files (en, fa, ps)
2. Use `{{ __('messages.your_key') }}` in Blade templates for plain text
3. Use `{!! __('messages.your_key') !!}` for HTML content
4. Test in all three languages including RTL layout

## Testing Checklist

After making translation changes, verify:

- [ ] Text displays correctly in English (LTR)
- [ ] Text displays correctly in Dari (RTL)
- [ ] Text displays correctly in Pashto (RTL)
- [ ] Layout adjusts properly for RTL languages
- [ ] Language switcher works on all pages
- [ ] Selected language persists after navigation

## Technical Details

### Language Detection Priority

1. URL parameter (`?lang=xx`)
2. Session stored language
3. Browser Accept-Language header
4. Default (English)

### Available Languages Array

The middleware shares `$available_languages` to all views containing:

- `en` => ['name' => 'English', 'rtl' => false]
- `fa` => ['name' => 'Dari', 'rtl' => true]
- `ps` => ['name' => 'Pashto', 'rtl' => true]
