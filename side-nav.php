<div class="side-nav">
  <a class="logo" href="index.php">
    <img src="css/images/logo-no-text.png" title="Dashboard" alt="Logo">
  </a>
  <div class="name">
    <p class="user-name"><?php echo($_SESSION["name"]); ?> </p>
    <p class="user-initials"><?php  $splitName = explode(" ",$_SESSION["name"]);
                                    $initials = "";
                                    foreach ($splitName as $values){
                                      $initials .= substr($values,0,1);
                                    };
                                    echo($initials); ?> </p>
      <ul class="user-settings">
        <li><a href="phpScripts/logout.php">Logout</a></li>
    </ul>
    </div>
  <a href="exerciseMaker.php" class="nav-icon">
    <img src="css/images/side-nav-images/weight.png" title="Create New Exercise">
    <p>Create New Exercise</p>
  </a>
  <a href="graphs.php" class="nav-icon">
    <img src="css/images/side-nav-images/graph.png" title="Graphs">
    <p>Progress<br>Graphs</p>
  </a>
  <a href="inputSession.php" class="nav-icon">
    <img src="css/images/side-nav-images/stopwatch.png" title="Input Session">
    <p>Input Session</p>
  </a>
  <a href="map.php" class="nav-icon">
    <img src="css/images/side-nav-images/gym-finder.png" title="Find a Gym">
    <p>Find a Gym</p>
  </a>
</div>
