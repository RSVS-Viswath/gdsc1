<?php
include('db.php');

$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0; // Validate and get the category ID

$category_query = "SELECT category_name FROM categories WHERE id = $category_id";
$category_result = mysqli_query($conn, $category_query);
$category = mysqli_fetch_assoc($category_result);

if (!$category) {
    die("Category not found."); 
}

$blogs_query = "SELECT * FROM blogs WHERE category_id = $category_id";
$blogs_result = mysqli_query($conn, $blogs_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogs in Category: <?php echo htmlspecialchars($category['category_name']); ?></title>
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
                            <?php
                            $query = "SELECT * FROM categories";
                            $result = mysqli_query($conn, $query);
                            while($row = mysqli_fetch_assoc($result)) {
                                echo "<li><a class='dropdown-item text-white' href='category.php?id=" . $row['id'] . "'>" . htmlspecialchars($row['category_name']) . "</a></li>";
                            }
                            ?>
                        </ul>
                    </div>
                </li>
                <li class="nav-item"><a href="contact.php" class="nav-link text-white">Contact</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container my-5">
  <center>  <h2>Blogs in Category: <?php echo htmlspecialchars($category['category_name']); ?></h2><br></center>

    <?php if (mysqli_num_rows($blogs_result) > 0): ?>
        <?php while ($blog = mysqli_fetch_assoc($blogs_result)): ?>
            <div class="card mb-4 bg-secondary text-light">
                <div class="row g-0">
                    <div class="col-md-4" style="margin-bottom:20px;">
                        <img src="admin/uploads/<?php echo htmlspecialchars($blog['image']); ?>" class="img-fluid rounded-start" alt="Blog Image">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h4 class="card-title"><?php echo htmlspecialchars($blog['title']); ?></h4>
                            <p class="card-text"><?php echo substr(htmlspecialchars($blog['content']), 0, 200); ?>...</p>
                            <a href="blog.php?id=<?php echo $blog['id']; ?>" class="btn btn-primary">Read More</a>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <span class="">Category: <?php echo htmlspecialchars($category['category_name']); ?></span>
                            <span class=""><?php echo date('F j, Y', strtotime($blog['published_at'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No blogs found in this category.</p>
    <?php endif; ?>
</div>
<footer class="text-center p-4 bg-black">
        <p>© 2024 My Blog. All rights reserved.</p>
        <div>
            <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
        </div>
</footer>
</body>
</html>
