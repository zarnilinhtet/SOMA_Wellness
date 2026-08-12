<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [

            // Dashboard
            'dashboard.view',

            // Classes
            'classes.view',
            'classes.manage',

            // Class Schedules
            'class_schedules.view',
            'class_schedules.manage',

            // Bookings
            'bookings.view',
            'bookings.manage',

            // Waiting Lists
            'waitlists.view',
            'waitlists.manage',

            // Attendance
            'attendance.view',
            'attendance.record',
            'attendance.manage',

            // Attendance Records
            'attendance_records.view',

            'reports.view',
            // Instructor Report
            'instructor_reports.view',
            'instructor_reports.record',

            // Customer Report
            'customer_reports.view',

            // Instructors
            'instructors.view',
            'instructors.manage',

            // Categories
            'categories.view',
            'categories.manage',

            // Packages
            'packages.view',
            'packages.manage',

            // Users
            'users.view',
            'users.manage',

            // Payments
            'payments.view',
            'payments.manage',

            // Transactions
            'transactions.view',
            'transactions.manage',

            // Close Dates
            'close_dates.view',
            'close_dates.manage',

            // Gallery
            'gallery.view',
            'gallery.manage',

            // Workshops
            'workshops.view',
            'workshops.manage',

            // Comments
            'comments.view',
            'comments.manage',
        ];


        /*
        |--------------------------------------------------------------------------
        | Create Permissions
        |--------------------------------------------------------------------------
        */

        foreach ($permissions as $permission) {

            Permission::firstOrCreate(
                [
                    'name' => $permission,
                    'guard_name' => 'web',
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $admin = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        $instructor = Role::firstOrCreate([
            'name' => 'Instructor',
            'guard_name' => 'web',
        ]);

        $receptionist = Role::firstOrCreate([
            'name' => 'Receptionist',
            'guard_name' => 'web',
        ]);

        $customer = Role::firstOrCreate([
            'name' => 'Customer',
            'guard_name' => 'web',
        ]);


        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        |
        | Admin can access everything.
        |
        */

        $admin->syncPermissions($permissions);


        /*
        |--------------------------------------------------------------------------
        | INSTRUCTOR
        |--------------------------------------------------------------------------
        |
        | Instructor can:
        | - View dashboard
        | - View classes
        | - View schedules
        | - View bookings
        | - View waiting lists
        | - Record attendance
        | - View attendance records
        | - Work with instructor reports
        |
        */

        $instructor->syncPermissions([

            // Dashboard
            'dashboard.view',

            // Classes
            'classes.view',

            // Class Schedules
            'class_schedules.view',

            // Bookings
            'bookings.view',

            // Waiting Lists
            'waitlists.view',

            // Attendance
            'attendance.view',
            'attendance.record',

            // Attendance Records
            'attendance_records.view',

            // Instructor Reports
            'instructor_reports.view',
            'instructor_reports.record',
        ]);


        /*
        |--------------------------------------------------------------------------
        | RECEPTIONIST
        |--------------------------------------------------------------------------
        |
        | Receptionist is similar to Admin,
        | but does NOT have access to:
        |
        | - Payments
        | - Transactions
        | - Reports
        | - Close Dates
        | - Gallery
        | - Comments
        |
        */

        $receptionist->syncPermissions([

            // Dashboard
            'dashboard.view',

            // Classes
            'classes.view',

            // Class Schedules
            'class_schedules.view',
            'class_schedules.manage',

            // Close Dates
            'close_dates.view',
            'close_dates.manage',

            // Gallery
            'gallery.view',
            'gallery.manage',

            // Workshops
            'workshops.view',
            'workshops.manage',

            // Comments
            'comments.view',
            'comments.manage',

            // Bookings
            'bookings.view',
            'bookings.manage',

            // Waiting Lists
            'waitlists.view',
            'waitlists.manage',

            // Attendance
            'attendance.view',
            'attendance.record',

            // Attendance Records
            'attendance_records.view',

            // Instructors
            'instructors.view',
            'instructors.manage',

            // Categories
            'categories.view',
            'categories.manage',

            // Packages
            'packages.view',
            'packages.manage',

            // Users
            'users.view',
            'users.manage',

            // Workshops
            'workshops.view',
            'workshops.manage',
        ]);


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        |
        | Customer permissions can be added later.
        |
        */

        $customer->syncPermissions([]);
    }
}