
<?php
$name = "";
$basic = "";
$allowance = "";
$deduction = "";
$error = "";
$submitted = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $basic = $_POST["basic"] ?? "";
    $allowance = $_POST["allowance"] ?? "";
    $deduction = $_POST["deduction"] ?? "";

    if (
        $name === "" ||
        !is_numeric($basic) ||
        !is_numeric($allowance) ||
        !is_numeric($deduction) ||
        (float)$basic < 0 ||
        (float)$allowance < 0 ||
        (float)$deduction < 0
    ) {
        $error = "Please enter valid employee and salary details.";
    } elseif (
        (float)$deduction > (float)$basic + (float)$allowance
    ) {
        $error = "Deductions cannot exceed gross salary.";
    } else {
        $basic = (float)$basic;
        $allowance = (float)$allowance;
        $deduction = (float)$deduction;

        $gross = $basic + $allowance;
        $net = $gross - $deduction;
        $submitted = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayrollPro - Payroll Management System</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #eef5fb;
            color: #263747;
        }

        header {
            background: #174a7e;
            color: white;
            padding: 28px 15px;
            text-align: center;
        }

        header h1 {
            margin: 0 0 10px;
            font-size: 34px;
        }

        header p {
            margin: 0;
            font-size: 17px;
        }

        main {
            max-width: 900px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            padding: 30px;
            margin-bottom: 25px;
            border-radius: 13px;
            box-shadow: 0 5px 20px rgba(30, 65, 95, 0.08);
        }

        h2 {
            margin-top: 0;
            color: #174a7e;
        }

        label {
            display: block;
            margin: 17px 0 8px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #cbd8e5;
            border-radius: 7px;
            font-size: 16px;
        }

        input:focus {
            outline: 2px solid #83c8e5;
            border-color: #1769aa;
        }

        button {
            background: #1769aa;
            color: white;
            padding: 14px 22px;
            border: none;
            border-radius: 7px;
            margin-top: 22px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #104c80;
        }

        .result {
            background: #eaf7ef;
            border: 1px solid #c8e9d2;
            border-radius: 9px;
            padding: 20px;
        }

        .result p {
            padding: 6px 0;
            border-bottom: 1px solid #d4e8d9;
        }

        .net {
            color: #14763c;
            font-size: 25px;
            font-weight: bold;
        }

        .error {
            color: #b42318;
            background: #fff0ef;
            padding: 12px;
            border-radius: 7px;
        }

        footer {
            text-align: center;
            padding: 22px;
            color: #647586;
        }

        @media (max-width: 600px) {
            .card {
                padding: 20px;
            }

            header h1 {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>
    <header>
        <h1>PayrollPro</h1>
        <p>Employee Payroll Management System</p>
    </header>

    <main>
        <section class="card">
            <h2>Calculate Employee Salary</h2>

            <form method="POST" action="">
                <label for="name">Employee Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    maxlength="100"
                    required
                    value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
                >

                <label for="basic">Basic Salary (₹)</label>
                <input
                    type="number"
                    id="basic"
                    name="basic"
                    min="0"
                    step="0.01"
                    required
                    value="<?= htmlspecialchars((string)$basic, ENT_QUOTES, 'UTF-8') ?>"
                >

                <label for="allowance">Allowances (₹)</label>
                <input
                    type="number"
                    id="allowance"
                    name="allowance"
                    min="0"
                    step="0.01"
                    required
                    value="<?= htmlspecialchars((string)$allowance, ENT_QUOTES, 'UTF-8') ?>"
                >

                <label for="deduction">Deductions (₹)</label>
                <input
                    type="number"
                    id="deduction"
                    name="deduction"
                    min="0"
                    step="0.01"
                    required
                    value="<?= htmlspecialchars((string)$deduction, ENT_QUOTES, 'UTF-8') ?>"
                >

                <button type="submit">Calculate Payroll</button>
            </form>

            <?php if ($error !== ""): ?>
                <p class="error">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>
        </section>

        <?php if ($submitted): ?>
            <section class="card">
                <h2>Payroll Summary</h2>

                <div class="result">
                    <p>
                        <strong>Employee Name:</strong>
                        <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <p>
                        Basic Salary:
                        ₹<?= number_format($basic, 2) ?>
                    </p>

                    <p>
                        Allowances:
                        ₹<?= number_format($allowance, 2) ?>
                    </p>

                    <p>
                        Gross Salary:
                        ₹<?= number_format($gross, 2) ?>
                    </p>

                    <p>
                        Deductions:
                        ₹<?= number_format($deduction, 2) ?>
                    </p>

                    <p class="net">
                        Net Salary:
                        ₹<?= number_format($net, 2) ?>
                    </p>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <footer>
        PayrollPro | Docker and Kubernetes Micro-Project
    </footer>
</body>
</html>
