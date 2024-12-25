# Employee Shift Management System

## ShiftNester is an Automatic Employee Shift Management System that is designed to streamline the process of creating, assigning, and managing employee shifts. It caters to organizations with multiple departments and roles, allowing for efficient scheduling while considering employee preferences and introduces the idea of a open shift marketplace system.

## Table of Contents
1. [Installation](#installation)
2. [System Overview](#system-overview)
3. [Developer Documentation](#developer-documentation)
4. [User Documentation](#user-documentation)
   - [Admin Guide](#admin-guide)
   - [Employee Guide](#employee-guide)

## Installation

### Prerequisites
- PHP 8.2+
- Composer
- Node.js and npm
- MySQL or PostgreSQL

### Steps

1. Clone the repository:
   ```
   git clone https://github.com/your-repo/employee-shift-management.git
   ```

2. Install PHP dependencies:
   ```
   composer install
   ```

3. Install JavaScript dependencies:
   ```
   npm install
   ```

4. Create a copy of the .env file:
   ```
   cp .env.example .env
   ```

5. Generate an application key:
   ```
   php artisan key:generate
   ```

6. Configure your database in the .env file:
   ```
   DB_CONNECTION=sqlite
   ```

7. Run database migrations:
   ```
   php artisan migrate
   ```

8. Seed the database (optional):
   ```
   php artisan db:seed
   ```

9. Compile assets:
   ```
   npm run dev
   ```

10. Start the development server:
    ```
    php artisan serve
    ```

The application should now be accessible at `http://localhost:8000`.

## System Overview

The Employee Shift Management System is designed to streamline the process of creating, assigning, and managing employee shifts. It caters to organizations with multiple departments and roles, allowing for efficient scheduling while considering employee preferences and skills.

Key Features:
- User authentication with role-based access (Admin/Employee)
- Department and designation management
- Employee profile management including skills
- Shift creation with specific requirements
- Employee shift preference setting
- Automated roster generation
- Manual roster adjustment
- Shift publishing system

The system aims to balance employee preferences, required skills, and fair distribution of shifts to create optimal schedules.

## Developer Documentation

### Project Structure

The project follows the standard Laravel structure with some additional custom components:

- `app/Http/Controllers`: Contains all controllers
- `app/Models`: Contains Eloquent models
- `app/Http/Middleware`: Contains custom middleware
- `database/migrations`: Contains database migrations
- `resources/views`: Contains Blade view files
- `routes/web.php`: Defines web routes

### Key Components

1. Models:
   - `User`: User accounts
   - `Employee`: Employee profiles
   - `Department`: Departments
   - `Designation`: Job designations
   - `Shift`: Individual shifts
   - `ShiftPreference`: Employee shift preferences
   - `Skill`: Employee skills

2. Controllers:
   - `AdminController`: Admin-specific actions
   - `EmployeeController`: Employee-related operations
   - `ShiftController`: Shift management
   - `ShiftPreferenceController`: Shift preference handling
   - `DepartmentController`: Department management
   - `DesignationController`: Designation management

3. Middleware:
   - `AdminMiddleware`: Admin route protection
   - `EmployeeMiddleware`: Employee route protection

4. Key Processes:
   - Shift Creation: `ShiftController@store`
   - Preference Setting: `ShiftPreferenceController@store`
   - Roster Generation: `AdminController@generateRoster`
   - Shift Publishing: `AdminController@publishShifts`

### Extending the System

To add new features or modify existing ones:

1. Create/modify models in `app/Models`
2. Update controllers in `app/Http/Controllers`
3. Add routes in `routes/web.php`
4. Create/modify views in `resources/views`
5. Add database changes via migrations

Follow Laravel best practices and maintain consistent code structure.

## User Documentation

### Admin Guide

1. Dashboard
   - Access: Login with admin credentials
   - Features: Overview of system status, quick links to main functions

2. Department Management
   - Access: Navigate to "Departments" in the admin menu
   - Features:
     - View all departments
     - Add new department
     - Edit existing department
     - Delete department (if not associated with employees/shifts)

3. Designation Management
   - Access: Navigate to "Designations" in the admin menu
   - Features:
     - View all designations
     - Add new designation (associate with department)
     - Edit existing designation
     - Delete designation (if not associated with employees)

4. Employee Management
   - Access: Navigate to "Employees" in the admin menu
   - Features:
     - View all employees
     - Add new employee
       - Set name, email, department, designation
       - Assign skills
     - Edit employee details
     - Deactivate/reactivate employee

5. Skill Management
   - Access: Navigate to "Skills" in the admin menu
   - Features:
     - View all skills
     - Add new skill
     - Edit skill
     - Delete unused skills

6. Shift Creation
   - Access: Navigate to "Create Shift" in the admin menu
   - Features:
     - Set shift date and time
     - Specify required number of employees
     - Set department and designation requirements
     - Add skill requirements

7. Roster Generation
   - Access: Navigate to "Generate Roster" in the admin menu
   - Features:
     - Select date range for roster
     - Generate roster based on preferences and requirements
     - View generated roster
     - Make manual adjustments if necessary

8. Shift Publishing
   - Access: From the generated roster page
   - Features:
     - Review final roster
     - Publish roster to make it visible to employees

9. Reports
   - Access: Navigate to "Reports" in the admin menu
   - Features:
     - View shift coverage statistics
     - Employee preference fulfillment rates
     - Department-wise shift distribution

### Employee Guide

1. Dashboard
   - Access: Login with employee credentials
   - Features: View upcoming shifts, quick links to main functions

2. Profile Management
   - Access: Click on profile icon or "My Profile" in the menu
   - Features:
     - View personal information
     - Update contact details
     - View assigned department and designation

3. Skill Management
   - Access: Navigate to "My Skills" in the employee menu
   - Features:
     - View current skills
     - Request addition of new skills (subject to admin approval)

4. Shift Preference Setting
   - Access: Navigate to "Set Shift Preferences" in the employee menu
   - Features:
     - View available shifts for preference setting
     - Set preference level for each shift (e.g., 1 - Most Preferred, 3 - Least Preferred)
     - Update preferences before roster generation deadline

5. View Assigned Shifts
   - Access: Navigate to "My Shifts" in the employee menu
   - Features:
     - View all assigned shifts
     - Filter shifts by date range
     - See shift details (time, department, required skills)

6. Shift Swapping (if enabled)
   - Access: From "My Shifts" page
   - Features:
     - Request to swap shift with another employee
     - View swap requests from others
     - Accept/reject swap requests

7. Time-Off Requests
   - Access: Navigate to "Time-Off Requests" in the employee menu
   - Features:
     - Submit time-off requests
     - View status of submitted requests
     - Cancel pending requests

8. Notifications
   - Access: Click on notification icon in the top menu
   - Features:
     - Receive alerts for new shift assignments
     - Get notified of approved time-off requests
     - See reminders for upcoming shifts

This comprehensive guide covers the installation process, system overview, developer documentation, and detailed user guides for both administrators and employees. Each feature is explained with access instructions and available functionalities, providing a clear understanding of the Employee Shift Management System.
