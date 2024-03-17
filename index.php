<?php

session_start(); // Start session (or resume if already started)

$logged_in_user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$account_type = isset($_SESSION['account_type']) ? $_SESSION['account_type'] : '';

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<body>
    <header>
    <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="info.php">Info</a></li>
                <?php if ($account_type == 'professor'): ?>
        <li><a href="professor_assignment.php">Assignments</a></li>
        <?php elseif ($account_type == 'student'): ?>
        <li><a href="student_assignment.php">Assignments</a></li>
        <?php else: ?>
                <li><a href="defaultassignment.php">Assignments</a></li>
        <?php endif; ?>
                <?php if ($logged_in_user): ?>
      <li class = "welcomeuser"><a href="#"><?php echo $logged_in_user; ?></a></li>
      <li><a href="logout.php">Logout</a></li>
    <?php else: ?>
      <li><a href="login.php">Log in</a></li>
      <li> <a href="register.php">Sign up</a></li>
    <?php endif; ?>
            </ul>
        </nav>
    </header>

    <section id="hero">
        <div class="hero-content">
            <h1> <img src="img/BMCC Complete.png"> </h1>
            <p class = "centertext"> Helping students to finish what they started. </p>




           
           
            <?php if ($account_type === 'student'): ?>
                <a href="student_assignment.php"> <button class="button" type="submit" name="getstarted">Assignments</button> </a>
      <?php elseif ($account_type === 'professor'): ?>
      <a href="professor_assignment.php"> <button class="button" type="submit" name="getstarted">Assignments</button> </a>
      <?php else: ?>
            <a href="register.php"> <button class="button" type="submit" name="getstarted">Get Started</button> </a>
        <?php endif; ?>



        </div>
    </section>


    <div class = "footer">
        <p class = "footertext">  Tech Innovation Hub &copy; 2024 </p>
    </div>

    <script src="js/script.js"></script>
    



<!-- Chatbot Embed -->
<script>
  window.watsonAssistantChatOptions = {
    integrationID: "51ea6444-97d8-45cd-bbd6-4876ad5383cf", // The ID of this integration.
    region: "us-east", // The region your integration is hosted in.
    serviceInstanceID: "37278a73-59d6-4106-b380-97070831378a", // The ID of your service instance.
    onLoad: async (instance) => { await instance.render(); }
  };
  setTimeout(function(){
    const t=document.createElement('script');
    t.src="https://web-chat.global.assistant.watson.appdomain.cloud/versions/" + (window.watsonAssistantChatOptions.clientVersion || 'latest') + "/WatsonAssistantChatEntry.js";
    document.head.appendChild(t);
  });
</script>

</body>
</html>