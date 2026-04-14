<?php

return [
    'required' => 'Trường :attribute là bắt buộc.',
    'string' => 'Trường :attribute phải là một chuỗi.',
    'max' => [
        'string' => 'Trường :attribute không được dài quá :max ký tự.',
    ],
    'custom' => [
        'name' => [
            'required' => 'Tên là bắt buộc.',
        ],
        'phone' => [
            'required' => 'Số điện thoại là bắt buộc.',
        ],
        'address' => [
            'required' => 'Địa chỉ là bắt buộc.',
        ],
    ],
    'attributes' => [
        'name' => 'tên',
        'phone' => 'số điện thoại',
        'address' => 'địa chỉ',
        'company' => 'đơn vị vận chuyển',
        'cod' => 'COD',
        'item' => 'tên hàng hóa',
        'payer' => 'người trả phí',
    ],
];
