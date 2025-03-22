<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Loading...</title>
<style>
  /* Loading screen styles */
  body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    overflow: hidden;
  }
  .loading-screen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
  }
  .loading-text {
    font-size: 24px;
    color: #333333;
    animation: fadeIn 1s ease-in-out forwards;
  }
  @keyframes fadeIn {
    0% { opacity: 0; }
    100% { opacity: 1; }
  }
  /* Background image styles */
  .background-images {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    animation: fadeInBackground 5s ease-in-out forwards;
  }
  @keyframes fadeInBackground {
    0% { opacity: 0; }
    100% { opacity: 1; }
  }
</style>
</head>
<body>
<!-- Background images -->
<div class="background-images">
  <!-- Add your background images here -->
  <img src="image1.jpg" alt="">
  <img src="image2.jpg" alt="">
  <!-- Add more images as needed -->
</div>

<!-- Loading text -->
<div class="loading-screen">
  <div class="loading-text">SHARON DISTRIBUTION ENTERPRISES...</div>
</div>

<script>
  // Redirect to login page after a delay (for demonstration)
  setTimeout(function(){
    window.location.href = 'index.php';
  }, 3000); // Redirect after 3 seconds (adjust as needed)
</script>
</body>
</html>