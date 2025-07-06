<?php

// memuat data dari file gaji.php
function loadData()
{
    $file = __DIR__ . '/model/gaji.php';
    if (file_exists($file)) {
        return include($file);
    }
    return [];
}

//  menyimpan data ke file gaji.php
function saveData($data)
{
    $file = __DIR__ . '/model/gaji.php';
    $content = "<?php\nreturn " . var_export($data, true) .";\n";
    file_put_contents($file, $content);
}

// Fungsi menu
function menu(){
    do {
        echo "\nMenu:\n";
        echo "1. Lihat Karyawan\n";
        echo "2. Tambah Karyawan\n";
        echo "3. Update Karyawan\n";
        echo "4. Hapus Karyawan\n";
        echo "5. Hitung Gaji satu Karyawan\n";
        echo "6. Keluar Aplikasi\n";
        echo "Pilih menu: ";
        $pilihan = trim(fgets(STDIN));

        switch ($pilihan) {
            case '1':
                lihatKaryawan();
                break;
            case '2':
                tambahKaryawan();
                break;
            case '3':
                updateKaryawan();
                break;
            case '4':
                hapusKaryawan();
                break;
            case '5':
                hitungGajiKaryawan();
                break;
            case '6':
                echo "Terimakasih, sampai jumpa.\n";
                exit;
            default:
                echo "Pilihan tidak valid. Masukkan angka 1-6.\n";
        }
    } while (true);
}

menu();

// Fungsi untuk menampilkan daftar karyawan
function lihatKaryawan()
{
    $data = loadData();
    if (empty($data)) {
        echo "Tidak ada data karyawan.\n";
        return;
    }
    echo "Daftar Karyawan:\n";
    foreach ($data as $index => $karyawan) {
        echo ($index + 1) . ". Nama: {$karyawan['nama']}, Jabatan: {$karyawan['jabatan']}\n";
    }
}

// Fungsi untuk menambahkan karyawan baru
function tambahKaryawan()
{
    $data = loadData();

    echo "Masukkan nama karyawan: ";
    $nama = trim(fgets(STDIN));

    echo "Masukkan jabatan (Manajer/Supervisor/Staf): ";
    $jabatan = trim(fgets(STDIN));
    if (!in_array($jabatan, ['Manajer', 'Supervisor', 'Staf'])) {
        echo "Error: Jabatan tidak valid. Hanya menerima Manajer, Supervisor, atau Staf.\n";
        return;
    }

    $data[] = ['nama' => $nama, 'jabatan' => $jabatan];
    saveData($data);

    echo "Karyawan berhasil ditambahkan.\n";
}

// Fungsi untuk memperbarui data karyawan
function updateKaryawan()
{
    $data = loadData();
    lihatKaryawan();

    echo "Masukkan nomor karyawan yang ingin diubah: ";
    $index = (int)trim(fgets(STDIN)) - 1;

    if (!isset($data[$index])) {
        echo "Error: Nomor karyawan tidak ditemukan.\n";
        return;
    }

    echo "Masukkan nama baru: ";
    $data[$index]['nama'] = trim(fgets(STDIN));

    echo "Masukkan jabatan baru (Manajer/Supervisor/Staf): ";
    $jabatan = trim(fgets(STDIN));
    if (!in_array($jabatan, ['Manajer', 'Supervisor', 'Staf'])) {
        echo "Error: Jabatan tidak valid.\n";
        return;
    }
    $data[$index]['jabatan'] = $jabatan;

    saveData($data);
    echo "Data karyawan berhasil diperbarui.\n";
}

// Fungsi untuk menghapus karyawan
function hapusKaryawan() {
    $data = loadData();
    if (empty($data)) {
        echo "Tidak ada data karyawan yang tersedia.\n";
        return;
    }
    
    lihatKaryawan();
    
    echo "Masukkan nomor karyawan yang ingin dihapus: ";
    $index = (int)trim(fgets(STDIN)) - 1;

    if (!isset($data[$index])) {
        echo "Error: Nomor karyawan tidak ditemukan.\n";
        return;
    }

    $karyawan = $data[$index];
    echo "\nAnda akan menghapus karyawan berikut:\n";
    echo "Nama: {$karyawan['nama']}\n";
    echo "Jabatan: {$karyawan['jabatan']}\n\n";
    echo "Apakah Anda yakin ingin menghapus? (y/n): ";

    $confirm = trim(fgets(STDIN));
    if (strtolower($confirm) === 'y') {
        unset($data[$index]);
        $data = array_values($data); // Re-index array setelah penghapusan
        saveData($data);
        echo "Karyawan berhasil dihapus.\n";
    } else {
        echo "Penghapusan dibatalkan.\n";
    }

    echo "\nDaftar karyawan yang diperbarui:\n";
    lihatKaryawan();
}

// Fungsi untuk menghitung gaji karyawan
function hitungGajiKaryawan() {
    $data = loadData();
    
    lihatKaryawan();

    echo "Masukkan nomor karyawan untuk menghitung gaji: ";
    $index = (int)trim(fgets(STDIN)) - 1;

    if (!isset($data[$index])) {
        echo "Error: Nomor karyawan tidak ditemukan.\n";
        return;
    }

    echo "Masukkan jumlah jam lembur: ";
    $jamLembur = trim(fgets(STDIN));
    if (!is_numeric($jamLembur) || $jamLembur < 0) {
        echo "Error: Jumlah jam lembur harus angka positif.\n";
        return;
    }

    echo "Masukkan rating kinerja (1-5): ";
    $rating = trim(fgets(STDIN));
    if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
        echo "Error: Rating kinerja harus antara 1 sampai 5.\n";
        return;
    }

    $jabatan = $data[$index]['jabatan'];
    $gajiPokok = $jabatan === 'Manajer' ? 10000000 : ($jabatan === 'Supervisor' ? 7000000 : 5000000);
    $tunjangan = $jabatan === 'Manajer' ? 3000000 : ($jabatan === 'Supervisor' ? 2000000 : 1000000);
    $lembur = $jamLembur * 25000;
    $bonus = $rating == 5 ? 0.2 * $gajiPokok :
             ($rating == 4 ? 0.15 * $gajiPokok :
             ($rating == 3 ? 0.1 * $gajiPokok :
             ($rating == 2 ? 0.05 * $gajiPokok : 0)));

    $totalGaji = $gajiPokok + $tunjangan + $lembur + $bonus;

    echo "\nGaji Karyawan:\n";
    echo "Nama: {$data[$index]['nama']}\n";
    echo "Jabatan: {$jabatan}\n";
    echo "Gaji Pokok: Rp " . number_format($gajiPokok, 0, ',', '.') . "\n";
    echo "Tunjangan Jabatan: Rp " . number_format($tunjangan, 0, ',', '.') . "\n";
    echo "Jam Lembur: {$jamLembur} x Rp 25.000\n";
    echo "Lembur: Rp " . number_format($lembur, 0, ',', '.') . "\n";
    echo "Bonus Kinerja: Rp " . number_format($bonus, 0, ',', '.') . "\n";
    echo "Total Gaji: Rp " . number_format($totalGaji, 0, ',', '.') . "\n";
}



