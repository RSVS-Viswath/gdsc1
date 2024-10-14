<?php
session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include('db.php');

// Check if the blog ID is provided
if (!isset($_GET['id'])) {
    header("Location: manage-blog.php");
    exit();
}

$blog_id = intval($_GET['id']);

// Fetch the existing blog post
$blog_query = "SELECT * FROM blogs WHERE id = $blog_id";
$blog_result = mysqli_query($conn, $blog_query);
$blog = mysqli_fetch_assoc($blog_result);

if (!$blog) {
    echo "<script>alert('Blog not found!'); window.location.href='manage-blog.php';</script>";
    exit();
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $category_id = intval($_POST['category_id']);
    $old_image = $blog['image']; // Preserve the old image name

    // Image upload handling
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_name = $_FILES['image']['name'];
        $image_path = 'uploads/' . basename($image_name);

        // Move the uploaded image to the target directory
        if (move_uploaded_file($image_tmp, $image_path)) {
            $old_image = $image_name; // Update to new image name if uploaded successfully
        }
    }

    // Update the blog record
    $update_query = "UPDATE blogs SET title = '$title', content = '$content', category_id = $category_id, image = '$old_image' WHERE id = $blog_id";
    
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Blog updated successfully!'); window.location.href='manage-blog.php';</script>";
    } else {
        echo "<script>alert('Error updating blog.');</script>";
    }
}

// Fetch all categories for the dropdown
$categories_query = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $categories_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
        body {
            margin: 0;
            padding-bottom: 60px; 
        }

        .sticky-header {
            position: -webkit-sticky; 
            position: sticky;
            top: 0;
            background-color: black;
            z-index: 1000; 
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        }

        .sticky-header img {
            max-width: 100%;
            height: auto;
            max-height: 60px; 
        }

        .fixed-bottom-menu {
            overflow: hidden;
            background: linear-gradient(to bottom right, #a3f7f2, #54f7ee, #ff6c03, #fa8e41);
            position: fixed;
            bottom: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            color: white; 
        }

        .fixed-bottom-menu ul {
            display: flex;
            padding: 0;
            margin: 0;
            list-style: none;
            width: 100%;
            justify-content: space-around;
        }

        .fixed-bottom-menu li {
            flex: 1;
            text-align: center;
        }

        .fixed-bottom-menu li a {
            display: block;
            color: white; 
            padding: 14px 16px;
            text-decoration: none;
            background-color: black; 
            font-size: 24px; 
        }

        .fixed-bottom-menu li a:hover {
            background-color: rgba(0, 0, 0, 0.8); 
        }

        .active {
            color: gold !important; 
        }
  a {
    color: hotpink;
    font-weight:600;
  }
  
  nav{
    float: none; 
    clear: both;
    width: 70%; 
    margin: 5% auto;
    
  }
  
  nav ul {
    list-style: none;
    margin: 0px;
    padding: 0px;
  }
  
  nav li{
    float: none; 
    width: 100%;
  }
  
  nav li a{
    display: block; 
    width: 100%; 
    padding: 20px; 
    border-left: 5px solid; 
    position: relative; 
    z-index: 2;
    text-decoration: none;
    color: red;
    box-sizing: border-box;  
    -moz-box-sizing: border-box;  
    -webkit-box-sizing: border-box; 
  }
    
  nav li a:hover{ border-bottom: 0px; color: #fff;}
    nav li:first-child a{ border-left: 10px solid #4169E1; }
    nav li:nth-child(2) a{ border-left: 10px solid #8B0000; }
    nav li:nth-child(3) a{ border-left: 10px solid #2F4F4F; }
    nav li:nth-child(4) a{ border-left: 10px solid #228B22; }
      nav li:nth-child(5) a{ border-left: 10px solid #4B0082; }
      nav li:nth-child(6) a{ border-left: 10px solid #FF8C00; }
      nav li:nth-child(7) a{ border-left: 10px solid #9400D3; }
      nav li:nth-child(8) a{ border-left: 10px solid #7400D3; }
       nav li:nth-child(9) a{ border-left: 10px solid #7470D3; }
    nav li:last-child a{ border-left: 10px solid #008080; }
    
    nav li a:after { 
      content: "";
      height: 100%; 
      left: 0; 
      top: 0; 
      width: 0px;  
      position: absolute; 
      transition: all 0.3s ease 0s; 
      -webkit-transition: all 0.3s ease 0s; 
      z-index: -1;
    }
    
    nav li a:hover:after{ width: 100%; }
    nav li:first-child a:after{ background: #4169E1; }
    nav li:nth-child(2) a:after{ background: #8B0000; }
    nav li:nth-child(3) a:after{ background: #2F4F4F; }
    nav li:nth-child(4) a:after{ background: #228B22; }
      nav li:nth-child(5) a:after{ background: #4B0082; }
      nav li:nth-child(6) a:after{ background: #FF8C00; }
      nav li:nth-child(7) a:after{ background: #9400D3; }
      nav li:nth-child(8) a:after{ background: #9700D3; }
       nav li:nth-child(9) a:after{ background: #9700D7; }
    nav li:last-child a:after{ background: #008080; }
  
  
  @font-face {
    font-family: 'Lato';
    font-style: normal;
    font-weight: 100;
    src: local('Lato Hairline'), local('Lato-Hairline'), url(http://themes.googleusercontent.com/static/fonts/lato/v6/boeCNmOCCh-EWFLSfVffDg.woff) format('woff');
  }
    </style>
</head>
<body class="bg-light">
<header class="sticky-header">
<h1 style="color:white;">Manage Blogs</h1>
</header>
<div class="content">

    <div class="container mt-5">
        <h2>Edit Blog Post</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($blog['title']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars($blog['content']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="" disabled>Select Category</option>
                    <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                        <option value="<?php echo $category['id']; ?>" <?php if ($category['id'] == $blog['category_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Upload New Image (Leave blank if not changing)</label>
                <input type="file" class="form-control" id="image" name="image">
                <small class="form-text text-muted">Current Image: <img src="uploads/<?php echo htmlspecialchars($blog['image']); ?>" alt="Current Blog Image" style="width: 100px; height: auto;"></small>
            </div>
            <button type="submit" class="btn btn-primary">Update Blog</button>
            <a href="manage-blog.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    </div>
<div class="fixed-bottom-menu">
<ul>
<li><a href="index.php" title="Home"><i class="fa fa-home"></i></a></li>
<li><a href="logout.php" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a></li>
</ul>
</div>
</body>
</html>
