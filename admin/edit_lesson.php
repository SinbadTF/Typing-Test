<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$id = $_GET['id'] ?? '';
$type = $_GET['type'] ?? 'english';
$table = ($type === 'japanese') ? 'japanese_lessons' : 'lessons'; // Fixed table name from 'typing_texts' to 'lessons'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $level = $_POST['level'];
    $lessonNumber = $_POST['lesson_number'];
    
    $stmt = $pdo->prepare("UPDATE $table SET title = ?, content = ?, level = ?, lesson_number = ? WHERE id = ?");
    if ($stmt->execute([$title, $content, $level, $lessonNumber, $id])) {
        header('Location: manage_lessons.php?success=updated');
        exit();
    }
}

$stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
$stmt->execute([$id]);
$lesson = $stmt->fetch();

if (!$lesson) {
    header('Location: manage_lessons.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lesson</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #323437; color: #d1d0c5; }
        .form-control, .form-select { 
            background: #2c2e31; 
            border-color: #454545; 
            color: #d1d0c5; 
        }
        .form-control:focus, .form-select:focus { 
            background: #2c2e31; 
            border-color: #007bff; 
            color: #d1d0c5; 
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .form-select option {
            background: #2c2e31;
            color: #d1d0c5;
        }
        .card {
            background: #2c2e31;
            border: 1px solid #454545;
        }
        .card-header {
            background: #232427;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: #d1d0c5;
        }
        .btn-outline-primary {
            color: #4a9eff;
            border-color: #4a9eff;
        }
        .btn-outline-primary:hover {
            background: #4a9eff;
            color: #232427;
        }
        .card-body {
            color: #d1d0c5;
        }
            
    </style>
</head>
<body>
    <?php include 'includes/admin_navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Edit Lesson</h4>
                <a href="manage_lessons.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" 
                               value="<?php echo isset($lesson['title']) ? htmlspecialchars($lesson['title']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Content</label>
                        <textarea name="content" class="form-control" rows="5" required><?php echo isset($lesson['content']) ? htmlspecialchars($lesson['content']) : ''; ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Level</label>
                            <select name="level" class="form-select" required>
                                <option value="">Select Level</option>
                                <option value="Basic" <?php echo ($lesson['level'] == 'Basic') ? 'selected' : ''; ?>>Basic</option>
                                <option value="Intermediate" <?php echo ($lesson['level'] == 'Intermediate') ? 'selected' : ''; ?>>Intermediate</option>
                                <option value="Advanced" <?php echo ($lesson['level'] == 'Advanced') ? 'selected' : ''; ?>>Advanced</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Lesson Number</label>
                            <input type="number" name="lesson_number" class="form-control" 
                                   value="<?php echo isset($lesson['lesson_number']) ? $lesson['lesson_number'] : ''; ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>