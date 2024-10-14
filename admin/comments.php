<?php
session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include('db.php');

// Fetch all blogs for the dropdown
$blogs_query = "SELECT * FROM blogs";
$blogs_result = mysqli_query($conn, $blogs_query);

// Handle reply submission (admin reply)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reply'])) {
    $parent_id = intval($_POST['parent_id']); // The comment ID this reply is linked to
    $blog_id = intval($_POST['blog_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $reply = mysqli_real_escape_string($conn, $_POST['reply']);
    
    $insert_reply_query = "INSERT INTO comments (parent_id, blog_id, name, email, comment) VALUES ($parent_id, $blog_id, '$name', '$email', '$reply')";
    
    if (mysqli_query($conn, $insert_reply_query)) {
        echo "<script>alert('Reply added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding reply.');</script>";
    }
}

// Handle comment deletion
if (isset($_GET['delete_comment_id'])) {
    $comment_id = intval($_GET['delete_comment_id']);
    $delete_comment_query = "DELETE FROM comments WHERE id = $comment_id OR parent_id = $comment_id"; // Also delete replies
    
    if (mysqli_query($conn, $delete_comment_query)) {
        echo "<script>alert('Comment deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting comment.');</script>";
    }
}

// Fetch comments based on selected blog
$comments = [];
if (isset($_GET['blog_id'])) {
    $selected_blog_id = intval($_GET['blog_id']);
    $comments_query = "SELECT * FROM comments WHERE blog_id = $selected_blog_id AND parent_id = 0"; // Only main comments
    $comments_result = mysqli_query($conn, $comments_query);
    
    while ($comment = mysqli_fetch_assoc($comments_result)) {
        $comment['replies'] = [];
        
        // Fetch replies for each comment (where parent_id = comment id)
        $replies_query = "SELECT * FROM comments WHERE parent_id = " . $comment['id'];
        $replies_result = mysqli_query($conn, $replies_query);
        while ($reply = mysqli_fetch_assoc($replies_result)) {
            $comment['replies'][] = $reply;
        }
        
        $comments[] = $comment;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Comments Section</h2>
    
    <form method="GET" class="mb-4">
        <div class="mb-3">
            <label for="blog_id" class="form-label">Select Blog</label>
            <select class="form-select" id="blog_id" name="blog_id" onchange="this.form.submit()" required>
                <option value="" disabled selected>Select Blog</option>
                <?php while ($blog = mysqli_fetch_assoc($blogs_result)): ?>
                    <option value="<?php echo $blog['id']; ?>" <?php echo (isset($selected_blog_id) && $selected_blog_id == $blog['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($blog['title']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
    </form>

    <?php if (!empty($comments)): ?>
        <h4>Comments:</h4>
        <ul class="list-group">
            <?php foreach ($comments as $comment): ?>
                <li class="list-group-item">
                    <strong><?php echo htmlspecialchars($comment['name']); ?></strong> (<?php echo htmlspecialchars($comment['email']); ?>) <br>
                    <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                    
                    <!-- Delete Comment Button -->
                    <a href="?blog_id=<?php echo $selected_blog_id; ?>&delete_comment_id=<?php echo $comment['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this comment?');">Delete</a>
                    <button class="btn btn-link" data-bs-toggle="modal" data-bs-target="#replyModal" data-comment-id="<?php echo $comment['id']; ?>" data-blog-id="<?php echo $selected_blog_id; ?>">Reply</button>

                    <!-- Replies Section -->
                    <?php if (!empty($comment['replies'])): ?>
                        <div class="mt-3">
                            <strong>Replies:</strong>
                            <ul class="list-group">
                                <?php foreach ($comment['replies'] as $reply): ?>
                                    <li class="list-group-item">
                                        <strong><?php echo htmlspecialchars($reply['name']); ?></strong> <br>
                                        <?php echo nl2br(htmlspecialchars($reply['comment'])); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No comments found for this blog.</p>
    <?php endif; ?>
</div>

<!-- Modal for Reply -->
<div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replyModalLabel">Reply to Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="replyForm" method="POST">
                    <input type="hidden" name="parent_id" id="parent_id">
                    <input type="hidden" name="blog_id" id="reply_blog_id">
                    <div class="mb-3">
                        <label for="reply_name" class="form-label">Name</label>
                        <input type="text" value="Admin" class="form-control" id="reply_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="reply_email" class="form-label">Email</label>
                        <input type="email" value="admin@blog.com" class="form-control" id="reply_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="reply" class="form-label">Reply</label>
                        <textarea class="form-control" id="reply" name="reply" rows="3" required></textarea>
                    </div>
                    <button type="submit" name="submit_reply" class="btn btn-primary">Submit Reply</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#replyModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var commentId = button.data('comment-id');
        var blogId = button.data('blog-id');
        
        var modal = $(this);
        modal.find('#parent_id').val(commentId);  // This will set the parent_id to the comment ID
        modal.find('#reply_blog_id').val(blogId);
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
