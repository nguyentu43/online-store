<?php

namespace App;

class OrderStatusCode{
	const DA_TIEP_NHAN = 1;
	const DANG_XU_LI = 2;
	const DANG_GIAO = 3;
	const DA_GIAO = 4;
	const HOAN_TRA = 5;
	const HUY = 6;

	public static function getString($type)
	{
		switch ($type) {
			case OrderStatusCode::DA_TIEP_NHAN:
				return 'Đã tiếp nhận';
				break;
			case OrderStatusCode::DANG_XU_LI:
				return 'Đang xử lí';
				break;
			case OrderStatusCode::DANG_GIAO:
				return 'Đang giao';
				break;
			case OrderStatusCode::DA_GIAO:
				return 'Đã giao';
				break;
			case OrderStatusCode::HOAN_TRA:
				return 'Hoàn trả';
				break;
			case OrderStatusCode::HUY:
				return 'Huỷ đơn hàng';
				break;
			default:
				break;
		}
	}
}