# 💰 Expencify - Personal Expense & Income Tracker

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-blue.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/mysql-%3E%3D%205.7-orange.svg)](https://www.mysql.com/)

**Expencify** is a sleek, modern, and powerful web-based application designed to help you take full control of your personal finances. With intuitive dashboards, real-time analytics, and comprehensive reporting, tracking your expenses and income has never been easier.

---

## 🚀 Key Features

- **📊 Comprehensive Dashboard**: Get an immediate overview of your current month's expenses, income, and savings, along with historical performance charts.
- **📈 Advanced Analytics**: Visualize your spending habits with dynamic pie charts (category-wise breakdown) and line charts (daily trends).
- **📂 Category Management**: Organize your transactions with customizable categories, complete with an integrated icon picker.
- **💸 Expense & Income Tracking**: Easily add, edit, and delete transactions with detailed metadata.
- **📄 Detailed Reports**: Generate and view detailed financial reports to understand your long-term financial health.
- **👤 User Profiles**: Manage your personal information and profile settings for a personalized experience.
- **📱 Fully Responsive**: Optimized for all devices—from desktop monitors to mobile screens.
- **🔒 Secure Authentication**: Robust login and registration system to keep your financial data private.

---

## 🛠️ Technology Stack

- **Backend**: [PHP](https://www.php.net/) (Vanilla)
- **Database**: [MySQL](https://www.mysql.com/)
- **Frontend**: 
  - [Bootstrap 5](https://getbootstrap.com/) (Responsive UI)
  - [Bootstrap Icons](https://icons.getbootstrap.com/)
  - [ApexCharts](https://apexcharts.com/) (Interaction Line/Area Charts)
  - [ECharts](https://echarts.apache.org/) (Interactive Bar/Pie Charts)
  - [Simple Datatables](https://fiduswriter.github.io/Simple-DataTables/)

---

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **Web Server**: Apache or Nginx (e.g., [XAMPP](https://www.apachefriends.org/), [MAMP](https://www.mamp.info/), or [WAMP](https://www.wampserver.com/en/))
- **PHP**: version 7.4 or higher
- **Database**: MySQL 5.7 or higher

---

## ⚙️ Installation & Setup

Follow these steps to get Expencify running locally:

### 1. Clone the Repository
```bash
git clone https://github.com/tjana17/expense-tracker-web.git
cd expense-tracker-web
```

### 2. Database Configuration
1. Create a new database in your MySQL environment (e.g., via phpMyAdmin) named `expense_tracker`.
2. Import the database schema (look for a `.sql` file if available, or initialize manually using the structure shown in project files).
3. Update the database connection settings in `config/db.php`:

```php
// config/db.php
$conn = mysqli_connect("localhost", "your_username", "your_password", "expense_tracker");
```

### 3. Run the Application
1. Move the project folder to your server's root directory (e.g., `htdocs` for XAMPP/MAMP).
2. Start your Apache and MySQL services.
3. Open your browser and navigate to `http://localhost/expense-tracker-web`.

---

## 📁 Project Structure

```text
├── api/                # API endpoints for dynamic data
├── assets/             # CSS, JS, Images, and Vendor libraries
├── auth/               # Login, Register, and Logout logic
├── categories/         # Category management modules
├── config/             # Database connection settings
├── expenses/           # Expense tracking modules
├── income/             # Income tracking modules
├── analytics.php       # Data visualization page
├── header.php          # Shared header and navigation
├── footer.php          # Shared footer
├── index.php           # Main dashboard
├── profile.php         # User profile management
└── reports.php         # Financial reporting page
```

---

## 🤝 Contributing

Contributions are what make the open-source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

---

## 📧 Contact

Your Name - [@tjana17](https://github.com/tjana17) - jana@qpaymentz.com

Project Link: [Live Project Link](http://jk-expensify.infinityfree.me/)
