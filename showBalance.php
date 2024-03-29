<?php
session_start();

if (!isset($_SESSION['isUserLoggedIn'])) {
  header('Location: index.php');
  exit();
}

if (isset($_POST['formBalanceData'])) {
  $selectedInterval = $_POST['formBalanceData'];

  if ($selectedInterval == "currentMonth") {
    $currentMonth = date('m');
    $currentYear = date('Y');
    $startDate = date('Y-m-01', strtotime($currentYear . '-' . $currentMonth . '-01'));
    $endDate = date('Y-m-t', strtotime($currentYear . '-' . $currentMonth . '-01'));
  } else if ($selectedInterval == "previousMonth") {
    $firstDayPrevMonth = new DateTime('first day of last month');
    $startDate = $firstDayPrevMonth->format('Y-m-d');
    $lastDayPrevMonth = new DateTime('last day of last month');
    $endDate = $lastDayPrevMonth->format('Y-m-d');
  } else {
    $startDate = $_POST['fromDate'];
    $endDate = $_POST['toDate'];
  }

  $userId =  $_SESSION['idLoggedInUser'];

  require_once "DBconnect.php";
  mysqli_report(MYSQLI_REPORT_STRICT);
  try {
    $connect = new mysqli($host, $db_user, $db_password, $db_name);
    if ($connect->connect_errno != 0) {
      throw new Exception(mysqli_connect_errno());
    } else {
      $showIncomesBalanceQuery = "SELECT incomes_category_assigned_to_users.name AS category, SUM(incomes.amount) AS amount FROM incomes_category_assigned_to_users INNER JOIN incomes ON incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id WHERE incomes.user_id = '$userId' AND incomes.date_of_income  BETWEEN '$startDate' AND '$endDate' GROUP BY incomes.income_category_assigned_to_user_id ORDER BY amount DESC;";
      if ($queryResult = $connect->query($showIncomesBalanceQuery)) {
        $arrayWithResult = $queryResult->fetch_all();
      } else {
        echo '<script>alert("Server error")</script>';
      }

      $showExpensesBalanceQuery = "SELECT expenses_category_assigned_to_users.name AS category, SUM(expenses.amount) AS amount FROM expenses_category_assigned_to_users INNER JOIN expenses ON expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id WHERE expenses.user_id = '$userId' AND expenses.date_of_expense  BETWEEN '$startDate' AND '$endDate' GROUP BY expenses.expense_category_assigned_to_user_id ORDER BY amount DESC;";
      if ($queryExpensesResult = $connect->query($showExpensesBalanceQuery)) {
        $arrayWithExpenses = $queryExpensesResult->fetch_all();
      } else {
        echo '<script>alert("Server error")</script>';
      }

      //Try to get sum of expenses 
      $getSumOfExpensesQuery = "SELECT SUM(amount) AS totalExpenses FROM expenses  WHERE expenses.user_id = '$userId' AND expenses.date_of_expense  BETWEEN '$startDate' AND '$endDate';";
      if ($queryExpensesResult = $connect->query($getSumOfExpensesQuery)) {
        $queryExpensesResult = $connect->query($getSumOfExpensesQuery);
        $arrayWithSumOfExpenses =  $queryExpensesResult->fetch_assoc();
        $sumOfExpenses = $arrayWithSumOfExpenses['totalExpenses'];
      } else {
        echo '<script>alert("Server error")</script>';
      }

      //Try to get sum of incomes 
      $getSumOfIncomesQuery = "SELECT SUM(amount) AS totalIncomes FROM incomes  WHERE incomes.user_id = '$userId' AND incomes.date_of_income  BETWEEN '$startDate' AND '$endDate';";
      if ($queryIncomesResult = $connect->query($getSumOfIncomesQuery)) {
        $arrayWithSumOfIncomes =  $queryIncomesResult->fetch_assoc();
        $sumOfIncomes = $arrayWithSumOfIncomes['totalIncomes'];
      } else {
        echo '<script>alert("Server error")</script>';
      }
      $totalBalance = $sumOfIncomes - $sumOfExpenses;

      $connect->close();
    }
  } catch (Exception $error) {
    echo 'Server error';
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Show balance</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous" />
  <link rel="stylesheet" href="./css/style.css" />
</head>

<body>
  <section id="navigation-bar">
    <nav class="navbar navbar-expand-lg bg-body-tertiary px-3">
      <div class="container-fluid">
        <a class="navbar-brand" href="./logged.php"><img src="./sources/logo2.png" alt="moveit brand icon" height="85" />
          MyFinances</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0 pr-5" style="font-size: 18px">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="./addRevenue.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cash-coin" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0z" />
                  <path d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1h-.003zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195l.054.012z" />
                  <path d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083c.058-.344.145-.678.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1H1z" />
                  <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 5.982 5.982 0 0 1 3.13-1.567z" />
                </svg>
                Add revenue</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="./addExpense.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-wallet2" viewBox="0 0 16 16">
                  <path d="M12.136.326A1.5 1.5 0 0 1 14 1.78V3h.5A1.5 1.5 0 0 1 16 4.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 13.5v-9a1.5 1.5 0 0 1 1.432-1.499L12.136.326zM5.562 3H13V1.78a.5.5 0 0 0-.621-.484L5.562 3zM1.5 4a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-13z" />
                </svg>
                Add expense</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="./showBalance.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-spreadsheet" viewBox="0 0 16 16">
                  <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V9H3V2a1 1 0 0 1 1-1h5.5v2zM3 12v-2h2v2H3zm0 1h2v2H4a1 1 0 0 1-1-1v-1zm3 2v-2h3v2H6zm4 0v-2h3v1a1 1 0 0 1-1 1h-2zm3-3h-3v-2h3v2zm-7 0v-2h3v2H6z" />
                </svg>
                View balance sheet</a>
            </li>
            <li class="nav-item active">
              <a class="nav-link active" href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                  <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z" />
                  <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z" />
                </svg>
                Settings</a>
            </li>
          </ul>

          <div>
            <a href="./index.php" class="btn btn-outline-danger" role="button"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z" />
                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" />
              </svg>
              Sign out</a>
          </div>
        </div>
      </div>
    </nav>
  </section>

  <section id="balance-sheet">
    <div class="container col-lg-4 p-4 md-5">
      <div class="shadow">
        <div class="gradient-custom-2" style="
              color: white;
              height: 55px;
              border-radius: 0.3rem;
              display: flex;
              align-items: center;
            ">
          <h4 class="px-3">Your balance sheet</h4>
        </div>
        <div class="container mt-3">
          <div class="row g-0 mb-5">
            <h6 class="px-2">Select date range</h6>
            <form method="post">
              <div class="pb-3">
                <select id="options" class="form-select" name="formBalanceData" aria-label="Default select example" onchange="toggleFields()">
                  <option value="currentMonth">Current month</option>
                  <option value="previousMonth">Previous month</option>
                  <option value="individualInterval">Own scope</option>
                </select>
                <br>
                <div id="fields" class="hidden">
                  <h6 class="px-2">From:</h6>
                  <input type="date" name="fromDate" class="form-control">
                  <br>
                  <h6 class="px-2">To:</h6>
                  <input type="date" name="toDate" class="form-control">
                </div>
              </div>

              <div class="text-center">
                <button type="submit" class="btn btn-lg btn-primary mb-3" style="background-color: #ee7724; width: 40%">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-square-fill" viewBox="0 0 16 16">
                    <path d="M0 14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2zm4.5-6.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5a.5.5 0 0 1 0-1" />
                  </svg>
                  Show
                </button>
              </div>
            </form>

            <div class="pb-3">
              <table class="table">
                <thead>
                  <h6 class="px-2">Total incomes: <?php if (isset($sumOfIncomes)) {
                                                    echo $sumOfIncomes;
                                                  } ?></h6>
                  <tr>
                    <th>Category</th>
                    <th>Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (isset($_POST['formBalanceData'])) {
                    foreach ($arrayWithResult as $row) {
                      echo "<tr>
                    <td>{$row['0']}</td>
                    <td>{$row['1']}</td>
                    </tr>";
                    }
                  }
                  ?>
                </tbody>
              </table>

              <table class="table">
                <thead>
                  <h6 class="px-2">Total expenses: <?php if (isset($sumOfExpenses)) {
                                                      echo $sumOfExpenses;
                                                    } ?></h6>
                  <tr>
                    <th>Category</th>
                    <th>Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (isset($_POST['formBalanceData'])) {
                    foreach ($arrayWithExpenses as $row) {
                      echo "<tr>
                    <td>{$row['0']}</td>
                    <td>{$row['1']}</td>
                    </tr>";
                    }
                  }
                  ?>
                </tbody>
              </table>

              <div class="my-3">
                <h5 class="px-3" style="text-align: center;">Your balance is: <?php if (isset($totalBalance)) {
                                                                                echo $totalBalance;
                                                                              } ?></h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
  <script>
    function toggleFields() {
      const select = document.getElementById('options');
      const fields = document.getElementById('fields');

      if (select.value === 'individualInterval') {
        fields.classList.remove('hidden');
      } else {
        fields.classList.add('hidden');
      }
    }
  </script>
</body>

</html>