<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Add this after database connection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $level = $_POST['level'];
    $type = $_POST['type'];
    $table = ($type === 'japanese') ? 'japanese_lessons' : 'lessons';

    // Get the last lesson number for the specified level
    $stmt = $pdo->prepare("SELECT MAX(lesson_number) as last_number FROM $table WHERE level = ?");
    $stmt->execute([$level]);
    $result = $stmt->fetch();
    $nextLessonNumber = ($result['last_number'] ?? 0) + 1;
    
    $stmt = $pdo->prepare("INSERT INTO $table (title, content, level, lesson_number) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$title, $content, $level, $nextLessonNumber])) {
        header('Location: manage_lessons.php?success=added');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Lesson</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #d1d0c5;
            min-height: 100vh;
        }
        .card {
            background: rgba(45, 45, 45, 0.98);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 40px rgba(0,123,255,0.1);
        }
        .card-header {
            background: rgba(35, 35, 35, 0.7);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
        }
        .card-body {
            padding: 2rem;
        }
        .form-control, .form-select { 
            background: rgba(35, 35, 35, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #d1d0c5;
            padding: 0.8rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus { 
            background: rgba(35, 35, 35, 0.9);
            border-color: #007bff;
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.15);
        }
        .btn-primary {
            background: linear-gradient(45deg, #007bff, #00ff88);
            border: none;
            padding: 0.8rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.3);
        }
        .btn-outline-primary {
            color: #007bff;
            border-color: #007bff;
            padding: 0.8rem 1.5rem;
        }
        .btn-outline-primary:hover {
            background: #007bff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.3);
        }
        label {
            color: #adb5bd;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        textarea {
            min-height: 150px;
        }
    </style>
</head>
<body>
    <?php include 'includes/admin_navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Add New Lesson</h4>
                <a href="manage_lessons.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label>Lesson Type</label>
                        <select name="type" class="form-select" required>
                            <option value="english">English Lesson</option>
                            <option value="japanese">Japanese Lesson</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Content</label>
                        <textarea name="content" class="form-control" rows="5" required></textarea>
                    </div>
                    // In the form section, remove the lesson number input field
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label>Level</label>
                            <select name="level" class="form-select" required>
                                <option value="">Select Level</option>
                                <option value="Basic">Basic</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Advanced">Advanced</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Lesson
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>