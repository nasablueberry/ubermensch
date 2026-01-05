<?php
require_once('DBconnect.php');
if($_SERVER['REQUEST_METHOD']==='POST'){
    $inputs = [
        'name' => $_POST['name'] ?? null,
        'provider_type' => $_POST['provider_type'] ?? null,
        'contact_email' => $_POST['contact_email'] ?? $_POST['email'] ?? null,
        'contact_phone' => $_POST['contact_phone'] ?? $_POST['phone'] ?? null,
        'address' => $_POST['address'] ?? null,
    ];

    // Inspect actual table columns to avoid unknown-column errors
    $colsRes = $conn->query("DESCRIBE aidprovider");
    if(!$colsRes){
        $colsRes = $conn->query("DESCRIBE AidProvider");
    }
    if(!$colsRes){
        die('Could not inspect aidprovider table: ' . htmlspecialchars($conn->error));
    }
    $actualCols = [];
    while($c = $colsRes->fetch_assoc()) $actualCols[] = $c['Field'];

    // Map logical keys to actual column names if they differ
    $candidates = [
        'name' => ['name','provider_name','company_name','org_name'],
        'provider_type' => ['provider_type','type'],
        'contact_email' => ['contact_email','email','provider_email'],
        'contact_phone' => ['contact_phone','phone','provider_phone'],
        'address' => ['address','contact_address','provider_address']
    ];

    $insertCols = [];
    $placeholders = [];
    $values = [];
    foreach($inputs as $key => $val){
        // find first matching actual column
        $found = null;
        foreach($candidates[$key] as $cand){
            if(in_array($cand, $actualCols)){
                $found = $cand; break;
            }
        }
        if($found !== null){
            $insertCols[] = $found;
            $placeholders[] = '?';
            $values[] = $val;
        }
    }

    if(empty($insertCols)){
        die('No matching columns found on aidprovider table.');
    }

    $sql = "INSERT INTO aidprovider (" . implode(',', $insertCols) . ") VALUES (" . implode(',', $placeholders) . ")";
    $stmt = $conn->prepare($sql);
    if($stmt === false){
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // bind params dynamically (all treated as strings)
    $types = str_repeat('s', count($values));
    $bind_names[] = $types;
    for ($i=0; $i<count($values); $i++) {
        $bind_names[] = & $values[$i];
    }
    call_user_func_array(array($stmt,'bind_param'), $bind_names);

    try{
        $stmt->execute();
        header('Location: show_providers.php'); exit;
    }catch(Exception $e){
        echo 'Error: '.htmlspecialchars($e->getMessage());
    }
}
?>