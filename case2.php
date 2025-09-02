<?php

if (isset($_GET['name']) && isset($_GET['score'])) {
    $studentName = htmlspecialchars($_GET['name']);
    $finalScore = floatval($_GET['score']);

  
    $gradeLetter = "";
    $gradeLabel = "";
    $gradeRemark = "";

    
    if ($finalScore >= 0 && $finalScore <= 100) {
        if ($finalScore >= 95) {
            $gradeLetter = "A";
            $gradeLabel = "Excellent";
            $gradeRemark = "Outstanding Performance!";
        } else {
            if ($finalScore >= 90) {
                $gradeLetter = "B";
                $gradeLabel = "Very Good";
                $gradeRemark = "Great Job!";
            } else {
                if ($finalScore >= 85) {
                    $gradeLetter = "C";
                    $gradeLabel = "Good";
                    $gradeRemark = "Good effort, keep it up!";
                } else {
                    if ($finalScore >= 75) {
                        $gradeLetter = "D";
                        $gradeLabel = "Needs Improvement";
                        $gradeRemark = "Work harder next time.";
                    } else {
                        $gradeLetter = "F";
                        $gradeLabel = "Failed";
                        $gradeRemark = "You need to improve.";
                    }
                }
            }
        }
    } else {
        $gradeLetter = "Invalid";
        $gradeLabel = "Invalid Score";
        $gradeRemark = "Score must be between 0 and 100.";
    }
} else {
    $studentName = "";
    $finalScore = "";
    $gradeLetter = "";
    $gradeLabel = "";
    $gradeRemark = "Please provide 'name' and 'score' in the URL.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Grade Result</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #c3ecf7, #f6f6f6);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .grade-card {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        h1 {
            margin-bottom: 25px;
            font-size: 26px;
            color: #333;
        }

        .info {
            font-size: 18px;
            margin-bottom: 15px;
            color: #444;
        }

        .remark {
            font-style: italic;
            color: #555;
            margin-top: 10px;
        }

        .example {
            font-size: 14px;
            color: #888;
            margin-top: 20px;
        }

        code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 30px;
            color: white;
            margin-left: 8px;
        }

        .grade-A { background: #4caf50; }
        .grade-B { background: #2196f3; }
        .grade-C { background: #ffc107; color: #000; }
        .grade-D { background: #ff9800; }
        .grade-F { background: #f44336; }
        .grade-Invalid { background: #9e9e9e; }

        
        .grade-a-card {
            background: linear-gradient(135deg, #4caf50, #81c784);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-top: 20px;
            box-shadow: 0 8px 20px rgba(76, 175, 80, 0.3);
            animation: popIn 0.5s ease;
        }

        .trophy {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .grade-label {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .special-remark {
            font-size: 16px;
            margin-top: 10px;
            font-style: italic;
        }

        @keyframes popIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="grade-card">
        <h1>Student Grade Result</h1>

        <?php if ($studentName && $finalScore !== ""): ?>
            <div class="info"><strong>Name:</strong> <?= $studentName ?></div>
            <div class="info"><strong>Score:</strong> <?= $finalScore ?></div>

            <div class="info">
                <strong>Grade:</strong>
                <?php if ($gradeLetter === "A"): ?>
                    <div class="grade-a-card">
                        <div class="trophy">🏆</div>
                        <div class="grade-label">A — <?= $gradeLabel ?></div>
                        <div class="special-remark">🌟 <?= $gradeRemark ?> 🌟</div>
                    </div>
                <?php else: ?>
                    <?= $gradeLetter ?> (<?= $gradeLabel ?>)
                    <span class="badge grade-<?= $gradeLetter ?>"><?= $gradeLetter ?></span>
                    <div class="remark"><?= $gradeRemark ?></div>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div class="info"><?= $gradeRemark ?></div>
            <div class="example">
                Example: <code>?name=Maria%20Santos&score=95</code>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>