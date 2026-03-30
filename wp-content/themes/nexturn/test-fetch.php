<?php
function wp_external_page_fetch($url) {
    $response = file_get_contents($url);
    return $response ?: false;
}

function extract_clean_body_content($html) {
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    libxml_clear_errors();

    $body = $dom->getElementsByTagName('body')->item(0);
    if (!$body) return false;

    $clean_html = "";
    foreach ($body->childNodes as $child) {
        $clean_html .= $dom->saveHTML($child);
    }
    return $clean_html;
}

function fix_relative_urls($html, $url) {
    $parts = parse_url($url);
    $base_url = $parts['scheme'] . '://' . $parts['host'];

    $html = preg_replace('/(src|href)=["\']\/([^"\']+)["\']/', '$1="' . $base_url . '/$2"', $html);

    return $html;
}

// URL TO TEST
$url = "https://www.netsuite.com/portal/products/erp/financial-management/finance-accounting/accounts-payable-software/payment-automation.shtml?cid=Online_OrgSoc_Global_Champions";

$raw = wp_external_page_fetch($url);
$clean = extract_clean_body_content($raw);
$final = fix_relative_urls($clean, $url);
?>

<!DOCTYPE html>
<html>
<head>
    <title>External Campaign Test</title>
    <link rel="stylesheet" href="test.css"/>
</head>
<body>
    <?= $final ?>
</body>
</html>
