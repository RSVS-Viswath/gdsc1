<?php
include('db.php');

$blog_id = $_GET['id'];
$query = "SELECT * FROM blogs WHERE id = $blog_id";
$result = mysqli_query($conn, $query);
$blog = mysqli_fetch_assoc($result);

$category_id = $blog['category_id'];
$category_query = "SELECT category_name FROM categories WHERE id = $category_id";
$category_result = mysqli_query($conn, $category_query);
$category = mysqli_fetch_assoc($category_result);

// Fetch comments and replies
$comments_query = "SELECT * FROM comments WHERE blog_id = $blog_id AND parent_id IS NULL"; // Only top-level comments
$comments_result = mysqli_query($conn, $comments_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $blog['title']; ?></title>
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
                                    echo "<li><a class='dropdown-item text-white' href='category.php?id=" . $row['id'] . "'>" . $row['category_name'] . "</a></li>";
                                }
                                ?>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item"><a href="report_bug.php" class="nav-link text-white">Report Bug</a></li>
                    <li class="nav-item"><a href="contact.php" class="nav-link text-white">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container my-5">
        <h2><?php echo $blog['title']; ?></h2>
        <p style="color:red;">Category: <?php echo $category['category_name']; ?></p> 
      <center>  <img src="admin/uploads/<?php echo $blog['image']; ?>" width="450"><br><br></center>
        <p><?php echo $blog['content']; ?></p>
        
        <br>
        <h4>Comments</h4>
        <form action="submit_comment.php" method="POST">
            <input type="hidden" name="blog_id" value="<?php echo $blog_id; ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <label for="comment" class="form-label">Comment</label>
                <textarea class="form-control" name="comment" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <?php while ($comment = mysqli_fetch_assoc($comments_result)) : ?>
            <div class="border-bottom my-4">
                <h5><?php echo $comment['name']; ?></h5>
                <p><?php echo $comment['comment']; ?></p>
                <small><?php echo date('F j, Y, g:i a', strtotime($comment['comment_date'])); ?></small>
                
                <!-- Reply and View Replies Buttons -->
                <button class="btn btn-link" data-bs-toggle="modal" data-bs-target="#replyModal<?php echo $comment['id']; ?>">Reply</button>
                <button class="btn btn-link" data-bs-toggle="modal" data-bs-target="#viewRepliesModal<?php echo $comment['id']; ?>">View Replies</button>

                <!-- Reply Modal -->
                <div class="modal fade" id="replyModal<?php echo $comment['id']; ?>" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="replyModalLabel">Reply to <?php echo $comment['name']; ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="submit_comment.php" method="POST">
                                    <input type="hidden" name="blog_id" value="<?php echo $blog_id; ?>">
                                    <input type="hidden" name="parent_id" value="<?php echo $comment['id']; ?>">
                                    <div class="mb-3">
                                        <label for="reply_name" class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reply_email" class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reply_comment" class="form-label">Reply</label>
                                        <textarea class="form-control" name="comment" rows="4" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit Reply</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Replies Modal -->
                <div class="modal fade" id="viewRepliesModal<?php echo $comment['id']; ?>" tabindex="-1" aria-labelledby="viewRepliesModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewRepliesModalLabel">Replies to <?php echo $comment['name']; ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <?php
                                // Fetch replies for this comment
                                $replies_query = "SELECT * FROM comments WHERE parent_id = " . $comment['id'];
                                $replies_result = mysqli_query($conn, $replies_query);
                                while ($reply = mysqli_fetch_assoc($replies_result)) :
                                ?>
                                    <div class="border-bottom my-3">
                                        <strong><?php echo $reply['name']; ?></strong>
                                        <p><?php echo $reply['comment']; ?></p>
                                        <small><?php echo date('F j, Y, g:i a', strtotime($reply['comment_date'])); ?></small>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <footer class="text-center p-3 bg-black">
        <p>© 2024 My Blog | Follow us on 
            <a href="#" class="text-white"><i class="fab fa-facebook"></i></a> 
            <a href="#" class="text-white"><i class="fab fa-twitter"></i></a>
        </p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
