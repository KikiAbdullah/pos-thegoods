<?php

use Carbon\Carbon;

function st_aktif($var)
{
    if (empty($var)) {
        return "<center><div class=\"badge bg-primary\">Enabled</div></center>";
    } else {
        return "<center><div class=\"badge bg-light text-body\">Disabled</div></center>";
    }
}

function namaBulan($var)
{
    switch ($var) {
        case 1:
            $result = "Januari";
            break;
        case 2:
            $result = "Februari";
            break;
        case 3:
            $result = "Maret";
            break;
        case 4:
            $result = "April";
            break;
        case 5:
            $result = "Mei";
            break;
        case 6:
            $result = "Juni";
            break;
        case 7:
            $result = "Juli";
            break;
        case 8:
            $result = "Agustus";
            break;
        case 9:
            $result = "September";
            break;
        case 10:
            $result = "Oktober";
            break;
        case 11:
            $result = "November";
            break;
        case 12:
            $result = "Desember";
            break;
        default:
            $result = "";
    }
    return $result;
}


if (!function_exists('formatDate')) {

    function formatDate($from, $to, $date)
    {
        if (!empty($date)) {
            return Carbon::createFromFormat($from, $date)->format($to);
        }
    }
}


if (!function_exists('cleanNumber')) {
    function cleanNumber($val)
    {
        if (!empty($val)) {
            return  str_replace('.00', '', number_format($val, 2, '.', ','));
        }
    }
}


if (!function_exists('responseSuccess')) {

    function responseSuccess($data = [], $message = 'Data saved.')
    {
        return [
            'status'            => true,
            'msg'               => $message,
            'data'              => $data,
        ];
    }
}

if (!function_exists('responseFailed')) {

    function responseFailed($message = 'Gagal')
    {
        return [
            'status'            => false,
            'msg'               => $message,
            'data'              => [],
        ];
    }
}

if (!function_exists('time_elapsed_string')) {
    function time_elapsed_string($datetime, $full = false, $singkat = false)
    {
        $time_ago = strtotime($datetime);
        $current_time = time();
        $time_difference = $current_time - $time_ago;
        $seconds = $time_difference;
        $minutes      = round($seconds / 60);        // value 60 is seconds  
        $hours        = round($seconds / 3600);       //value 3600 is 60 minutes * 60 sec  
        $days         = round($seconds / 86400);      //86400 = 24 * 60 * 60;  
        $weeks        = round($seconds / 604800);     // 7*24*60*60;  
        $months       = round($seconds / 2629440);    //((365+365+365+365+366)/5/12)*24*60*60  
        $years        = round($seconds / 31553280);   //(365+365+365+365+366)/5 * 24 * 60 * 60  
        if ($seconds <= 60) {
            return "Sekarang";
        } else if ($minutes <= 60) {
            if ($minutes == 1) {
                return "1m yang lalu";
            } else {
                return $minutes . "m yang lalu";
            }
        } else if ($hours <= 24) {
            if ($hours == 1) {
                return "1j yang lalu";
            } else {
                return $hours . "j yang lalu";
            }
        } else if ($days <= 7) {
            if ($days == 1) {
                return "Kemarin";
            } else {
                return formatDate('Y-m-d H:i:s', 'd/m/Y', $datetime);
            }
        } else {
            return formatDate('Y-m-d H:i:s', 'd/m/Y', $datetime);
        }
    }
}