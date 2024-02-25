<?php
session_start();

if (!isset($_SESSION['isUserLoggedIn'])) {
  header('Location: index.php');
  exit();
}

require_once "DBconnect.php";
mysqli_report(MYSQLI_REPORT_STRICT);
try {
  $connect = new mysqli($host, $db_user, $db_password, $db_name);
  if ($connect->connect_errno != 0) {
    throw new Exception(mysqli_connect_errno());
  } else {
    $userCategoryQuery = "SELECT * 
                          FROM incomes_category_assigned_to_users 
                          WHERE user_id = '$_SESSION[idLoggedInUser]'";
    $queryResult = $connect->query($userCategoryQuery);
    $rows = $queryResult->fetch_all(MYSQLI_ASSOC);

    $connect->close();
  }
} catch (Exception $error) {
  echo 'Server error';
}

if (isset($_POST['formCategory'])) {

  $okValidation = true;

  //is field amout empty?
  $revenueSum = $_POST['formSum'];
  if ($revenueSum <= 0) {
    $okValidation = false;
    $_SESSION['e_formSum'] = "Amount field must be greater than 0.";
  }

  //is date greater than 2000.01.01?
  $revenueDate = $_POST['formDate'];
  $year = substr($revenueDate, 0, 4);
  $month = substr($revenueDate, 5, 2);
  $day = substr($revenueDate, 8, 2);
  (int)$dateFromForm = "$year$month$day";

  if ($dateFromForm < 20000101) {
    $okValidation = false;
    $_SESSION['e_formDate'] = "The program does not support dates before 2000.01.01.";
  }

  //is date greater than current date?
  $currentDate = date("Y-m-d");
  if ($revenueDate > $currentDate) {
    $okValidation = false;
    $_SESSION['e_formDate'] = "Date greater than the current one.";
  }

  //checking comment
  $revenueComment = $_POST['formComment'];
  if (!empty($revenueComment)) {
    if (strlen($revenueComment) > 60) {
      $okValidation = false;
      $_SESSION['e_Comment'] = "Comment can have a maximum of 60 characters.";
    }
    if (!ctype_alnum($revenueComment)) {
      $okValidation = false;
      $_SESSION['e_Comment'] = "Comment can only consist of letters and numbers.";
    }
  }

  $revenueCategory = $_POST['formCategory'];
  $userId =  $_SESSION['idLoggedInUser'];

  if ($okValidation) {
    mysqli_report(MYSQLI_REPORT_STRICT);
    try {
      $connect = new mysqli($host, $db_user, $db_password, $db_name);
      if ($connect->connect_errno != 0) {
        throw new Exception(mysqli_connect_errno());
      } else {
        if ($okValidation) {

          $incomeCategoryIdQuery = "SELECT id FROM incomes_category_assigned_to_users WHERE user_id = '$userId' AND name = '$revenueCategory'";
          $queryResult = $connect->query($incomeCategoryIdQuery);
          $row = $queryResult->fetch_assoc();
          $idCurrentCategory = $row['id'];

          if ($connect->query("INSERT INTO incomes VALUES (NULL, '$userId', '$idCurrentCategory', '$revenueSum', '$revenueDate', '$revenueComment')")) {
            echo '<script>alert("Income added successfully!")</script>';
          } else {
            throw new Exception(mysqli_connect_errno());
          }
        }
        $connect->close();
      }
    } catch (Exception $error) {
      echo '<script>alert("Server error")</script>';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add revenue</title>
  <script src="site.js"></script>
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
            <a href="./logout.php" class="btn btn-outline-danger" role="button"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z" />
                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" />
              </svg>
              Sign out</a>
          </div>
        </div>
      </div>
    </nav>
  </section>

  <section id="add-Revenue-menu">
    <form method="post">
      <div class="modal modal-sheet position-static d-block bg-body-secondary p-4 md-5" tabindex="-1" role="dialog" id="modalSheet">
        <div class="modal-dialog" role="document">
          <div class="modal-content rounded-4 shadow">
            <div class="gradient-custom-2 modal-header border-bottom-0" style="color: white">
              <h1 class="modal-title fs-5">Adding new revenue</h1>
            </div>

            <div class="modal-footer flex-column align-items-stretch w-100 gap-2 pb-4 border-top-0"></div>
            <div class="container">
              <div class="col pb-3">
                <h6 class="px-2">Amount of income</h6>
                <input type="number" step="0.01" name="formSum" class="form-control" placeholder=" Amount" />

              </div>
              <?php
              if (isset($_SESSION['e_formSum'])) {
                echo '<div class="error">' . $_SESSION['e_formSum'] . '</div>';
                unset($_SESSION['e_formSum']);
              }
              ?>
              <h6 class="px-2">Income date</h6>
              <div class="col pb-3">
                <input type="date" name="formDate" class="form-control" value="<?php echo date('Y-m-j'); ?>" />
              </div>
              <?php
              if (isset($_SESSION['e_formDate'])) {
                echo '<div class="error">' . $_SESSION['e_formDate'] . '</div>';
                unset($_SESSION['e_formDate']);
              }
              ?>
              <h6 class="px-2">Source of income</h6>
              <div class="pb-3">
                <select class="form-select" name="formCategory" aria-label="Default select example" value="">
                  <?php
                  foreach ($rows as $row) {
                  ?>
                    <option value="<?= $row['name'] ?>"><?= $row['name'] ?></option>
                  <?php
                  }
                  ?>
                </select>
              </div>
              <h6 class="px-2">Comment (optional)</h6>
              <div class="col pb-4">
                <input type="text" name="formComment" class="form-control" value="<?php
                                                    if (isset($revenueComment)) {
                                                      echo ($revenueComment);
                                                      unset($revenueComment);
                                                    }
                                                    ?>" />
              </div>
              <?php
              if (isset($_SESSION['e_Comment'])) {
                echo '<div class="error">' . $_SESSION['e_Comment'] . '</div>';
                unset($_SESSION['e_Comment']);
              }
              ?>
            </div>

            <div class="text-center">
              <button type="submit" class="btn btn-lg btn-primary mb-3" style="background-color: #ee7724; width: 40%">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-floppy" viewBox="0 0 16 16">
                  <path d="M11 2H9v3h2z" />
                  <path d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z" />
                </svg>
                Add revenue
              </button>
            </div>

            <div class="text-center pb-4">
              <a href="./logged.php" class="btn btn-lg btn-secondary mb-2" style="width: 40%">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                  <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                </svg>
                Close
              </a>
            </div>
          </div>
        </div>
      </div>
    </form>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>