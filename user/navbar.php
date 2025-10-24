<nav>
  <label class="profile">
    HOŞ GELDİNİZ: <?= htmlspecialchars($_SESSION['full_name'] ?? 'Kullanıcı') ?>
  </label>

  <ul>
    <?php
    $home_link = ($_SESSION['role'] === 'user') ? '/user/index.php' : '/index.php';
    ?>
    <li><a class="home" href="<?= $home_link ?>">ANA SAYFA</a></li>
    <li><a href="tickets.php">BİLETLERİM</a></li>
    <li><a href="profile.php">PROFİLİM</a></li>
    <li><a href="logout.php">ÇIKIŞ YAP</a></li>
  </ul>

  <label class="balance">
    BAKİYENİZ: <?= htmlspecialchars($_SESSION['balance'] ?? '0') ?> ₺
  </label>
</nav>
