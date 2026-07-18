import { createSlice } from "@reduxjs/toolkit";

const loadWishlistFromStorage = () => {
  try {
    const data = JSON.parse(localStorage.getItem("wishlist"));
    if (Array.isArray(data)) return data;
    return [];
  } catch {
    return [];
  }
};

const initialItems = loadWishlistFromStorage();

const wishlistSlice = createSlice({
  name: "wishlist",
  initialState: {
    items: initialItems, // [id]
  },
  reducers: {
    toggleWishlist: (state, action) => {
      const id = action.payload;
      let newItems;

      if (state.items.includes(id)) {
        // remove
        newItems = state.items.filter((itemId) => itemId !== id);
      } else {
        // add
        newItems = [...state.items, id];
      }

      state.items = newItems;
      localStorage.setItem("wishlist", JSON.stringify(newItems));
    },
  },
});

export const { toggleWishlist } = wishlistSlice.actions;

export default wishlistSlice.reducer;
