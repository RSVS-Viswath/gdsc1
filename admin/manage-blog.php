<?php
session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include('db.php');

// Fetch all blogs
$blogs_query = "SELECT b.id, b.title, b.content, b.image, b.published_at, c.category_name FROM blogs b JOIN categories c ON b.category_id = c.id ORDER BY b.published_at DESC";
$blogs_result = mysqli_query($conn, $blogs_query);

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM blogs WHERE id = $delete_id";
    
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Blog deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting blog.');</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Blogs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        <h2>All Blogs</h2>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Published At</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($blog = mysqli_fetch_assoc($blogs_result)): ?>
                    <tr>
                        <td><?php echo $blog['id']; ?></td>
                        <td><?php echo htmlspecialchars($blog['title']); ?></td>
                        <td><?php echo htmlspecialchars($blog['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($blog['published_at']); ?></td>
                        <td>
                            <?php if ($blog['image']): ?>
                                <img src="uploads/<?php echo htmlspecialchars($blog['image']); ?>" alt="Blog Image" style="width: 100px; height: auto;">
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit-blog.php?id=<?php echo $blog['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="?delete_id=<?php echo $blog['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this blog?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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
