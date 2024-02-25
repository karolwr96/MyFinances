<?php
session_start();

require_once "DBconnect.php";
mysqli_report(MYSQLI_REPORT_STRICT);
try {
  $connect = new mysqli($host, $db_user, $db_password, $db_name);
  if ($connect->connect_errno != 0) {
    throw new Exception(mysqli_connect_errno());
  } else {
    if (isset($_POST['email'])) {
      $email = $_POST['email'];
      $password = $_POST['password'];

      $loggingQuery = "SELECT * FROM users WHERE email = '$email'";

      if ($loggingResult = $connect->query($loggingQuery)) {
        $howManyUsers = $loggingResult->num_rows;
        if ($howManyUsers > 0) {
          $row = $loggingResult->fetch_assoc();
          if (password_verify($password, $row['password'])) {
            $_SESSION['isUserLoggedIn'] = true;
            $_SESSION['idLoggedInUser'] = $row['id'];
            header('Location:logged.php');
          } else {
            $_SESSION['loggingError'] = '<span style="color:red">Wrong email or password.</span>';
          }
        } else {
          $_SESSION['loggingError'] = '<span style="color:red">Wrong email or password.</span>';
        }
      } else {
        throw new Exception(mysqli_connect_errno());
      }
      $connect->close();
    }
  }
} catch (Exception $error) {
  echo '<script>alert("Server error")</script>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MyFinances</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous" />
  <link rel="stylesheet" href="./css/style.css" />
</head>

<body>
  <section class="h-100 gradient-form" style="background-color: #eee;">
    <div class="container py-0 h-100">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-xl-10">
          <div class="card rounded-3 text-black">
            <div class="row g-0">
              <div class="col-lg-6">
                <div class="card-body p-md-5 mx-md-4">

                  <div class="text-center">
                    <img src="./sources/logo2.png" style="width: 185px;" alt="logo">
                    <h3 class="mt-1 mb-5 pb-1">We are MyFinances</h3>
                  </div>

                  <form method="post">
                    <p class="mb-4">Please login to your account</p>

                    <div class="form-outline mb-3">
                      <input type="email" id="form2Example11" name="email" class="form-control " placeholder="E-mail adress" />
                      <label class="form-label" for="form2Example11"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                          <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z" />
                        </svg> E-mail adress</label>
                    </div>

                    <div class="form-outline mb-4">
                      <input type="password" id="form2Example22" name="password" class="form-control" placeholder="********" />
                      <label class="form-label" for="form2Example22"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star" viewBox="0 0 16 16">
                          <path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288L8 2.223l1.847 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.565.565 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z" />
                        </svg> Password</label>
                      <br />
                      <?php
                      if (isset($_SESSION['loggingError'])) echo $_SESSION['loggingError'];
                      unset($_SESSION['loggingError']);
                      ?>
                    </div>

                    <div class="text-center pt-1 mb-1 pb-1">
                      <button class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="submit"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                          <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z" />
                          <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" />
                        </svg> Log
                        in</button>
                    </div>
                  </form>

                  <div class="text-center mb-4">
                    <a class="text-muted" href="">Forgot password?</a>
                  </div>
                  <div class="d-flex align-items-center justify-content-center pb-4">
                    <p class="mb-0 me-2">Don't have an account?</p>
                    <a href="./register.php" class="btn btn-outline-danger" role="button"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hammer" viewBox="0 0 16 16">
                        <path d="M9.972 2.508a.5.5 0 0 0-.16-.556l-.178-.129a5.009 5.009 0 0 0-2.076-.783C6.215.862 4.504 1.229 2.84 3.133H1.786a.5.5 0 0 0-.354.147L.146 4.567a.5.5 0 0 0 0 .706l2.571 2.579a.5.5 0 0 0 .708 0l1.286-1.29a.5.5 0 0 0 .146-.353V5.57l8.387 8.873A.5.5 0 0 0 14 14.5l1.5-1.5a.5.5 0 0 0 .017-.689l-9.129-8.63c.747-.456 1.772-.839 3.112-.839a.5.5 0 0 0 .472-.334z" />
                      </svg> Create now</a>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                  <h2 class="mb-4">We are more than just a company.
                    Why our app?
                  </h2>
                  <div class="newFont">
                    <p class="small mb-0">
                    <ul>
                      <li>Simplify Your Finances</li>
                      <li>Real-Time Updates</li>
                      <li>Budgeting Made Easy</li>
                      <li>Secure and Convenient</li>
                      <li>Financial Insights</li>
                      <li>Integration with Other Services</li>
                    </ul>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</body>

</html>