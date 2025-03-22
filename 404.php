<?php
session_start();

// Game state variables
$location = isset($_SESSION['location']) ? $_SESSION['location'] : 'start';

// Game logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle user input
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($location) {
        case 'start':
            if ($action == 'explore') {
                $location = 'cave';
            }
            break;
        case 'cave':
            if ($action == 'back') {
                $location = 'start';
            } elseif ($action == 'open_chest') {
                $location = 'treasure';
            }
            break;
        case 'treasure':
            if ($action == 'continue') {
                $location = 'start';
            }
            break;
    }

    $_SESSION['location'] = $location;
}

// Game content
switch ($location) {
    case 'start':
        $output = "You are standing in front of a cave. What do you want to do?<br>";
        $output .= "<form method='post'><input type='hidden' name='action' value='explore'><button type='submit'>Explore the cave</button></form>";
        break;
    case 'cave':
        $output = "You have entered the cave. You see a closed chest. What do you want to do?<br>";
        $output .= "<form method='post'><input type='hidden' name='action' value='back'><button type='submit'>Go back</button></form>";
        $output .= "<form method='post'><input type='hidden' name='action' value='open_chest'><button type='submit'>Open the chest</button></form>";
        break;
    case 'treasure':
        $output = "Congratulations! You found a treasure! What do you want to do now?<br>";
        $output .= "<form method='post'><input type='hidden' name='action' value='continue'><button type='submit'>Continue exploring</button></form>";
        break;
}

// Output game content
echo $output;
?>