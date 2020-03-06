<?php
  include 'config.php';
  require 'l10n/' . $_SESSION['language'] . '.php';
?>

<div id="navigation">
  <ul>
    <li<?php if ($active_page == "index")
      echo ' id="currentpage"'; ?>><a href="index.php"><?php echo L10N_SERVER ?></a>
    </li>
    <li<?php if ($active_page == "users")
      echo ' id="currentpage"'; ?>><a href="users.php"><?php echo L10N_USERS ?></a>
    </li>
    <li<?php if ($active_page == "groups")
      echo ' id="currentpage"'; ?>><a href="groups.php"><?php echo L10N_GROUPS ?></a>
    </li>
    <li<?php if ($active_page == "email")
      echo ' id="currentpage"'; ?>><a href="email.php"><?php echo L10N_EMAIL ?></a></li>
    <li style="float:right;"><a style="font-size: 14px;"
      href="https://github.com/bpcurse/nextcloud-userexport">
      Nextcloud Userexport v1.1.0 beta</a>
  </ul>
</div>