<?php
session_start(); 

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include('db.php');

// Handle the blog submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $category_id = $_POST['category_id'];
    $content = $_POST['content'];
    $published_at = $_POST['published_at'];

    // Handle image upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        echo "<script>alert('File is not an image.');</script>";
        $uploadOk = 0;
    }

    // Check file size (limit to 5MB)
    if ($_FILES["image"]["size"] > 5000000) {
        echo "<script>alert('Sorry, your file is too large.');</script>";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Use prepared statement for database insertion
            $insert_query = "INSERT INTO blogs (title, category_id, content, image, published_at) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, "sisss", $title, $category_id, $content, basename($_FILES["image"]["name"]), $published_at);
            
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Blog added successfully!');</script>";
            } else {
                echo "<script>alert('Error adding blog: " . mysqli_error($conn) . "');</script>";
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
        }
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
    <title>Admin - Manage Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
    <h1 style="color:white;">Logo</h1>
</header>
<div class="content">     
<div class="container mt-5">
    <h2>Add New Blog</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Blog Title</label>
            <input type="text" class="form-control" name="title" id="title" required>
        </div>
        <div class="mb-3">
            <label for="category_id" class="form-label">Category</label>
            <select class="form-select" name="category_id" id="category_id" required>
                <option value="">Select Category</option>
                <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea class="form-control" name="content" id="content" rows="5" required></textarea>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            <input type="file" class="form-control" name="image" id="image" accept="image/*" required>
        </div>
        <div class="mb-3">
            <label for="published_at" class="form-label">Published At</label>
            <input type="datetime-local" class="form-control" name="published_at" id="published_at" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Blog</button>
    </form>
</div>
</div>
<br><br><br>
<div class="fixed-bottom-menu">
    <ul>
        <li><a class="active" title="Home" href="index.php"><i class="fa fa-home"></i></a></li>
        <li><a href="https://viswath.me/gdsc/index.php" title="Browser"><i class="fa-brands fa-chrome" aria-hidden="true"></i></a></li>
        <li><a href="logout.php" title="Logout"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i></a></li>
    </ul>
</div>
</body>
</html>
