<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/admin-auth.php';
$user = admin_require_login();
$db = payless_db();
$csrf = admin_csrf_token();
$notice = '';
$error = '';
$statuses = ['new' => 'New', 'contacted' => 'Contacted', 'quoted' => 'Quoted', 'completed' => 'Completed', 'archived' => 'Archived'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  admin_verify_csrf();
  $action = (string) ($_POST['action'] ?? '');
  try {
    if ($action === 'update_status') {
      $id = filter_var($_POST['request_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
      $status = (string) ($_POST['request_status'] ?? '');
      if (!$id || !isset($statuses[$status])) {
        $error = 'The request or status was not valid.';
      } else {
        $stmt = $db->prepare('UPDATE customer_requests SET request_status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
        $notice = $stmt->rowCount() ? 'Request status updated.' : 'No change was needed.';
      }
    } elseif ($action === 'create_admin') {
      admin_require_owner($user);
      $name = trim((string) ($_POST['display_name'] ?? ''));
      $email = strtolower(trim((string) ($_POST['email'] ?? '')));
      $password = (string) ($_POST['password'] ?? '');
      if ($name === '' || strlen($name) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254 || strlen($password) < 14 || strlen($password) > 4096) {
        $error = 'Enter a valid name and email, and use a temporary password of at least 14 characters.';
      } else {
        $stmt = $db->prepare("INSERT INTO admin_users (email,password_hash,display_name,role,is_active) VALUES (?,?,?,'admin',1)");
        $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT), $name]);
        $notice = 'Admin account created. Share the sign-in URL and temporary password securely; ask the admin to change their password after sign-in.';
      }
    } elseif ($action === 'toggle_admin') {
      admin_require_owner($user);
      $id = filter_var($_POST['admin_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
      if (!$id || $id === (int) $user['id']) {
        $error = 'You cannot disable your own owner account here.';
      } else {
        $stmt = $db->prepare("UPDATE admin_users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ? AND role = 'admin'");
        $stmt->execute([$id]);
        $notice = $stmt->rowCount() ? 'Admin account access updated.' : 'No admin account was changed.';
      }
    } else {
      $error = 'Unknown action.';
    }
  } catch (PDOException $e) {
    if ($e->getCode() === '23000') $error = 'That email address is already assigned to an account.';
    else {
      error_log('PayLess admin action failed: ' . $e->getMessage());
      $error = 'The change could not be completed. Please try again.';
    }
  } catch (Throwable $e) {
    error_log('PayLess admin action failed: ' . $e->getMessage());
    $error = 'The change could not be completed. Please try again.';
  }
}

$detailId = filter_var($_GET['request'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$detail = null;
if ($detailId) {
  $stmt = $db->prepare('SELECT * FROM customer_requests WHERE id = ? LIMIT 1');
  $stmt->execute([$detailId]);
  $detail = $stmt->fetch() ?: null;
}
$q = trim((string) ($_GET['q'] ?? ''));
$statusFilter = (string) ($_GET['status'] ?? '');
$typeFilter = (string) ($_GET['type'] ?? '');
$page = max(1, min(100000, (int) ($_GET['p'] ?? 1)));
$perPage = 25;
$where = [];
$params = [];
if ($q !== '') {
  $where[] = '(CAST(id AS CHAR) = :exact_id OR customer_name LIKE :q_name OR company LIKE :q_company OR phone LIKE :q_phone OR email LIKE :q_email OR job_location LIKE :q_location)';
  $params[':exact_id'] = ctype_digit($q) ? $q : '-1';
  foreach ([':q_name', ':q_company', ':q_phone', ':q_email', ':q_location'] as $key) $params[$key] = '%' . $q . '%';
}
if (isset($statuses[$statusFilter])) { $where[] = 'request_status = :status'; $params[':status'] = $statusFilter; }
if (in_array($typeFilter, ['equipment','gravel'], true)) { $where[] = 'request_type = :type'; $params[':type'] = $typeFilter; }
$whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';
$countStmt = $db->prepare('SELECT COUNT(*) FROM customer_requests' . $whereSql);
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));
$page = min($page, $pages);
$listStmt = $db->prepare('SELECT id,request_type,customer_name,company,phone,email,job_location,request_status,created_at,start_date,rental_period FROM customer_requests' . $whereSql . ' ORDER BY created_at DESC, id DESC LIMIT ' . $perPage . ' OFFSET ' . (($page - 1) * $perPage));
$listStmt->execute($params);
$requests = $listStmt->fetchAll();
$stats = $db->query("SELECT COUNT(*) AS total, SUM(request_status = 'new') AS new_count, SUM(request_status = 'contacted') AS contacted_count, SUM(request_status = 'quoted') AS quoted_count FROM customer_requests")->fetch();
$admins = [];
if (($user['role'] ?? '') === 'owner') {
  $admins = $db->query("SELECT id,email,display_name,role,is_active,last_login_at,created_at FROM admin_users ORDER BY role='owner' DESC, created_at ASC")->fetchAll();
}
function admin_url(array $replace = []): string {
  $query = array_merge($_GET, $replace);
  foreach ($query as $k => $v) if ($v === null || $v === '') unset($query[$k]);
  return '/admin/' . ($query ? '?' . http_build_query($query) : '');
}
admin_layout_start('Request dashboard', $user);
?>
<div class="head"><div><div class="eyebrow">PAYLESS / PRIVATE WORKSPACE</div><h1>Request dashboard</h1><p>Review customer enquiries, follow up, and keep the team’s queue organized.</p></div><a class="btn accent" href="/admin/">↻ Refresh data</a></div>
<?php if ($notice !== ''): ?><div class="notice" role="status"><?= admin_escape($notice) ?></div><?php endif; ?>
<?php if ($error !== ''): ?><div class="notice error" role="alert"><?= admin_escape($error) ?></div><?php endif; ?>
<div class="grid">
  <div class="stat"><span>Total requests</span><strong><?= number_format((int) ($stats['total'] ?? 0)) ?></strong></div>
  <div class="stat"><span>New · needs review</span><strong><?= number_format((int) ($stats['new_count'] ?? 0)) ?></strong></div>
  <div class="stat"><span>Contacted</span><strong><?= number_format((int) ($stats['contacted_count'] ?? 0)) ?></strong></div>
  <div class="stat"><span>Quoted</span><strong><?= number_format((int) ($stats['quoted_count'] ?? 0)) ?></strong></div>
</div>

<?php if ($detailId): ?>
<section class="card" aria-labelledby="detail-heading">
  <?php if (!$detail): ?><div class="empty">That request could not be found. <a href="/admin/">Return to the dashboard.</a></div>
  <?php else: ?>
  <div class="detail">
    <div class="wide"><a class="btn light" href="<?= admin_escape(admin_url(['request' => null])) ?>">← Back to requests</a></div>
    <section><div class="eyebrow">REQUEST #<?= (int) $detail['id'] ?> · <?= admin_escape(ucfirst((string) $detail['request_type'])) ?></div><h2 id="detail-heading"><?= admin_escape((string) $detail['customer_name']) ?></h2><dl>
      <dt>Company</dt><dd><?= admin_escape((string) ($detail['company'] ?: 'Not provided')) ?></dd>
      <dt>Phone</dt><dd><a href="tel:<?= admin_escape((string) $detail['phone']) ?>"><?= admin_escape((string) $detail['phone']) ?></a></dd>
      <dt>Email</dt><dd><a href="mailto:<?= admin_escape((string) $detail['email']) ?>"><?= admin_escape((string) $detail['email']) ?></a></dd>
      <dt>Job-site location</dt><dd><?= nl2br(admin_escape((string) $detail['job_location'])) ?></dd>
      <dt>ZIP code</dt><dd><?= admin_escape((string) ($detail['zip_code'] ?: 'Not provided')) ?></dd>
      <dt>Submitted</dt><dd><?= admin_escape((string) $detail['created_at']) ?></dd>
    </dl></section>
    <section><h2>Request details</h2><dl>
      <dt>Status</dt><dd><span class="pill <?= admin_escape((string) $detail['request_status']) ?>"><?= admin_escape((string) $detail['request_status']) ?></span></dd>
      <dt>Equipment selected</dt><dd><?= admin_escape(implode(', ', (array) json_decode((string) $detail['equipment_json'], true))) ?: 'None listed' ?></dd>
      <dt>Rental period</dt><dd><?= admin_escape((string) ($detail['rental_period'] ?: 'Not specified')) ?></dd>
      <dt>Start date / needed by</dt><dd><?= admin_escape((string) ($detail['start_date'] ?: 'Not specified')) ?></dd>
      <dt>Project type</dt><dd><?= admin_escape((string) ($detail['project_type'] ?: 'Not specified')) ?></dd>
      <dt>Quantity</dt><dd><?= admin_escape((string) ($detail['quantity'] ?: 'Not specified')) ?></dd>
      <dt>ZIP code</dt><dd><?= admin_escape((string) ($detail['zip_code'] ?: 'Not provided')) ?></dd>
    </dl></section>
    <section class="wide"><h2>Customer notes</h2><p><?= nl2br(admin_escape((string) ($detail['details'] ?: 'No additional notes provided.'))) ?></p></section>
    <section class="wide"><form method="post" action="/admin/?request=<?= (int) $detail['id'] ?>" class="actions"><input type="hidden" name="_csrf" value="<?= admin_escape($csrf) ?>"><input type="hidden" name="action" value="update_status"><input type="hidden" name="request_id" value="<?= (int) $detail['id'] ?>"><label for="detail-status">Update status</label><select id="detail-status" name="request_status"><?php foreach ($statuses as $key => $label): ?><option value="<?= admin_escape($key) ?>" <?= $detail['request_status'] === $key ? 'selected' : '' ?>><?= admin_escape($label) ?></option><?php endforeach; ?></select><button class="btn accent" type="submit">Save status</button></form></section>
  </div><?php endif; ?>
</section>
<?php endif; ?>

<section class="card">
  <form class="filters" method="get" action="/admin/">
    <?php if ($detailId): ?><input type="hidden" name="request" value="<?= (int) $detailId ?>"><?php endif; ?>
    <input type="search" name="q" value="<?= admin_escape($q) ?>" placeholder="Search name, company, phone, email, location or request #">
    <select name="status" aria-label="Filter by status"><option value="">All statuses</option><?php foreach ($statuses as $key => $label): ?><option value="<?= admin_escape($key) ?>" <?= $statusFilter === $key ? 'selected' : '' ?>><?= admin_escape($label) ?></option><?php endforeach; ?></select>
    <select name="type" aria-label="Filter by request type"><option value="">All request types</option><option value="equipment" <?= $typeFilter === 'equipment' ? 'selected' : '' ?>>Equipment</option><option value="gravel" <?= $typeFilter === 'gravel' ? 'selected' : '' ?>>Gravel</option></select>
    <button class="btn" type="submit">Search</button><a class="btn light" href="/admin/">Clear</a>
  </form>
  <div class="table-wrap"><table><thead><tr><th>Request</th><th>Customer</th><th>Contact</th><th>Submitted</th><th>Status</th><th>Manage</th></tr></thead><tbody>
  <?php if (!$requests): ?><tr><td colspan="6" class="empty">No requests match these filters.</td></tr><?php else: foreach ($requests as $row): ?>
    <tr><td><strong>#<?= (int) $row['id'] ?> · <?= admin_escape(ucfirst((string) $row['request_type'])) ?></strong><small><?= admin_escape((string) ($row['start_date'] ?: $row['rental_period'] ?: 'Date not specified')) ?></small><small><?= admin_escape((string) $row['job_location']) ?></small></td>
    <td><strong><?= admin_escape((string) $row['customer_name']) ?></strong><small><?= admin_escape((string) ($row['company'] ?: 'Individual customer')) ?></small></td>
    <td><a href="tel:<?= admin_escape((string) $row['phone']) ?>"><?= admin_escape((string) $row['phone']) ?></a><small><a href="mailto:<?= admin_escape((string) $row['email']) ?>"><?= admin_escape((string) $row['email']) ?></a></small></td>
    <td><?= admin_escape((string) $row['created_at']) ?></td><td><span class="pill <?= admin_escape((string) $row['request_status']) ?>"><?= admin_escape((string) $row['request_status']) ?></span></td>
    <td><div class="actions"><a class="btn light" href="<?= admin_escape(admin_url(['request' => (string) $row['id']])) ?>">View details</a><form method="post" action="/admin/" class="actions"><input type="hidden" name="_csrf" value="<?= admin_escape($csrf) ?>"><input type="hidden" name="action" value="update_status"><input type="hidden" name="request_id" value="<?= (int) $row['id'] ?>"><select name="request_status" aria-label="Status for request <?= (int) $row['id'] ?>"><?php foreach ($statuses as $key => $label): ?><option value="<?= admin_escape($key) ?>" <?= $row['request_status'] === $key ? 'selected' : '' ?>><?= admin_escape($label) ?></option><?php endforeach; ?></select><button class="btn" type="submit">Save</button></form></div></td></tr>
  <?php endforeach; endif; ?></tbody></table></div>
  <div class="pager"><span>Showing <?= $total ? (($page - 1) * $perPage + 1) : 0 ?>–<?= min($page * $perPage, $total) ?> of <?= number_format($total) ?> requests</span><span class="actions"><?php if ($page > 1): ?><a class="btn light" href="<?= admin_escape(admin_url(['p' => $page - 1])) ?>">← Previous</a><?php endif; ?><?php if ($page < $pages): ?><a class="btn light" href="<?= admin_escape(admin_url(['p' => $page + 1])) ?>">Next →</a><?php endif; ?></span></div>
</section>

<?php if (($user['role'] ?? '') === 'owner'): ?>
<h2 class="section-title">Access management · Owner only</h2>
<p class="helper">Only the owner can create or disable admin accounts. Admins can manage requests but cannot grant access to other people.</p>
<div class="grid" style="grid-template-columns:minmax(0,1fr) minmax(0,1.3fr);align-items:start">
  <section class="card" style="padding:20px"><h2 style="margin:0 0 5px;font-size:20px">Assign an admin</h2><p class="helper">Use a unique work email and a strong temporary password. Give the credentials to the person privately.</p>
  <form method="post" action="/admin/#access-management" class="form-grid"><input type="hidden" name="_csrf" value="<?= admin_escape($csrf) ?>"><input type="hidden" name="action" value="create_admin"><label>Full name<input name="display_name" maxlength="120" required></label><label>Work email<input type="email" name="email" maxlength="254" required></label><label class="wide">Temporary password (14+ characters)<input type="password" name="password" minlength="14" maxlength="4096" autocomplete="new-password" required></label><div class="wide"><button class="btn accent" type="submit">Create admin account</button></div></form></section>
  <section class="card" id="access-management" style="padding:20px"><h2 style="margin:0 0 5px;font-size:20px">Authorized accounts</h2>
  <?php foreach ($admins as $admin): ?><div class="user-row"><div><strong><?= admin_escape((string) $admin['display_name']) ?> <?= $admin['role'] === 'owner' ? '<span class="pill">Owner</span>' : '' ?></strong><small><?= admin_escape((string) $admin['email']) ?> · <?= (int) $admin['is_active'] ? 'Active' : 'Disabled' ?></small><small><?= $admin['last_login_at'] ? 'Last login: ' . admin_escape((string) $admin['last_login_at']) : 'Never signed in' ?></small></div><?php if ($admin['role'] === 'admin'): ?><form method="post" action="/admin/#access-management"><input type="hidden" name="_csrf" value="<?= admin_escape($csrf) ?>"><input type="hidden" name="action" value="toggle_admin"><input type="hidden" name="admin_id" value="<?= (int) $admin['id'] ?>"><button class="btn <?= (int) $admin['is_active'] ? 'light' : 'accent' ?>" type="submit"><?= (int) $admin['is_active'] ? 'Disable access' : 'Enable access' ?></button></form><?php endif; ?></div><?php endforeach; ?>
  </section>
</div>
<?php endif; ?>
<?php admin_layout_end(); ?>
