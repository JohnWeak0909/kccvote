# Online Voting System

A secure, campus-restricted, and responsive Online Voting System built with PHP, MySQL, JavaScript, and CSS.

## Features

- **User Roles**: Admin and Student Voter.
- **Student Registration**: Requires Student ID, personal details, and account credentials.
- **Strict Enforcement**: One account per Student ID and one vote per election.
- **Campus Restriction**: Voting is only accessible within the school campus (IP-based validation).
- **Admin Panel**: Manage elections, positions, parties, candidates, and students.
- **Real-time Results**: Automatic vote counting and result visualization.
- **Responsive Design**: Fully functional on desktop, tablet, and mobile devices.

## Project Structure

- `/public/`: Main web-accessible pages (voting dashboard, login, register).
- `/admin/`: Admin panel pages (dashboard, management, results).
- `/assets/`: CSS and JavaScript files.
- `/includes/`: Core logic (database connection, authentication, campus check).
- `/scripts/`: Utility and testing scripts.
- `/database/`: Database schema and setup files.
- `index.php`: Root redirect to public folder.
- `.htaccess`: URL rewriting for proper routing.

## Setup Instructions

1. **Database Setup**:
   - Create a MySQL database named `school_voting`.
   - Import `database/schema.sql` to set up the tables.
2. **Configuration**:
   - Update `includes/db.php` with your database credentials.
3. **Campus Restriction**:
   - Add authorized campus IP addresses to the `campus_ips` table.
   - By default, `127.0.0.1` is allowed for development.
4. **Admin Access**:
   - Default Admin Username: `admin`
   - Default Admin Password: `admin123`

## Security Measures

- **Password Hashing**: Uses `password_hash()` for secure storage.
- **SQL Injection Prevention**: Uses PDO prepared statements for all queries.
- **XSS Protection**: Sanitizes user input before rendering.
- **Session Security**: Implements session-based authentication.
- **Database Constraints**: Unique keys prevent duplicate accounts and multiple votes.
