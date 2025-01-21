# Inventory Management System

A comprehensive inventory management system built with CodeIgniter 4.

## Features

- User Authentication (Login/Register)
- Product Management (CRUD operations)
- Category Management
- Transaction Tracking (Inbound/Outbound)
- Stock Management
- REST API
- Real-time Stock Updates
- CSV Import/Export
- Mobile-Friendly Interface

## Installation

1. Clone the repository
```bash
git clone https://github.com/Mugen-code/Inventory-manager.git
```
2. Configure your database settings in either:
   - `.env` file (recommended)
   - or `app/Config/Database.php`
3. Import the database from `sql/database.sql`
4. Access the site through: http://localhost/inventory_manager/public/

## Environment Setup

1. Create a `.env` file in the root directory
2. Add these configuration settings to your `.env`:
```
#ENVIROMENT
CI_ENVIRONMENT = development

#APP
app.baseURL = 'http://localhost:8080'

#DATABASE
database.default.hostname = localhost
database.default.database = inventory_manager
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306

#JWT
JWT_SECRET = '3d7c6f59a8b4e2d1c9f0a5b8e4d2c1f9a8b4e2d1c9f0a5b8e4d2c1f9a8b4e2d1'

#SESSION
session.savePath = writable/sessions
```
4. Make sure the database settings match your local MySQL configuration
5. The JWT_SECRET is required for API authentication
