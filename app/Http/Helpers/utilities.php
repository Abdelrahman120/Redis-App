<?php

function sendMail($template, $to, $subject, $data) {
    $dataArray = is_array($data) ? $data : (method_exists($data, 'toArray') ? $data->toArray() : []);

    \Mail::send($template, $dataArray, function ($message) use ($to, $subject) {
        $message->to($to)->subject($subject);
    });
}
