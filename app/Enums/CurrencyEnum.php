<?php

namespace App\Enums;

enum CurrencyEnum: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case GBP = 'GBP';
    case CAD = 'CAD';
    case AUD = 'AUD';
    case JPY = 'JPY';
    case CHF = 'CHF';
    case CNY = 'CNY';
    case INR = 'INR';
    case BRL = 'BRL';
    case MXN = 'MXN';
    case ZAR = 'ZAR';
    case RUB = 'RUB';
    case KRW = 'KRW';
    case SGD = 'SGD';
    case HKD = 'HKD';
    case NOK = 'NOK';
    case SEK = 'SEK';
    case DKK = 'DKK';
    case PLN = 'PLN';
    case CZK = 'CZK';
    case HUF = 'HUF';
    case TRY = 'TRY';
    case ILS = 'ILS';
    case AED = 'AED';
    case SAR = 'SAR';
    case EGP = 'EGP';
    case THB = 'THB';
    case MYR = 'MYR';
    case PHP = 'PHP';
    case IDR = 'IDR';
    case VND = 'VND';

    public function label(): string
    {
        return match ($this) {
            self::USD => 'USD - US Dollar',
            self::EUR => 'EUR - Euro',
            self::GBP => 'GBP - British Pound',
            self::CAD => 'CAD - Canadian Dollar',
            self::AUD => 'AUD - Australian Dollar',
            self::JPY => 'JPY - Japanese Yen',
            self::CHF => 'CHF - Swiss Franc',
            self::CNY => 'CNY - Chinese Yuan',
            self::INR => 'INR - Indian Rupee',
            self::BRL => 'BRL - Brazilian Real',
            self::MXN => 'MXN - Mexican Peso',
            self::ZAR => 'ZAR - South African Rand',
            self::RUB => 'RUB - Russian Ruble',
            self::KRW => 'KRW - South Korean Won',
            self::SGD => 'SGD - Singapore Dollar',
            self::HKD => 'HKD - Hong Kong Dollar',
            self::NOK => 'NOK - Norwegian Krone',
            self::SEK => 'SEK - Swedish Krona',
            self::DKK => 'DKK - Danish Krone',
            self::PLN => 'PLN - Polish Zloty',
            self::CZK => 'CZK - Czech Koruna',
            self::HUF => 'HUF - Hungarian Forint',
            self::TRY => 'TRY - Turkish Lira',
            self::ILS => 'ILS - Israeli Shekel',
            self::AED => 'AED - UAE Dirham',
            self::SAR => 'SAR - Saudi Riyal',
            self::EGP => 'EGP - Egyptian Pound',
            self::THB => 'THB - Thai Baht',
            self::MYR => 'MYR - Malaysian Ringgit',
            self::PHP => 'PHP - Philippine Peso',
            self::IDR => 'IDR - Indonesian Rupiah',
            self::VND => 'VND - Vietnamese Dong',
        };
    }

    public function symbol(): string
    {
        return match ($this) {
            self::USD => '$',
            self::EUR => '€',
            self::GBP => '£',
            self::CAD => 'C$',
            self::AUD => 'A$',
            self::JPY => '¥',
            self::CHF => 'CHF',
            self::CNY => '¥',
            self::INR => '₹',
            self::BRL => 'R$',
            self::MXN => '$',
            self::ZAR => 'R',
            self::RUB => '₽',
            self::KRW => '₩',
            self::SGD => 'S$',
            self::HKD => 'HK$',
            self::NOK => 'kr',
            self::SEK => 'kr',
            self::DKK => 'kr',
            self::PLN => 'zł',
            self::CZK => 'Kč',
            self::HUF => 'Ft',
            self::TRY => '₺',
            self::ILS => '₪',
            self::AED => 'د.إ',
            self::SAR => 'ر.س',
            self::EGP => '£',
            self::THB => '฿',
            self::MYR => 'RM',
            self::PHP => '₱',
            self::IDR => 'Rp',
            self::VND => '₫',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function options(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'symbol' => $case->symbol(),
        ], self::cases());
    }
}