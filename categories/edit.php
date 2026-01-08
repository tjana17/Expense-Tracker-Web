<?php
$path = "../";
include "../config/db.php";

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM categories WHERE id=$id");
$category = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $icon = mysqli_real_escape_string($conn, $_POST['icon']);

    mysqli_query($conn,
        "UPDATE categories SET name='$name', icon='$icon' WHERE id=$id");

    header("Location: index.php");
    exit;
}

include "../header.php";
?>

<div class="pagetitle">
  <h1>Edit Category</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php">Categories</a></li>
      <li class="breadcrumb-item active">Edit Category</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Edit Category</h5>

          <form method="POST">
            <div class="mb-3">
              <label for="categoryName" class="form-label">Category Name</label>
              <input type="text" class="form-control" id="categoryName" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
            </div>
            <?php
            // Load icons from JSON
            $jsonFile = __DIR__ . '/../assets/vendor/bootstrap-icons/bootstrap-icons.json';
            $iconData = json_decode(file_get_contents($jsonFile), true);
            $icons = [];
            foreach ($iconData as $iconName => $unicode) {
                $icons[] = 'bi-' . $iconName;
            }
            $currentIcon = $category['icon'] ?? 'bi-tag';
            ?>
            <div class="mb-3">
              <label class="form-label">Select Icon</label>
              <input type="text" id="iconSearch" class="form-control mb-2" placeholder="Search icons..." onkeyup="filterIcons()">
              <div class="icon-grid border p-3 rounded" id="iconGrid">
                <?php foreach ($icons as $icon): ?>
                  <div class="icon-option <?= $icon === $currentIcon ? 'active' : '' ?>" data-icon="<?= $icon ?>" onclick="selectIcon(this, '<?= $icon ?>')">
                    <i class="bi <?= $icon ?>"></i>
                  </div>
                <?php endforeach; ?>
              </div>
              <input type="hidden" name="icon" id="selectedIcon" value="<?= htmlspecialchars($currentIcon) ?>">
            </div>

            <style>
              .icon-grid { display: flex; flex-wrap: wrap; gap: 10px; max-height: 200px; overflow-y: auto; }
              .icon-option { 
                width: 40px; height: 40px; 
                display: flex; align-items: center; justify-content: center; 
                border: 1px solid #dee2e6; border-radius: 5px; 
                cursor: pointer; font-size: 1.2rem; transition: all 0.2s;
              }
              .icon-option:hover { background-color: #f8f9fa; border-color: #adb5bd; }
              .icon-option.active { background-color: #0d6efd; color: white; border-color: #0d6efd; }
              .icon-option.d-none { display: none !important; }
            </style>

            <script>
              function selectIcon(element, iconClass) {
                document.getElementById('selectedIcon').value = iconClass;
                document.querySelectorAll('.icon-option').forEach(el => el.classList.remove('active'));
                element.classList.add('active');
              }

              function filterIcons() {
                const search = document.getElementById('iconSearch').value.toLowerCase();
                const icons = document.querySelectorAll('.icon-option');
                icons.forEach(icon => {
                  const iconName = icon.getAttribute('data-icon');
                  if (iconName.includes(search)) {
                    icon.classList.remove('d-none');
                  } else {
                    icon.classList.add('d-none');
                  }
                });
              }
            </script>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="index.php" class="btn btn-secondary ms-2">Cancel</a>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>

<?php include "../footer.php"; ?>
