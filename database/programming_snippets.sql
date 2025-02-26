INSERT INTO programming_snippets (content, language) VALUES
-- JavaScript
('function calculateSum(arr) {\n    return arr.reduce((sum, num) => sum + num, 0);\n}', 'JavaScript'),
('const filterEvenNumbers = numbers => {\n    return numbers.filter(num => num % 2 === 0);\n};', 'JavaScript'),

-- Python
('def bubble_sort(arr):\n    n = len(arr)\n    for i in range(n):\n        for j in range(0, n-i-1):\n            if arr[j] > arr[j+1]:\n                arr[j], arr[j+1] = arr[j+1], arr[j]\n    return arr', 'Python'),
('def fibonacci(n):\n    if n <= 1:\n        return n\n    return fibonacci(n-1) + fibonacci(n-2)', 'Python'),

-- PHP
('function validateEmail($email) {\n    return filter_var($email, FILTER_VALIDATE_EMAIL);\n}', 'PHP'),
('public function getUserById($id) {\n    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?")\n    $stmt->execute([$id]);\n    return $stmt->fetch();\n}', 'PHP'),

-- SQL
('SELECT users.name, COUNT(orders.id) as order_count\nFROM users\nLEFT JOIN orders ON users.id = orders.user_id\nGROUP BY users.id\nHAVING order_count > 5;', 'SQL'),

-- Java
('public class BinarySearch {\n    public static int search(int[] arr, int target) {\n        int left = 0;\n        int right = arr.length - 1;\n        while (left <= right) {\n            int mid = left + (right - left) / 2;\n            if (arr[mid] == target) return mid;\n            if (arr[mid] < target) left = mid + 1;\n            else right = mid - 1;\n        }\n        return -1;\n    }\n}', 'Java');