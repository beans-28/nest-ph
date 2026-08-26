<?php
@mkdir(__DIR__ . '/../storage/app/public/vr-assets', 0777, true);
$b64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5CYII=';
$data = base64_decode($b64);
file_put_contents(__DIR__ . '/../storage/app/public/vr-assets/test_vr.png', $data);
echo "created\n";
