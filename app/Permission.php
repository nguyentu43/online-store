<?php

namespace App;

class Permission
{
	public static function getList()
	{
		$permission = [
			[ 'name' => 'Toàn quyền', 'value' => 'all.manage'],
			[ 'name' => 'Mở trang quản lí', 'value' => 'admin-page.read'],
			[ 'name' => 'Lưu file', 'value' => 'storage.insert'],
			[ 'name' => 'Xoá file', 'value' => 'storage.delete']
		];

		$models = [
			'Dashbroad' => 'dashbroad', 
			'Thương hiệu' => 'brand', 
			'Campaign' => 'campaign', 
			'Danh mục' => 'category', 
			'Bình luận' => 'comment', 
			'Đơn hàng' => 'order', 
			'Sản phẩm' => 'product',
			'Loại sản phẩm' => 'producttype',
			'Thuộc tính sản phẩm' => 'productattr',
			'Đánh giá' => 'rate', 
			'Phân quyền' => 'role',
			'Thống kê' => 'statistical',
			'Tài khoản' => 'user',
			'Mã giảm giá' => 'discount-code'
		];

		$action = [
			'Đọc' => 'read',
			'Sửa' => 'update',
			'Tạo' => 'create',
			'Xoá' => 'delete',
			'Tất cả' => 'manage'
		];

		$owner = [
			'comment',
			'rate'
		];

		foreach($models as $model_key => $model_value)
		{
			foreach($action as $action_key => $action_value)
			{
				array_push($permission, [ 'name' => "$model_key - $action_key",  'value' => $model_value.'.'.$action_value]);
			}
			if(in_array($model_value, $owner))
			{
				array_push($permission, [ 'name' => "$model_key - Sửa (Sở hữu)",  'value' => $model_value.'.'.'update.owner']);
				array_push($permission, [ 'name' => "$model_key - Xoá (Sở hữu)",  'value' => $model_value.'.'.'delete.owner']);
			}
		}

		return $permission;
	}
}