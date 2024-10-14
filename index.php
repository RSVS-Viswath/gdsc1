<?php
include('db.php');

// Fetch blogs and their categories
$query = "SELECT blogs.*, categories.category_name FROM blogs 
          LEFT JOIN categories ON blogs.category_id = categories.id";
$result = mysqli_query($conn, $query);

// Check for query errors
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

// Fetch categories for dropdown
$categories_query = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $categories_query);
if (!$categories_result) {
    die("Database query for categories failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to My Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-dark text-light">
    <header class="p-3 bg-black">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <a href="index.php" class="text-white fs-3">My Blog Logo</a>
            </div>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a href="index.php" class="nav-link text-white">Home</a></li>
                    <li class="nav-item">
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Categories
                            </a>
                            <ul class="dropdown-menu bg-dark text-white">
                                <?php while($row = mysqli_fetch_assoc($categories_result)): ?>
                                    <li><a class='dropdown-item text-white' href='category.php?id=<?php echo $row['id']; ?>'><?php echo $row['category_name']; ?></a></li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item"><a href="admin/index.php" class="nav-link text-white">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container my-5">
        <h1 class="text-center">Welcome to My Blog</h1><br>
      
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="card mb-4 bg-secondary text-light">
                <div class="row g-0">
                    <div class="col-md-4" style="margin-bottom:20px;">
                        <img width="250" src="admin/uploads/<?php echo $row['image']; ?>" class="card-img-top" alt="<?php echo $row['title']; ?>">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['title']; ?></h5>
                            <p class="card-text"><?php echo substr($row['content'], 0, 150); ?>...</p>
                            <a href="blog.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Read More</a>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <span class="">Category: <?php echo $row['category_name']; ?></span>
                            <span class=""><?php echo date('F j, Y', strtotime($row['published_at'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div> <!-- Closing div for container -->

    <footer class="text-center p-4 bg-black">
        <p>© 2024 My Blog. All rights reserved.</p>
        <div>
            <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
