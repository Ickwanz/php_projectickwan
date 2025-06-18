<?php
header('Content-Type: text/html; charset=utf-8');

$api_key = "4e8a4aa99c17f3d3a84e1b5d660f19274c217ce64bfdcf21143dc8b62facc613";

$id_provinsi = $_POST['id_provinsi'] ?? '';

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.binderbyte.com/wilayah/kabupaten?api_key=$api_key&id_provinsi=$id_provinsi",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_TIMEOUT => 30,
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "Terjadi kesalahan koneksi: $err";
} else {
    $data = json_decode($response, true);

    if (!isset($data['value'])) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        exit;
    }

    echo '<option value="">-- Pilih Kota/Kabupaten --</option>';
    foreach ($data['value'] as $kabupaten) {
        $id_kab = htmlspecialchars($kabupaten['id']);
        $nama_kab = htmlspecialchars($kabupaten['name']);
        $id_prov = htmlspecialchars($kabupaten['id_provinsi']);

        echo "<option value=\"$id_kab\" 
            id_distrik=\"$id_kab\" 
            nama_provinsi=\"$id_prov\" 
            nama_distrik=\"$nama_kab\" 
            tipe_distrik=\"Kota/Kab\">$nama_kab</option>";
    }
}
?>
