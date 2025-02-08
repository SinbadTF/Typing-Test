<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

// Fetch all lessons
$stmt = $pdo->prepare("SELECT * FROM lessons ORDER BY level, lesson_number");
$stmt->execute();
$lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all Japanese lessons
$stmt = $pdo->prepare("SELECT * FROM japanese_lessons ORDER BY level, lesson_number");
$stmt->execute();
$japaneseLessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Lessons - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background: #323437;
            color: #d1d0c5;
            min-height: 100vh;
        }
        .card {
            background: #2c2e31;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            margin-bottom: 2rem;
        }
        .card-header {
            background: #232427;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
        }
        .btn-primary {
            background: #4a9eff;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: #2f7fd4;
            transform: translateY(-2px);
        }
        .btn-warning {
            background: #e2b714;
            border: none;
            color: #232427;
        }
        .btn-danger {
            background: #ca4754;
            border: none;
        }
        .table {
            color: #d1d0c5;
        }
        .table th {
            color: #d1d0c5;
            font-weight: 500;
            background: #232427;
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }
        .table td {
            border-color: rgba(255, 255, 255, 0.05);
            vertical-align: middle;
            background: #2c2e31;
            color: #d1d0c5;
        }
        .table tbody tr:hover td {
            background: #35373a;
            transition: background-color 0.3s ease;
        }
            
        }
        .btn-warning:hover, .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #28a745;
        }
        h2, h3 {
            color: #d1d0c5;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/includes/admin_navbar.php'; ?>
    
    <div class="container">
        <h2 class="mb-4">Manage Lessons</h2>
        
        <!-- English Lessons -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>English Typing Lessons</h3>
                <a href="add_lesson.php" class="btn btn-primary">Add New Lesson</a>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Lesson #</th>
                            <th>Title</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lessons as $lesson): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($lesson['level']); ?></td>
                            <td><?php echo htmlspecialchars($lesson['lesson_number']); ?></td>
                            <td><?php echo htmlspecialchars($lesson['title']); ?></td>
                            <td>
                                <a href="edit_lesson.php?id=<?php echo $lesson['id']; ?>&type=english" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="delete_lesson.php?id=<?php echo $lesson['id']; ?>&type=english" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this lesson?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Japanese Lessons -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Japanese Typing Lessons</h3>
               
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Lesson #</th>
                            <th>Title</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($japaneseLessons as $lesson): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($lesson['level']); ?></td>
                            <td><?php echo htmlspecialchars($lesson['lesson_number']); ?></td>
                            <td><?php echo htmlspecialchars($lesson['title']); ?></td>
                            <td>
                                <a href="edit_lesson.php?id=<?php echo $lesson['id']; ?>&type=japanese" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="delete_lesson.php?id=<?php echo $lesson['id']; ?>&type=japanese" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this lesson?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>