# 🛒 Ecommerce Website — PHP & MySQL Project

This ecommerce platform allows customers to shop and manage their carts, while admins can manage product listings. Below are structured explanations for each part of the project.


 1. User Section

# 1.1. How can users register?
 
Users can create an account on the `register.php` page by providing their name, email, and password. Registration ensures they can manage their carts and make purchases.

# 1.2. How can users log in and log out?
- **Login:**  
Users log in from the `login.php` page using their email and password.
- **Logout:**  
Session is destroyed securely through `logout.php`, logging users out.

# 1.3. How can users view products?
 
All available products are displayed on the homepage (`index.php`) with name, price, description, and an image.

# 1.4. How can users add products to their cart?
 
On the homepage (`index.php`), users click "Add to Cart" on any product, which adds the item into their session-based cart.

# 1.5. How can users manage their cart?
 
On `cart.php`, users can:
- View added items
- Update item quantities
- Remove items
- See real-time total cost calculation

---

 2. Admin Section

# 2.1. How do admins log in and log out?
- **Login:**  
Admins can access `admin/login.php` with their credentials.
- **Logout:**  
Admins log out via `admin/logout.php`, terminating their session.

# 2.2. What is the admin dashboard?
 
`admin/dashboard.php` gives access to manage products — add, edit, and delete.

# 2.3. How do admins add products?
 
On `admin/add_product.php`, admins can upload product details:
- Name
- Price
- Description
- Product Image

# 2.4. How do admins manage products?
 
Using `admin/manage_products.php`, admins can:
- View all products in a table format
- Edit or delete any product directly

---

 3. Database Section

# 3.1. What does the users table do?
 
The `users` table stores:
- Full name
- Email address
- Hashed password
- Role field (to distinguish admin from regular users)

# 3.2. What does the products table do?
 
The `products` table includes:
- Product name
- Price
- Description
- Image filename

# 3.3. What does the cart (session-based) do?
 
Instead of a database table, the cart is managed via PHP sessions:
- Temporary storage of products
- Quantity management
- Real-time cost calculation

---

 4. Flow of the Website

# 4.1. What happens when a user registers?
 
The user inputs are validated, passwords are hashed, and data is inserted into the `users` table.

# 4.2. What happens when a user logs in?
 
Credentials are checked against the database, sessions are created to track logged-in status.

# 4.3. What happens when a product is added to the cart?
 
Product details are saved in a PHP session variable linked to the user session.

# 4.4. What happens when an admin adds a product?
 
The image is uploaded to the `uploads/` folder and the details are inserted into the `products` table.

# 4.5. What happens when an admin deletes a product?
 
The product record is removed from the `products` table and no longer shown on the homepage.

---

 5. Security Measures

# 5.1. How are passwords secured?
 
Passwords are hashed securely using PHP’s `password_hash()` function before storage.

# 5.2. How is session management handled?
 
Sessions (using `$_SESSION`) track both user and admin states, ensuring protected access to sensitive pages.

# 5.3. How is admin access secured?
 
The `users` table uses a role field. Admin functionalities are restricted to users with role "admin" only.

---

 6. How to Use This Guide

- Follow the user and admin sections to understand interactions.
- Follow database section to understand data storage.
- Follow the flow to understand action results.
- For more detail, explore the respective PHP files under the project folders (`admin/`, `pages/`, `includes/`, etc).

---

 📂 Folder Structure (Quick View)

```plaintext
ecommerce/
├── admin/              # Admin dashboard files
├── css/                # Stylesheets
├── images/             # Uploaded product images
├── includes/           # Reusable PHP functions like DB connection
├── pages/              # Cart, login, register pages
├── index.php           # Home page showing products
├── cart.php            # Shopping cart page
├── login.php           # User login
├── register.php        # User registration
├── logout.php          # User logout
├── README.md           # This documentation
└── test_db.php         # Test database connection


---

# ✨ Conclusion

This project covers core ecommerce functionality:  
Shopping Cart, Product Management, Secure Authentication, and a Minimal Admin Panel — all developed in PHP with MySQL database support.

