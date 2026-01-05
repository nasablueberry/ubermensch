<?php
require_once('DBconnect.php');
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    // minimal form to call this endpoint
    if(isset($_GET['application_id'])){
        $app = (int)$_GET['application_id'];
        echo '<form method="post"><input type="hidden" name="application_id" value="'.htmlspecialchars($app).'">Amount: <input name="amount" step="0.01" type="number"><br>Method: <input name="payment_method"><br>Transaction ref: <input name="transaction_reference"><br><input type="submit" value="Release"></form>';
        exit;
    }
    echo 'POST required'; exit;
}
$app_id = (int)($_POST['application_id'] ?? 0);
$amount = (float)($_POST['amount'] ?? 0);
$method = $_POST['payment_method'] ?? null;
$tx = $_POST['transaction_reference'] ?? null;

// load application and program
$stmt = $conn->prepare('SELECT a.*, sp.program_id, sp.funds_remaining FROM Application a JOIN ScholarshipProgram sp ON a.program_id = sp.program_id WHERE a.application_id = ?');
$stmt->bind_param('i',$app_id); $stmt->execute(); $row = $stmt->get_result()->fetch_assoc();
if(!$row){ echo 'Application not found'; exit; }
if($row['status'] !== 'approved'){ echo 'Only approved applications can receive disbursements'; exit; }

$program_id = $row['program_id'];
$funds_remaining = (float)$row['funds_remaining'];
if($amount <= 0){ echo 'Invalid amount'; exit; }
if($amount > $funds_remaining){ echo 'Not enough funds remaining'; exit; }

// transaction: decrement funds and insert disbursement
try{
    $conn->begin_transaction();
    $upd = $conn->prepare('UPDATE ScholarshipProgram SET funds_remaining = funds_remaining - ? WHERE program_id = ? AND funds_remaining >= ?');
    $upd->bind_param('did', $amount, $program_id, $amount);
    // Note: bind types corrected below in execute
    $upd->execute();
    $ins = $conn->prepare('INSERT INTO Disbursement (amount_released, payment_method, transaction_reference, application_id) VALUES (?, ?, ?, ?)');
    $ins->bind_param('dssi', $amount, $method, $tx, $app_id);
    $ins->execute();
    $conn->commit();
    echo 'Disbursement recorded';
} catch(Exception $e){
    $conn->rollback();
    echo 'Error: '.htmlspecialchars($e->getMessage());
}
?>