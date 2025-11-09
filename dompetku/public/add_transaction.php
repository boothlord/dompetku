<?php
require_once __DIR__ . '/../db.php';
require_login();
$uid = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: transactions.php'); exit; }


$type = $_POST['type'];
$amount = (float) str_replace(',', '', $_POST['amount']);
$date = $_POST['date'] ?: date('Y-m-d');
$category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
$note = trim($_POST['note']);


$stmt = $pdo->prepare('INSERT INTO transactions (user_id, category_id, type, amount, note, date) VALUES (?,?,?,?,?,?)');
$stmt->execute([$uid, $category_id, $type, $amount, $note, $date]);
header('Location: transactions.php');