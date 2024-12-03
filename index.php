<?php
// Database connection (replace placeholders with your credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "multiple_choice";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define questions and answers
$questions = [
    [
        "question" => "What does PHP stand for?",
        "options" => ["Personal Home Page", "Private Home Page", "PHP: Hypertext Preprocessor", "Public Hypertext Preprocessor"],
        "answer" => 2
    ],
    [
        "question" => "Which symbol is used to access a property of an object in PHP?",
        "options" => [".", "->", "::", "#"],
        "answer" => 1
    ],
    [
        "question" => "Which function is used to include a file in PHP?",
        "options" => ["include()", "require()", "import()", "load()"],
        "answer" => 0
    ],
    [
        "question" => "Which of the following is the correct way to start a PHP block in a file?",
        "options" => ["<php>", "<?php", "<?", "<?php?>"],
        "answer" => 1
    ],
    [
        "question" => "How do you declare a variable in PHP?",
        "options" => ["var \$variable;", "\$variable;", "declare \$variable;", "let \$variable;"],
        "answer" => 1
    ],
    [
        "question" => "What is the correct way to end a PHP statement?",
        "options" => [".", ";", ":", "}"],
        "answer" => 1
    ],
    [
        "question" => "Which of the following functions is used to output data in PHP?",
        "options" => ["print()", "echo()", "display()", "Both print() and echo()"],
        "answer" => 3
    ],
    [
        "question" => "Which superglobal variable in PHP is used to collect form data?",
        "options" => ["\$_POST", "\$_SESSION", "\$_GET", "Both \$_POST and \$_GET"],
        "answer" => 3
    ],
    [
        "question" => "Which function is used to check if a file exists in PHP?",
        "options" => ["file_exists()", "is_file()", "check_file()", "exists_file()"],
        "answer" => 0
    ],
    [
        "question" => "What is the default file extension for PHP files?",
        "options" => [".php", ".html", ".php3", ".phtml"],
        "answer" => 0
    ]
];


// Initialize score
$score = 0;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($questions as $index => $question) {
        if (isset($_POST["question$index"]) && $_POST["question$index"] == $question['answer']) {
            $score++;
        }
    }
    
    // Save score to database
    $name = $_POST['name'] ?? 'Anonymous';
    $stmt = $conn->prepare("INSERT INTO users (name, score) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $score);
    $stmt->execute();
    $stmt->close();

    // Display score
    echo "<h1>PHP Quiz</h1>";
    echo "<h2>Your Score: $score/" . count($questions) . "</h2>";

    // Display leaderboard
    echo "<h2>Leaderboard</h2>";
    $result = $conn->query("SELECT name, score FROM users ORDER BY score DESC, quiz_date ASC LIMIT 10");
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 50%;'>
                <tr>
                    <th>Name</th>
                    <th>Score</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . $row['score'] . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "No scores yet.";
    }
    
    echo '<br><a href="index.php">Try Again</a>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Quiz</title>
</head>
<body>
    <h1>PHP Quiz</h1>
    <form method="post" action="">
        <label>
            Name:
            <input type="text" name="name" required>
        </label><br><br>
        <?php foreach ($questions as $index => $question): ?>
            <fieldset>
                <legend><?php echo $question['question']; ?></legend>
                <?php foreach ($question['options'] as $optionIndex => $option): ?>
                    <label>
                        <input type="radio" name="question<?php echo $index; ?>" value="<?php echo $optionIndex; ?>" required>
                        <?php echo $option; ?>
                    </label><br>
                <?php endforeach; ?>
            </fieldset>
            <br>
            <br>
            <br>
        <?php endforeach; ?>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
