<?php

class AdminController extends Controller
{
    public function login()
    {
        if (auth_check()) {
            $this->redirect('admin');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify()) {
                flash('error', 'Session expired.');
                $this->redirect('admin/login');
                return;
            }

            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                flash('error', 'Username dan password wajib diisi.');
                $this->redirect('admin/login');
                return;
            }

            if (auth_login($username, $password)) {
                csrf_regenerate();
                flash('success', 'Selamat datang, ' . auth_user()['display_name'] . '!');
                $this->redirect('admin');
            } else {
                flash('error', 'Username atau password salah.');
                $this->redirect('admin/login');
            }
            return;
        }

        $this->viewFull('admin/login');
    }

    public function logout()
    {
        auth_logout();
        flash('success', 'Anda berhasil logout.');
        $this->redirect('admin/login');
    }

    public function dashboard()
    {
        auth_required();

        $bookingModel = new BookingModel();
        $paymentModel = new PaymentModel();

        $stats = $bookingModel->getStats();
        $recentPayments = $paymentModel->getRecentPayments(10);
        $bookingsByField = $bookingModel->getBookingsByField();

        $this->viewFull('admin/dashboard', [
            'stats' => $stats,
            'recentPayments' => $recentPayments,
            'bookingsByField' => $bookingsByField,
        ]);
    }

    public function fields()
    {
        auth_required();
        require_once __DIR__ . '/../helpers/validation.php';
        require_once __DIR__ . '/../helpers/storage.php';
        $model = new FieldModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if (!csrf_verify()) {
                flash('error', 'Session expired.');
                $this->redirect('admin/fields');
                return;
            }

            if ($action === 'create' || $action === 'update') {
                $errors = validateField($_POST);
                if (!empty($errors)) {
                    flash('error', implode('<br>', $errors));
                    $this->redirect('admin/fields');
                    return;
                }

                $data = $_POST;
                if (!empty($_FILES['image']['tmp_name'])) {
                    $imagePath = storage_put($_FILES['image'], 'fields');
                    if ($imagePath) {
                        $data['image'] = $imagePath;
                    } else {
                        flash('error', 'Gagal mengupload gambar. Pastikan format JPG/PNG/WEBP dan ukuran max 2MB.');
                        $this->redirect('admin/fields');
                        return;
                    }
                }
                
                if ($action === 'create') {
                    $model->create($data);
                    flash('success', 'Lapangan berhasil ditambahkan.');
                } else {
                    $oldField = $model->find($data['id']);
                    if (!empty($data['image']) && !empty($oldField['image'])) {
                        storage_delete($oldField['image']);
                    }
                    $model->update($data['id'], $data);
                    flash('success', 'Lapangan berhasil diperbarui.');
                }
            } elseif ($action === 'delete') {
                $model->delete($_POST['id']);
                flash('success', 'Lapangan berhasil dinonaktifkan.');
            } elseif ($action === 'toggle_active') {
                $model->toggleActive($_POST['id'], $_POST['value']);
                flash('success', 'Status lapangan berhasil diubah.');
            }

            csrf_regenerate();
            $this->redirect('admin/fields');
            return;
        }

        $fields = $model->getAllAdmin();
        $this->viewFull('admin/fields', ['fields' => $fields, 'model' => $model]);
    }

    public function bookings()
    {
        auth_required();
        $model = new BookingModel();

        $page = max(1, intval($_GET['page'] ?? 1));
        $filters = [
            'status' => $_GET['status'] ?? '',
            'payment_status' => $_GET['payment_status'] ?? '',
            'search' => $_GET['search'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
        ];

        [$bookings, $total] = $model->all($page, 20, $filters);
        $totalPages = max(1, ceil($total / 20));

        $this->viewFull('admin/bookings', [
            'bookings' => $bookings,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'filters' => $filters,
        ]);
    }

    public function validatePayment()
    {
        auth_required();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify()) {
                flash('error', 'Session expired.');
                $this->redirect('admin');
                return;
            }

            $bookingId = $_POST['booking_id'] ?? null;
            $action = $_POST['action'] ?? '';

            if (!$bookingId || !in_array($action, ['confirm', 'cancel', 'reject'])) {
                flash('error', 'Parameter tidak valid.');
                $this->redirect('admin');
                return;
            }

            $model = new BookingModel();
            $booking = $model->find($bookingId);

            if (!$booking) {
                flash('error', 'Booking tidak ditemukan.');
                $this->redirect('admin');
                return;
            }

            if ($action === 'confirm') {
                if ($booking['payment_proof']) {
                    $model->updateStatus($bookingId, 'confirmed', 'paid');
                    flash('success', 'Pembayaran booking #' . $bookingId . ' dikonfirmasi.');
                } else {
                    $model->updateStatus($bookingId, 'confirmed', 'paid');
                    flash('success', 'Booking #' . $bookingId . ' dikonfirmasi (tanpa bukti bayar).');
                }
            } elseif ($action === 'reject') {
                if ($booking['payment_proof']) {
                    storage_delete($booking['payment_proof']);
                }
                $model->updateStatus($bookingId, 'pending', 'waiting');
                $model->updatePaymentProof($bookingId, null);
                flash('success', 'Bukti bayar booking #' . $bookingId . ' ditolak.');
            } elseif ($action === 'cancel') {
                if ($booking['payment_proof']) {
                    storage_delete($booking['payment_proof']);
                }
                $model->updateStatus($bookingId, 'cancelled', 'cancelled');
                flash('success', 'Booking #' . $bookingId . ' dibatalkan.');
            }

            csrf_regenerate();
            $this->redirect('admin');
            return;
        }

        $this->redirect('admin');
    }

    public function reports()
    {
        auth_required();
        $model = new BookingModel();

        $year = $_GET['year'] ?? date('Y');
        $revenueByMonth = $model->getRevenueByMonth($year);

        $this->viewFull('admin/reports', [
            'revenueByMonth' => $revenueByMonth,
            'year' => $year,
        ]);
    }

    public function users()
    {
        auth_required();
        $model = new UserModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify()) {
                flash('error', 'Session expired.');
                $this->redirect('admin/users');
                return;
            }

            $action = $_POST['action'] ?? '';
            $currentUser = auth_user();

            if ($action === 'create') {
                $username    = trim($_POST['username'] ?? '');
                $password    = $_POST['password'] ?? '';
                $displayName = trim($_POST['display_name'] ?? '');
                $role        = $_POST['role'] ?? 'admin';

                if (empty($username) || empty($password) || empty($displayName)) {
                    flash('error', 'Semua field wajib diisi.');
                } elseif (strlen($password) < 8) {
                    flash('error', 'Password minimal 8 karakter.');
                } elseif ($model->findByUsername($username)) {
                    flash('error', 'Username sudah digunakan.');
                } else {
                    $model->create([
                        'username'     => $username,
                        'password'     => $password,
                        'display_name' => $displayName,
                        'role'         => $role,
                    ]);
                    flash('success', 'Admin baru berhasil ditambahkan.');
                }

            } elseif ($action === 'update') {
                $id          = intval($_POST['id'] ?? 0);
                $username    = trim($_POST['username'] ?? '');
                $password    = $_POST['password'] ?? '';
                $displayName = trim($_POST['display_name'] ?? '');
                $role        = $_POST['role'] ?? 'admin';

                if (empty($username) || empty($displayName)) {
                    flash('error', 'Username dan nama wajib diisi.');
                } elseif (!empty($password) && strlen($password) < 8) {
                    flash('error', 'Password minimal 8 karakter.');
                } else {
                    $existing = $model->findByUsername($username);
                    if ($existing && $existing['id'] != $id) {
                        flash('error', 'Username sudah digunakan oleh pengguna lain.');
                    } else {
                        $model->update($id, [
                            'username'     => $username,
                            'display_name' => $displayName,
                            'role'         => $role,
                            'password'     => $password,
                        ]);
                        if ($id == $currentUser['id']) {
                            $_SESSION['admin']['display_name'] = $displayName;
                            $_SESSION['admin']['username']     = $username;
                        }
                        flash('success', 'Data admin berhasil diperbarui.');
                    }
                }

            } elseif ($action === 'delete') {
                $id = intval($_POST['id'] ?? 0);
                if ($id == $currentUser['id']) {
                    flash('error', 'Tidak dapat menghapus akun yang sedang aktif.');
                } elseif ($model->countAll() <= 1) {
                    flash('error', 'Minimal harus ada satu akun admin.');
                } else {
                    $model->delete($id);
                    flash('success', 'Admin berhasil dihapus.');
                }
            }

            csrf_regenerate();
            $this->redirect('admin/users');
            return;
        }

        $users = $model->getAll();
        $this->viewFull('admin/users', [
            'users'       => $users,
            'currentUser' => auth_user(),
        ]);
    }
}
