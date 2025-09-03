Billing System - REST API for Invoicing

This project is a professional invoicing system developed in PHP and MySQL, with a layered architecture and JWT authentication.
It allows you to manage users, customers, products, invoices, and payments through a modular and secure REST API.
It includes route protection, role management, and is ready to integrate with any modern frontend (HTML, CSS, JS, Bootstrap, React, etc.).

Key Features:
CRUD for users, customers, and products.
Invoice and details management.
Payment registration and query.
Authentication and authorization with JWT.
Clean and scalable architecture.
Routes protected by middleware.
Easy integration with a separate frontend.
Docker-ready for production deployment.

-------------------------------------------------------------------------------------------------

Installation

1. Clone the repository:

git clone https://github.com/your-username/invoice-system.git
cd invoice-system 

2. Install dependencies with Composer:

Bash:
composer install

3. Configure environment variables:

DB_HOST=localhost
DB_NAME=invoice_system
DB_USER=root
DB_PASS=
JWT_SECRET=your_secret_key

4. Import the database schema:

Import the SQL file in your MySQL server (see /database/schema.sql or use the SQL provided in the documentation).

5. Run the project locally: 

A. php -S localhost:8080 -t public

B. docker-compose up

6. Access the API:

The API will be available at http://localhost:8080/

------------------------------------------------------------------------------------------------

Example Usage (in postman):
Login (Get JWT Token)

POST /users/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "admin123"
}

Get All Customers:
GET /customers
Authorization: Bearer YOUR_JWT_TOKEN


Create a Product:
POST /products
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json

{
  "name": "Laptop Dell",
  "description": "Dell Inspiron 15",
  "price": 750.00,
  "stock": 10
}


Update a Customer:
PUT /customers/1
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json

{
  "name": "Updated Name",
  "email": "updated@email.com",
  "phone": "555987654",
  "address": "New Address"
}

Delete a Product:
DELETE /products/1
Authorization: Bearer YOUR_JWT_TOKEN