<?php
session_start(); 

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $category_name = $_POST['category_name'];
        $add_query = "INSERT INTO categories (category_name) VALUES ('$category_name')";
        if (mysqli_query($conn, $add_query)) {
            echo "<script>alert('Category added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding category.');</script>";
        }
    }

    if (isset($_POST['edit_category'])) {
        $category_id = $_POST['category_id'];
        $category_name = $_POST['category_name'];
        $update_query = "UPDATE categories SET category_name = '$category_name' WHERE id = $category_id";
        if (mysqli_query($conn, $update_query)) {
            echo "<script>alert('Category updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating category.');</script>";
        }
    }

    if (isset($_POST['delete_category'])) {
        $category_id = $_POST['category_id'];
        $delete_blogs_query = "DELETE FROM blogs WHERE category_id = $category_id";
        mysqli_query($conn, $delete_blogs_query);
        $delete_category_query = "DELETE FROM categories WHERE id = $category_id";
        if (mysqli_query($conn, $delete_category_query)) {
            echo "<script>alert('Category and associated blogs deleted successfully!');</script>";
        } else {
            echo "<script>alert('Error deleting category.');</script>";
        }
    }
}

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
    <h2>Manage Categories</h2>

    <form method="POST" class="mb-4">
        <input type="hidden" name="category_id" id="category_id">
        <div class="mb-3">
            <label for="category_name" class="form-label">Category Name</label>
            <input type="text" class="form-control" name="category_name" id="category_name" required>
        </div>
        <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
        <button type="submit" name="edit_category" class="btn btn-warning">Update Category</button>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                <tr>
                    <td><?php echo $category['id']; ?></td>
                    <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="populateForm(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['category_name']); ?>')">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $category['id']; ?>)">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function populateForm(id, name) {
    document.getElementById('category_id').value = id;
    document.getElementById('category_name').value = name;
}

function confirmDelete(id) {
    const confirmation = confirm("Deleting this category will also delete all blogs associated with it. Do you want to proceed?");
    if (confirmation) {
        const form = document.createElement('form');
        form.method = 'POST';
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'category_id';
        input.value = id;
        const deleteInput = document.createElement('input');
        deleteInput.type = 'hidden';
        deleteInput.name = 'delete_category';
        deleteInput.value = true;
        form.appendChild(input);
        form.appendChild(deleteInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
</div>

 <div class="fixed-bottom-menu">
        <ul>
            <li><a class="active" title="Home" href="index.php"><i class="fa fa-home"></i></a></li>
            <li><a href="https://viswath.me/gdsc/index.php" title="Browser"><i class="fa-brands fa-chrome" aria-hidden="true"></i></a></li>
             <li><a href="logout.php" title="Logout"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i></a></li>
        </ul>
    </div>
</body>
</html>
