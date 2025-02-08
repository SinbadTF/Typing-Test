// ... existing login validation code ...
if (password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['profile_image'] = $user['profile_image']; // Add this line
    header('Location: index.php');
    exit();
}