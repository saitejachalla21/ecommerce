<?php
session_start();
include('../includes/db.php');

// Function to get product details
function getProductDetails($conn, $productId) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart (This part seems to be working, so I'll leave it as is)
if (isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity']++; // Increase quantity if product exists
    } else {
        $_SESSION['cart'][$productId] = ['quantity' => 1]; // Add new product to cart
    }
    header("Location: ../index.php"); // Redirect back to product page
    exit();
}

// Handle Remove from Cart
if (isset($_POST['remove_from_cart'])) {
    $productIdToRemove = $_POST['product_id_to_remove']; // Get the specific product ID to remove
    if (isset($_SESSION['cart'][$productIdToRemove])) {
        unset($_SESSION['cart'][$productIdToRemove]);
    }
    header("Location: cart.php"); // Refresh cart page
    exit();
}

// Handle Quantity Update
if (isset($_POST['update_quantity'])) {
    foreach ($_POST['quantity'] as $productId => $quantity) {
        if ($quantity > 0) {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        } else {
            unset($_SESSION['cart'][$productId]); // Remove if quantity is 0 or less
        }
    }
    header("Location: cart.php"); // Refresh cart page
    exit();
}

// Handle Proceed to Checkout
if (isset($_POST['checkout'])) {
    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php"); // Redirect to login page if not logged in
        exit();
    } else {
        // If logged in, proceed to checkout (you'll need to create this page)
        header("Location: checkout.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .cart-container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .cart-table th, .cart-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .cart-table th {
            background-color: #f2f2f2;
        }
        .cart-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        .quantity-input {
            width: 60px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .update-button, .remove-button {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }
        .remove-button {
            background-color: #d9534f;
        }
        .total-price {
            font-size: 1.2em;
            font-weight: bold;
            text-align: right;
        }
        .empty-cart {
            text-align: center;
            color: #777;
        }
        .checkout-button {
            background-color: #008CBA; /* Blue */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .checkout-button:hover {
            background-color: #0077a3;
        }
        .cart-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <h1>Shopping Cart</h1>
            <nav>
                <a href="../index.php">Continue Shopping</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="POST" action="../pages/logout.php" style="display: inline;">
                        <button type="submit" class="logout-button">Logout</button>
                    </form>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <div class="main-container">
        <main class="cart-container">
            <?php if (empty($_SESSION['cart'])): ?>
                <p class="empty-cart">Your cart is empty.</p>
            <?php else: ?>
                <form method="POST">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_cost = 0;
                            foreach ($_SESSION['cart'] as $productId => $item):
                                $product = getProductDetails($conn, $productId);
                                if ($product): // Ensure product exists
                                    $subtotal = $product['price'] * $item['quantity'];
                                    $total_cost += $subtotal;
                            ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center;">
                                            <img src="../images/<?= htmlspecialchars($product['image'] ?? 'default.png') ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="cart-image">
                                            <span style="margin-left: 10px;"><?= htmlspecialchars($product['name']) ?></span>
                                        </div>
                                    </td>
                                    <td>$<?= number_format($product['price'], 2) ?></td>
                                    <td>
                                        <input type="number" name="quantity[<?= $productId ?>]" value="<?= $item['quantity'] ?>" min="1" class="quantity-input">
                                    </td>
                                    <td>$<?= number_format($subtotal, 2) ?></td>
                                    <td>
                                        <button type="submit" name="update_quantity" class="update-button">Update</button>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="product_id_to_remove" value="<?= $productId ?>">
                                            <button type="submit" name="remove_from_cart" value="Remove" class="remove-button">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php
                                endif;
                            endforeach;
                            ?>
                            <tr>
                                <td colspan="3" class="total-price">Total:</td>
                                <td colspan="2" class="total-price">$<?= number_format($total_cost, 2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="cart-actions">
                        <button type="submit" name="update_quantity" class="update-button" style="width: auto;">Update Cart</button>
                        <form method="POST" style="display: inline;">
                            <button type="submit" name="checkout" class="checkout-button">Proceed to Checkout</button>
                        </form>
                    </div>
                </form>
            <?php endif; ?>
        </main>
    </div>
    <footer>
        <p>&copy; <?= date('Y'); ?> Online Store. All rights reserved.</p>
    </footer>
</body>
</html>

