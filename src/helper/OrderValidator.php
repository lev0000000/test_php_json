<?php
namespace App\Helper;

final class OrderValidator
{
    private const REQUIRED_FIELDS = ['id', 'customer', 'amount', 'status'];
    private const ALLOWED_STATUSES = ['paid', 'pending', 'cancelled'];

    public static function errors(object $order): array
    {
        $errors = [];

        foreach (self::REQUIRED_FIELDS as $field) {
            if (!property_exists($order, $field)) {
                $errors[] = "Отсутствует поле: $field";
            }
        }

        if ($errors) return $errors;

        if (!is_int($order->amount) && !is_float($order->amount) || $order->amount < 0) {
            $errors[] = 'Некорректная сумма';
        }

        if (!in_array($order->status, self::ALLOWED_STATUSES, true)) {
            $errors[] = 'Неизвестный статус';
        }

        return $errors;
    }

    public static function validate(object $order): bool
    {
        return empty(self::errors($order));
    }
}
