<?php

$api_key = "4e8a4aa99c17f3d3a84e1b5d660f19274c217ce64bfdcf21143dc8b62facc613";

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.binderbyte.com/wilayah/provinsi?api_key=" . $api_key,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_TIMEOUT => 30,
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "Terjadi kesalahan koneksi: " . $err;
} else {
    $array_response = json_decode($response, true);

    // Debug response jika gagal
    if (!isset($array_response['value'])) {
        echo "<pre>";
        print_r($array_response); // LIHAT APA YANG DITERIMA DARI API
        echo "</pre>";
        exit;
    }

    $dataprovinsi = $array_response['value'];

    echo '<option value="">-- Pilih Provinsi --</option>';
    foreach ($dataprovinsi as $provinsi) {
        $id = htmlspecialchars($provinsi['id']);
        $nama = htmlspecialchars($provinsi['name']);
        echo "<option value=\"$id\" id_provinsi=\"$id\">$nama</option>";
    }
}
?>
