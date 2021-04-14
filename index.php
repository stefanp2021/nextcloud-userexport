<?php

  session_start();
  $active_page = "index";
  require_once 'functions.php';
  include_once 'config.php';

  /**
  * Get parameters if any, set defaults
  */
  if($_GET['logout']) {

    unset($_SESSION['data_choices']);
    unset($_SESSION['userlist']);
    unset($_SESSION['grouplist']);
    unset($_SESSION['raw_user_data']);
    unset($_SESSION['raw_groupfolders_data']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_pass']);
    unset($_SESSION['target_url']);

    session_destroy();
    session_write_close();
    setcookie(session_name(),'',0,'/');
    
    header('Location: index.php');
  }

  // Set UI language to config value or to english, if it is not configured
  $_SESSION['language'] = $language ?? 'en';
  require_once 'l10n/'.$_SESSION['language'].'.php';
  $target_url = $_GET['url']
    ?? filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);
  $user_name = $_GET['user'];
  $user_pass = $_GET['pass'];
  $_SESSION['data_choices'] = isset($_GET["select"])
    ? explode(",", $_GET["select"])
    : ['id', 'displayname', 'email', 'lastLogin'];
  $_SESSION['export_type'] = $_GET['type'] ?? 'table';
  $_SESSION['message_mode'] = $_GET['msg_mode'] ?? 'bcc';

  set_data_options();

  if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Set SESSION variables to POST values
    if (isset($_POST['target_url'])) {
      $_SESSION['user_name'] = $_POST['user_name'];
      $_SESSION['user_pass'] = $_POST['user_pass'];

      // Save the script's start timestamp to measure execution time
      define('TIMESTAMP_SCRIPT_START', microtime(true));

      // Check if plain HTTP is used without override command and exit if not
      $_SESSION['target_url'] = check_https($_POST['target_url']);

      // Fast cURL API call fetching userlist (containing only user IDs) from target server
      fetch_userlist();
      // Fast cURL API call fetching grouplist (containing only group names) from target server
      fetch_grouplist();
      fetch_raw_groupfolders_data();

      // Count the list items and save them as session variable
      $_SESSION['user_count'] = count($_SESSION['userlist']);
      $_SESSION['group_count'] = count($_SESSION['grouplist']);
      $_SESSION['groupfolders_count'] =
          $_SESSION['groupfolders_active'] == true
        ? count($_SESSION['raw_groupfolders_data']['ocs']['data'])
        : null;
    }

    // Fetch all user details (this can take a long time)
    $_SESSION['raw_user_data'] = fetch_raw_user_data();

    calculate_quota();
  }

  echo "<html lang='{$_SESSION['language']}'>";

?>

  <head>
    <link rel="stylesheet" type="text/css" href="style.php">
    <meta charset="UTF-8">
    <title>Nextcloud Userexport</title>
  </head>

  <body>
    <?php
      include 'navigation.php';
      if($_SERVER['REQUEST_METHOD'] == 'POST') {
        print_status_success();
        exit();
      }
    ?>
    <div style="width: 305px;">
    <form method='post' id='auth_form'>
      <br>
      <u><?php echo L10N_SERVER_AND_LOGIN_DATA ?></u>
      <br><br>
      <table>
        <tr>
        <td colspan="2">
          <input style="width: 100%;" id='url' type='text' name='target_url' required
          placeholder='https://cloud.example.com'
          value='<?php echo $target_url; ?>'>
        </td></tr>
        <tr>
        <td><input style="width: 100%;" id='user_name' type='text' name='user_name' required
          placeholder='<?php echo L10N_USERNAME ?>'
          value='<?php echo $user_name; ?>'>
        </td>
        <td><input style="width: 100%;" id='user_password' type='password' name='user_pass' required
          placeholder='<?php echo L10N_PASSWORD ?>'
          value='<?php echo $user_pass; ?>'>
        </td>
        </tr>
      </table>
      <br>
      <input id='button-connect' value='<?php echo L10N_CONNECT_AND_FETCH ?>'
          type='submit' name='submit'>
      <div style="text-align: center; font-size: small; color: grey;"><?php echo L10N_WAIT ?></div>
    </form>
    </div>
  </body>
</html>
