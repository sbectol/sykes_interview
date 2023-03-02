<?php
session_start();
 if($_SERVER['REQUEST_METHOD'] === "POST") {
  
  session_unset();
  
 }
  $servername = "localhost";
  $username = "";
  $password = "";
  $dbname = "sykes_interview";
  $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
  $num_results_on_page = 1;

  if(isset($_POST['location'])) 
      {
          $location = $_POST['location'];
          $_SESSION["location"] = $_POST['location'];
      }
      else 
      {
          $location = "";
      };

  if(isset($_POST['near_beach'])) {
    $near_beach = 1;
    $_SESSION["near_beach"] = 1;
   } 
  if(isset($_POST['pets'])) {
    $pets = 1; 
    $_SESSION["pets"] = 1; 
  } 
   
  if(isset($_POST['min_sleeps'])) 
      {
          $min_sleeps = intval($_POST['min_sleeps']);
          $_SESSION["min_sleeps"] = intval($_POST['min_sleeps']);
      }
      else 
      {
          $min_sleeps = 0;
      };
  if(isset($_POST['min_beds'])) 
      {
          $min_beds = intval($_POST['min_beds']);
          $_SESSION["min_beds"] = intval($_POST['min_beds']);
      }
      else 
      {
          $min_beds = 0;
      };
  if(isset($_POST['start_date'])) 
      {
          $start_date = $_POST['start_date'];
          $_SESSION["start_date"] = $_POST['start_date'];
      }
      else 
      {
          $start_date = date("Y-m-d");
      };
  if(isset($_POST['end_date'])) 
      {
        $end_date = $_POST['end_date'];
        $_SESSION["end_date"] = $_POST['end_date'];
      }
      else 
      {
        $nextweek = strtotime("+7 day");
        $end_date = date("Y-m-d", $nextweek);
      };
    if($_SERVER['REQUEST_METHOD'] === "GET") {
      $location = $_SESSION["location"];
      $near_beach = $_SESSION["near_beach"];
      $pets = $_SESSION["pets"];
      $min_sleeps = $_SESSION["min_sleeps"];
      $min_beds = $_SESSION["min_beds"];
      $start_date = $_SESSION["start_date"];
      $end_date = $_SESSION["end_date"];
      
    }  
  ?>
<!doctype html>
<html lang="en">
    <head>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <header>
  <div class="bg-dark" id="navbarHeader">
    <div class="container">
      <div class="row">
        <div class="col-sm-10 col-md-12 py-4">
        <form action="index.php" method="post" class="d-flex" role="search">
          <div class="form-text mx-4 px-2">
            <input class="form-control me-2" name="location" type="search" placeholder="Search" aria-label="Search" <?php echo 'value='.$location; ?>>
          </div>
          <div class="form-check px-2">
            <input name="pets" type="checkbox" class="form-check-input" id="pets" <?php if(isset($pets)) echo 'checked'; ?>>
            <label class="form-check-label text-white" for="pets">Pets welcome?</label>
          </div>
          <div class="form-check ">
            <input name="near_beach" type="checkbox" class="form-check-input" id="near_beach" <?php if(isset($near_beach)) echo 'checked'; ?>>
            <label class="form-check-label text-white" for="near_beach">Near beach?</label>
          </div>
          <div class="form-text px-2">
            <input class="form-control me-2" name="min_sleeps" type="search" id="min_sleeps" placeholder="Sleeps" <?php echo 'value='.$min_sleeps; ?>>
            <label class="form-check-label text-white" for="min_sleeps">Sleeps?</label>
          </div>
          <div class="form-text px-2">
            <input class="form-control me-2" name="min_beds" type="search" id="min_beds" placeholder="Minimum beds" <?php echo 'value='.$min_beds; ?>>
            <label class="form-check-label text-white" for="min_beds">Minimum number of beds?</label>
          </div>
          <div class="form-text px-2">
            <input class="form-control me-4" name="start_date" type="search" id="start_date" placeholder="Start Date" <?php echo 'value='.$start_date; ?>>
            <label class="form-check-label text-white" for="min_sleeps">Start Date</label>
          </div>
          <div class="form-text px-2">
            <input class="form-control me-4" name="end_date" type="search" id="end_date" placeholder="End Date" <?php echo 'value='.$end_date; ?>>
            <label class="form-check-label text-white" for="end_date">End Date</label>
          </div>
          <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
        </div>
        
      </div>
    </div>
  </div>
  
</header>

<main>
<div class="album py-5 bg-light">
    <div class="container">
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
      <?php
        $connection = new mysqli($servername, $username, $password, $dbname);
        $calc_page = ($page - 1) * $num_results_on_page;
        $sql = "SELECT properties.property_name, locations.location_name
        FROM properties
        left join locations on properties._fk_location = locations.__pk
        where locations.location_name like '%".$location."%'
        and properties.sleeps >= $min_sleeps 
        and properties.beds >= $min_beds
        and properties.__pk not in (select _fk_property from bookings where (start_date <= '" . $end_date . "' and end_date >= '" . $start_date ."'))";
        if(isset($pets)) $sql .= " and properties.accepts_pets = $pets ";
        if(isset($near_beach)) $sql .= "and properties.near_beach = $near_beach ";
        
        $result = $connection->query($sql);
        $total_pages = $result->num_rows;
        $sql = "SELECT properties.property_name, locations.location_name
        FROM properties
        left join locations on properties._fk_location = locations.__pk
        where locations.location_name like '%".$location."%'
        and properties.sleeps >= $min_sleeps 
        and properties.beds >= $min_beds
        and properties.__pk not in (select _fk_property from bookings where (start_date <= '" . $end_date . "' and end_date >= '" . $start_date ."'))";
        if(isset($pets)) $sql .= " and properties.accepts_pets = $pets ";
        if(isset($near_beach)) $sql .= "and properties.near_beach = $near_beach ";
        $sql .= "limit $calc_page,$num_results_on_page";
        $result = $connection->query($sql);

        // if ($result->num_rows > 0) {
        //   while($row = $result->fetch_assoc()) {
        // echo  $row["property_name"] . " " .  $row["location_name"] . "<br>";
        //   }
        // }
        
       
        if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            ?>
            <div class="col">
                <div class="card shadow-sm">
                    <svg class="bd-placeholder-img card-img-top" width="100%" height="225" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#55595c"/><text x="50%" y="50%" fill="#eceeef" dy=".3em">Thumbnail</text></svg>
                    <div class="card-body">
                        <p class="card-text"><?php echo  $row["property_name"] . " " .  $row["location_name"] ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary">View</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary">Book Now</button>
                            </div>
                        <small class="text-muted">£200</small>
                    </div>
                </div>
            </div>
        </div>
                
                <?php
            
          }
        } else {
        echo "0 results";
        }
        $connection->close(); 
?>
        
        
        
      </div>
      <?php if (ceil($total_pages / $num_results_on_page) > 0): ?>
        <ul class="pagination">
	      <?php if ($page > 1): ?>
          <li class="page-item"><a class="page-link" href="index.php?page=<?php echo $page-1 ?>">Prev</a></li>
          <?php endif; ?>

          <?php if ($page > 3): ?>
          <li class="page-item"><a class="page-link" href="index.php?page=1">1</a></li>
          <li class="page-item"><span class="page-link">...</span></li>
          <?php endif; ?>

          <?php if ($page-2 > 0): ?><li class="page-item"><a class="page-link" href="index.php?page=<?php echo $page-2 ?>"><?php echo $page-2 ?></a></li><?php endif; ?>
          <?php if ($page-1 > 0): ?><li class="page-item"><a class="page-link" href="index.php?page=<?php echo $page-1 ?>"><?php echo $page-1 ?></a></li><?php endif; ?>

          <li class="page-item"><a class="page-link" href="index.php?page=<?php echo $page ?>"><?php echo $page ?></a></li>

          <?php if ($page+1 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page-item"><a class="page-link" href="index.php?page=<?php echo $page+1 ?>"><?php echo $page+1 ?></a></li><?php endif; ?>
          <?php if ($page+2 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page-item"><a class="page-link" href="index.php?page=<?php echo $page+2 ?>"><?php echo $page+2 ?></a></li><?php endif; ?>

          <?php if ($page < ceil($total_pages / $num_results_on_page)-2): ?>
          <li class="page-item"><span class="page-link">...</span></li>
          <li class="page-item"><a class="page-link" href="index.php?page=<?php echo ceil($total_pages / $num_results_on_page) ?>"><?php echo ceil($total_pages / $num_results_on_page) ?></a></li>
          <?php endif; ?>

          <?php if ($page < ceil($total_pages / $num_results_on_page)): ?>
          <li class="page-item"><a class="page-link" href="index.php?page=<?php echo $page+1 ?>">Next</a></li>
          <?php endif; ?>
</ul>
<?php endif; ?>
    </div>
  </div>

</main>

<footer class="text-muted py-5">
  <div class="container">
    <p class="float-end mb-1">
      <a href="#">Back to top</a>
    </p>
   
  </div>
</footer>


    <script src="js/bootstrap.bundle.min.js"></script>
 
</form>
   
    </body>
</html>