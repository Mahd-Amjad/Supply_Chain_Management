# SC_Project Database Management System

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
  - [Prerequisites](#prerequisites)
  - [Setup Steps](#setup-steps)
- [License](#license)

## Overview

The **SC_Project Database Management System** is a comprehensive web-based application designed to manage various aspects of a supply chain, including inventory, orders, shipments, suppliers, customers, and more. Built with PHP and Microsoft SQL Server, this system facilitates efficient CRUD (Create, Read, Update, Delete) operations and supports complex queries for in-depth data analysis and reporting.

## Features

- **User Authentication:** Secure login and logout functionality to restrict access to authorized users.
- **CRUD Operations:** Perform Create, Read, Update, and Delete operations on multiple entities such as Suppliers, Customers, Orders, Products, etc.
- **Complex Queries:** Execute advanced queries for inventory management, financial reporting, customer relationship management (CRM), supplier performance, shipment tracking, forecasting, auditing, and data integrity.
- **Centralized Logging:** Log errors and system activities to a centralized log file for easier debugging and monitoring.
- **Responsive Design:** User-friendly interface styled with CSS for an enhanced user experience.

## Technologies Used

- **Frontend:**
  - HTML5
  - CSS3

- **Backend:**
  - PHP
  - Microsoft SQL Server (with ODBC Driver 17)

- **Others:**
  - ODBC Driver 17 for SQL Server
  - Apache Webserver (through xampp)

### Prerequisites

Before setting up the SC_Project Database Management System, ensure you have the following installed:

- **Web Server:** Apache, IIS, or any compatible web server.
- **PHP:** Version 7.4 or higher with SQL Server extensions enabled.
- **Microsoft SQL Server:** Ensure it's installed and running.
- **ODBC Driver 17 for SQL Server:** Required for PHP to communicate with SQL Server.
- **Browser:** Modern web browser (e.g., Chrome, Firefox, Edge).


### Setup Steps

1. **Clone the Repository:**

   `bash
   git clone https://github.com/yourusername/SC_Project.git `

1.  **Configure the Web Server:**

    -   Place the `SC_Project` folder in your web server's root directory (e.g., `htdocs` for XAMPP or `www` for WAMP).
    -   Ensure the web server has read and write permissions for the project files.
2.  **Set Up the Database:**

    -   Open Microsoft SQL Server Management Studio (SSMS).
    -   Create a new database named `SC_Project`.
    -   Execute the SQL scripts provided in the `SQL_Scripts` directory to create the necessary tables and relationships.
3.  **Configure Database Connection:**

    -   Open `PHP_Files/config.php`.
    -   Update the database connection parameters with your SQL Server credentials.

4.  **Set Up Logging:**

    -   Ensure the `logs` directory exists in the project root.
    -   Create an empty `error_log.txt` file inside the `logs` directory.
    -   Set appropriate write permissions for the web server to write to `logs/error_log.txt`.
5.  **Access the Application:**

    -   Open your web browser.
    -   Navigate to `http://localhost/SC_Project/HTML_Files/index.html` (adjust the URL based on your setup).
License
This project is licensed under the MIT License.

