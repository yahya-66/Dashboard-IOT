<?php
include 'koneksi.php';

if(isset($_POST['submit'])) {

    $nama = $_POST['nama'];
    $role = $_POST['role'];

    mysqli_query(
        $conn,
        "INSERT INTO admin(nama, role)
        VALUES('$nama','$role')"
    );
}

$data = mysqli_query($conn, "SELECT * FROM admin");
?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD Admin</title>
</head>
<body>

<h1>Tambah Admin</h1>

<form method="POST">

<input type="text" name="nama" placeholder="Nama">

<input type="text" name="role" placeholder="Role">

<button type="submit" name="submit">
Simpan
</button>

</form>

<hr>

<h2>Data Admin</h2>

<table border="1" cellpadding="10">

<tr>
    <th>ID</th>
    <th>Nama</th>
    <th>Role</th>
</tr>

<?php while($row = mysqli_fetch_assoc($data)) { ?>

<tr>
    <td><?= $row['id']; ?></td>
    <td><?= $row['nama']; ?></td>
    <td><?= $row['role']; ?></td>
</tr>

<?php } ?>

</table>

</body>
</html>
