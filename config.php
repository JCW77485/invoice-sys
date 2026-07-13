<?php
function get_config() {
    $config_file = __DIR__ . '/config.json';
    $default_config = [
        'company_name' => 'NETVORA STUDIO',
        'ssm' => 'AS0515392-M',
        'address' => '4238 JALAN UDANG GALAH 1, TAMAN SERI SEGAMBUT, 52000 KUALA LUMPUR, WILAYAH PERSEKUTUAN',
        'payment_instructions' => 'Make sure payment within 10 days',
        'bank_name' => 'Maybank',
        'bank_acc' => '564584401054',
        'account_holder' => 'Netvora'
    ];

    if (!file_exists($config_file)) {
        file_put_contents($config_file, json_encode($default_config, JSON_PRETTY_PRINT));
        return $default_config;
    }

    $json = file_get_contents($config_file);
    $data = json_decode($json, true);
    if (!$data) {
        return $default_config;
    }
    return array_merge($default_config, $data);
}

function save_config($data) {
    $config_file = __DIR__ . '/config.json';
    $current = get_config();
    $updated = array_merge($current, $data);
    return file_put_contents($config_file, json_encode($updated, JSON_PRETTY_PRINT)) !== false;
}
