## PDO MySQL Driver - Management Guide

### ✓ Current Status
✓ PDO MySQL driver is **properly installed and working**
✓ All required extensions are loaded
✓ Database connection test successful

---

### Issue: Duplicate mysqli Extension Warning

**Problem:** PHP shows warning "Module 'mysqli' is already loaded"

**Cause:** The `php.ini` file has duplicate `extension=mysqli` entries

**Solution:**

1. **Open php.ini file:**
   ```
   C:\xampp\php\php.ini
   ```

2. **Find and remove duplicates:**
   - Search for `extension=mysqli`
   - You should find 2 entries (one may be indented)
   - Keep ONLY ONE uncommented line
   - Remove or comment out the duplicate

3. **Example of what to fix:**
   ```ini
   ; WRONG - has duplicates:
   extension=mysqli
    extension=mysqli
   
   ; CORRECT - keep only one:
   extension=mysqli
   ; extension=mysqli  (commented out)
   ```

4. **Save the file and restart Apache:**
   - In XAMPP Control Panel, click "Stop" on Apache
   - Then click "Start"
   - Or use: `net stop Apache2.4` and `net start Apache2.4`

---

### How to Check PDO MySQL Status

**Run the diagnostic script:**
```bash
C:\xampp\php\php.exe C:\xampp\htdocs\plms-pnvc\check_pdo.php
```

**Or check via web browser:**
- Navigate to: http://localhost/phpmyadmin
- If it works, PDO MySQL is properly configured

---

### PDO MySQL Configuration in PHP

The application now has better error handling:

1. **Automatic detection** - Detects both Web and CLI contexts
2. **Graceful fallback** - Works with either `pdo_mysql` or `pdo` loaded
3. **Better error messages** - Shows detailed errors on localhost only

---

### Common Issues and Solutions

| Issue | Solution |
|-------|----------|
| "PDO MySQL driver is not installed" | Uncomment `extension=php_pdo_mysql.dll` in php.ini |
| "Module mysqli already loaded" | Remove duplicate `extension=mysqli` lines in php.ini |
| Database connection fails | Check MySQL is running in XAMPP Control Panel |
| Connection timeout | Check database credentials in `config.php` |

---

### Quick Fixes Checklist

✓ **Config.php updated** - Better error handling for CLI and Web contexts  
✓ **Diagnostic script created** - `check_pdo.php` for quick verification  
✓ **All extensions working** - PDO, MySQLi, MySQLnd loaded successfully  

---

### File Locations

| File | Location | Purpose |
|------|----------|---------|
| config.php | `/config.php` | Main database configuration |
| check_pdo.php | `/check_pdo.php` | Diagnostic tool for PDO status |
| php.ini | `C:\xampp\php\php.ini` | PHP configuration (for fixing mysqli warning) |

---

### Next Steps

1. ✓ Run the diagnostic: `php check_pdo.php`
2. ✓ Fix duplicate mysqli in `C:\xampp\php\php.ini`
3. ✓ Restart Apache
4. ✓ Application should work without errors

