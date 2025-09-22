<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Multiplication Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px;
        }
        .container {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 900px;
            width: 100%;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        form {
            text-align: center;
            margin-bottom: 25px;
        }
        input[type="number"] {
            padding: 8px;
            width: 80px;
            margin: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            background: #007bff;
            color: #fff;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        th, td {
            padding: 10px;
            border: 1px solid #333;
        }
        th {
            background: #e0e0e0;
            font-weight: bold;
        }
        .odd {
            background: yellow;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Multiplication Table Generator</h2>
        <form method="post">
            <label>Rows: <input type="number" name="rows" min="1" required></label>
            <label>Columns: <input type="number" name="cols" min="1" required></label>
            <br><br>
            <input type="submit" value="Generate">
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $rows = intval($_POST["rows"]);
            $cols = intval($_POST["cols"]);

            echo "<table>";
            
            // Header row
            echo "<tr><th>X</th>";
            for ($c = 1; $c <= $cols; $c++) {
                echo "<th>$c</th>";
            }
            echo "</tr>";

            // Table body
            for ($r = 1; $r <= $rows; $r++) {
                echo "<tr>";
                echo "<th>$r</th>"; // Row header
                for ($c = 1; $c <= $cols; $c++) {
                    $product = $r * $c;
                    if ($product % 2 != 0) {
                        echo "<td class='odd'>$product</td>";
                    } else {
                        echo "<td>$product</td>";
                    }
                }
                echo "</tr>";
            }

            echo "</table>";
        }
        ?>
    </div>
</body>
</html>