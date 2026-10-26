<?php


require_once __DIR__ . '/helper/OrderValidator.php';

use App\Helper\OrderValidator;

$file_path = dirname(__DIR__, 1) . '/src/data/order.json';

$orders = json_decode(file_get_contents($file_path)) ?: false;

!$orders && die('Ошибка чтения файла и декодирования ');

$paidOrders = array_filter($orders, fn($item) => $item->status === 'paid');

$totalAmount = array_reduce($paidOrders, fn($sum, $order) => $sum + $order->amount);

$paidCount = count($paidOrders);

$maxOrder = (usort($paidOrders, fn($a, $b) => $b->amount <=> $a->amount) ? $paidOrders[0] : null);

$validOrders = array_filter($orders, function ($order) {
    if (!is_object($order)) {
        echo "Некорректный формат заказа\n";
        return false;
    }

    $errors = OrderValidator::errors($order);

    if ($errors) {
        echo implode(', ', $errors) . PHP_EOL;
        return false;
    }

    return true;
});

$groupedOrders = array_reduce($validOrders, function ($result, $order) {
    $result[$order->status] = ($result[$order->status] ?? 0) + 1;
    return $result;
}, []);


echo "Количество оплаченных заказов: " . count($paidOrders) . PHP_EOL;
echo "Общая сумма: $totalAmount" . PHP_EOL;

if ($maxOrder !== null) {
    echo "Самый дорогой заказ: №{$maxOrder->id}, сумма {$maxOrder->amount}" . PHP_EOL;
}

echo "Группировка по статусам:" . PHP_EOL;
print_r($groupedOrders);