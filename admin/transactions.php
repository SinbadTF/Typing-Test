<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Add this at the top with other PHP code
if (isset($_POST['approve_transaction'])) {
    $transaction_id = $_POST['transaction_id'];
    $user_id = $_POST['user_id'];
    
    try {
        $pdo->beginTransaction();
        
        // Update transaction status
        $stmt = $pdo->prepare("UPDATE transactions SET status = 'completed' WHERE id = ?");
        $stmt->execute([$transaction_id]);
        
        // Update user to premium
        $stmt = $pdo->prepare("UPDATE users SET is_premium = 1 WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        $pdo->commit();
        header("Location: transactions.php?success=approved");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: transactions.php?error=failed");
        exit();
    }
}

// In the table row, add this before the Date column
// Update the SQL query to include screenshot
// Update the SQL query to sort in descending order
// Update the SQL query
$stmt = $pdo->query("
    SELECT 
        t.id,
        t.user_id,
        COALESCE(u.username, 'Unknown User') as username,
        t.amount,
        t.payment_method,
        t.status,
        t.created_at,
        t.screenshot_path
    FROM transactions t
    LEFT JOIN users u ON t.user_id = u.user_id
    ORDER BY t.created_at DESC
");
$transactions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #ffffff;
            min-height: 100vh;
        }
        .dashboard {
            padding: 40px;
            background: rgba(45, 45, 45, 0.98);
            border-radius: 25px;
            margin: 40px auto;
            box-shadow: 0 0 40px rgba(0,123,255,0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
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
        .card {
            background: #2c2e31;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }
        .card-header {
            background: #232427;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard">
            <?php if (isset($_GET['success']) && $_GET['success'] === 'approved'): ?>
                <div class="alert custom-alert success-alert alert-dismissible fade show mb-4" role="alert">
                    <div class="alert-content">
                        <i class="fas fa-check-circle alert-icon"></i>
                        <div class="alert-text">
                            <strong>Success!</strong> Transaction approved and user upgraded to premium.
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error']) && $_GET['error'] === 'failed'): ?>
                <div class="alert custom-alert error-alert alert-dismissible fade show mb-4" role="alert">
                    <div class="alert-content">
                        <i class="fas fa-exclamation-circle alert-icon"></i>
                        <div class="alert-text">
                            <strong>Error!</strong> Failed to process the transaction. Please try again.
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Transaction History</h2>
                <a href="dashboard.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Proof</th>
                            <th>Actions</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No transactions found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $transaction): ?>
                                <tr data-transaction-id="<?php echo $transaction['id']; ?>">
                                    <td><?php echo $transaction['user_id']; ?></td>
                                    <td class="username"><?php echo htmlspecialchars($transaction['username']); ?></td>
                                    <td class="amount">$<?php echo number_format($transaction['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['payment_method']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($transaction['status']); ?>">
                                            <?php echo ucfirst($transaction['status']); ?>
                                        </span>
                                    </td>
                                    <!-- Add this at the bottom of the file, before closing body tag -->
                                    <div class="modal fade" id="confirmApprovalModal" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content bg-dark">
                                                <div class="modal-header border-secondary">
                                                    <h5 class="modal-title">Confirm Transaction Approval</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="text-center mb-4">
                                                        <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                                                    </div>
                                                    <div class="transaction-details mb-4">
                                                        <div class="detail-item">
                                                            <span class="label">Username:</span>
                                                            <span class="value" id="modalUsername"></span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span class="label">Transaction ID:</span>
                                                            <span class="value" id="modalTransactionDisplay"></span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span class="label">Amount:</span>
                                                            <span class="value" id="modalAmount"></span>
                                                        </div>
                                                    </div>
                                                    <p class="text-center">Are you sure you want to approve this transaction?</p>
                                                    <p class="text-center text-muted small">This action cannot be undone.</p>
                                                </div>
                                                <div class="modal-footer border-secondary">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form id="approvalForm" method="POST" class="d-inline">
                                                        <input type="hidden" name="transaction_id" id="modalTransactionId">
                                                        <input type="hidden" name="user_id" id="modalUserId">
                                                        <button type="submit" name="approve_transaction" class="btn btn-success">
                                                            <i class="fas fa-check me-2"></i>Approve Transaction
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Update the approve button in the table -->
                                    <td>
                                        <?php if ($transaction['status'] === 'pending'): ?>
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="showApprovalModal(<?php echo $transaction['id']; ?>, <?php echo $transaction['user_id']; ?>)">
                                                <i class="fas fa-check me-1"></i> Approve
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <!-- Add this to your existing style section -->
                                    <style>
                                        .modal-content.bg-dark {
                                            background: rgba(35, 35, 35, 0.95) !important;
                                            backdrop-filter: blur(10px);
                                            border: 1px solid rgba(255, 255, 255, 0.1);
                                        }
                                        .btn-success {
                                            background: #28a745;
                                            border: none;
                                            padding: 8px 16px;
                                            transition: all 0.3s ease;
                                        }
                                        .btn-success:hover {
                                            background: #218838;
                                            transform: translateY(-1px);
                                        }
                                        .btn-secondary {
                                            background: rgba(108, 117, 125, 0.2);
                                            border: 1px solid rgba(108, 117, 125, 0.4);
                                            color: #6c757d;
                                        }
                                        .btn-secondary:hover {
                                            background: rgba(108, 117, 125, 0.3);
                                            color: #6c757d;
                                        }
                                    </style>
                                    <!-- Add this to your scripts section -->
                                    <script>
                                    function showApprovalModal(transactionId, userId) {
                                        const row = document.querySelector(`tr[data-transaction-id="${transactionId}"]`);
                                        const username = row.querySelector('.username').textContent;
                                        const amount = row.querySelector('.amount').textContent;
                                        
                                        document.getElementById('modalTransactionId').value = transactionId;
                                        document.getElementById('modalUserId').value = userId;
                                        document.getElementById('modalUsername').textContent = username;
                                        document.getElementById('modalTransactionDisplay').textContent = '#' + transactionId;
                                        document.getElementById('modalAmount').textContent = amount;
                                        
                                        new bootstrap.Modal(document.getElementById('confirmApprovalModal')).show();
                                    }
                                    </script>
                                    <td>
                                        <?php if ($transaction['screenshot_path']): ?>
                                            <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#screenshotModal<?php echo $transaction['id']; ?>">
                                                <i class="fas fa-image"></i> View
                                            </a>
                                            <!-- Modal for screenshot -->
                                            <div class="modal fade" id="screenshotModal<?php echo $transaction['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content bg-dark">
                                                        <div class="modal-header border-secondary">
                                                            <h5 class="modal-title">Payment Proof</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                       
                                                        <div class="modal-body text-center">
                                                            <img src="../<?php echo htmlspecialchars($transaction['screenshot_path']); ?>" 
                                                                 class="img-fluid" 
                                                                 alt="Payment Proof"
                                                                 style="max-height: 80vh;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">No proof</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y H:i', strtotime($transaction['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>


<style>
    .modal-content {
        background: rgba(35, 35, 35, 0.95) !important;
        color: #ffffff;
    }
    .btn-info {
        background: rgba(23, 162, 184, 0.2);
        border: 1px solid rgba(23, 162, 184, 0.4);
        color: #17a2b8;
    }
    .btn-info:hover {
        background: rgba(23, 162, 184, 0.3);
        border: 1px solid rgba(23, 162, 184, 0.5);
        color: #17a2b8;
    }
    .custom-alert {
            border: none;
            border-radius: 15px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
        }

        .alert-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .alert-icon {
            font-size: 1.5rem;
        }

        .alert-text {
            font-size: 1rem;
        }

        .success-alert {
            background: rgba(40, 167, 69, 0.2);
            border-left: 4px solid #28a745;
            color: #28a745;
        }

        .error-alert {
            background: rgba(220, 53, 69, 0.2);
            border-left: 4px solid #dc3545;
            color: #dc3545;
        }

        .btn-close-white {
            filter: brightness(0) invert(1);
            opacity: 0.8;
        }

        .btn-close-white:hover {
            opacity: 1;
        }
</style>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            </div>
        </div>
    </div>
</body>
</html>