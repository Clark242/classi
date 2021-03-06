<?php
//require_once __DIR__.'/vendor/autoload.php';
//require 'config/config.php';



require "../predis-vendor/autoload.php";

Predis\Autoloader::register();

$redis = new Predis\Client(array(
    "scheme" => "tcp",
    "host" => "127.0.0.1",
    "port" => 6379
  ));



$classidevs = $redis->smembers('classidevs');





echo '
<title>classi</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
<link rel="manifest" href="favicon/site.webmanifest">
<link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
';

if ( isset($_COOKIE['theme']) ) {

    if ( $_COOKIE['theme'] == 'Light Theme' ) {
          echo '
            <style>
                body {
                    font-family: Comfortaa;
                }
            </style>
          ';
        }

    if ( $_COOKIE['theme'] == 'Dark Theme' ) {
          echo '
            <style>
                body {
                    font-family: Comfortaa;
                    background-color: #121212;
                    color: #cccccc;
                }

                a {
                    color: #cccccc;
                    text-decoration: none;
                }
            </style>
          ';
        }

    if ( $_COOKIE['theme'] == 'Da Lincoln Theme' ) {
          echo '
            <style>
                body {
                    font-family: Comfortaa;
                    background-color: #121212;
                    color: #ffa500;
                }

                a {
                    color: #ffa500;
                    text-decoration: none;
                }
            </style>
          ';
        }

}

echo '<body>';

echo '<center>';
echo "<br><img src='classi.png' height='100px' width='100px'>";
echo '<h1>classi User Preferences</h1>';
echo '<br>';





echo '<form method="post"><input type="submit" name="logout" value="Logout"/>';
echo '<br><br><br>';
echo '<u><h4>Theme</h4></u>';
echo '<form method="post"><input type="submit" name="theme" value="Light Theme"/> ';
echo '<form method="post"><input type="submit" name="theme" value="Dark Theme"/>';
echo '<br><br><br>';
echo '<u><h4>Advanced</h4></u>';
echo '<form method="post">' . '<b>Auth User: </b>' . '<input id="authuser" name="authuser" value="' . $_COOKIE['authuser'] . '" placeholder="Auth User Number (0-3)"><input type="submit" name="authuserBtn" value="Set Auth User"/></form>';
echo '<form method="post">' . '<b>Erase <u>ALL</u> Cookies: </b>' . '<input type="submit" name="delete-cookies" value="THIS BUTTON WILL ERASE ALL OF YOUR CLASSI SETTINGS AND INFORMATION" width="100%" max-width="100px"/>';

//setcookie('lincoln', 'This is Lincoln', time() + (86400 * 30 * 9999), "/");

if ( $redis->sismember('classidevs', $_COOKIE['email']) == true ) {
        echo '<br><br><br>';
        echo '<u><h4>~ classi Dev Settings ~</h4></u>';
        echo '<form method="post">' . '<b>classi Global Announcement: </b>' . '<input id="globalann" name="globalann" value="' . $redis->get('global_message') . '" placeholder="Global Announcement Text (HTML Formatting)"><input type="submit" name="annBtn" value="Set Global Announcement"/></form>';
      }

if ( isset($_COOKIE['lincoln']) ) {
        echo '<br><br><br>';
        echo '<u><h4>~ Lincoln Only Experimental Features ~</h4></u>';
        echo '<form method="post"><input type="submit" name="theme" value="Da Lincoln Theme"/>';
    }





function successMessage() {
        ob_clean();
        echo "<center><p style='margin-top:100px'><img src='animations/c-spinner.gif' height='100px' width='100px'><br><br>Preferences updated!</p></center>";
        header("Refresh:2");
    }





if(isset($_POST['logout'])) {
    setcookie('auth-login-hint', '', time() - 3600);
    session_unset();
    session_destroy();
    ob_clean();
    echo "<center><p style='margin-top:100px'><img src='animations/imploding-loader.gif' height='100px' width='100px'><br>Deauthenticated! Please prepare to log in.</p></center>";
    header("Refresh:3");
  }

if(isset($_POST['authuser'])) {
    setcookie('authuser', $_POST['authuser'], time() + (86400 * 30 * 9999), "/");
    successMessage();
  }

if(isset($_POST['globalann'])) {
    $redis->set('global_message', $_POST['globalann']);
    successMessage();
  }

if(isset($_POST['delete-cookies'])) {
    ob_clean();
    echo "<center><p style='margin-top:100px'><img src='classi.png' height='100px' width='100px'><br>Are you sure? Remember: <b>THIS ACTION WILL ERASE ALL OF YOUR CLASSI SETTINGS AND INFORMATION</b></p>";
    echo '<br><form method="post"><input type="submit" name="delete-cookies-confirm" value="Yes, I want to do this."/></center>';
  }

if(isset($_POST['delete-cookies-confirm'])) {
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            setcookie($name, '', time()-1000);
            setcookie($name, '', time()-1000, '/');
        }
    }
    ob_clean();
    echo "<center><p style='margin-top:100px'><img src='classi.png' height='100px' width='100px'><br>Success</p>";
    echo '<a href="/" target="_blank"><button>OK</button></a></center>';
  }

if(isset($_POST['theme'])) {
    setcookie('theme', $_POST['theme'], time() + (86400 * 30 * 9999), "/");
    successMessage();
  }