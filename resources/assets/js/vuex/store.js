import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import _ from 'lodash'

Vue.use(Vuex)

const state = {
	user: null,
	cart: [],
	compare_product: []
};

const mutations = {
	SET_USER(state, data){
		state.user = data;
	},
	ADD_ITEM_CART(state, data)
	{

		let item = _.find(state.cart, { product_sku_id: data.product_sku_id });
		if(item === undefined)
			state.cart.push(data)
		else
			item.quantity += data.quantity
		localStorage.setItem('cart', JSON.stringify(state.cart));
	},
	CHANGE_ITEM_CART(state, data)
	{

		let item = _.find(state.cart, { product_sku_id: data.product_sku_id });
		if(item !== undefined)
			item.quantity = data.quantity
		localStorage.setItem('cart', JSON.stringify(state.cart));
	},
	REMOVE_ITEM_CART(state, id)
	{
		state.cart = _.filter(state.cart, i => i.product_sku_id != id);
		localStorage.setItem('cart', JSON.stringify(state.cart));
	},
	UPDATE_CART(state, data)
	{
		state.cart = data;
		localStorage.setItem('cart', JSON.stringify(state.cart));
	},
	ADD_COMPARE_PRODUCT(state, id)
	{
		if(state.compare_product.indexOf(id))
			state.compare_product.push(id);
	},
	REMOVE_COMPARE_PRODUCT(state, id)
	{
		_.remove(state.compare_product, item => item == id);
	}

}

const actions = {
	setUser({commit}, data)
	{
		commit('SET_USER', data);
	},
	logout()
	{
		localStorage.removeItem('user');
        localStorage.removeItem('token');
        location.reload();
	},
	setDataCart({commit}, data)
	{
		commit('UPDATE_CART', data);
	},
	addItemCart({commit}, data)
	{
		commit('ADD_ITEM_CART', data);

		if(state.user)
		{
			return axios.post('/api/cart/items', data)
	       	.then(res => {

	        })
	    }

	    return Promise.resolve(true);

	},
	changeItemCart({commit}, data)
	{
		commit('CHANGE_ITEM_CART', data);

		if(state.user)
		{

	        return axios.put('/api/cart/items/' + data.product_sku_id, data)
	        .then(res => {

	        })
	    }

	    return Promise.resolve(true);

	},
	removeItemCart({commit}, id)
	{
		commit('REMOVE_ITEM_CART', id)
		
		if(state.user)
		{
	       	return axios.delete('/api/cart/items/' + id).then(res => {
	        	
	        })
	    }
        
        return Promise.resolve(true);
	},
	addCompareProduct({commit}, id)
	{
		commit('ADD_COMPARE_PRODUCT', id);
	},
	removeCompareProduct({commit}, id)
	{
		commit('REMOVE_COMPARE_PRODUCT', id);
	}
}

export default new Vuex.Store({
	state, mutations, actions
});