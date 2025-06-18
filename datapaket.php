<?php
header('Content-Type: text/html; charset=utf-8');

$api_key = "4e8a4aa99c17f3d3a84e1b5d660f19274c217ce64bfdcf21143dc8b62facc613";

$ekspedisi = $_POST["ekspedisi"] ?? '';
$distrik_terpilih = $_POST["distrik"] ?? '';
$total_berat = $_POST["berat"] ?? 0;

$url = "https://api.binderbyte.com/ongkir?api_key=$api_key&courier=$ekspedisi&destination=$distrik_terpilih&weight=$total_berat";

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => $url,
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

    echo '<option value="">-- Pilih Paket --</option>';

    foreach ($data['value'] as $paket) {
        $service = htmlspecialchars($paket['service']);
        $ongkir = htmlspecialchars($paket['cost']);
        $etd = htmlspecialchars($paket['etd']);

        echo "<option 
            paket=\"$service\" 
            ongkir=\"$ongkir\" 
            etd=\"$etd\">";

        echo "$service / Rp. " . number_format($ongkir) . " ($etd)";
        echo "</option>";
    }
}
?>
