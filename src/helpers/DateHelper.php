<?php
/**
 * DateHelper - Tarih ve saat formatlama yardımcı fonksiyonları
 * 
 * JavaScript kullanmadan PHP ile tarih formatlama
 */

class DateHelper {
    /**
     * Tarih ve saati Türkçe formatında döndürür
     * @param string $dateTime
     * @return string
     */
    public static function formatDateTime($dateTime) {
        if (empty($dateTime)) {
            return '';
        }
        
        $timestamp = strtotime($dateTime);
        return date('d.m.Y H:i', $timestamp);
    }
    
    /**
     * Sadece tarihi Türkçe formatında döndürür
     * @param string $date
     * @return string
     */
    public static function formatDate($date) {
        if (empty($date)) {
            return '';
        }
        
        $timestamp = strtotime($date);
        return date('d.m.Y', $timestamp);
    }
    
    /**
     * Sadece saati formatlar
     * @param string $time
     * @return string
     */
    public static function formatTime($time) {
        if (empty($time)) {
            return '';
        }
        
        // Eğer time string ise (HH:MM:SS formatında)
        if (strlen($time) <= 8 && strpos($time, ':') !== false) {
            $parts = explode(':', $time);
            return sprintf('%02d:%02d', $parts[0], $parts[1]);
        }
        
        // Eğer datetime ise
        $timestamp = strtotime($time);
        return date('H:i', $timestamp);
    }
    
    /**
     * Gün adını Türkçe döndürür
     * @param string $date
     * @return string
     */
    public static function getDayName($date) {
        if (empty($date)) {
            return '';
        }
        
        $days = [
            'Monday' => 'Pazartesi',
            'Tuesday' => 'Salı',
            'Wednesday' => 'Çarşamba',
            'Thursday' => 'Perşembe',
            'Friday' => 'Cuma',
            'Saturday' => 'Cumartesi',
            'Sunday' => 'Pazar'
        ];
        
        $timestamp = strtotime($date);
        $englishDay = date('l', $timestamp);
        
        return $days[$englishDay] ?? $englishDay;
    }
    
    /**
     * İki tarih arasındaki farkı hesaplar
     * @param string $date1
     * @param string $date2
     * @return int Gün cinsinden fark
     */
    public static function getDaysDifference($date1, $date2) {
        $timestamp1 = strtotime($date1);
        $timestamp2 = strtotime($date2);
        
        $difference = abs($timestamp2 - $timestamp1);
        return floor($difference / (60 * 60 * 24));
    }
    
    /**
     * Bugünün tarihini döndürür
     * @param string $format
     * @return string
     */
    public static function today($format = 'Y-m-d') {
        return date($format);
    }
    
    /**
     * Tarihin geçmiş olup olmadığını kontrol eder
     * @param string $date
     * @return bool
     */
    public static function isPast($date) {
        return strtotime($date) < strtotime(date('Y-m-d'));
    }
    
    /**
     * Tarihin gelecek olup olmadığını kontrol eder
     * @param string $date
     * @return bool
     */
    public static function isFuture($date) {
        return strtotime($date) > strtotime(date('Y-m-d'));
    }
    
    /**
     * Tarihin bugün olup olmadığını kontrol eder
     * @param string $date
     * @return bool
     */
    public static function isToday($date) {
        return date('Y-m-d', strtotime($date)) === date('Y-m-d');
    }
    
    /**
     * Zaman farkını insan okunabilir formatta döndürür
     * @param string $dateTime
     * @return string (örn: "2 saat önce", "5 dakika önce")
     */
    public static function timeAgo($dateTime) {
        $timestamp = strtotime($dateTime);
        $diff = time() - $timestamp;
        
        if ($diff < 60) {
            return 'Az önce';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' dakika önce';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' saat önce';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' gün önce';
        } else {
            return self::formatDate($dateTime);
        }
    }
    
    /**
     * Seyahat süresini hesaplar
     * @param string $departureTime
     * @param string $arrivalTime
     * @return string (örn: "3 saat 30 dakika")
     */
    public static function calculateDuration($departureTime, $arrivalTime) {
        $start = strtotime($departureTime);
        $end = strtotime($arrivalTime);
        
        $diff = $end - $start;
        
        $hours = floor($diff / 3600);
        $minutes = floor(($diff % 3600) / 60);
        
        if ($hours > 0 && $minutes > 0) {
            return $hours . ' saat ' . $minutes . ' dakika';
        } elseif ($hours > 0) {
            return $hours . ' saat';
        } else {
            return $minutes . ' dakika';
        }
    }
}
