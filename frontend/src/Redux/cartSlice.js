import { createSlice } from "@reduxjs/toolkit";

const initialCart = JSON.parse(localStorage.getItem("cart")) || {};

const getTotal = (cart) => {
  return Object.values(cart).reduce((sum, qty) => sum + qty, 0);
};

export const cartSlice = createSlice({
  name: "cart",
  initialState: {
    cart: initialCart,
    cartCount: getTotal(initialCart),
  },

  reducers: {
    addToCart: (state, action) => {
      const id = action.payload;

      state.cart[id] = (state.cart[id] || 0) + 1;
      state.cartCount = getTotal(state.cart);

      localStorage.setItem("cart", JSON.stringify(state.cart));
    },

    increaseQty: (state, action) => {
      const id = action.payload;
      state.cart[id] += 1;
      state.cartCount = getTotal(state.cart);
      localStorage.setItem("cart", JSON.stringify(state.cart));
    },

    decreaseQty: (state, action) => {
      const id = action.payload;

      if (state.cart[id] > 1) {
        state.cart[id] -= 1;
      } else {
        delete state.cart[id];
      }

      state.cartCount = getTotal(state.cart);
      localStorage.setItem("cart", JSON.stringify(state.cart));
    },

    removeItem: (state, action) => {
      const id = action.payload;
      delete state.cart[id];
      state.cartCount = getTotal(state.cart);
      localStorage.setItem("cart", JSON.stringify(state.cart));
    },
  },
});

export const { addToCart, increaseQty, decreaseQty, removeItem } =
  cartSlice.actions;

export default cartSlice.reducer;
