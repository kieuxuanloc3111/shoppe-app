import React, { useEffect, useState } from "react";
import axios from "axios";
import { useSelector, useDispatch } from "react-redux";
import { increaseQty, decreaseQty, removeItem } from "../Redux/cartSlice";

const Cart = () => {
  const dispatch = useDispatch();
  const { cart } = useSelector((state) => state.cart); // { id: qty, ... }

  const [cartItems, setCartItems] = useState([]);
  const [subTotal, setSubTotal] = useState(0);

  const ecoTax = 2; 

  //  tổng tiền
  const updateTotal = (items) => {
    let total = 0;
    items.forEach((i) => {
      total += i.price * i.qty;
    });
    setSubTotal(total);
  };

  // gọi API 
  useEffect(() => {
    const fetchCart = async () => {
      if (!cart || Object.keys(cart).length === 0) {
        setCartItems([]);
        setSubTotal(0);
        return;
      }

      try {
        const res = await axios.post(
          "http://localhost/laravel8/laravel8/public/api/product/cart",
          cart,
          { headers: { "Content-Type": "application/json" } }
        );

        const items = res.data.data || [];
        setCartItems(items);
        updateTotal(items);
      } catch (err) {
        console.log("CART ERROR:", err);
      }
    };

    fetchCart();
  }, [cart]);


  const getImg = (item) => {
    let arr = [];
    try {
      arr = JSON.parse(item.image);
    } catch {
      arr = [];
    }
    const first = arr[0];
    return `http://localhost/laravel8/laravel8/public/upload/product/${item.id_user}/${first}`;
  };

  return (
    <div>
      {/*CART ITEMS */}
      <section id="cart_items">
        <div className="container">
          <div className="breadcrumbs">
            <ol className="breadcrumb">
              <li><a href="#">Home</a></li>
              <li className="active">Shopping Cart</li>
            </ol>
          </div>

          <div className="table-responsive cart_info">
            <table className="table table-condensed">
              <thead>
                <tr className="cart_menu">
                  <td className="image">Item</td>
                  <td className="description"></td>
                  <td className="price">Price</td>
                  <td className="quantity">Quantity</td>
                  <td className="total">Total</td>
                  <td></td>
                </tr>
              </thead>

              <tbody>
                {cartItems.length === 0 && (
                  <tr>
                    <td colSpan="6" style={{ textAlign: "center", padding: 20 }}>
                      Giỏ hàng đang trống.
                    </td>
                  </tr>
                )}

                {cartItems.map((item) => (
                  <tr key={item.id}>
                    <td className="cart_product">
                      <a href="#">
                        <img
                          src={getImg(item)}
                          alt=""
                          style={{ width: 80, height: 80, objectFit: "cover" }}
                        />
                      </a>
                    </td>

                    <td className="cart_description">
                      <h4><a href="#">{item.name}</a></h4>
                      <p>Product ID: {item.id}</p>
                    </td>

                    <td className="cart_price">
                      <p>${item.price}</p>
                    </td>

                    <td className="cart_quantity">
                      <div className="cart_quantity_button">
                        <a
                          className="cart_quantity_up"
                          style={{ cursor: "pointer" }}
                          onClick={() => dispatch(increaseQty(item.id))}
                        >
                          {" "}
                          +{" "}
                        </a>

                        <input
                          className="cart_quantity_input"
                          type="text"
                          value={item.qty}
                          readOnly
                        />

                        <a
                          className="cart_quantity_down"
                          style={{ cursor: "pointer" }}
                          onClick={() => dispatch(decreaseQty(item.id))}
                        >
                          {" "}
                          -{" "}
                        </a>
                      </div>
                    </td>

                    <td className="cart_total">
                      <p className="cart_total_price">
                        ${item.price * item.qty}
                      </p>
                    </td>

                    <td className="cart_delete">
                      <a
                        className="cart_quantity_delete"
                        style={{ cursor: "pointer" }}
                        onClick={() => dispatch(removeItem(item.id))}
                      >
                        <i className="fa fa-times" />
                      </a>
                    </td>
                  </tr>
                ))}
              </tbody>

            </table>
          </div>
        </div>
      </section>

      {/* DO ACTION */}
      <section id="do_action">
        <div className="container">
          <div className="heading">
            <h3>What would you like to do next?</h3>
            <p>
              Choose if you have a discount code or reward points you want to
              use or would like to estimate your delivery cost.
            </p>
          </div>

          <div className="row">
            <div className="col-sm-6">
              <div className="chose_area">
                <ul className="user_option">
                  <li>
                    <input type="checkbox" />
                    <label>Use Coupon Code</label>
                  </li>
                  <li>
                    <input type="checkbox" />
                    <label>Use Gift Voucher</label>
                  </li>
                  <li>
                    <input type="checkbox" />
                    <label>Estimate Shipping &amp; Taxes</label>
                  </li>
                </ul>

                <ul className="user_info">
                  <li className="single_field">
                    <label>Country:</label>
                    <select>
                      <option>United States</option>
                      <option>Canada</option>
                    </select>
                  </li>

                  <li className="single_field">
                    <label>Region / State:</label>
                    <select>
                      <option>Select</option>
                      <option>London</option>
                    </select>
                  </li>

                  <li className="single_field zip-field">
                    <label>Zip Code:</label>
                    <input type="text" />
                  </li>
                </ul>

                <a className="btn btn-default update" href="#">
                  Get Quotes
                </a>
                <a className="btn btn-default check_out" href="#">
                  Continue
                </a>
              </div>
            </div>

            {/* subTotal + ecoTax */}
            <div className="col-sm-6">
              <div className="total_area">
                <ul>
                  <li>
                    Cart Sub Total <span>${subTotal}</span>
                  </li>
                  <li>
                    Eco Tax <span>${cartItems.length * ecoTax}</span>
                  </li>
                  <li>
                    Shipping Cost <span>Free</span>
                  </li>
                  <li>
                    Total{" "}
                    <span>
                      ${subTotal + cartItems.length * ecoTax}
                    </span>
                  </li>
                </ul>

                <a className="btn btn-default update" href="#">
                  Update
                </a>
                <a className="btn btn-default check_out" href="#">
                  Check Out
                </a>
              </div>
            </div>

          </div>
        </div>
      </section>
    </div>
  );
};

export default Cart;
