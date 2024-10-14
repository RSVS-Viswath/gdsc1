<?php
include('db.php');

$blog_id = $_POST['blog_id'];
$name = $_POST['name'];
$email = $_POST['email'];
$comment = $_POST['comment'];

// Check if the comment is a reply
$parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : NULL;

$query = "INSERT INTO comments (blog_id, name, email, comment, parent_id, comment_date) 
          VALUES ('$blog_id', '$name', '$email', '$comment', '$parent_id', NOW())";

if (mysqli_query($conn, $query)) {
    echo "<script>
            alert('Comment submitted successfully!');
            window.location.href = 'blog.php?id=$blog_id';
          </script>";
} else {
    echo "<script>
            alert('Failed to submit the comment. Please try again.');
            window.location.href = 'blog.php?id=$blog_id';
          </script>";
}

mysqli_close($conn);
?>
