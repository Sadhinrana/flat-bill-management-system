# Multi-Tenant Flat & Bill Management System

A comprehensive Laravel-based system for managing buildings, flats, tenants, and bills with multi-tenant data isolation.

## Features

### User Roles & Permissions

#### Admin (Super Admin)
- Can create and manage House Owners
- Can create Tenants
- Can view Tenant details
- Can remove tenants
- Can assign tenants to buildings
- Can access all data across all buildings

#### House Owner
- Can create Flats in their building
- Can manage flat details (flat number, flat owner details)
- Can create Bill Categories (Electricity, Gas bill, Water bill, Utility Charges)
- Can create Bills for flats
- Can add due amounts if a flat hasn't paid previous bills
- Receives email notifications when:
  - A new bill is created
  - A bill is paid
- Can only access their own building and related data

### Multi-Tenant Isolation
- House Owners cannot see other owners' buildings, flats, tenants, or bills
- Data isolation enforced at the query and middleware level
- Column-based tenant identification using `building_id`

### Core Functionality
- **Buildings Management**: Create, update, delete buildings
- **Flats Management**: Manage flats with owner details
- **Tenant Management**: Assign tenants to buildings and flats
- **Bill Categories**: Create custom bill categories per building
- **Bill Management**: Create bills with due amounts and status tracking
- **Email Notifications**: Automated notifications for bill creation and payment

## Technical Stack

- **Backend**: Laravel 12.x
- **Frontend**: Tailwind CSS (included with Laravel)
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel's built-in authentication
- **Email**: Laravel Mail with customizable templates

## Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL/PostgreSQL
- Node.js & NPM (for asset compilation)

### Step 1: Clone and Install Dependencies

```bash
git clone <repository-url>
cd multi-tenant-flat-bill-management-system
composer install
```

### Step 2: Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Update your `.env` file with database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=flat_bill_management
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Flat & Bill Management"
```

### Step 3: Database Setup

```bash
php artisan migrate
php artisan db:seed
```

### Step 4: Asset Compilation

```bash
npm install
npm run dev
```

For production:
```bash
npm run build
```

### Step 5: Start the Application

```bash
php artisan serve
```

Visit `http://localhost:8000` to access the application.

## Default Login Credentials

After running the seeders, you can use these credentials:

### Admin User
- **Email**: admin@example.com
- **Password**: password
- **Role**: Admin

### House Owner Users
- **Email**: john@example.com
- **Password**: password
- **Role**: House Owner

- **Email**: sarah@example.com
- **Password**: password
- **Role**: House Owner

- **Email**: mike@example.com
- **Password**: password
- **Role**: House Owner

## Database Structure

### Tables Overview

1. **users** - User accounts with roles (admin, house_owner)
2. **buildings** - Building information owned by house owners
3. **flats** - Individual flats within buildings
4. **tenants** - Tenant information assigned to buildings/flats
5. **bill_categories** - Bill categories per building (Electricity, Gas, etc.)
6. **bills** - Individual bills with amounts, status, and due tracking

### Key Relationships

- Users (house_owner) → Buildings (one-to-many)
- Buildings → Flats (one-to-many)
- Buildings → Tenants (one-to-many)
- Buildings → Bill Categories (one-to-many)
- Buildings → Bills (one-to-many)
- Flats → Bills (one-to-many)
- Bill Categories → Bills (one-to-many)

## Multi-Tenant Implementation

### Data Isolation Strategy

The system uses **column-based tenant identification** with the following approach:

1. **Building ID Scoping**: All tenant-specific data includes a `building_id` column
2. **Middleware Protection**: `MultiTenantMiddleware` ensures users only access their data
3. **Model Scopes**: Eloquent scopes automatically filter data by building ownership
4. **Controller Authorization**: Each controller method checks user permissions

### Security Features

- Role-based access control
- Data isolation at query level
- Authorization checks in all controllers
- CSRF protection on all forms
- SQL injection prevention through Eloquent ORM

## API Endpoints

### Authentication
- `GET /login` - Login form
- `POST /login` - Process login
- `POST /logout` - Logout user
- `GET /register` - Registration form
- `POST /register` - Process registration

### Protected Routes (require authentication)
- `GET /dashboard` - Dashboard (role-specific)
- `GET /buildings` - List buildings
- `POST /buildings` - Create building
- `GET /buildings/{id}` - Show building
- `PUT /buildings/{id}` - Update building
- `DELETE /buildings/{id}` - Delete building

Similar CRUD endpoints exist for:
- `/flats`
- `/tenants`
- `/bill-categories`
- `/bills`

## Email Notifications

### Bill Created Notification
- Sent to flat owner when a new bill is created
- Includes bill details, amount, and due date
- Template: `resources/views/emails/bill-created.blade.php`

### Bill Paid Notification
- Sent to flat owner when bill status changes to paid
- Includes payment confirmation details
- Template: `resources/views/emails/bill-paid.blade.php`

## Performance Optimizations

### Database Optimizations
- Proper indexing on frequently queried columns
- Eager loading relationships to prevent N+1 queries
- Optimized queries with specific column selection
- Database constraints for data integrity

### Application Optimizations
- Efficient Eloquent relationships
- Caching for frequently accessed data
- Optimized Blade templates
- Minimal database queries per request

## Development Notes

### Code Structure
- **Models**: Located in `app/Models/` with proper relationships and scopes
- **Controllers**: Resource controllers with authorization checks
- **Middleware**: Custom middleware for multi-tenant isolation
- **Views**: Tailwind CSS-based responsive UI
- **Mail**: Customizable email templates

### Design Decisions
1. **Multi-tenancy**: Column-based approach for simplicity and performance
2. **Authentication**: Laravel's built-in system with role-based access
3. **UI Framework**: Tailwind CSS for modern, utility-first styling and responsive design
4. **Email System**: Laravel Mail with HTML templates
5. **Database**: MySQL/PostgreSQL with proper normalization

## Troubleshooting

### Common Issues

1. **Authentication Errors**
   - Ensure users have proper roles assigned
   - Check middleware registration in `bootstrap/app.php`

2. **Database Connection Issues**
   - Verify database credentials in `.env`
   - Ensure database exists and is accessible

3. **Email Notifications Not Working**
   - Check SMTP settings in `.env`
   - Verify mail configuration in `config/mail.php`

4. **Permission Denied Errors**
   - Ensure users have proper building assignments
   - Check multi-tenant middleware is working correctly

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support and questions, please contact the development team or create an issue in the repository.