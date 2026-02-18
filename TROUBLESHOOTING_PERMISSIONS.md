# Troubleshooting Permissions Page

## Issue: Permissions page not loading / Module not working

### Step 1: Check Browser Console

1. Open permissions page: `/admin/settings/permissions`
2. Press `F12` to open Developer Tools
3. Go to **Console** tab
4. Look for errors (red text)

**Expected Console Output:**
```
Script loaded!
jQuery version: 3.7.0
Document ready!
Loading permissions...
Permissions loaded: {permissions: Array, modules: Array}
```

**If you see errors, note them down**

---

### Step 2: Check Network Tab

1. In Developer Tools, go to **Network** tab
2. Refresh the page (`Ctrl + R`)
3. Look for request to `/admin/settings/permissions`
4. Click on it
5. Check **Response** tab

**Expected Response:**
```json
{
    "permissions": [...],
    "modules": [...]
}
```

**If you see HTML instead of JSON:**
- Controller is not returning JSON
- Check if request has `Accept: application/json` header

---

### Step 3: Common Errors & Solutions

#### Error: "$ is not defined" or "jQuery is not defined"
**Solution:**
```bash
# Clear cache
Ctrl + Shift + R

# Or check if jQuery is loaded
# In console type:
typeof jQuery
# Should return "function"
```

#### Error: "Unexpected token < in JSON"
**Cause:** Server returning HTML instead of JSON

**Solution:**
Check controller method:
```php
public function permissionsIndex(Request $request)
{
    if ($request->ajax() || $request->wantsJson()) {
        // Return JSON
        $permissions = Permission::all();
        $modules = Permission::distinct()->pluck('module');
        return response()->json([
            'permissions' => $permissions,
            'modules' => $modules
        ]);
    }
    
    // Return view for normal requests
    return view('admin.settings.permissions');
}
```

#### Error: 500 Internal Server Error
**Solution:**
```bash
# Check Laravel logs
cat storage/logs/laravel.log

# Or on Windows
type storage\logs\laravel.log
```

#### Error: 404 Not Found
**Solution:**
```bash
# Clear route cache
php artisan route:clear

# List routes to verify
php artisan route:list | findstr permissions
```

---

### Step 4: Manual AJAX Test

Open browser console and run:

```javascript
$.ajax({
    url: '/admin/settings/permissions',
    type: 'GET',
    dataType: 'json',
    headers: {
        'Accept': 'application/json'
    },
    success: function(response) {
        console.log('Success:', response);
    },
    error: function(xhr) {
        console.log('Error:', xhr.status, xhr.responseText);
    }
});
```

**Expected:** Should log permissions and modules data

---

### Step 5: Check Database

```sql
-- Check if permissions exist
SELECT COUNT(*) FROM permissions;

-- Check modules
SELECT DISTINCT module FROM permissions;

-- Check sample data
SELECT * FROM permissions LIMIT 5;
```

**If no data:**
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

---

### Step 6: Verify Routes

```bash
php artisan route:list | findstr "permissions"
```

**Expected output:**
```
GET|HEAD  admin/settings/permissions ............... admin.settings.permissions
POST      admin/settings/permissions ............... admin.settings.permissions.store
GET|HEAD  admin/settings/permissions/{id}/edit ..... admin.settings.permissions.edit
PUT       admin/settings/permissions/{id} .......... admin.settings.permissions.update
DELETE    admin/settings/permissions/{id} .......... admin.settings.permissions.delete
```

---

### Step 7: Check File Permissions (if on Linux/Mac)

```bash
# Make sure storage is writable
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

### Step 8: Clear All Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Then refresh browser with `Ctrl + Shift + R`

---

### Step 9: Check app.blade.php

File: `resources/views/admin/layouts/app.blade.php`

**Should be:**
```php
@extends('admin.layouts.horizontal')

@push('scripts')
    @yield('scripts')
@endpush
```

**NOT:**
```php
@section('scripts')
    @yield('scripts')
@endsection
```

---

### Step 10: Verify jQuery Loading Order

In `horizontal.blade.php`, scripts should be in this order:

```html
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/admin-script.js') }}"></script>
@stack('scripts')
```

jQuery MUST be loaded before your custom scripts!

---

## Quick Fix Checklist

- [ ] Browser console shows no errors
- [ ] jQuery is loaded (check with `typeof jQuery`)
- [ ] Network tab shows JSON response
- [ ] Database has permissions data
- [ ] Routes are registered
- [ ] Caches are cleared
- [ ] app.blade.php uses @push not @section
- [ ] jQuery loads before custom scripts

---

## Still Not Working?

### Debug Mode

Add this at the top of permissions.blade.php:

```php
@section('content')
<div class="container-fluid">
    <div class="alert alert-info">
        <strong>Debug Info:</strong><br>
        jQuery Loaded: <span id="jqueryCheck">Checking...</span><br>
        AJAX URL: {{ route("admin.settings.permissions") }}<br>
        CSRF Token: {{ csrf_token() }}
    </div>
    
    <script>
        document.getElementById('jqueryCheck').textContent = 
            typeof jQuery !== 'undefined' ? 'Yes ✓' : 'No ✗';
    </script>
    
    <!-- Rest of your content -->
</div>
```

This will show if jQuery is loaded and what URL is being called.

---

## Contact Support

If still facing issues, provide:
1. Browser console screenshot
2. Network tab screenshot
3. Laravel log errors
4. Database permissions count

---

**Last Updated:** 2026-02-07
**Status:** Troubleshooting Guide
