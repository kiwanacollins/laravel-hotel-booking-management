# Dashboard Access Troubleshooting Guide

## Problem
You can't access the dashboard even with the correct credentials.

## Root Cause Analysis

The dashboard access is protected by the `CheckRole` middleware which requires users to have one of these roles:
- `Super`
- `Admin`
- `Customer`

The issue could be one of the following:

1. **User Role Not Set Correctly** - User doesn't have a valid role in the database
2. **Database Not Seeded** - Users table doesn't have any data
3. **Authentication Issue** - User is not properly authenticated before reaching the middleware
4. **Session Issue** - Session data is not being maintained across requests

## Diagnostic Steps

### Step 1: Check Your Database Setup

Run the following artisan commands to ensure your database is properly set up:

```bash
php artisan migrate:fresh --seed
```

This will:
- Reset all tables
- Run all migrations
- Seed the database with test users

Expected output should show:
```
✓ Super User (john@gmail.com)
✓ Admin User (admin@gmail.com)
✓ Customer User (customer@gmail.com)
```

### Step 2: Verify Users in Database

Use the diagnostic endpoint to check all users:

```
http://localhost:8000/diagnostic/database
```

You should see output like:
```json
{
  "database_connected": true,
  "users_count": 3,
  "all_users": [
    {
      "id": 1,
      "name": "John Smith",
      "email": "john@gmail.com",
      "role": "Super"
    },
    {
      "id": 2,
      "name": "Admin User",
      "email": "admin@gmail.com",
      "role": "Admin"
    },
    {
      "id": 3,
      "name": "Customer User",
      "email": "customer@gmail.com",
      "role": "Customer"
    }
  ]
}
```

### Step 3: Test Login and Check Authentication

After logging in, visit:

```
http://localhost:8000/diagnostic/auth
```

You should see:
```json
{
  "authenticated": true,
  "user": {
    "id": 1,
    "name": "John Smith",
    "email": "john@gmail.com",
    "role": "Super"
  }
}
```

If `authenticated` is `false`, the login is not working properly.

### Step 4: Verify Role-Based Access

After logging in, visit:

```
http://localhost:8000/diagnostic/role-access
```

You should see:
```json
{
  "user_role": "Super",
  "required_roles": ["Super", "Admin", "Customer"],
  "role_in_enum": true,
  "role_exact_match": {
    "Super": true,
    "Admin": false,
    "Customer": false
  }
}
```

The `role_in_enum` should be `true`.

### Step 5: Check Dashboard Accessibility

After logging in, visit:

```
http://localhost:8000/diagnostic/dashboard-access
```

You should see:
```json
{
  "user_id": 1,
  "user_name": "John Smith",
  "user_role": "Super",
  "has_access": true,
  "access_granted": "YES"
}
```

If `has_access` is `false`, the role is not matching.

## Common Issues and Solutions

### Issue 1: Role Mismatch
**Symptom**: `role_in_enum` is `false` or `access_granted` is `NO`

**Solution**: Check the exact role value in the database. Make sure it's one of:
- `Super` (capital S)
- `Admin` (capital A)
- `Customer` (capital C)

The roles are **case-sensitive**.

### Issue 2: User Not Authenticated
**Symptom**: `authenticated` is `false` in `/diagnostic/auth`

**Solution**: 
1. Check your login credentials are correct
2. Verify the users table has data from the seeder
3. Check that the `auth` middleware is working

### Issue 3: Database Connection Error
**Symptom**: `/diagnostic/database` returns connection error

**Solution**:
1. Check your `.env` file has correct database credentials
2. Ensure the database exists
3. Run `php artisan migrate --seed` again

### Issue 4: Empty Database
**Symptom**: `/diagnostic/database` shows 0 users

**Solution**: 
```bash
php artisan migrate:fresh --seed
```

## Testing the Dashboard

Once you've verified all the diagnostic endpoints are working:

1. Log in with one of these credentials:

**Super Admin:**
- Email: `john@gmail.com`
- Password: `workwithjohn`

**Admin:**
- Email: `admin@gmail.com`
- Password: `admin12345`

**Customer:**
- Email: `customer@gmail.com`
- Password: `customer12345`

2. Navigate to the dashboard:
```
http://localhost:8000/dashboard
```

## Checking Application Logs

If you're still having issues, check the application logs:

```bash
tail -f storage/logs/laravel.log
```

Look for any error messages related to:
- Database queries
- Authentication
- Authorization/role checks
- Database table structure

## Important Notes

- The `CheckRole` middleware is **case-sensitive** for role names
- Users must be properly seeded in the database before testing
- Authentication must complete successfully before role checking occurs
- Session data must be properly maintained across requests

## Advanced Debugging

If the diagnostic endpoints still show issues, add these to your `.env` file:

```
APP_DEBUG=true
LOG_LEVEL=debug
SESSION_DRIVER=cookie
```

Then check `storage/logs/laravel.log` for detailed error messages.

## Still Having Issues?

1. Verify PHP version: `php --version` (should be 8.0+)
2. Clear all caches: `php artisan config:clear && php artisan cache:clear`
3. Restart Laravel server: `php artisan serve`
4. Check browser cookies are enabled
5. Try a different browser or private/incognito mode
