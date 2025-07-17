<?php
session_start();

// If you want to add more questions, just add them to health-quiz-questions.json below
$questionsFile = 'health-quiz-questions.json';
if (file_exists($questionsFile)) {
    $questions = json_decode(file_get_contents($questionsFile), true);
} else {
    // Fallback: Use a default set of questions if the file doesn't exist
    $questions = [
        [
            "question" => "Do you have a fever or chills?",
            "options" => [
                ["text" => "No", "score" => 0],
                ["text" => "Mild fever (below 38°C)", "score" => 1],
                ["text" => "High fever (38°C or above)", "score" => 2],
            ]
        ],
        [
            "question" => "Are you experiencing a cough?",
            "options" => [
                ["text" => "No", "score" => 0],
                ["text" => "Occasional", "score" => 1],
                ["text" => "Frequent/Persistent", "score" => 2],
            ]
        ],
        [
            "question" => "Do you feel shortness of breath?",
            "options" => [
                ["text" => "No", "score" => 0],
                ["text" => "On exertion only", "score" => 1],
                ["text" => "Even at rest", "score" => 2],
            ]
        ],
        [
            "question" => "Are you experiencing fatigue?",
            "options" => [
                ["text" => "No", "score" => 0],
                ["text" => "Sometimes", "score" => 1],
                ["text" => "Often", "score" => 2],
            ]
        ],
        [
            "question" => "Do you have headaches?",
            "options" => [
                ["text" => "No", "score" => 0],
                ["text" => "Occasionally", "score" => 1],
                ["text" => "Frequently", "score" => 2],
            ]
        ],
        [
            "question" => "Are you experiencing muscle or body aches?",
            "options" => [
                ["text" => "No", "score" => 0],
                ["text" => "Mild", "score" => 1],
                ["text" => "Severe", "score" => 2],
            ]
        ],
        [
            "question" => "Do you have a sore throat?",
            "options" => [
                ["text" => "No", "score" => 0],
                ["text" => "Slight discomfort", "score" => 1],
                ["text" => "Painful", "score" => 2],
            ]
        ],
        [
            "question" => "Have you lost your sense of taste or smell?",
            "options" => [
                ["text" => "No", "score" => 0],
                ["text" => "Partially", "score" => 1],
                ["text" => "Completely", "score" => 2],
            ]
        ],
        [
            "question" => "Are you experiencing nausea or vomiting?",
            "options" => [
                ["text" => "No", "score" => 0],
                ["text" => "Mild", "score" => 1],
                ["text" => "Severe", "score" => 2],
            ]
        ],
        [
            "question" => "Do you have diarrhea?",
            "options" => [
                ["text" => "No", "score" => 0],
                ["text" => "Occasionally", "score" => 1],
                ["text" => "Frequently", "score" => 2],
            ]
        ],
        [
            "question" => "Do you feel anxious or depressed?",
            "options" => [
                ["text" => "No", "score" => 0],
                ["text" => "Sometimes", "score" => 1],
                ["text" => "Often", "score" => 2],
            ]
        ],
        [
            "question" => "Are you having trouble sleeping?",
            "options" => [
                ["text" => "No", "score" => 0],
                ["text" => "Sometimes", "score" => 1],
                ["text" => "Frequently", "score" => 2],
            ]
        ],
        [
            "question" => "Do you have any chest pain?",
            "options" => [
                ["text" => "No", "score" => 0],
                ["text" => "Mild", "score" => 1],
                ["text" => "Severe", "score" => 2],
            ]
        ],
        [
            "question" => "Are you experiencing confusion or difficulty concentrating?",
            "options" => [
                ["text" => "No", "score" => 0],
                ["text" => "Sometimes", "score" => 1],
                ["text" => "Often", "score" => 2],
            ]
        ],
        [
            "question" => "Are you experiencing loss of appetite?",
            "options" => [
                ["text" => "No", "score" => 0],
                ["text" => "Mild", "score" => 1],
                ["text" => "Severe", "score" => 2],
            ]
        ]
    ];
}

$totalQuestions = count($questions);
$score = 0;
$result = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($questions as $idx => $q) {
        $score += isset($_POST["q$idx"]) ? intval($_POST["q$idx"]) : 0;
    }
    $maxScore = $totalQuestions * 2;
    $lowThreshold = (int)($maxScore * 0.25);
    $medThreshold = (int)($maxScore * 0.6);

    if ($score <= $lowThreshold) {
        $result = "<span class='score-low'>Low Risk:</span> Your symptoms are mild. Maintain a healthy lifestyle and drink plenty of water. If you feel unwell or symptoms worsen, consult a healthcare provider.";
    } elseif ($score <= $medThreshold) {
        $result = "<span class='score-moderate'>Moderate Risk:</span> You are showing some symptoms. Monitor your health closely, rest, hydrate, and consider doing a self-test or speaking to a doctor if symptoms persist.";
    } else {
        $result = "<span class='score-high'>High Risk:</span> Your symptoms are significant. Please consult a healthcare provider or visit a clinic for testing as soon as possible. Avoid contact with others and follow medical advice.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Comprehensive Health Status Quiz | Telemedicine</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="health-quiz.css" rel="stylesheet">
</head>
<body>
<?php /* You can adjust this to your site's actual header */ ?>
<header style="text-align:center;margin-bottom: 2rem;">
    <h1 style="color:#2563eb;font-size:2.2rem;margin:1rem 0 0.4rem 0;">Telemedicine Health Check</h1>
    <div style="color:#888;font-size:1.08rem;">Self-assessment for your current health status</div>
</header>

<section class="quiz-container">
    <h2>Comprehensive Health Status Quiz</h2>
    <p class="quiz-desc">Answer the following questions to evaluate your health status. Your answers are confidential and help you understand when to seek medical attention.</p>
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="quiz-result animated-pop">
            <div class="score-progress">
                <div class="progress-label">Your Score</div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width:<?php echo round(($score/($totalQuestions*2))*100); ?>%;"></div>
                </div>
                <div class="progress-score"><strong><?php echo $score; ?></strong> / <?php echo $totalQuestions*2; ?></div>
            </div>
            <hr>
            <div class="score-advice"><?php echo $result; ?></div>
        </div>
        <div class="quiz-action-btns">
            <a href="health-quiz.php" class="quiz-submit-btn quiz-again-btn">Take Again</a>
        </div>
    <?php else: ?>
    <form method="post" autocomplete="off" id="quizForm">
        <?php foreach ($questions as $idx => $q): ?>
            <div class="quiz-question">
                <strong><?php echo ($idx+1).". ".$q["question"]; ?></strong>
                <div class="quiz-options">
                    <?php foreach ($q["options"] as $opt): ?>
                        <label>
                            <input type="radio" name="q<?php echo $idx; ?>" value="<?php echo $opt["score"]; ?>" required>
                            <?php echo $opt["text"]; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="quiz-submit-btn">Submit &amp; Get Advice</button>
    </form>
    <?php endif; ?>
</section>
<footer style="text-align:center;color:#888;padding:2rem 0 1rem 0;">&copy; <?php echo date('Y'); ?> Telemedicine Health Platform</footer>
</body>
</html>