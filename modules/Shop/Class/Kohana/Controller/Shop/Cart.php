<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 购物车控制器
 */
class Kohana_Controller_Shop_Cart {

	/**
	 * 购物车首页
	 */
	public function action_index()
	{
		//
	}
	
	/**
	 * 添加到购物车，返回json信息
	 */
	public function action_add()
	{
		if ($this->request->is_post())
		{
			try
			{
				$cart_item = Model::factory('Shop.Cart.Item')
					->values($this->request->post())
					->save();
			}
			catch (ORM_Validation_Exception $e)
			{
			}
		}
	}
	
	/**
	 * 清空购物车
	 */
	public function action_clear()
	{
	}
	
	/**
	 * 更新商品数量等等
	 */
	public function action_update()
	{
	}
	
	/**
	 * 从购物车删除
	 */
	public function action_remove()
	{
	}
}
