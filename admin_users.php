<?php
session_start();
// ถ้าต้องการให้เฉพาะแอดมินเข้าถึง สามารถเพิ่มตรวจสอบ SESSION แอดมินที่นี่

require 'db_connect.php';

// ค้นหา (ถ้ามี)
$search = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = trim($_GET['search']);
    $sql = "SELECT user_id, room_number, phone, fullname, note, active 
            FROM users 
            WHERE phone LIKE :search 
               OR fullname LIKE :search 
               OR room_number LIKE :search
            ORDER BY room_number ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':search' => '%' . $search . '%']);
} else {
    $sql = "SELECT user_id, room_number, phone, fullname, note, active FROM users ORDER BY room_number ASC";
    $stmt = $pdo->query($sql);
}

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผู้ใช้ - Safe Locker Admin</title>
    <link rel="stylesheet" href="css/styles_admin_users.css">
</head>
<body>
    <div class="container">
        <h2>จัดการผู้ใช้ Safe Locker 🔒</h2>

        <div class="search-box">
            <form action="" method="get">
                <input type="text" name="search" placeholder="ค้นหาด้วย เบอร์ / ชื่อ / ห้อง" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">ค้นหา</button>
                <?php if ($search): ?>
                    <a href="admin_users.php" class="clear-search">ล้างการค้นหา</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- ตารางสำหรับจอใหญ่ -->
        <table>
            <thead>
                <tr>
                    <th>ห้อง</th>
                    <th>เบอร์โทรศัพท์</th>
                    <th>ชื่อ-นามสกุล</th>
                    <th>หมายเหตุ</th>
                    <th>สถานะ</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) == 0): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; color:#999; padding:30px;">ไม่พบข้อมูลผู้ใช้</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['room_number']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone']); ?></td>
                            <td><?php echo htmlspecialchars($user['fullname'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($user['note'] ?: '-'); ?></td>
                            <td>
                                <span class="status <?php echo $user['active'] ? 'active' : 'inactive'; ?>">
                                    <?php echo $user['active'] ? 'ใช้งาน' : 'ปิดใช้งาน'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="admin_edit_user.php?id=<?php echo $user['user_id']; ?>" class="btn-edit">แก้ไข</a>
                                <a href="open_locker.php?id=<?php echo $user['user_id']; ?>" 
                                   class="btn-open"
                                   onclick="return confirm('ยืนยันการเปิด Locker ห้อง <?php echo htmlspecialchars($user['room_number']); ?> หรือไม่?');">
                                    เปิด Locker
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Card Layout สำหรับมือถือ -->
        <div class="user-list">
            <?php if (count($users) == 0): ?>
                <div style="text-align:center; color:#999; padding:40px;">ไม่พบข้อมูลผู้ใช้</div>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <div class="user-card">
                        <p><strong>ห้อง:</strong> <?php echo htmlspecialchars($user['room_number']); ?></p>
                        <p><strong>เบอร์โทรศัพท์:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                        <p><strong>ชื่อ-นามสกุล:</strong> <?php echo htmlspecialchars($user['fullname'] ?: '-'); ?></p>
                        <p><strong>หมายเหตุ:</strong> <?php echo htmlspecialchars($user['note'] ?: '-'); ?></p>
                        <p><strong>สถานะ:</strong> 
                            <span class="status <?php echo $user['active'] ? 'active' : 'inactive'; ?>">
                                <?php echo $user['active'] ? 'ใช้งาน' : 'ปิดใช้งาน'; ?>
                            </span>
                        </p>
                        <div class="actions">
                            <a href="admin_edit_user.php?id=<?php echo $user['user_id']; ?>" class="btn-edit">แก้ไข</a>
                            <a href="open_locker.php?id=<?php echo $user['user_id']; ?>" 
                               class="btn-open"
                               onclick="return confirm('ยืนยันการเปิด Locker ห้อง <?php echo htmlspecialchars($user['room_number']); ?> หรือไม่?');">
                                เปิด Locker
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="back-link">
            <a href="dashboard.php">กลับหน้าหลัก</a>
        </div>
    </div>
</body>
</html>