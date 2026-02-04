import React, { createContext, useState, useEffect } from "react";

export const CartContext = createContext();

export const CartProvider = ({ children }) => {
  const [cart, setCart] = useState({});
  const [cartCount, setCartCount] = useState(0);

  useEffect(() => {
    const data = JSON.parse(localStorage.getItem("cart")) || {};
    setCart(data);

    const total = Object.values(data).reduce((sum, qty) => sum + qty, 0);
    setCartCount(total);
  }, []);


  const addToCart = (id) => {
    setCart((prev) => {
      const updated = { ...prev };
      updated[id] = (updated[id] || 0) + 1;

      localStorage.setItem("cart", JSON.stringify(updated));

      const total = Object.values(updated).reduce((s, q) => s + q, 0);
      setCartCount(total);

      return updated;
    });
  };

  return (
    <CartContext.Provider value={{ cart, cartCount, addToCart }}>
      {children}
    </CartContext.Provider>
  );
};

// b