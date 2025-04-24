<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0; // Get product ID, sanitize
if ($product_id <= 0) {
    echo "Invalid product ID.";
    exit; // Or redirect to manage_products.php
}

// Fetch the product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Product not found.";
    exit; // Or redirect
}

$upload_errors = [];
$success_message = "";

if (isset($_POST['update_product'])) {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);

    // Validate inputs (similar to add_product.php)
    if (empty($name)) {
        $upload_errors[] = "Product name is required.";
    }
    if ($price <= 0) {
        $upload_errors[] = "Price must be greater than 0.";
    }
    if (empty($description)) {
        $upload_errors[] = "Description is required.";
    }

    // File upload handling (optional: only if a new image is uploaded)
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        if (in_array($file_type, $allowed_types)) {
            $upload_dir = "../images/";
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_file_name = uniqid('product_', true) . '.' . $file_ext; // Unique name
            $upload_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = $new_file_name; // Store the new filename
            } else {
                $upload_errors[] = "Failed to upload image.";
                $image = $product['image']; //Revert to old image
            }
        } else {
            $upload_errors[] = "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
            $image = $product['image']; //Revert to old image
        }
    } else {
        $image = $product['image']; // Keep the old image if no new one was uploaded
    }


    if (empty($upload_errors)) {
        // Update product details
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $price, $description, $image, $product_id]);

        $success_message = "Product updated successfully!";

        // Refresh product data after update
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?"); // Use a separate query to fetch updated data
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, textarea {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
            text-align: center;
        }
        .img-preview {
            max-width: 100px;
            max-height: 100px;
            margin-bottom: 10px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Product</h2>

        <?php if (!empty($upload_errors)): ?>
            <div class="error">
                <?php foreach ($upload_errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="success">
                <p><?= htmlspecialchars($success_message) ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="name">Product Name:</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($product['name']); ?>" required>

            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" id="price" value="<?= htmlspecialchars($product['price']); ?>" required>

            <label for="description">Description:</label>
            <textarea name="description" id="description" required><?= htmlspecialchars($product['description']); ?></textarea>

            <label for="image">Image:</label>
            <?php if (!empty($product['image'])): ?>
                <img src="../images/<?= htmlspecialchars($product['image']); ?>" alt="Current Image" class="img-preview">
            <?php endif; ?>
            <input type="file" name="image" id="image">
            <small>Leave blank to keep the current image.</small>

            <button type="submit" name="update_product">Update Product</button>
        </form>
        <a href="manage_products.php" class="back-link">Back to Manage Products</a>
    </div>
</body>
</html>